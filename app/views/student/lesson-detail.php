<?php View::section('content'); ?>
<div class="dashboard-content">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="<?= url('/matieres') ?>">Matières</a></li>
            <li class="breadcrumb-item"><a href="<?= url('/cours/' . ($lesson['subject_slug'] ?? '')) ?>"><?= e($lesson['subject_name'] ?? '') ?></a></li>
            <li class="breadcrumb-item active"><?= e($lesson['title'] ?? '') ?></li>
        </ol>
    </nav>
    
    <div class="row g-4">
        <div class="col-lg-8">
            <!-- Video Player -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <div class="ratio ratio-16x9 bg-dark">
                    <iframe id="youtube-player" src="<?= e(str_replace('watch?v=', 'embed/', $lesson['youtube_url'] ?? '')) ?>?enablejsapi=1" title="<?= e($lesson['title'] ?? '') ?>" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                </div>
            </div>
            
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h4 class="fw-bold"><?= e($lesson['title'] ?? '') ?></h4>
                            <span class="badge bg-primary bg-opacity-10 text-primary"><?= e($lesson['subject_name'] ?? '') ?></span>
                            <span class="badge bg-secondary bg-opacity-10 text-secondary"><?= e($lesson['level_name'] ?? '') ?></span>
                        </div>
                        <span class="badge bg-warning text-dark"><i class="bi bi-lightning-charge me-1"></i>+<?= $lesson['xp_reward'] ?? 10 ?> XP</span>
                    </div>
                    <p class="text-secondary"><?= nl2br(e($lesson['description'] ?? '')) ?></p>
                </div>
            </div>
            
            <!-- PDF Course -->
            <?php if ($lesson['pdf_course_url']): ?>
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="fw-semibold mb-0"><i class="bi bi-file-earmark-pdf me-2 text-danger"></i>Cours PDF</h5>
                </div>
                <div class="card-body p-4">
                    <iframe src="<?= e($lesson['pdf_course_url']) ?>" class="w-100 rounded-3" style="height:500px;border:1px solid #e9ecef"></iframe>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- PDF Exercises -->
            <?php if ($lesson['pdf_exercises_url']): ?>
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="fw-semibold mb-0"><i class="bi bi-file-earmark-text me-2 text-info"></i>Exercices PDF</h5>
                </div>
                <div class="card-body p-4">
                    <iframe src="<?= e($lesson['pdf_exercises_url']) ?>" class="w-100 rounded-3" style="height:500px;border:1px solid #e9ecef"></iframe>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="col-lg-4">
            <!-- Progress -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h6 class="fw-semibold mb-3">Votre Progression</h6>
                    <div class="d-flex flex-column gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-play-circle <?= !empty($progress['video_completed']) ? 'text-success' : 'text-muted' ?>"></i>
                            <span class="small">Vidéo <?= !empty($progress['video_completed']) ? 'terminée' : 'en cours' ?></span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-question-circle <?= !empty($progress['quiz_completed']) ? 'text-success' : 'text-muted' ?>"></i>
                            <span class="small">Quiz <?= !empty($progress['quiz_completed']) ? ($progress['quiz_passed'] ? 'réussi (' . $progress['quiz_score'] . '%)' : 'non réussi') : 'non passé' ?></span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-check-circle <?= !empty($progress['completed_at']) ? 'text-success' : 'text-muted' ?>"></i>
                            <span class="small">Leçon <?= !empty($progress['completed_at']) ? 'complétée' : 'incomplète' ?></span>
                        </div>
                    </div>
                    
                    <?php if ($lesson['quiz_id'] && !empty($progress['video_completed']) && empty($progress['quiz_completed'])): ?>
                    <a href="<?= url('/quiz/' . $lesson['slug']) ?>" class="btn btn-primary w-100 mt-3">
                        <i class="bi bi-question-circle me-2"></i>Passer le Quiz
                    </a>
                    <?php elseif (!empty($progress['completed_at'])): ?>
                    <div class="alert alert-success mt-3 mb-0">
                        <i class="bi bi-check-circle-fill me-2"></i>Leçon terminée ! +<?= $lesson['xp_reward'] ?? 10 ?> XP gagnés
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Lesson Info -->
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h6 class="fw-semibold mb-3">Informations</h6>
                    <ul class="list-unstyled mb-0">
                        <li class="d-flex justify-content-between py-2 border-bottom"><span class="text-muted">Durée</span><span><?= floor(($lesson['youtube_duration'] ?? 0) / 60) ?> min</span></li>
                        <li class="d-flex justify-content-between py-2 border-bottom"><span class="text-muted">XP</span><span class="text-primary fw-semibold">+<?= $lesson['xp_reward'] ?? 10 ?></span></li>
                        <li class="d-flex justify-content-between py-2 border-bottom"><span class="text-muted">Niveau</span><span><?= e($lesson['level_name'] ?? '') ?></span></li>
                        <li class="d-flex justify-content-between py-2"><span class="text-muted">Plan</span><span><?= e($lesson['plan_name'] ?? 'Free') ?></span></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Simple video progress tracking (simplified - no YouTube API required for free hosting)
(function() {
    let watchedSeconds = 0;
    let interval;
    const lessonId = <?= $lesson['id'] ?? 0 ?>;
    const token = document.querySelector('input[name="_token"]')?.value;
    
    function trackProgress() {
        watchedSeconds += 5;
        
        fetch('<?= url('/lecon/progres-video') ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
            body: `_token=${token}&lesson_id=${lessonId}&seconds=${watchedSeconds}`
        }).catch(() => {});
    }
    
    // Start tracking when page is visible
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            clearInterval(interval);
        } else {
            interval = setInterval(trackProgress, 5000);
        }
    });
    
    interval = setInterval(trackProgress, 5000);
})();
</script>
<?php View::endSection(); ?>
