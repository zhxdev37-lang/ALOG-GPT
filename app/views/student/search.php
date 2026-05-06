<?php View::section('content'); ?>
<div class="dashboard-content">
    <div class="mb-4">
        <h4 class="fw-bold mb-1">Recherche</h4>
        <p class="text-secondary mb-0">Résultats pour "<?= e($query) ?>"</p>
    </div>
    
    <form action="<?= url('/recherche') ?>" method="GET" class="mb-4">
        <div class="input-group">
            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
            <input type="text" name="q" class="form-control border-start-0" value="<?= e($query) ?>" placeholder="Rechercher un cours..." minlength="2">
            <button type="submit" class="btn btn-primary">Rechercher</button>
        </div>
    </form>
    
    <?php if (strlen($query) < 2): ?>
    <div class="alert alert-info">Veuillez entrer au moins 2 caractères pour la recherche.</div>
    <?php elseif (empty($results)): ?>
    <div class="text-center py-5">
        <i class="bi bi-search display-4 text-muted"></i>
        <p class="mt-3 text-muted">Aucun résultat trouvé pour "<?= e($query) ?>".</p>
    </div>
    <?php else: ?>
    <div class="row g-4">
        <?php foreach ($results as $lesson): ?>
        <div class="col-md-6">
            <a href="<?= url('/lecon/' . $lesson['slug']) ?>" class="text-decoration-none">
                <div class="card border-0 shadow-sm rounded-4 p-3 d-flex flex-row align-items-center gap-3">
                    <div class="rounded-3 bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center flex-shrink-0" style="width:60px;height:60px">
                        <i class="bi bi-play-circle fs-4"></i>
                    </div>
                    <div class="flex-grow-1 min-width-0">
                        <h6 class="fw-semibold text-dark mb-1 text-truncate"><?= e($lesson['title']) ?></h6>
                        <div class="small text-muted"><?= e($lesson['subject_name'] ?? '') ?></div>
                    </div>
                    <span class="badge bg-success bg-opacity-10 text-success flex-shrink-0">+<?= $lesson['xp_reward'] ?? 10 ?> XP</span>
                </div>
            </a>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>
<?php View::endSection(); ?>
