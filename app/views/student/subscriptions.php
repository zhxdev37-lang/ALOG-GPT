<?php View::section('content'); ?>
<div class="dashboard-content">
    <div class="mb-4">
        <h4 class="fw-bold mb-1">Mes Abonnements</h4>
        <p class="text-secondary mb-0">Gérez votre plan et consultez votre historique</p>
    </div>
    
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h6 class="fw-semibold mb-3">Abonnement Actuel</h6>
                    <?php if (!empty($active)): ?>
                    <div class="text-center mb-3">
                        <div class="plan-badge mb-2" style="background:<?= e($active['color'] ?? '#0975e4') ?>20; color:<?= e($active['color'] ?? '#0975e4') ?>">
                            <i class="bi bi-<?= e($active['badge'] ?? 'star') ?> me-2"></i><?= e($active['plan_name'] ?? 'Free') ?>
                        </div>
                        <div class="small text-muted">Expire le <?= formatDate($active['expires_at'] ?? '') ?></div>
                    </div>
                    <ul class="list-unstyled small">
                        <?php foreach (json_decode($active['features'] ?? '[]', true) as $feature): ?>
                        <li class="d-flex align-items-center gap-2 mb-1"><i class="bi bi-check-circle-fill text-success"></i><?= e($feature) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <?php else: ?>
                    <div class="text-center py-3">
                        <span class="badge bg-secondary mb-2">Free</span>
                        <p class="small text-muted">Vous êtes sur le plan gratuit.</p>
                        <a href="<?= url('/tarifs') ?>" class="btn btn-sm btn-primary">Passer Pro</a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h6 class="fw-semibold mb-0">Historique des Paiements</h6>
                </div>
                <div class="card-body p-4">
                    <?php if (!empty($history['data'])): ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead><tr><th>Date</th><th>Plan</th><th>Montant</th><th>Statut</th><th>Méthode</th></tr></thead>
                            <tbody>
                                <?php foreach ($history['data'] as $sub): ?>
                                <tr>
                                    <td><?= formatDate($sub['created_at']) ?></td>
                                    <td><?= e($sub['plan_name']) ?></td>
                                    <td><?= formatPrice((float)$sub['amount_paid']) ?></td>
                                    <td><span class="badge bg-<?= match($sub['status']){'active'=>'success','expired'=>'secondary','pending'=>'warning','cancelled'=>'danger',default=>'secondary'} ?>"><?= e($sub['status']) ?></span></td>
                                    <td class="text-uppercase small"><?= e($sub['payment_method']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <p class="text-muted text-center py-3">Aucun historique de paiement.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php View::endSection(); ?>
