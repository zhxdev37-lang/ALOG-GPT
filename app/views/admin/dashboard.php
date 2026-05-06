<?php View::section('content'); ?>
<div class="admin-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Tableau de Bord</h4>
            <p class="text-secondary mb-0">Vue d'ensemble de la plateforme</p>
        </div>
        <div class="text-end">
            <span class="text-muted small"><?= date('d/m/Y H:i') ?></span>
        </div>
    </div>
    
    <!-- Stats Row 1 -->
    <div class="row g-3 mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="admin-stat-card p-3 rounded-4 border bg-white">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-secondary small">Utilisateurs</div>
                        <div class="stat-value fw-bold fs-4"><?= number_format($stats['users']['total'] ?? 0) ?></div>
                        <div class="small text-success">+<?= $stats['users']['today'] ?? 0 ?> aujourd'hui</div>
                    </div>
                    <div class="stat-icon bg-primary bg-opacity-10 text-primary"><i class="bi bi-people"></i></div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="admin-stat-card p-3 rounded-4 border bg-white">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-secondary small">Abonnements Actifs</div>
                        <div class="stat-value fw-bold fs-4"><?= number_format($stats['active_subs'] ?? 0) ?></div>
                        <div class="small text-muted">en cours</div>
                    </div>
                    <div class="stat-icon bg-success bg-opacity-10 text-success"><i class="bi bi-credit-card"></i></div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="admin-stat-card p-3 rounded-4 border bg-white">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-secondary small">Revenus du Mois</div>
                        <div class="stat-value fw-bold fs-4"><?= formatPrice($stats['revenue']['month'] ?? 0) ?></div>
                        <div class="small text-success">+<?= formatPrice($stats['revenue']['today'] ?? 0) ?> aujourd'hui</div>
                    </div>
                    <div class="stat-icon bg-warning bg-opacity-10 text-warning"><i class="bi bi-cash-stack"></i></div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="admin-stat-card p-3 rounded-4 border bg-white">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-secondary small">Messages Non Lus</div>
                        <div class="stat-value fw-bold fs-4"><?= $stats['contacts'] ?? 0 ?></div>
                        <div class="small text-muted">en attente</div>
                    </div>
                    <div class="stat-icon bg-danger bg-opacity-10 text-danger"><i class="bi bi-envelope"></i></div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Stats Row 2 -->
    <div class="row g-3 mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="admin-stat-card p-3 rounded-4 border bg-white">
                <div class="text-secondary small">Leçons Totales</div>
                <div class="stat-value fw-bold fs-4"><?= number_format($stats['total_lessons'] ?? 0) ?></div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="admin-stat-card p-3 rounded-4 border bg-white">
                <div class="text-secondary small">Quiz Totaux</div>
                <div class="stat-value fw-bold fs-4"><?= number_format($stats['total_quizzes'] ?? 0) ?></div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="admin-stat-card p-3 rounded-4 border bg-white">
                <div class="text-secondary small">Articles Blog</div>
                <div class="stat-value fw-bold fs-4"><?= number_format($stats['total_posts'] ?? 0) ?></div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="admin-stat-card p-3 rounded-4 border bg-white">
                <div class="text-secondary small">Revenus Totaux</div>
                <div class="stat-value fw-bold fs-4"><?= formatPrice($stats['revenue']['total'] ?? 0) ?></div>
            </div>
        </div>
    </div>
    
    <div class="row g-4">
        <!-- Revenue Chart -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="fw-semibold mb-0">Revenus sur 7 Jours</h5>
                </div>
                <div class="card-body p-4">
                    <?php if (!empty($weeklyRevenue)): ?>
                    <div class="chart-container" style="height: 200px;">
                        <div class="d-flex align-items-end gap-2 h-100">
                            <?php foreach ($weeklyRevenue as $day): ?>
                            <div class="flex-grow-1 d-flex flex-column align-items-center gap-1">
                                <div class="text-muted small"><?= formatPrice((float)$day['revenue']) ?></div>
                                <div class="bar bg-primary rounded-top" style="width: 100%; height: <?= min(100, ((float)$day['revenue'] / max(1, (float)($weeklyRevenue[0]['revenue'] ?? 1))) * 100) ?>%"></div>
                                <div class="small text-muted"><?= formatDate($day['date'], 'd/m') ?></div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php else: ?>
                    <p class="text-muted text-center py-4">Aucune donnée de revenus pour cette période.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Recent Activity -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between">
                    <h5 class="fw-semibold mb-0">Nouveaux Inscrits</h5>
                    <a href="<?= url('/admin/utilisateurs') ?>" class="small text-decoration-none">Voir tout</a>
                </div>
                <div class="card-body p-4">
                    <?php if (!empty($recentUsers)): ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($recentUsers as $u): ?>
                        <div class="list-group-item px-0 py-2 d-flex align-items-center gap-2">
                            <img src="<?= avatar($u['avatar'] ?? 'avatar1.png') ?>" alt="" class="rounded-circle" style="width:32px;height:32px">
                            <div class="flex-grow-1">
                                <div class="fw-semibold small"><?= e(($u['first_name'] ?? '') . ' ' . ($u['last_name'] ?? '')) ?></div>
                                <div class="small text-muted"><?= e($u['email']) ?></div>
                            </div>
                            <span class="small text-muted"><?= timeAgo($u['created_at']) ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <p class="text-muted small">Aucun nouvel inscrit récemment.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Admin Logs -->
    <div class="card border-0 shadow-sm rounded-4 mt-4">
        <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between">
            <h5 class="fw-semibold mb-0">Activité Récente</h5>
            <a href="<?= url('/admin/logs') ?>" class="small text-decoration-none">Voir tout</a>
        </div>
        <div class="card-body p-4">
            <?php if (!empty($recentLogs)): ?>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead><tr><th>Admin</th><th>Action</th><th>Entité</th><th>Date</th><th>IP</th></tr></thead>
                    <tbody>
                        <?php foreach ($recentLogs as $log): ?>
                        <tr>
                            <td><?= e(($log['first_name'] ?? '') . ' ' . ($log['last_name'] ?? '')) ?></td>
                            <td><span class="badge bg-<?= match($log['action']){'create'=>'success','update'=>'primary','delete'=>'danger',default=>'secondary'} ?>"><?= e($log['action']) ?></span></td>
                            <td><?= e($log['entity_type']) ?> #<?= $log['entity_id'] ?? '-' ?></td>
                            <td class="small text-muted"><?= timeAgo($log['created_at']) ?></td>
                            <td class="small text-muted"><?= e($log['ip_address']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <p class="text-muted">Aucune activité récente.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php View::endSection(); ?>
