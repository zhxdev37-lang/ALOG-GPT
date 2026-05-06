<?php $this->layout = 'main'; ?>
<?php View::section('content'); ?>
<section class="auth-section min-vh-100 d-flex align-items-center py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="auth-card card border-0 shadow-lg rounded-4">
                    <div class="card-body p-4 p-lg-5">
                        <div class="text-center mb-4">
                            <div class="brand-icon-lg mx-auto mb-3">
                                <i class="bi bi-mortarboard-fill text-white"></i>
                            </div>
                            <h3 class="fw-bold">Créer un Compte</h3>
                            <p class="text-secondary small">Rejoignez la meilleure plateforme éducative du Maroc</p>
                        </div>
                        
                        <form action="<?= url('/inscription') ?>" method="POST">
                            <?= $csrf ?>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Prénom *</label>
                                    <input type="text" name="first_name" class="form-control <?= isset($errors['first_name']) ? 'is-invalid' : '' ?>" value="<?= old('first_name') ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Nom *</label>
                                    <input type="text" name="last_name" class="form-control <?= isset($errors['last_name']) ? 'is-invalid' : '' ?>" value="<?= old('last_name') ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email *</label>
                                    <input type="email" name="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" value="<?= old('email') ?>" required>
                                    <?php if (isset($errors['email'])): ?><div class="invalid-feedback"><?= e($errors['email']) ?></div><?php endif; ?>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Téléphone</label>
                                    <input type="tel" name="phone" class="form-control" value="<?= old('phone') ?>" placeholder="06XX-XXXXXX">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Mot de passe *</label>
                                    <input type="password" name="password" class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" required minlength="8">
                                    <?php if (isset($errors['password'])): ?><div class="invalid-feedback"><?= e($errors['password']) ?></div><?php endif; ?>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Confirmer *</label>
                                    <input type="password" name="password_confirmation" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Date de naissance *</label>
                                    <input type="date" name="birth_date" class="form-control <?= isset($errors['birth_date']) ? 'is-invalid' : '' ?>" value="<?= old('birth_date') ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Région *</label>
                                    <select name="region" class="form-select <?= isset($errors['region']) ? 'is-invalid' : '' ?>" required>
                                        <option value="">Choisir...</option>
                                        <?php foreach ($regions as $region): ?>
                                        <option value="<?= e($region) ?>" <?= old('region') === $region ? 'selected' : '' ?>><?= e($region) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Niveau scolaire *</label>
                                    <select name="school_level_id" class="form-select <?= isset($errors['school_level_id']) ? 'is-invalid' : '' ?>" required>
                                        <option value="">Choisir...</option>
                                        <?php foreach ($levels as $level): ?>
                                        <option value="<?= $level['id'] ?>" <?= old('school_level_id') == $level['id'] ? 'selected' : '' ?>><?= e($level['name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Filière</label>
                                    <select name="filiere_id" class="form-select">
                                        <option value="">Non applicable</option>
                                        <?php foreach ($filieres as $filiere): ?>
                                        <option value="<?= $filiere['id'] ?>" <?= old('filiere_id') == $filiere['id'] ? 'selected' : '' ?>><?= e($filiere['name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Avatar *</label>
                                    <div class="d-flex flex-wrap gap-2">
                                        <?php foreach ($avatars as $file => $name): ?>
                                        <label class="avatar-option">
                                            <input type="radio" name="avatar" value="<?= e($file) ?>" <?= old('avatar') === $file || (!old('avatar') && $file === 'avatar1.png') ? 'checked' : '' ?> class="d-none">
                                            <img src="<?= avatar($file) ?>" alt="<?= e($name) ?>" class="avatar-choice rounded-circle border <?= old('avatar') === $file ? 'border-primary border-3' : 'border-2' ?>">
                                        </label>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="terms" required>
                                        <label class="form-check-label small" for="terms">
                                            J'accepte les <a href="<?= url('/conditions') ?>" target="_blank">conditions d'utilisation</a> et la <a href="<?= url('/confidentialite') ?>" target="_blank">politique de confidentialité</a>.
                                        </label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="bi bi-person-plus me-2"></i>Créer mon Compte
                                    </button>
                                </div>
                            </div>
                        </form>
                        
                        <div class="text-center mt-3">
                            <p class="text-secondary small mb-0">Déjà inscrit ? <a href="<?= url('/connexion') ?>" class="text-decoration-none fw-semibold">Se connecter</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php View::endSection(); ?>
