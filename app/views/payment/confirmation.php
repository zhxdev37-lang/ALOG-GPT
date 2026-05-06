<?php View::section('content'); ?>
<div class="admin-content">
    <div class="mb-4">
        <h4 class="fw-bold mb-1">Paiement - Confirmation</h4>
        <p class="text-secondary mb-0">Statut de votre transaction</p>
    </div>
    
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4 text-center">
            <div class="mb-4">
                <i class="bi bi-clock-history text-warning" style="font-size:4rem"></i>
            </div>
            <h5 class="fw-bold mb-2">Paiement en cours de traitement</h5>
            <p class="text-secondary mb-4">Methode: <strong><?= e(strtoupper($method)) ?></strong></p>
            <p class="text-muted small mb-4">Reference: <?= e($subId) ?></p>
            <a href="<?= url('/tableau-de-bord') ?>" class="btn btn-primary">Retour au tableau de bord</a>
        </div>
    </div>
</div>
<?php View::endSection(); ?>
