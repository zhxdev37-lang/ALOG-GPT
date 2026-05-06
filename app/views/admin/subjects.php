<?php View::section('content'); ?>
<div class="admin-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Matieres</h4>
            <p class="text-secondary mb-0">Gestion des matieres par niveau</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createSubjectModal">
            <i class="bi bi-plus-lg me-2"></i>Nouvelle Matiere
        </button>
    </div>
    
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Slug</th>
                            <th>Couleur</th>
                            <th>Icone</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($subjects as $s): ?>
                        <tr>
                            <td><?= $s['id'] ?></td>
                            <td class="fw-semibold"><?= e($s['name']) ?></td>
                            <td class="small text-muted"><?= e($s['slug']) ?></td>
                            <td><span class="badge" style="background:<?= e($s['color'] ?? '#0975e4') ?>"><?= e($s['color'] ?? '#0975e4') ?></span></td>
                            <td><i class="bi bi-<?= e($s['icon'] ?? 'book') ?>"></i></td>
                            <td><span class="badge bg-<?= ($s['is_active'] ? 'success' : 'secondary') ?>"><?= $s['is_active'] ? 'Actif' : 'Inactif' ?></span></td>
                            <td>
                                <a href="<?= url('/matieres') ?>" target="_blank" class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($subjects)): ?>
                        <tr><td colspan="7" class="text-center text-muted py-4">Aucune matiere</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php View::endSection(); ?>
