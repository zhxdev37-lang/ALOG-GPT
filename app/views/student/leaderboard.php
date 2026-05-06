<?php View::section('content'); ?>
<div class="dashboard-content">
    <div class="mb-4">
        <h4 class="fw-bold mb-1">Classement</h4>
        <p class="text-secondary mb-0">Découvrez les meilleurs étudiants du Maroc</p>
    </div>
    
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <div class="d-flex gap-2 flex-wrap">
                <a href="<?= url('/classement?type=global') ?>" class="btn btn-sm <?= $type === 'global' ? 'btn-primary' : 'btn-outline-primary' ?>">Global</a>
                <a href="<?= url('/classement?type=weekly') ?>" class="btn btn-sm <?= $type === 'weekly' ? 'btn-primary' : 'btn-outline-primary' ?>">Hebdomadaire</a>
                <a href="<?= url('/classement?type=regional') ?>" class="btn btn-sm <?= $type === 'regional' ? 'btn-primary' : 'btn-outline-primary' ?>">Régional</a>
                <a href="<?= url('/classement?type=level') ?>" class="btn btn-sm <?= $type === 'level' ? 'btn-primary' : 'btn-outline-primary' ?>">Par Niveau</a>
            </div>
        </div>
    </div>
    
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="leaderboard-body">
                <?php $rank = 1; foreach ($leaders as $student): ?>
                <div class="leaderboard-row d-flex align-items-center gap-3 p-3 border-bottom <?= $rank <= 3 ? 'top-' . $rank : '' ?> <?= ($student['id'] ?? 0) == ($user['id'] ?? 0) ? 'bg-primary bg-opacity-10' : '' ?>">
                    <div class="rank-number fw-bold <?= $rank <= 3 ? 'text-primary' : 'text-muted' ?>">#<?= $rank ?></div>
                    <img src="<?= avatar($student['avatar'] ?? 'avatar1.png') ?>" alt="" class="leaderboard-avatar rounded-circle">
                    <div class="flex-grow-1">
                        <div class="fw-semibold"><?= e(($student['first_name'] ?? '') . ' ' . ($student['last_name'] ?? '')) ?></div>
                        <div class="small text-muted">Niveau <?= $student['level'] ?? 1 ?> • <?= e($student['region'] ?? 'Maroc') ?> • <?= e($student['level_name'] ?? '') ?></div>
                    </div>
                    <div class="text-end">
                        <div class="fw-bold text-primary"><?= number_format($student['xp_total'] ?? ($student['xp_earned'] ?? 0)) ?> XP</div>
                        <?php if ($type === 'weekly'): ?>
                        <div class="small text-muted"><?= $student['lessons_completed'] ?? 0 ?> leçons</div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php $rank++; endforeach; ?>
                
                <?php if (empty($leaders)): ?>
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-trophy display-4"></i>
                    <p class="mt-2">Aucune donnée de classement pour cette catégorie.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php View::endSection(); ?>
