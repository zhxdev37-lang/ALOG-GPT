<?php View::section('content'); ?>
<div class="dashboard-content">
    <div class="mb-4">
        <nav aria-label="breadcrumb" class="mb-2">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?= url('/matieres') ?>">Matières</a></li>
                <li class="breadcrumb-item active"><?= e($subject['name']) ?></li>
            </ol>
        </nav>
        <h4 class="fw-bold"><?= e($subject['name']) ?></h4>
        <p class="text-secondary"><?= e($subject['description'] ?? '') ?></p>
    </div>
    
    <div class="row g-4">
        <?php foreach ($lessons as $lesson): ?>
        <div class="col-md-6">
            <div class="lesson-card card border-0 shadow-sm rounded-4 overflow-hidden <?= $lesson['is_locked'] ? 'opacity-75' : '' ?>">
                <div class="lesson-image-wrapper position-relative">
                    <img src="<?= e($lesson['image_url'] ?? asset('images/lesson-default.jpg')) ?>" alt="" class="w-100" style="height:160px;object-fit:cover" loading="lazy">
                    <?php if (!empty($lesson['progress']['completed_at'])): ?>
                    <div class="position-absolute top-0 end-0 m-2">
                        <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Complété</span>
                    </div>
                    <?php elseif ($lesson['is_locked']): ?>
                    <div class="position-absolute top-0 end-0 m-2">
                        <span class="badge bg-secondary"><i class="bi bi-lock me-1"></i>Verrouillé</span>
                    </div>
                    <?php endif; ?>
                    <div class="position-absolute bottom-0 start-0 end-0 p-2" style="background:linear-gradient(transparent, rgba(0,0,0,0.7))">
                        <span class="badge bg-dark bg-opacity-50"><i class="bi bi-play-circle me-1"></i><?= floor(($lesson['youtube_duration'] ?? 0) / 60) ?> min</span>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h5 class="fw-semibold mb-0"><?= e($lesson['title']) ?></h5>
                        <span class="badge bg-primary bg-opacity-10 text-primary">+<?= $lesson['xp_reward'] ?? 10 ?> XP</span>
                    </div>
                    <p class="text-secondary small mb-3"><?= e(truncate($lesson['description'] ?? '', 80)) ?></p>
                    
                    <?php if (!empty($lesson['progress'])): ?>
                    <div class="progress mb-3" style="height:6px">
                        <div class="progress-bar bg-success" style="width:<?= $lesson['video_progress'] ?? 0 ?>%"></div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <?php if ($lesson['is_locked']): ?>
                            <?php if ($lesson['is_unlocked_by_xp']): ?>
                            <form action="<?= url('/lecon/debloquer-xp') ?>" method="POST" class="d-inline">
                                <?= $csrf ?>
                                <input type="hidden" name="lesson_id" value="<?= $lesson['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-warning">
                                    <i class="bi bi-unlock me-1"></i>Débloquer <?= $lesson['xp_unlock_cost'] ?> XP
                                </button>
                            </form>
                            <?php else: ?>
                            <span class="badge bg-secondary"><i class="bi bi-lock me-1"></i>Plan requis</span>
                            <?php endif; ?>
                        <?php else: ?>
                        <a href="<?= url('/lecon/' . $lesson['slug']) ?>" class="btn btn-sm btn-primary">
                            <i class="bi bi-play-fill me-1"></i><?= !empty($lesson['progress']) ? 'Continuer' : 'Commencer' ?>
                        </a>
                        <?php endif; ?>
                        
                        <?php if ($lesson['quiz_id']): ?>
                        <span class="small text-muted"><i class="bi bi-question-circle me-1"></i>Quiz</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        
        <?php if (empty($lessons)): ?>
        <div class="col-12 text-center py-5">
            <div class="text-muted">
                <i class="bi bi-journal-x display-4"></i>
                <p class="mt-3">Aucune leçon disponible pour cette matière et votre niveau.</p>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php View::endSection(); ?>
