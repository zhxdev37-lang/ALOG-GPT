<?php View::section('content'); ?>
<div class="dashboard-content">
    <div class="mb-4">
        <h4 class="fw-bold mb-1">Matières</h4>
        <p class="text-secondary mb-0">Choisissez une matière pour commencer à apprendre</p>
    </div>
    
    <div class="row g-4">
        <?php foreach ($subjects as $subject): ?>
        <div class="col-md-6 col-lg-4">
            <a href="<?= url('/cours/' . $subject['slug']) ?>" class="text-decoration-none">
                <div class="subject-card card border-0 shadow-sm rounded-4 h-100 p-4" style="border-left: 4px solid <?= e($subject['color'] ?? '#0975e4') ?> !important;">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="subject-icon rounded-3 d-flex align-items-center justify-content-center" style="width:56px;height:56px;background:<?= e($subject['color'] ?? '#0975e4') ?>15;color:<?= e($subject['color'] ?? '#0975e4') ?>">
                            <i class="bi bi-<?= e($subject['icon'] ?? 'book') ?> fs-4"></i>
                        </div>
                        <div>
                            <h5 class="fw-semibold text-dark mb-0"><?= e($subject['name']) ?></h5>
                            <span class="small text-muted"><?= e($subject['description'] ?? '') ?></span>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="badge bg-light text-dark border"><?= e($subject['level_name'] ?? 'Tous niveaux') ?></span>
                        <i class="bi bi-arrow-right text-primary"></i>
                    </div>
                </div>
            </a>
        </div>
        <?php endforeach; ?>
        
        <?php if (empty($subjects)): ?>
        <div class="col-12 text-center py-5">
            <div class="text-muted">
                <i class="bi bi-book display-4"></i>
                <p class="mt-3">Aucune matière disponible pour votre niveau actuel.</p>
                <a href="<?= url('/profil') ?>" class="btn btn-primary">Mettre à jour mon niveau</a>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<style>
.subject-card { transition: all 0.3s ease; }
.subject-card:hover { transform: translateY(-4px); box-shadow: 0 10px 40px rgba(0,0,0,0.12) !important; }
</style>
<?php View::endSection(); ?>
