<?php View::section('content'); ?>
<div class="dashboard-content">
    <div class="mb-4">
        <h4 class="fw-bold mb-1">Mes Badges</h4>
        <p class="text-secondary mb-0">Vos succès et ceux à débloquer</p>
    </div>
    
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="fw-semibold mb-0">Badges Gagnés</h5>
                </div>
                <div class="card-body p-4">
                    <?php if (!empty($earned)): ?>
                    <div class="row g-3">
                        <?php foreach ($earned as $ach): ?>
                        <div class="col-6">
                            <div class="achievement-earned p-3 rounded-3 border text-center" style="border-color:<?= e($ach['color'] ?? '#ffd700') ?>40 !important; background:<?= e($ach['color'] ?? '#ffd700') ?>08">
                                <div class="achievement-icon mb-2" style="color:<?= e($ach['color'] ?? '#ffd700') ?>">
                                    <i class="bi bi-<?= e($ach['icon'] ?? 'trophy') ?> fs-2"></i>
                                </div>
                                <div class="fw-semibold small"><?= e($ach['name']) ?></div>
                                <div class="small text-muted">+<?= $ach['xp_bonus'] ?? 0 ?> XP</div>
                                <div class="small text-muted"><?= timeAgo($ach['earned_at']) ?></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-award display-4"></i>
                        <p class="mt-2">Aucun badge encore. Complétez des leçons pour commencer !</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="fw-semibold mb-0">Badges à Débloquer</h5>
                </div>
                <div class="card-body p-4">
                    <?php if (!empty($available)): ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($available as $ach): ?>
                        <div class="list-group-item px-0 py-3 d-flex align-items-center gap-3">
                            <div class="achievement-icon-locked text-muted">
                                <i class="bi bi-<?= e($ach['icon'] ?? 'trophy') ?> fs-3"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold"><?= e($ach['name']) ?></div>
                                <div class="small text-muted"><?= e($ach['description'] ?? '') ?></div>
                                <div class="small text-muted">Requis : <?= $ach['requirement_value'] ?> <?= e($ach['requirement_type']) ?></div>
                            </div>
                            <span class="badge bg-secondary bg-opacity-10 text-secondary"><?= $ach['xp_bonus'] ?? 0 ?> XP</span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-stars display-4"></i>
                        <p class="mt-2">Félicitations ! Vous avez débloqué tous les badges disponibles.</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php View::endSection(); ?>
