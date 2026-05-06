<?php View::section('content'); ?>
<div class="admin-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Abonnements</h4>
            <p class="text-secondary mb-0">Gestion des abonnements et paiements</p>
        </div>
    </div>
    
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>ID</th>
                            <th>Utilisateur</th>
                            <th>Plan</th>
                            <th>Montant</th>
                            <th>Statut</th>
                            <th>Debut</th>
                            <th>Expiration</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (($subscriptions['data'] ?? []) as $sub): ?>
                        <tr>
                            <td><?= $sub['id'] ?></td>
                            <td class="small"><?= e(($sub['first_name'] ?? '') . ' ' . ($sub['last_name'] ?? '')) ?></td>
                            <td><span class="badge bg-primary bg-opacity-10 text-primary"><?= e($sub['plan_name'] ?? '-') ?></span></td>
                            <td><?= formatPrice($sub['amount_paid'] ?? 0) ?></td>
                            <td><span class="badge bg-<?= ($sub['status'] === 'active' ? 'success' : ($sub['status'] === 'pending' ? 'warning' : 'secondary')) ?>"><?= e($sub['status']) ?></span></td>
                            <td class="small"><?= formatDate($sub['starts_at'] ?? '') ?></td>
                            <td class="small"><?= formatDate($sub['expires_at'] ?? '') ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($subscriptions['data'])): ?>
                        <tr><td colspan="7" class="text-center text-muted py-4">Aucun abonnement</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <?= pagination($subscriptions ?? []) ?>
</div>
<?php View::endSection(); ?>
