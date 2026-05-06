<?php View::section('content'); ?>
<div class="dashboard-content">
    <div class="mb-4">
        <h4 class="fw-bold mb-1">Recherche</h4>
        <p class="text-secondary mb-0">Resultats pour "<?= e($query) ?>"</p>
    </div>
    
    <div class="row g-4">
        <?php foreach (($results['data'] ?? []) as $post): ?>
        <div class="col-lg-4 col-md-6">
            <article class="blog-card h-100">
                <a href="<?= url('/blog/' . $post['slug']) ?>" class="text-decoration-none text-dark">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <?php if ($post['featured_image']): ?>
                        <img src="<?= e($post['featured_image']) ?>" alt="" class="card-img-top rounded-top-4" style="height:180px;object-fit:cover">
                        <?php endif; ?>
                        <div class="card-body p-4">
                            <span class="badge bg-primary bg-opacity-10 text-primary mb-2"><?= e($post['category_name'] ?? 'Blog') ?></span>
                            <h5 class="fw-bold mb-2"><?= e($post['title']) ?></h5>
                            <p class="text-secondary small mb-3"><?= e(truncate(strip_tags($post['excerpt'] ?? ''), 120)) ?></p>
                            <div class="d-flex justify-content-between align-items-center text-muted small">
                                <span><?= timeAgo($post['published_at'] ?? $post['created_at']) ?></span>
                                <span><i class="bi bi-eye me-1"></i><?= number_format($post['views_count'] ?? 0) ?></span>
                            </div>
                        </div>
                    </div>
                </a>
            </article>
        </div>
        <?php endforeach; ?>
        <?php if (empty($results['data'])): ?>
        <div class="col-12 text-center text-muted py-5">Aucun resultat trouve.</div>
        <?php endif; ?>
    </div>
    
    <?= pagination($results ?? []) ?>
</div>
<?php View::endSection(); ?>
