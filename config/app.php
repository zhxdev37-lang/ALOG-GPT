<?php
/**
 * ALOG ACADEMY - Configuration Principale
 * Optimisé pour hébergement mutualisé gratuit
 */

define('APP_NAME', 'ALOG Academy');
define('APP_VERSION', '1.0.0');
define('APP_URL', (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST']);
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');
define('PUBLIC_PATH', BASE_PATH . '/public');
define('STORAGE_PATH', BASE_PATH . '/storage');
define('CONFIG_PATH', BASE_PATH . '/config');
define('ROUTES_PATH', BASE_PATH . '/routes');

// Sécurité
define('CSRF_TOKEN_NAME', '_token');
define('SESSION_NAME', 'alog_session');
define('SESSION_LIFETIME', 7200);
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOCKOUT_TIME', 1800);

// Environnement
define('ENVIRONMENT', getenv('APP_ENV') ?: 'production');
define('DEBUG_MODE', ENVIRONMENT === 'development');

// Cache
define('CACHE_ENABLED', true);
define('CACHE_PATH', STORAGE_PATH . '/cache');
define('CACHE_TTL', 3600);

// Uploads
define('MAX_UPLOAD_SIZE', 2 * 1024 * 1024);
define('ALLOWED_UPLOAD_TYPES', ['image/jpeg', 'image/png', 'image/gif']);

// API Keys (à configurer via l'admin)
define('YOUTUBE_API_KEY', $_ENV['YOUTUBE_API_KEY'] ?? '');
define('GOOGLE_CLIENT_ID', $_ENV['GOOGLE_CLIENT_ID'] ?? '');
define('GOOGLE_CLIENT_SECRET', $_ENV['GOOGLE_CLIENT_SECRET'] ?? '');

// Timezone
date_default_timezone_set('Africa/Casablanca');

// Error reporting
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    ini_set('error_log', STORAGE_PATH . '/logs/error.log');
}

// Autoloader
spl_autoload_register(function ($class) {
    $map = [
        'Database' => APP_PATH . '/core/Database.php',
        'Router' => APP_PATH . '/core/Router.php',
        'Session' => APP_PATH . '/core/Session.php',
        'View' => APP_PATH . '/core/View.php',
        'Cache' => APP_PATH . '/core/Cache.php',
        'Security' => APP_PATH . '/core/Security.php',
        'Validator' => APP_PATH . '/core/Validator.php',
        'BaseController' => APP_PATH . '/core/BaseController.php',
        'BaseModel' => APP_PATH . '/core/BaseModel.php',
        'Auth' => APP_PATH . '/services/Auth.php',
        'Mailer' => APP_PATH . '/services/Mailer.php',
        'Logger' => APP_PATH . '/services/Logger.php',
    ];
    
    if (isset($map[$class])) {
        require_once $map[$class];
        return;
    }
    
    // Models
    if (file_exists(APP_PATH . '/models/' . $class . '.php')) {
        require_once APP_PATH . '/models/' . $class . '.php';
        return;
    }
    
    // Controllers
    if (file_exists(APP_PATH . '/controllers/' . $class . '.php')) {
        require_once APP_PATH . '/controllers/' . $class . '.php';
        return;
    }
    
    // Services
    if (file_exists(APP_PATH . '/services/' . $class . '.php')) {
        require_once APP_PATH . '/services/' . $class . '.php';
        return;
    }
    
    // Helpers
    if (file_exists(APP_PATH . '/helpers/' . $class . '.php')) {
        require_once APP_PATH . '/helpers/' . $class . '.php';
        return;
    }
});

require_once APP_PATH . '/helpers/functions.php';
