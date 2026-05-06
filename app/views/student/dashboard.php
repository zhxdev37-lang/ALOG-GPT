<?php View::section('content'); ?>
<div class="dashboard-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Bonjour, <?= e($user['first_name'] ?? '') ?> !</h4>
            <p class="text-secondary mb-0">Voici votre progression aujourd'hui</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-warning text-dark"><i class="bi bi-lightning-charge me-1"></i><?= $xpToday ?? 0 ?> XP aujourd'hui</span>
            <?php if (!empty($activeSubscription)): ?>
            <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i><?= e($activeSubscription['plan_name'] ?? 'Free') ?></span>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-card p-3 rounded-4 border bg-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon bg-primary bg-opacity-10 text-primary"><i class="bi bi-journal-check"></i></div>
                    <div>
                        <div class="stat-value fw-bold"><?= $completedLessons ?? 0 ?></div>
                        <div class="stat-label text-secondary small">Leçons Complétées</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card p-3 rounded-4 border bg-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon bg-warning bg-opacity-10 text-warning"><i class="bi bi-lightning-charge"></i></div>
                    <div>
                        <div class="stat-value fw-bold"><?= number_format($user['xp_total'] ?? 0) ?></div>
                        <div class="stat-label text-secondary small">XP Total</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card p-3 rounded-4 border bg-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon bg-success bg-opacity-10 text-success"><i class="bi bi-trophy"></i></div>
                    <div>
                        <div class="stat-value fw-bold"><?= $user['level'] ?? 1 ?></div>
                        <div class="stat-label text-secondary small">Niveau</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card p-3 rounded-4 border bg-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon bg-danger bg-opacity-10 text-danger"><i class="bi bi-fire"></i></div>
                    <div>
                        <div class="stat-value fw-bold"><?= $user['streak_days'] ?? 0 ?></div>
                        <div class="stat-label text-secondary small">Jours de Série</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- XP Progress -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="fw-semibold">Progression vers Niveau <?= ($user['level'] ?? 1) + 1 ?></span>
                <span class="small text-muted"><?= number_format($user['xp_current'] ?? 0) ?> / <?= number_format(($user['level'] ?? 1) * 1000) ?> XP</span>
            </div>
            <div class="progress" style="height: 10px;">
                <div class="progress-bar bg-primary" style="width: <?= xpProgress($user['xp_total'] ?? 0) ?>%"></div>
            </div>
        </div>
    </div>
    
    <div class="row g-4">
        <!-- Recommended Lessons -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="fw-semibold mb-0">Leçons Recommandées</h5>
                </div>
                <div class="card-body p-4">
                    <?php if (!empty($recommendedLessons)): ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($recommendedLessons as $lesson): ?>
                        <a href="<?= url('/lecon/' . $lesson['slug']) ?>" class="list-group-item list-group-item-action d-flex align-items-center gap-3 px-0">
                            <div class="lesson-icon rounded-3 bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center" style="width:48px;height:48px">
                                <i class="bi bi-play-circle"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold"><?= e($lesson['title']) ?></div>
                                <div class="small text-muted"><?= e($lesson['subject_name']) ?></div>
                            </div>
                            <span class="badge bg-success bg-opacity-10 text-success">+<?= $lesson['xp_reward'] ?? 10 ?> XP</span>
                        </a>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-journal-x display-4"></i>
                        <p class="mt-2">Commencez par explorer les matières disponibles pour votre niveau.</p>
                        <a href="<?= url('/matieres') ?>" class="btn btn-primary">Explorer les Matières</a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Weekly Rank -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h6 class="fw-semibold mb-3">Classement Hebdo</h6>
                    <?php if (!empty($weeklyRank)): ?>
                    <div class="d-flex align-items-center gap-3">
                        <div class="rank-badge">#<?= $weeklyRank['rank_position'] ?? '-' ?></div>
                        <div>
                            <div class="fw-semibold"><?= number_format($weeklyRank['xp_earned'] ?? 0) ?> XP</div>
                            <div class="small text-muted">Cette semaine</div>
                        </div>
                    </div>
                    <?php else: ?>
                    <p class="text-muted small mb-0">Commencez à apprendre pour apparaître dans le classement.</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Achievements -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h6 class="fw-semibold mb-3">Derniers Badges</h6>
                    <?php if (!empty($achievements)): ?>
                    <div class="d-flex flex-wrap gap-2">
                        <?php foreach (array_slice($achievements, 0, 5) as $ach): ?>
                        <span class="badge rounded-pill" style="background:<?= e($ach['color'] ?? '#ffd700') ?>20;color:<?= e($ach['color'] ?? '#ffd700') ?>">
                            <i class="bi bi-award me-1"></i><?= e($ach['name']) ?>
                        </span>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <p class="text-muted small mb-0">Aucun badge encore. Complétez des leçons pour en gagner !</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Upcoming Events -->
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h6 class="fw-semibold mb-3">Prochains Événements</h6>
                    <?php if (!empty($upcomingEvents)): ?>
                    <div class="list-group list-group-flush">
                        <?php foreach (array_slice($upcomingEvents, 0, 3) as $event): ?>
                        <a href="<?= url('/evenement/' . $event['slug']) ?>" class="list-group-item list-group-item-action px-0 py-2">
                            <div class="fw-semibold small"><?= e($event['title']) ?></div>
                            <div class="small text-muted"><?= formatDatetime($event['event_date']) ?></div>
                        </a>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <p class="text-muted small mb-0">Aucun événement prévu prochainement.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php View::endSection(); ?>
