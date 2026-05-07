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

// Servir les fichiers statiques directement (fallback si .htaccess ne fonctionne pas)
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$staticDirs = ['/assets/', '/uploads/', '/storage/'];
foreach ($staticDirs as $dir) {
    if (str_starts_with($requestUri, $dir)) {
        $filePath = __DIR__ . $requestUri;
        if (file_exists($filePath) && is_file($filePath)) {
            // Definir les headers appropriés basés sur le type de fichier
            $mimeTypes = [
                'css' => 'text/css',
                'js' => 'application/javascript',
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
                'svg' => 'image/svg+xml',
                'webp' => 'image/webp',
                'woff' => 'font/woff',
                'woff2' => 'font/woff2',
                'ttf' => 'font/ttf',
                'eot' => 'application/vnd.ms-fontobject',
                'json' => 'application/json',
                'pdf' => 'application/pdf',
                'zip' => 'application/zip'
            ];
            
            $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
            $mimeType = $mimeTypes[$ext] ?? 'application/octet-stream';
            
            header('Content-Type: ' . $mimeType);
            header('Cache-Control: public, max-age=31536000');
            header('Content-Length: ' . filesize($filePath));
            readfile($filePath);
            exit;
        }
    }
}

require __DIR__ . '/routes/web.php';
