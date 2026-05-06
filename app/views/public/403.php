<?php View::section('content'); ?>
<section class="section-padding">
    <div class="container text-center">
        <div class="error-page">
            <div class="error-code text-danger">403</div>
            <h2 class="fw-bold mb-3">Accès Interdit</h2>
            <p class="text-secondary mb-4">Vous n'avez pas les permissions nécessaires pour accéder à cette page.</p>
            <div class="d-flex justify-content-center gap-3">
                <a href="<?= url('/') ?>" class="btn btn-primary">
                    <i class="bi bi-house me-2"></i>Retour à l'Accueil
                </a>
                <a href="<?= url('/contact') ?>" class="btn btn-outline-primary">
                    <i class="bi bi-envelope me-2"></i>Contact
                </a>
            </div>
        </div>
    </div>
</section>
<?php View::endSection(); ?>
