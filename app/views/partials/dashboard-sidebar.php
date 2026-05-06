<aside class="dashboard-sidebar">
    <div class="sidebar-brand">
        <a href="<?= url('/') ?>" class="d-flex align-items-center gap-2 text-decoration-none">
            <div class="brand-icon-sm">
                <i class="bi bi-mortarboard-fill text-white"></i>
            </div>
            <span class="fw-bold text-white">ALOG</span>
        </a>
    </div>
    
    <nav class="sidebar-nav">
        <a href="<?= url('/tableau-de-bord') ?>" class="sidebar-link <?= activeRoute('tableau-de-bord') ?>">
            <i class="bi bi-grid"></i><span>Dashboard</span>
        </a>
        <a href="<?= url('/matieres') ?>" class="sidebar-link <?= containsRoute('matieres') ?>">
            <i class="bi bi-book"></i><span>Mes Cours</span>
        </a>
        <a href="<?= url('/mes-cours') ?>" class="sidebar-link <?= activeRoute('mes-cours') ?>">
            <i class="bi bi-journal-check"></i><span>Progression</span>
        </a>
        <a href="<?= url('/classement') ?>" class="sidebar-link <?= containsRoute('classement') ?>">
            <i class="bi bi-trophy"></i><span>Classement</span>
        </a>
        <a href="<?= url('/badges') ?>" class="sidebar-link <?= activeRoute('badges') ?>">
            <i class="bi bi-award"></i><span>Badges</span>
        </a>
        <a href="<?= url('/abonnements') ?>" class="sidebar-link <?= containsRoute('abonnements') ?>">
            <i class="bi bi-credit-card"></i><span>Abonnement</span>
        </a>
        <a href="<?= url('/profil') ?>" class="sidebar-link <?= activeRoute('profil') ?>">
            <i class="bi bi-person"></i><span>Profil</span>
        </a>
        
        <?php if ($user && in_array($user['role']['slug'] ?? '', ['super_admin', 'content_manager', 'blogger', 'moderator', 'support'])): ?>
            <div class="sidebar-divider">Administration</div>
            <a href="<?= url('/admin') ?>" class="sidebar-link">
                <i class="bi bi-shield-lock"></i><span>Panel Admin</span>
            </a>
        <?php endif; ?>
    </nav>
    
    <div class="sidebar-footer">
        <a href="<?= url('/deconnexion') ?>" class="sidebar-link text-danger">
            <i class="bi bi-box-arrow-right"></i><span>Déconnexion</span>
        </a>
    </div>
</aside>
