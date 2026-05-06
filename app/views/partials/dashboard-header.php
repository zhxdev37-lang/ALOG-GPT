<header class="dashboard-header">
    <div class="d-flex justify-content-between align-items-center w-100">
        <button class="btn btn-sm btn-outline-secondary d-lg-none" onclick="document.querySelector('.dashboard-sidebar').classList.toggle('show')">
            <i class="bi bi-list"></i>
        </button>
        
        <div class="d-flex align-items-center gap-3 ms-auto">
            <a href="<?= url('/tarifs') ?>" class="btn btn-sm btn-outline-primary d-none d-md-inline-flex">
                <i class="bi bi-stars me-1"></i>Passer Pro
            </a>
            
            <div class="dropdown">
                <button class="btn btn-sm btn-light d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                    <img src="<?= avatar($user['avatar'] ?? 'avatar1.png') ?>" alt="" class="avatar-sm rounded-circle">
                    <span class="d-none d-md-inline"><?= e(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="<?= url('/profil') ?>"><i class="bi bi-person me-2"></i>Mon Profil</a></li>
                    <li><a class="dropdown-item" href="<?= url('/abonnements') ?>"><i class="bi bi-credit-card me-2"></i>Abonnement</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="<?= url('/deconnexion') ?>"><i class="bi bi-box-arrow-right me-2"></i>Déconnexion</a></li>
                </ul>
            </div>
        </div>
    </div>
</header>
