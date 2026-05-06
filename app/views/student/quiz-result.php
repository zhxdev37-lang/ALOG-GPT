<?php View::section('content'); ?>
<div class="dashboard-content">
    <div class="mb-4">
        <a href="<?= url('/tableau-de-bord') ?>" class="btn btn-sm btn-outline-secondary mb-2"><i class="bi bi-arrow-left me-2"></i>Retour</a>
        <h4 class="fw-bold mb-1">Resultat du Quiz</h4>
        <p class="text-secondary mb-0"><?= e($attempt['quiz_title'] ?? 'Quiz') ?></p>
    </div>
    
    <div class="row g-4 justify-content-center">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 text-center p-4">
                <div class="mb-3">
                    <?php $pct = $attempt['percentage'] ?? 0; ?>
                    <?php if ($attempt['passed'] ?? false): ?>
                    <i class="bi bi-trophy-fill text-warning" style="font-size:4rem"></i>
                    <h3 class="fw-bold text-success mt-3">Bravo ! Vous avez reussi !</h3>
                    <?php else: ?>
                    <i class="bi bi-x-circle-fill text-danger" style="font-size:4rem"></i>
                    <h3 class="fw-bold text-danger mt-3">Dommage, essayez encore !</h3>
                    <?php endif; ?>
                </div>
                
                <div class="row g-3 mb-4">
                    <div class="col-4">
                        <div class="p-3 rounded-3 bg-light">
                            <div class="fw-bold fs-4"><?= $attempt['score'] ?? 0 ?></div>
                            <div class="small text-muted">Points</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-3 rounded-3 bg-light">
                            <div class="fw-bold fs-4"><?= $attempt['total_points'] ?? 0 ?></div>
                            <div class="small text-muted">Total</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-3 rounded-3 bg-light">
                            <div class="fw-bold fs-4"><?= $pct ?>%</div>
                            <div class="small text-muted">Score</div>
                        </div>
                    </div>
                </div>
                
                <?php if ($attempt['passed'] ?? false): ?>
                <div class="alert alert-success">
                    <i class="bi bi-stars me-2"></i>Vous avez gagne <?= e($attempt['xp_earned'] ?? 0) ?> XP !
                </div>
                <?php endif; ?>
                
                <?php if (!empty($attempt['lesson_slug'])): ?>
                <a href="<?= url('/lecon/' . $attempt['lesson_slug']) ?>" class="btn btn-primary">Retour a la lecon</a>
                <?php else: ?>
                <a href="<?= url('/tableau-de-bord') ?>" class="btn btn-primary">Tableau de bord</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php View::endSection(); ?>
