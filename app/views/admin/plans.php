<?php View::section('content'); ?>
<div class="admin-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Plans d'Abonnement</h4>
            <p class="text-secondary mb-0">Configuration des offres commerciales</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createPlanModal">
            <i class="bi bi-plus-lg me-2"></i>Nouveau Plan
        </button>
    </div>
    
    <div class="row g-4">
        <?php foreach ($plans as $plan): ?>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100" style="border-top: 4px solid <?= e($plan['color'] ?? '#0975e4') ?> !important;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <h5 class="fw-bold mb-0"><?= e($plan['name']) ?></h5>
                        <span class="badge bg-<?= ($plan['is_active'] ?? 0) ? 'success' : 'secondary' ?>"><?= ($plan['is_active'] ?? 0) ? 'Actif' : 'Inactif' ?></span>
                    </div>
                    <div class="mb-3">
                        <span class="display-6 fw-bold" style="color:<?= e($plan['color'] ?? '#0975e4') ?>"><?= formatPrice((float)$plan['price_mad']) ?></span>
                        <span class="text-muted">/<?= $plan['duration_days'] >= 365 ? 'an' : 'mois' ?></span>
                    </div>
                    <p class="text-secondary small mb-3"><?= e($plan['description'] ?? '') ?></p>
                    <ul class="list-unstyled mb-3">
                        <?php foreach (json_decode($plan['features'] ?? '[]', true) as $feature): ?>
                        <li class="d-flex align-items-center gap-2 small mb-1">
                            <i class="bi bi-check-circle-fill text-success"></i><?= e($feature) ?>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <div class="small text-muted mb-2">Accès : <?= e($plan['lesson_access_type'] ?? 'limited') ?></div>
                    <div class="small text-muted mb-3">Support : <?= e($plan['support_level'] ?? 'none') ?></div>
                    <button class="btn btn-sm btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#editPlan<?= $plan['id'] ?>">
                        <i class="bi bi-pencil me-2"></i>Modifier
                    </button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Create Modal -->
<div class="modal fade" id="createPlanModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nouveau Plan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= url('/admin/plans/creer') ?>" method="POST">
                <?= $csrf ?>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nom *</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Prix MAD *</label>
                            <input type="number" step="0.01" name="price_mad" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Prix USD</label>
                            <input type="number" step="0.01" name="price_usd" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Durée (jours) *</label>
                            <input type="number" name="duration_days" class="form-control" value="30" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Fonctionnalités (une par ligne)</label>
                            <textarea name="features[]" class="form-control" rows="4" placeholder="Leçons illimitées&#10;Quiz premium&#10;Support email"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Type d'accès</label>
                            <select name="lesson_access_type" class="form-select">
                                <option value="all">Toutes les leçons</option>
                                <option value="limited">Limité</option>
                                <option value="none">Aucune</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Max leçons/jour (optionnel)</label>
                            <input type="number" name="max_lessons_per_day" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Niveau support</label>
                            <select name="support_level" class="form-select">
                                <option value="none">Aucun</option>
                                <option value="email">Email</option>
                                <option value="priority">Prioritaire</option>
                                <option value="dedicated">Dédié</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Couleur</label>
                            <input type="color" name="color" class="form-control form-control-color w-100" value="#0975e4">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Ordre</label>
                            <input type="number" name="sort_order" class="form-control" value="0">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Créer</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php View::endSection(); ?>
