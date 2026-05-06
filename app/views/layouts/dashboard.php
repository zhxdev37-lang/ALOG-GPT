<!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($seo['title'] ?? 'Dashboard') ?> - ALOG Academy</title>
    <meta name="description" content="<?= e($seo['description'] ?? '') ?>">
    <meta name="robots" content="noindex, nofollow">
    <link rel="manifest" href="<?= url('/manifest.json') ?>">
    <link rel="apple-touch-icon" href="<?= asset('images/icon-192x192.png') ?>">
    <meta name="theme-color" content="#0975e4">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="<?= asset('css/dashboard.css') ?>" rel="stylesheet">
</head>
<body class="dashboard-body">
    <?php View::partial('dashboard-sidebar', ['user' => $user ?? null]); ?>
    
    <div class="dashboard-wrapper">
        <?php View::partial('dashboard-header', ['user' => $user ?? null]); ?>
        
        <main class="dashboard-main">
            <?php View::yield('content'); ?>
        </main>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" defer></script>
    <script src="<?= asset('js/dashboard.js') ?>" defer></script>
</body>
</html>
