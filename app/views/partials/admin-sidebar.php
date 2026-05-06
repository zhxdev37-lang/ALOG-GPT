<aside class="admin-sidebar">
    <div class="sidebar-brand">
        <a href="<?= url('/admin') ?>" class="d-flex align-items-center gap-2 text-decoration-none">
            <div class="brand-icon-sm"><i class="bi bi-shield-lock text-white"></i></div>
            <span class="fw-bold text-white">Admin</span>
        </a>
    </div>
    
    <nav class="sidebar-nav">
        <a href="<?= url('/admin') ?>" class="sidebar-link <?= activeRoute('admin') ?>"><i class="bi bi-speedometer2"></i><span>Dashboard</span></a>
        <a href="<?= url('/admin/analytics') ?>" class="sidebar-link <?= containsRoute('admin/analytics') ?>"><i class="bi bi-bar-chart"></i><span>Analytics</span></a>
        
        <div class="sidebar-divider">Contenu</div>
        <a href="<?= url('/admin/utilisateurs') ?>" class="sidebar-link <?= containsRoute('admin/utilisateurs') ?>"><i class="bi bi-people"></i><span>Utilisateurs</span></a>
        <a href="<?= url('/admin/niveaux') ?>" class="sidebar-link <?= containsRoute('admin/niveaux') ?>"><i class="bi bi-layers"></i><span>Niveaux</span></a>
        <a href="<?= url('/admin/matieres') ?>" class="sidebar-link <?= containsRoute('admin/matieres') ?>"><i class="bi bi-book"></i><span>Matières</span></a>
        <a href="<?= url('/admin/lecons') ?>" class="sidebar-link <?= containsRoute('admin/lecons') ?>"><i class="bi bi-journal-text"></i><span>Leçons</span></a>
        <a href="<?= url('/admin/quiz') ?>" class="sidebar-link <?= containsRoute('admin/quiz') ?>"><i class="bi bi-question-circle"></i><span>Quiz</span></a>
        
        <div class="sidebar-divider">Business</div>
        <a href="<?= url('/admin/plans') ?>" class="sidebar-link <?= containsRoute('admin/plans') ?>"><i class="bi bi-tags"></i><span>Plans</span></a>
        <a href="<?= url('/admin/abonnements') ?>" class="sidebar-link <?= containsRoute('admin/abonnements') ?>"><i class="bi bi-credit-card"></i><span>Abonnements</span></a>
        
        <div class="sidebar-divider">Site</div>
        <a href="<?= url('/admin/blog') ?>" class="sidebar-link <?= containsRoute('admin/blog') ?>"><i class="bi bi-pencil-square"></i><span>Blog</span></a>
        <a href="<?= url('/admin/evenements') ?>" class="sidebar-link <?= containsRoute('admin/evenements') ?>"><i class="bi bi-calendar-event"></i><span>Événements</span></a>
        <a href="<?= url('/admin/contacts') ?>" class="sidebar-link <?= containsRoute('admin/contacts') ?>"><i class="bi bi-envelope"></i><span>Messages</span></a>
        
        <div class="sidebar-divider">Système</div>
        <a href="<?= url('/admin/parametres') ?>" class="sidebar-link <?= containsRoute('admin/parametres') ?>"><i class="bi bi-gear"></i><span>Paramètres</span></a>
        <a href="<?= url('/admin/logs') ?>" class="sidebar-link <?= containsRoute('admin/logs') ?>"><i class="bi bi-clock-history"></i><span>Logs</span></a>
    </nav>
    
    <div class="sidebar-footer">
        <a href="<?= url('/') ?>" class="sidebar-link"><i class="bi bi-arrow-left"></i><span>Retour au site</span></a>
    </div>
</aside>
