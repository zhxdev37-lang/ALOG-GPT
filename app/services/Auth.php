<?php
/**
 * Auth - Service d'authentification central
 * Sessions, JWT-like stateless token option, RBAC
 */
class Auth {
    
    public static function user(): ?array {
        Session::start();
        $userId = Session::get('user_id');
        if (!$userId) return null;
        
        $user = (new User())->find((int)$userId);
        if ($user) {
            $user['role'] = (new Role())->find((int)$user['role_id']);
            $user['permissions'] = json_decode($user['role']['permissions'] ?? '[]', true);
        }
        return $user;
    }
    
    public static function check(): bool {
        return self::user() !== null;
    }
    
    public static function id(): ?int {
        $user = self::user();
        return $user ? (int)$user['id'] : null;
    }
    
    public static function hasRole(string $role): bool {
        $user = self::user();
        if (!$user) return false;
        if ($user['role']['slug'] === 'super_admin') return true;
        return $user['role']['slug'] === $role;
    }
    
    public static function can(string $permission): bool {
        $user = self::user();
        if (!$user) return false;
        
        $permissions = $user['permissions'] ?? [];
        
        // Super admin wildcard
        if (in_array('*', $permissions)) return true;
        
        // Exact match
        if (in_array($permission, $permissions)) return true;
        
        // Wildcard match (e.g., "lessons.*" matches "lessons.create")
        $parts = explode('.', $permission);
        if (count($parts) === 2) {
            $wildcard = $parts[0] . '.*';
            if (in_array($wildcard, $permissions)) return true;
        }
        
        return false;
    }
    
    public static function login(string $email, string $password, bool $remember = false): array {
        // Rate limiting
        if (!Security::checkRateLimit('login', MAX_LOGIN_ATTEMPTS, LOCKOUT_TIME)) {
            return ['success' => false, 'error' => 'Trop de tentatives. Veuillez réessayer dans 30 minutes.'];
        }
        
        $user = (new User())->findBy('email', $email);
        
        if (!$user) {
            Logger::loginAttempt($email, false);
            return ['success' => false, 'error' => 'Email ou mot de passe incorrect.'];
        }
        
        if ($user['status'] === 'suspended') {
            return ['success' => false, 'error' => 'Votre compte est suspendu. Contactez le support.'];
        }
        
        if (!Security::verifyPassword($password, $user['password_hash'])) {
            Logger::loginAttempt($email, false);
            return ['success' => false, 'error' => 'Email ou mot de passe incorrect.'];
        }
        
        // Update last login
        Database::execute(
            "UPDATE users SET last_login_at = NOW(), last_login_ip = :ip WHERE id = :id",
            [':ip' => Security::getIp(), ':id' => $user['id']]
        );
        
        Logger::loginAttempt($email, true);
        
        self::setUserSession($user);
        
        return ['success' => true, 'user' => $user];
    }
    
    public static function register(array $data): array {
        $userModel = new User();
        
        if ($userModel->exists('email', $data['email'])) {
            return ['success' => false, 'error' => 'Cet email est déjà utilisé.'];
        }
        
        $insertData = [
            'role_id' => 3, // Student
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password_hash' => Security::hashPassword($data['password']),
            'avatar' => $data['avatar'] ?? 'avatar1.png',
            'birth_date' => $data['birth_date'] ?? null,
            'region' => $data['region'] ?? null,
            'school_level_id' => $data['school_level_id'] ?? null,
            'filiere_id' => $data['filiere_id'] ?? null,
            'plan_id' => 1,
            'status' => 'pending',
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $userId = $userModel->create($insertData);
        
        if (!$userId) {
            return ['success' => false, 'error' => 'Erreur lors de la création du compte.'];
        }
        
        // Send verification email
        self::sendVerificationEmail($userId, $data['email']);
        
        return ['success' => true, 'user_id' => $userId];
    }
    
    public static function sendVerificationEmail(int $userId, string $email): void {
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', time() + 86400);
        
        Database::execute(
            "INSERT INTO email_verifications (user_id, token, expires_at) VALUES (:uid, :token, :expires)",
            [':uid' => $userId, ':token' => $token, ':expires' => $expires]
        );
        
        $verifyUrl = APP_URL . '/verifier-email?token=' . $token;
        
        $subject = 'Vérifiez votre email - ALOG Academy';
        $body = "Bonjour,<br><br>Merci de vous être inscrit sur ALOG Academy.<br><br>
                Cliquez sur le lien suivant pour vérifier votre email :<br>
                <a href='{$verifyUrl}'>{$verifyUrl}</a><br><br>
                Ce lien expire dans 24 heures.<br><br>
                L'équipe ALOG Academy";
        
        Mailer::send($email, $subject, $body);
    }
    
    public static function verifyEmail(string $token): bool {
        $verification = Database::fetch(
            "SELECT * FROM email_verifications WHERE token = :token AND used_at IS NULL AND expires_at > NOW() LIMIT 1",
            [':token' => $token]
        );
        
        if (!$verification) {
            return false;
        }
        
        Database::beginTransaction();
        
        Database::execute(
            "UPDATE email_verifications SET used_at = NOW() WHERE id = :id",
            [':id' => $verification['id']]
        );
        
        Database::execute(
            "UPDATE users SET email_verified_at = NOW(), status = 'active' WHERE id = :id",
            [':id' => $verification['user_id']]
        );
        
        Database::commit();
        
        return true;
    }
    
    public static function forgotPassword(string $email): bool {
        $user = (new User())->findBy('email', $email);
        if (!$user) return false;
        
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', time() + 3600);
        
        Database::execute(
            "INSERT INTO password_resets (user_id, token, expires_at) VALUES (:uid, :token, :expires)",
            [':uid' => $user['id'], ':token' => $token, ':expires' => $expires]
        );
        
        $resetUrl = APP_URL . '/reinitialiser-mot-de-passe?token=' . $token;
        
        $subject = 'Réinitialisation de mot de passe - ALOG Academy';
        $body = "Bonjour,<br><br>Vous avez demandé une réinitialisation de mot de passe.<br><br>
                Cliquez ici pour réinitialiser :<br>
                <a href='{$resetUrl}'>{$resetUrl}</a><br><br>
                Ce lien expire dans 1 heure.<br><br>
                Si vous n'avez pas fait cette demande, ignorez cet email.";
        
        Mailer::send($email, $subject, $body);
        
        return true;
    }
    
    public static function resetPassword(string $token, string $newPassword): bool {
        $reset = Database::fetch(
            "SELECT * FROM password_resets WHERE token = :token AND used_at IS NULL AND expires_at > NOW() LIMIT 1",
            [':token' => $token]
        );
        
        if (!$reset) return false;
        
        $hash = Security::hashPassword($newPassword);
        
        Database::beginTransaction();
        
        Database::execute(
            "UPDATE users SET password_hash = :hash WHERE id = :id",
            [':hash' => $hash, ':id' => $reset['user_id']]
        );
        
        Database::execute(
            "UPDATE password_resets SET used_at = NOW() WHERE id = :id",
            [':id' => $reset['id']]
        );
        
        Database::commit();
        
        return true;
    }
    
    public static function logout(): void {
        Session::destroy();
    }
    
    private static function setUserSession(array $user): void {
        Session::set('user_id', $user['id']);
        Session::set('user_email', $user['email']);
        Session::set('user_role', $user['role_id']);
        Session::regenerateIfNeeded();
    }
}
