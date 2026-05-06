<?php
/**
 * ALOG ACADEMY - Point d'entree public
 * Optimise pour hebergement mutualise (AeOnFree/htdocs)
 */

// PHP 7.4+ polyfill for str_starts_with if not available
if (!function_exists('str_starts_with')) {
    function str_starts_with(string $haystack, string $needle): bool {
        return $needle === '' || strpos($haystack, $needle) === 0;
    }
}

// Charger les variables d'environnement depuis .env si existant
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (str_starts_with(trim($line), '#')) continue;
        if (strpos($line, '=') === false) continue;
        list($key, $value) = explode('=', $line, 2);
        $_ENV[trim($key)] = trim($value);
    }
}

require __DIR__ . '/config/app.php';
require __DIR__ . '/routes/web.php';
