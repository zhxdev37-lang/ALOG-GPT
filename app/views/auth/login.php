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
                                <i class="bi bi-mortarboard-fill text-white"></i>
                            </div>
                            <h3 class="fw-bold">Connexion</h3>
                            <p class="text-secondary small">Accédez à votre espace personnel</p>
                        </div>
                        
                        <?php if (!empty($success)): ?>
                        <div class="alert alert-success"><?= e($success) ?></div>
                        <?php endif; ?>
                        
                        <form action="<?= url('/connexion') ?>" method="POST">
                            <?= $csrf ?>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" value="<?= old('email') ?>" required>
                                <?php if (isset($errors['email'])): ?><div class="invalid-feedback"><?= e($errors['email']) ?></div><?php endif; ?>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Mot de passe</label>
                                <input type="password" name="password" class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" required>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="form-check">
                                    <input type="checkbox" name="remember" class="form-check-input" id="remember">
                                    <label class="form-check-label small" for="remember">Se souvenir de moi</label>
                                </div>
                                <a href="<?= url('/mot-de-passe-oublie') ?>" class="small text-decoration-none">Mot de passe oublié ?</a>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 mb-3">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Se Connecter
                            </button>
                        </form>
                        
                        <div class="text-center">
                            <p class="text-secondary small mb-0">Pas encore de compte ? <a href="<?= url('/inscription') ?>" class="text-decoration-none fw-semibold">S'inscrire</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php View::endSection(); ?>
