<?php View::section('content'); ?>
<div class="dashboard-content">
    <div class="mb-4">
        <h4 class="fw-bold mb-1">Mon Profil</h4>
        <p class="text-secondary mb-0">Gérez vos informations personnelles</p>
    </div>
    
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4 text-center">
                    <img src="<?= avatar($user['avatar'] ?? 'avatar1.png') ?>" alt="" class="rounded-circle mb-3" style="width:100px;height:100px;object-fit:cover">
                    <h5 class="fw-bold"><?= e(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?></h5>
                    <p class="text-secondary small mb-1"><?= e($user['email'] ?? '') ?></p>
                    <span class="badge bg-primary">Niveau <?= $user['level'] ?? 1 ?></span>
                    <div class="mt-3">
                        <div class="d-flex justify-content-between small text-muted mb-1">
                            <span>XP Total</span>
                            <span><?= number_format($user['xp_total'] ?? 0) ?></span>
                        </div>
                        <div class="progress" style="height:6px">
                            <div class="progress-bar bg-primary" style="width:<?= xpProgress($user['xp_total'] ?? 0) ?>%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h5 class="fw-semibold mb-4">Modifier mes Informations</h5>
                    
                    <?php if (!empty($success)): ?>
                    <div class="alert alert-success"><?= e($success) ?></div>
                    <?php endif; ?>
                    
                    <form action="<?= url('/profil') ?>" method="POST">
                        <?= $csrf ?>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Prénom</label>
                                <input type="text" name="first_name" class="form-control" value="<?= e($user['first_name'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nom</label>
                                <input type="text" name="last_name" class="form-control" value="<?= e($user['last_name'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Téléphone</label>
                                <input type="tel" name="phone" class="form-control" value="<?= e($user['phone'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Région</label>
                                <select name="region" class="form-select" required>
                                    <?php foreach ($regions as $region): ?>
                                    <option value="<?= e($region) ?>" <?= ($user['region'] ?? '') === $region ? 'selected' : '' ?>><?= e($region) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Niveau Scolaire</label>
                                <select name="school_level_id" class="form-select" required>
                                    <option value="">Choisir...</option>
                                    <?php foreach ($levels as $level): ?>
                                    <option value="<?= $level['id'] ?>" <?= ($user['school_level_id'] ?? 0) == $level['id'] ? 'selected' : '' ?>><?= e($level['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="form-text text-warning small"><i class="bi bi-exclamation-triangle me-1"></i>Changement limité à 1 fois tous les 3 mois. Votre progression sera réinitialisée.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Filière</label>
                                <select name="filiere_id" class="form-select">
                                    <option value="">Non applicable</option>
                                    <?php foreach (($levels[0]['filieres'] ?? []) as $filiere): ?>
                                    <option value="<?= $filiere['id'] ?>" <?= ($user['filiere_id'] ?? 0) == $filiere['id'] ? 'selected' : '' ?>><?= e($filiere['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Avatar</label>
                                <div class="d-flex flex-wrap gap-2">
                                    <?php foreach ($avatars as $file => $name): ?>
                                    <label class="avatar-option">
                                        <input type="radio" name="avatar" value="<?= e($file) ?>" <?= ($user['avatar'] ?? '') === $file ? 'checked' : '' ?> class="d-none">
                                        <img src="<?= avatar($file) ?>" alt="<?= e($name) ?>" class="avatar-choice rounded-circle border <?= ($user['avatar'] ?? '') === $file ? 'border-primary border-3' : 'border-2' ?>">
                                    </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle me-2"></i>Enregistrer les Modifications
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php View::endSection(); ?>
