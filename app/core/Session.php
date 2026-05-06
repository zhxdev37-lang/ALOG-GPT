<?php
/**
 * Session - Gestion sécurisée des sessions
 * Protection fixation, hijacking, expiration
 */
class Session {
    public static function start(): void {
        if (session_status() === PHP_SESSION_NONE) {
            ini_set('session.cookie_httponly', '1');
            ini_set('session.cookie_secure', isset($_SERVER['HTTPS']) ? '1' : '0');
            ini_set('session.cookie_samesite', 'Strict');
            ini_set('session.gc_maxlifetime', SESSION_LIFETIME);
            session_name(SESSION_NAME);
            session_start();
            
            self::regenerateIfNeeded();
            self::validateSession();
        }
    }
    
    public static function regenerateIfNeeded(): void {
        if (!isset($_SESSION['created'])) {
            $_SESSION['created'] = time();
            session_regenerate_id(true);
        } else if (time() - $_SESSION['created'] > 1800) {
            session_regenerate_id(true);
            $_SESSION['created'] = time();
        }
    }
    
    public static function validateSession(): void {
        if (isset($_SESSION['ip']) && $_SESSION['ip'] !== $_SERVER['REMOTE_ADDR']) {
            self::destroy();
            return;
        }
        if (isset($_SESSION['user_agent']) && $_SESSION['user_agent'] !== ($_SERVER['HTTP_USER_AGENT'] ?? '')) {
            self::destroy();
            return;
        }
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_LIFETIME)) {
            self::destroy();
            return;
        }
        $_SESSION['ip'] = $_SERVER['REMOTE_ADDR'] ?? '';
        $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $_SESSION['last_activity'] = time();
    }
    
    public static function set(string $key, $value): void {
        self::start();
        $_SESSION[$key] = $value;
    }
    
    public static function get(string $key, $default = null) {
        self::start();
        return $_SESSION[$key] ?? $default;
    }
    
    public static function has(string $key): bool {
        self::start();
        return isset($_SESSION[$key]);
    }
    
    public static function remove(string $key): void {
        self::start();
        unset($_SESSION[$key]);
    }
    
    public static function flash(string $key, $value = null) {
        self::start();
        if ($value !== null) {
            $_SESSION['flash'][$key] = $value;
        } else {
            $val = $_SESSION['flash'][$key] ?? null;
            unset($_SESSION['flash'][$key]);
            return $val;
        }
    }
    
    public static function destroy(): void {
        self::start();
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params['path'], $params['domain'],
                $params['secure'], $params['httponly']
            );
        }
        session_destroy();
    }
}
