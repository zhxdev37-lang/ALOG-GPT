<?php
$isDark = isset($_COOKIE['theme']) && $_COOKIE['theme'] === 'dark';
?>
<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom sticky-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="<?= url('/') ?>">
            <div class="brand-icon">
                <i class="bi bi-mortarboard-fill text-white"></i>
            </div>
            <span class="fw-bold text-primary">ALOG</span>
            <span class="fw-semibold text-dark">Academy</span>
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item"><a class="nav-link <?= activeRoute('/') ?>" href="<?= url('/') ?>">Accueil</a></li>
                <li class="nav-item"><a class="nav-link <?= activeRoute('/cours') ?>" href="<?= url('/cours') ?>">Cours</a></li>
                <li class="nav-item"><a class="nav-link <?= activeRoute('/classement') ?>" href="<?= url('/classement') ?>">Classement</a></li>
                <li class="nav-item"><a class="nav-link <?= activeRoute('/tarifs') ?>" href="<?= url('/tarifs') ?>">Tarifs</a></li>
                <li class="nav-item"><a class="nav-link <?= containsRoute('/blog') ?>" href="<?= url('/blog') ?>">Blog</a></li>
                <li class="nav-item"><a class="nav-link <?= activeRoute('/evenements') ?>" href="<?= url('/evenements') ?>">Événements</a></li>
                <li class="nav-item"><a class="nav-link <?= activeRoute('/contact') ?>" href="<?= url('/contact') ?>">Contact</a></li>
            </ul>
            
            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-sm btn-outline-secondary border-0" onclick="toggleTheme()" title="Mode sombre">
                    <i class="bi <?= $isDark ? 'bi-sun' : 'bi-moon' ?>"></i>
                </button>
                
                <?php if ($user): ?>
                    <a href="<?= url('/tableau-de-bord') ?>" class="btn btn-primary btn-sm">
                        <i class="bi bi-grid me-1"></i>Dashboard
                    </a>
                <?php else: ?>
                    <a href="<?= url('/connexion') ?>" class="btn btn-outline-primary btn-sm">Connexion</a>
                    <a href="<?= url('/inscription') ?>" class="btn btn-primary btn-sm">S'inscrire</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<script>
function toggleTheme() {
    const html = document.documentElement;
    const isDark = html.getAttribute('data-bs-theme') === 'dark';
    html.setAttribute('data-bs-theme', isDark ? 'light' : 'dark');
    document.cookie = 'theme=' + (isDark ? 'light' : 'dark') + ';path=/;max-age=31536000';
}
<?php if ($isDark): ?>
document.documentElement.setAttribute('data-bs-theme', 'dark');
<?php endif; ?>
</script>
