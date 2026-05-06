<?php
/**
 * AuthController - Authentification et gestion de compte
 * Inscription, connexion, Google, vérification email
 */
class AuthController extends BaseController {
    
    public function showLogin(): void {
        $this->requireGuest();
        $seo = generateSeoMeta(['title' => 'Connexion', 'description' => 'Connectez-vous à votre compte ALOG Academy.']);
        $this->view('auth/login', ['seo' => $seo]);
    }
    
    public function login(): void {
        $this->requireGuest();
        
        $email = Security::email('email');
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']);
        
        if (!$email || !$password) {
            Session::flash('errors', ['email' => 'Veuillez fournir un email et un mot de passe.']);
            Router::back();
        }
        
        $result = Auth::login($email, $password, $remember);
        
        if (!$result['success']) {
            Session::flash('errors', ['email' => $result['error']]);
            Router::back();
        }
        
        $redirect = Session::flash('redirect_after_login') ?? '/tableau-de-bord';
        Router::redirect($redirect);
    }
    
    public function showRegister(): void {
        $this->requireGuest();
        $seo = generateSeoMeta(['title' => 'Inscription', 'description' => 'Rejoignez ALOG Academy gratuitement.']);
        
        $levels = (new SchoolLevel())->getActive();
        $regions = getRegions();
        $avatars = getAvatarOptions();
        
        $this->view('auth/register', [
            'seo' => $seo,
            'levels' => $levels,
            'regions' => $regions,
            'avatars' => $avatars,
            'filieres' => (new Filiere())->getActive()
        ]);
    }
    
    public function register(): void {
        $this->requireGuest();
        
        $validator = new Validator($_POST);
        $validator->required('first_name', 'Prénom')->max('first_name', 100)
                  ->required('last_name', 'Nom')->max('last_name', 100)
                  ->required('email', 'Email')->email('email')
                  ->required('password', 'Mot de passe')->min('password', 8)
                  ->match('password', 'password_confirmation', 'Mot de passe', 'Confirmation')
                  ->required('birth_date', 'Date de naissance')->date('birth_date')
                  ->required('region', 'Région')->in('region', getRegions())
                  ->required('school_level_id', 'Niveau scolaire')->numeric('school_level_id')
                  ->required('avatar', 'Avatar')->in('avatar', array_keys(getAvatarOptions()));
        
        if ($validator->fails()) {
            Session::flash('errors', $validator->errors());
            Router::back();
        }
        
        $result = Auth::register([
            'first_name' => Security::input('first_name'),
            'last_name' => Security::input('last_name'),
            'email' => Security::email('email'),
            'password' => $_POST['password'],
            'phone' => Security::input('phone'),
            'birth_date' => Security::input('birth_date'),
            'region' => Security::input('region'),
            'school_level_id' => Security::int('school_level_id'),
            'filiere_id' => Security::int('filiere_id') ?: null,
            'avatar' => Security::input('avatar')
        ]);
        
        if (!$result['success']) {
            Session::flash('errors', ['email' => $result['error']]);
            Router::back();
        }
        
        Session::flash('success', 'Inscription réussie ! Vérifiez votre email pour activer votre compte.');
        Router::redirect('/connexion');
    }
    
    public function verifyEmail(): void {
        $token = $_GET['token'] ?? '';
        
        if (Auth::verifyEmail($token)) {
            Session::flash('success', 'Votre email a été vérifié avec succès. Vous pouvez maintenant vous connecter.');
        } else {
            Session::flash('errors', ['global' => 'Le lien de vérification est invalide ou a expiré.']);
        }
        
        Router::redirect('/connexion');
    }
    
    public function showForgotPassword(): void {
        $this->requireGuest();
        $seo = generateSeoMeta(['title' => 'Mot de passe oublié', 'description' => 'Réinitialisez votre mot de passe ALOG Academy.']);
        $this->view('auth/forgot-password', ['seo' => $seo]);
    }
    
    public function forgotPassword(): void {
        $this->requireGuest();
        $email = Security::email('email');
        
        if (!$email) {
            Session::flash('errors', ['email' => 'Veuillez entrer une adresse email valide.']);
            Router::back();
        }
        
        Auth::forgotPassword($email);
        Session::flash('success', 'Si un compte existe avec cet email, vous recevrez un lien de réinitialisation.');
        Router::redirect('/connexion');
    }
    
    public function showResetPassword(): void {
        $this->requireGuest();
        $token = $_GET['token'] ?? '';
        $valid = (bool)Database::fetch(
            "SELECT 1 FROM password_resets WHERE token = :t AND used_at IS NULL AND expires_at > NOW() LIMIT 1",
            [':t' => $token]
        );
        
        if (!$valid) {
            Session::flash('errors', ['global' => 'Le lien de réinitialisation est invalide ou a expiré.']);
            Router::redirect('/mot-de-passe-oublie');
        }
        
        $this->view('auth/reset-password', ['token' => $token]);
    }
    
    public function resetPassword(): void {
        $this->requireGuest();
        $token = Security::input('token');
        $password = $_POST['password'] ?? '';
        
        if (strlen($password) < 8) {
            Session::flash('errors', ['password' => 'Le mot de passe doit contenir au moins 8 caractères.']);
            Router::back();
        }
        
        if (Auth::resetPassword($token, $password)) {
            Session::flash('success', 'Votre mot de passe a été réinitialisé. Connectez-vous maintenant.');
            Router::redirect('/connexion');
        } else {
            Session::flash('errors', ['global' => 'Le lien de réinitialisation est invalide ou a expiré.']);
            Router::redirect('/mot-de-passe-oublie');
        }
    }
    
    public function logout(): void {
        Auth::logout();
        Router::redirect('/');
    }
    
    public function googleCallback(): void {
        // Implementation for Google OAuth
        // Requires Google API PHP Client or custom OAuth flow
        // Lightweight implementation for shared hosting
        
        $code = $_GET['code'] ?? '';
        if (!$code) {
            Router::redirect('/connexion');
        }
        
        // Exchange code for token (simplified - use cURL in production)
        // This is a placeholder for the actual Google OAuth implementation
        Session::flash('errors', ['global' => 'La connexion Google nécessite une configuration API.']);
        Router::redirect('/connexion');
    }
}
