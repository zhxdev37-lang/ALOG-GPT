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
                                <i class="bi bi-key-fill text-white"></i>
                            </div>
                            <h3 class="fw-bold">Mot de Passe Oublié ?</h3>
                            <p class="text-secondary small">Entrez votre email pour recevoir un lien de réinitialisation.</p>
                        </div>
                        
                        <form action="<?= url('/mot-de-passe-oublie') ?>" method="POST">
                            <?= $csrf ?>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 mb-3">
                                <i class="bi bi-send me-2"></i>Envoyer le Lien
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
