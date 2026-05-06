<?php View::section('content'); ?>
<div class="admin-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Analytics</h4>
            <p class="text-secondary mb-0">Statistiques de la plateforme</p>
        </div>
        <div>
            <a href="<?= url('/admin/analytics?period=7') ?>" class="btn btn-sm <?= $period === '7' ? 'btn-primary' : 'btn-outline-primary' ?>">7 jours</a>
            <a href="<?= url('/admin/analytics?period=30') ?>" class="btn btn-sm <?= $period === '30' ? 'btn-primary' : 'btn-outline-primary' ?>">30 jours</a>
            <a href="<?= url('/admin/analytics?period=90') ?>" class="btn btn-sm <?= $period === '90' ? 'btn-primary' : 'btn-outline-primary' ?>">90 jours</a>
            <a href="<?= url('/admin/analytics?period=365') ?>" class="btn btn-sm <?= $period === '365' ? 'btn-primary' : 'btn-outline-primary' ?>">1 an</a>
        </div>
    </div>
    
    <div class="row g-3 mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="admin-stat-card p-3 rounded-4 border bg-white">
                <div class="text-secondary small">Nouveaux Utilisateurs</div>
                <div class="stat-value fw-bold fs-4"><?= number_format($stats['new_users'] ?? 0) ?></div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="admin-stat-card p-3 rounded-4 border bg-white">
                <div class="text-secondary small">Utilisateurs Actifs</div>
                <div class="stat-value fw-bold fs-4"><?= number_format($stats['active_users'] ?? 0) ?></div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="admin-stat-card p-3 rounded-4 border bg-white">
                <div class="text-secondary small">Lecons Completees</div>
                <div class="stat-value fw-bold fs-4"><?= number_format($stats['completed_lessons'] ?? 0) ?></div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="admin-stat-card p-3 rounded-4 border bg-white">
                <div class="text-secondary small">Tentatives Quiz</div>
                <div class="stat-value fw-bold fs-4"><?= number_format($stats['quiz_attempts'] ?? 0) ?></div>
            </div>
        </div>
    </div>
    
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <h5 class="fw-semibold mb-4">Inscriptions quotidiennes</h5>
            <?php if (!empty($daily)): ?>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead><tr><th>Date</th><th>Nouveaux</th></tr></thead>
                    <tbody>
                        <?php foreach ($daily as $row): ?>
                        <tr><td><?= e($row['date']) ?></td><td><?= e($row['count']) ?></td></tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <p class="text-muted">Aucune donnee pour cette periode.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php View::endSection(); ?>
