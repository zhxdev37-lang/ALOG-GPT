<?php View::section('content'); ?>
<div class="admin-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Leçons</h4>
            <p class="text-secondary mb-0">Gestion du contenu pédagogique</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createLessonModal">
            <i class="bi bi-plus-lg me-2"></i>Nouvelle Leçon
        </button>
    </div>
    
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>ID</th>
                            <th>Titre</th>
                            <th>Matière</th>
                            <th>Niveau</th>
                            <th>Plan</th>
                            <th>XP</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (($lessons['data'] ?? []) as $l): ?>
                        <tr>
                            <td><?= $l['id'] ?></td>
                            <td class="fw-semibold"><?= e($l['title']) ?></td>
                            <td class="small"><?= e($l['subject_name'] ?? '-') ?></td>
                            <td class="small"><?= e($l['level_name'] ?? '-') ?> <?= $l['filiere_name'] ? '- ' . e($l['filiere_name']) : '' ?></td>
                            <td><span class="badge bg-primary bg-opacity-10 text-primary"><?= e($l['plan_name'] ?? 'Free') ?></span></td>
                            <td><span class="badge bg-warning text-dark">+<?= $l['xp_reward'] ?? 0 ?></span></td>
                            <td><span class="badge bg-<?= ($l['is_active'] ?? 0) ? 'success' : 'secondary' ?>"><?= ($l['is_active'] ?? 0) ? 'Actif' : 'Inactif' ?></span></td>
                            <td>
                                <div class="d-flex gap-1">
                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editLesson<?= $l['id'] ?>"><i class="bi bi-pencil"></i></button>
                                    <form action="<?= url('/admin/lecons/supprimer') ?>" method="POST" class="d-inline" onsubmit="return confirm('Supprimer cette leçon ?')">
                                        <?= $csrf ?>
                                        <input type="hidden" name="id" value="<?= $l['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <?php if (!empty($lessons['last_page']) && $lessons['last_page'] > 1): ?>
    <div class="mt-3">
        <?= pagination($lessons) ?>
    </div>
    <?php endif; ?>
</div>

<!-- Create Modal -->
<div class="modal fade" id="createLessonModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nouvelle Leçon</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= url('/admin/lecons/creer') ?>" method="POST">
                <?= $csrf ?>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Titre *</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Matière *</label>
                            <select name="subject_id" class="form-select" required>
                                <?php foreach ($subjects as $s): ?>
                                <option value="<?= $s['id'] ?>"><?= e($s['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Niveau *</label>
                            <select name="school_level_id" class="form-select" required>
                                <?php foreach ($levels as $lvl): ?>
                                <option value="<?= $lvl['id'] ?>"><?= e($lvl['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Filière</label>
                            <select name="filiere_id" class="form-select">
                                <option value="">Non applicable</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">URL YouTube</label>
                            <input type="url" name="youtube_url" class="form-control" placeholder="https://youtube.com/watch?v=...">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Durée (secondes)</label>
                            <input type="number" name="youtube_duration" class="form-control" placeholder="600">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">URL PDF Cours (GitHub raw)</label>
                            <input type="url" name="pdf_course_url" class="form-control" placeholder="https://raw.githubusercontent.com/...">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">URL PDF Exercices (GitHub raw)</label>
                            <input type="url" name="pdf_exercises_url" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Image URL</label>
                            <input type="url" name="image_url" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">XP Récompense</label>
                            <input type="number" name="xp_reward" class="form-control" value="10">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Coût XP Déblocage</label>
                            <input type="number" name="xp_unlock_cost" class="form-control" placeholder="Optionnel">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Plan Requis *</label>
                            <select name="plan_id" class="form-select" required>
                                <?php foreach ($plans as $p): ?>
                                <option value="<?= $p['id'] ?>"><?= e($p['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Ordre</label>
                            <input type="number" name="sort_order" class="form-control" value="0">
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input type="checkbox" name="is_active" class="form-check-input" id="lessonActive" checked>
                                <label class="form-check-label" for="lessonActive">Actif</label>
                            </div>
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
