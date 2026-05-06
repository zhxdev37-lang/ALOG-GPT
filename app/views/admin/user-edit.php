<?php View::section('content'); ?>
<div class="admin-content">
    <div class="mb-4">
        <a href="<?= url('/admin/utilisateurs') ?>" class="btn btn-sm btn-outline-secondary mb-2"><i class="bi bi-arrow-left me-2"></i>Retour</a>
        <h4 class="fw-bold mb-1">Modifier Utilisateur</h4>
        <p class="text-secondary mb-0">Edition du compte #<?= $editUser['id'] ?></p>
    </div>
    
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <form action="<?= url('/admin/utilisateurs/modifier') ?>" method="POST">
                <?= $csrf ?>
                <input type="hidden" name="id" value="<?= $editUser['id'] ?>">
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Prenom</label>
                        <input type="text" name="first_name" class="form-control" value="<?= e($editUser['first_name']) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nom</label>
                        <input type="text" name="last_name" class="form-control" value="<?= e($editUser['last_name']) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="<?= e($editUser['email']) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Telephone</label>
                        <input type="text" name="phone" class="form-control" value="<?= e($editUser['phone'] ?? '') ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Role</label>
                        <select name="role_id" class="form-select">
                            <?php foreach ($roles as $role): ?>
                            <option value="<?= $role['id'] ?>" <?= $editUser['role_id'] == $role['id'] ? 'selected' : '' ?>><?= e($role['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Niveau</label>
                        <select name="school_level_id" class="form-select">
                            <option value="">-</option>
                            <?php foreach ($levels as $level): ?>
                            <option value="<?= $level['id'] ?>" <?= ($editUser['school_level_id'] ?? '') == $level['id'] ? 'selected' : '' ?>><?= e($level['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Plan</label>
                        <select name="plan_id" class="form-select">
                            <?php foreach ($plans as $plan): ?>
                            <option value="<?= $plan['id'] ?>" <?= $editUser['plan_id'] == $plan['id'] ? 'selected' : '' ?>><?= e($plan['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Statut</label>
                        <select name="status" class="form-select">
                            <option value="active" <?= $editUser['status'] === 'active' ? 'selected' : '' ?>>Actif</option>
                            <option value="pending" <?= $editUser['status'] === 'pending' ? 'selected' : '' ?>>En attente</option>
                            <option value="suspended" <?= $editUser['status'] === 'suspended' ? 'selected' : '' ?>>Suspendu</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">XP Total</label>
                        <input type="number" name="xp_total" class="form-control" value="<?= $editUser['xp_total'] ?? 0 ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">XP Actuel</label>
                        <input type="number" name="xp_current" class="form-control" value="<?= $editUser['xp_current'] ?? 0 ?>">
                    </div>
                </div>
                
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-2"></i>Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php View::endSection(); ?>
