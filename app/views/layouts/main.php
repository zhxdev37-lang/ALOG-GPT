<!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?= e($seo['title'] ?? 'ALOG Academy') ?></title>
    <meta name="description" content="<?= e($seo['description'] ?? '') ?>">
    <meta name="keywords" content="éducation, Maroc, cours, baccalauréat, études, quiz, e-learning">
    <meta name="author" content="ALOG Academy">
    <meta name="robots" content="index, follow">
    
    <!-- OpenGraph -->
    <meta property="og:title" content="<?= e($seo['title'] ?? 'ALOG Academy') ?>">
    <meta property="og:description" content="<?= e($seo['description'] ?? '') ?>">
    <meta property="og:image" content="<?= e($seo['image'] ?? asset('images/og-default.jpg')) ?>">
    <meta property="og:url" content="<?= e($seo['url'] ?? APP_URL . $_SERVER['REQUEST_URI']) ?>">
    <meta property="og:type" content="<?= e($seo['type'] ?? 'website') ?>">
    <meta property="og:locale" content="fr_FR">
    
    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= e($seo['title'] ?? 'ALOG Academy') ?>">
    <meta name="twitter:description" content="<?= e($seo['description'] ?? '') ?>">
    <meta name="twitter:image" content="<?= e($seo['image'] ?? asset('images/og-default.jpg')) ?>">
    
    <!-- Favicon & PWA -->
    <link rel="manifest" href="<?= url('/manifest.json') ?>">
    <link rel="apple-touch-icon" href="<?= asset('images/icon-192x192.png') ?>">
    <meta name="theme-color" content="#0975e4">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="ALOG Academy">
    
    <!-- Preconnect -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="<?= asset('css/main.css') ?>" rel="stylesheet">
    
    <!-- JSON-LD -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Organization",
        "name": "ALOG Academy",
        "url": "<?= APP_URL ?>",
        "logo": "<?= asset('images/logo.png') ?>",
        "description": "Plateforme éducative premium pour les étudiants marocains",
        "address": {
            "@type": "PostalAddress",
            "addressCountry": "MA",
            "addressLocality": "Casablanca"
        },
        "sameAs": [
            "<?= Setting::get('facebook_page', '') ?>",
            "<?= Setting::get('instagram_page', '') ?>",
            "<?= Setting::get('youtube_channel', '') ?>"
        ]
    }
    </script>
</head>
<body>
    <?php View::partial('navbar', ['user' => $user ?? null]); ?>
    
    <main>
        <?php View::yield('content'); ?>
    </main>
    
    <?php View::partial('footer'); ?>
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" defer></script>
    <script src="<?= asset('js/app.js') ?>" defer></script>
    
    <!-- Service Worker -->
    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('<?= url('/service-worker.js') ?>')
                .then(reg => console.log('SW registered'))
                .catch(err => console.log('SW error', err));
        }
    </script>
</body>
</html>
