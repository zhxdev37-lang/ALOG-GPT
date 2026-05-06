<?php $this->layout = 'main'; ?>
<?php View::section('content'); ?>
<section class="auth-section min-vh-100 d-flex align-items-center py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="auth-card card border-0 shadow-lg rounded-4">
                    <div class="card-body p-4 p-lg-5">
                        <div class="text-center mb-4">
                            <div class="brand-icon-lg mx-auto mb-3">
                                <i class="bi bi-shield-lock-fill text-white"></i>
                            </div>
                            <h3 class="fw-bold">Nouveau Mot de Passe</h3>
                            <p class="text-secondary small">Créez un nouveau mot de passe sécurisé.</p>
                        </div>
                        
                        <form action="<?= url('/reinitialiser-mot-de-passe') ?>" method="POST">
                            <?= $csrf ?>
                            <input type="hidden" name="token" value="<?= e($token ?? '') ?>">
                            <div class="mb-3">
                                <label class="form-label">Nouveau mot de passe</label>
                                <input type="password" name="password" class="form-control" required minlength="8">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Confirmer le mot de passe</label>
                                <input type="password" name="password_confirmation" class="form-control" required minlength="8">
                            </div>
                            <button type="submit" class="btn btn-primary w-100 mb-3">
                                <i class="bi bi-check-circle me-2"></i>Réinitialiser
                            </button>
                        </form>
                        
                        <div class="text-center">
                            <a href="<?= url('/connexion') ?>" class="small text-decoration-none"><i class="bi bi-arrow-left me-1"></i>Retour à la connexion</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php View::endSection(); ?>
