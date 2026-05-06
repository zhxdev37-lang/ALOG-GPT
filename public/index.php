<?php
/**
 * ALOG ACADEMY - Point d'entrée public
 * Optimisé pour hébergement mutualisé
 */

// Charger les variables d'environnement depuis .env si existant
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (str_starts_with(trim($line), '#')) continue;
        if (strpos($line, '=') === false) continue;
        list($key, $value) = explode('=', $line, 2);
        $_ENV[trim($key)] = trim($value);
    }
}

require __DIR__ . '/../config/app.php';
require __DIR__ . '/../routes/web.php';
