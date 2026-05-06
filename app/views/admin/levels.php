<?php View::section('content'); ?>
<div class="admin-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Niveaux Scolaires</h4>
            <p class="text-secondary mb-0">Gestion des niveaux et filieres</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createLevelModal">
            <i class="bi bi-plus-lg me-2"></i>Nouveau Niveau
        </button>
    </div>
    
    <div class="row g-4">
        <?php foreach ($levels as $level): ?>
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h5 class="fw-bold mb-1"><?= e($level['name']) ?></h5>
                            <p class="text-secondary small mb-0"><?= e($level['description'] ?? '') ?></p>
                        </div>
                        <span class="badge bg-primary"><?= count($level['filieres'] ?? []) ?> filieres</span>
                    </div>
                    
                    <?php if (!empty($level['filieres'])): ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($level['filieres'] as $filiere): ?>
                        <li class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center">
                            <span><?= e($filiere['name']) ?></span>
                            <span class="badge bg-secondary bg-opacity-10 text-secondary"><?= e($filiere['slug']) ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php else: ?>
                    <p class="text-muted small mb-0">Aucune filiere associee.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        <?php if (empty($levels)): ?>
        <div class="col-12 text-center text-muted py-5">Aucun niveau cree.</div>
        <?php endif; ?>
    </div>
</div>
<?php View::endSection(); ?>
