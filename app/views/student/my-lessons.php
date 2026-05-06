<?php View::section('content'); ?>
<div class="dashboard-content">
    <div class="mb-4">
        <h4 class="fw-bold mb-1">Mes Cours</h4>
        <p class="text-secondary mb-0">Suivez votre progression</p>
    </div>
    
    <ul class="nav nav-pills mb-4">
        <li class="nav-item">
            <a href="<?= url('/mes-cours?tab=in-progress') ?>" class="nav-link <?= $tab === 'in-progress' ? 'active' : '' ?>">En cours</a>
        </li>
        <li class="nav-item">
            <a href="<?= url('/mes-cours?tab=completed') ?>" class="nav-link <?= $tab === 'completed' ? 'active' : '' ?>">Termines</a>
        </li>
    </ul>
    
    <div class="row g-4">
        <?php foreach ($lessons as $lesson): ?>
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="badge bg-primary bg-opacity-10 text-primary"><?= e($lesson['subject_name'] ?? '-') ?></span>
                        <?php if ($tab === 'completed'): ?>
                        <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Termine</span>
                        <?php endif; ?>
                    </div>
                    <h5 class="fw-bold mb-2"><?= e($lesson['title']) ?></h5>
                    <p class="text-secondary small mb-3"><?= e(truncate(strip_tags($lesson['description'] ?? ''), 100)) ?></p>
                    <div class="d-flex justify-content-between align-items-center">
                        <?php if ($tab === 'completed'): ?>
                        <span class="small text-muted">Complete le <?= formatDate($lesson['completed_at']) ?></span>
                        <?php else: ?>
                        <div class="progress flex-grow-1 me-3" style="height:6px">
                            <div class="progress-bar" style="width:<?= min(100, round(($lesson['video_watched_seconds'] ?? 0) / max(1, $lesson['youtube_duration'] ?? 1) * 100)) ?>"></div>
                        </div>
                        <?php endif; ?>
                        <a href="<?= url('/lecon/' . $lesson['slug']) ?>" class="btn btn-sm btn-primary"><?= $tab === 'completed' ? 'Revoir' : 'Continuer' ?></a>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        <?php if (empty($lessons)): ?>
        <div class="col-12 text-center text-muted py-5">
            <i class="bi bi-book" style="font-size:3rem"></i>
            <p class="mt-3">Aucun cours <?= $tab === 'completed' ? 'termine' : 'en cours' ?>.</p>
            <a href="<?= url('/matieres') ?>" class="btn btn-primary">Parcourir les matieres</a>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php View::endSection(); ?>
