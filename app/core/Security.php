<?php
/**
 * Security - Protection CSRF, XSS, SQL Injection
 * Couches de validation et sanitization
 */
class Security {
    
    // CSRF Token
    public static function generateToken(): string {
        $token = bin2hex(random_bytes(32));
        Session::set('csrf_token', $token);
        Session::set('csrf_time', time());
        return $token;
    }
    
    public static function getToken(): string {
        if (!Session::has('csrf_token')) {
            return self::generateToken();
        }
        return Session::get('csrf_token');
    }
    
    public static function validateToken(string $token): bool {
        $stored = Session::get('csrf_token');
        $time = Session::get('csrf_time', 0);
        
        if (!$stored || !$time || (time() - $time > 3600)) {
            return false;
        }
        
        return hash_equals($stored, $token);
    }
    
    public static function tokenField(): string {
        $token = self::getToken();
        return '<input type="hidden" name="' . CSRF_TOKEN_NAME . '" value="' . $token . '">';
    }
    
    // XSS Protection
    public static function e(string $text): string {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }
    
    public static function clean(string $text): string {
        return strip_tags($text, '<p><br><strong><em><ul><ol><li><h1><h2><h3><h4><h5><h6><blockquote><a>');
    }
    
    // Input sanitization
    public static function input(string $key, string $method = 'POST', string $default = ''): string {
        $source = strtoupper($method) === 'POST' ? $_POST : $_GET;
        $value = $source[$key] ?? $default;
        return trim(self::e($value));
    }
    
    public static function int(string $key, string $method = 'POST', int $default = 0): int {
        $source = strtoupper($method) === 'POST' ? $_POST : $_GET;
        return filter_input(INPUT_POST, $key, FILTER_VALIDATE_INT) ?: 
               (isset($source[$key]) ? (int)$source[$key] : $default);
    }
    
    public static function email(string $key, string $method = 'POST', string $default = ''): string {
        $source = strtoupper($method) === 'POST' ? $_POST : $_GET;
        $value = $source[$key] ?? $default;
        $clean = filter_var(trim($value), FILTER_SANITIZE_EMAIL);
        return filter_var($clean, FILTER_VALIDATE_EMAIL) ? $clean : $default;
    }
    
    // Rate limiting simple (par IP)
    public static function checkRateLimit(string $action, int $max = 10, int $window = 60): bool {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $key = 'rate_' . $action . '_' . md5($ip);
        $cacheFile = CACHE_PATH . '/' . $key . '.cache';
        
        $data = ['count' => 0, 'time' => time()];
        if (file_exists($cacheFile)) {
            $data = json_decode(file_get_contents($cacheFile), true) ?: $data;
        }
        
        if (time() - $data['time'] > $window) {
            $data = ['count' => 0, 'time' => time()];
        }
        
        $data['count']++;
        file_put_contents($cacheFile, json_encode($data), LOCK_EX);
        
        return $data['count'] <= $max;
    }
    
    // Password hashing
    public static function hashPassword(string $password): string {
        return password_hash($password, PASSWORD_ARGON2ID, [
            'memory_cost' => 65536,
            'time_cost' => 4,
            'threads' => 3
        ]);
    }
    
    public static function verifyPassword(string $password, string $hash): bool {
        return password_verify($password, $hash);
    }
    
    // IP & Device info
    public static function getIp(): string {
        $headers = ['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'];
        foreach ($headers as $header) {
            if (!empty($_SERVER[$header])) {
                $ips = explode(',', $_SERVER[$header]);
                return trim($ips[0]);
            }
        }
        return 'unknown';
    }
    
    public static function getDevice(): string {
        return $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
    }
}
