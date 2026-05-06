<?php View::section('content'); ?>
<div class="dashboard-content">
    <div class="mb-4">
        <h4 class="fw-bold mb-1">Blog</h4>
        <p class="text-secondary mb-0">Conseils, actualités et orientation</p>
    </div>
    
    <div class="row g-4">
        <?php foreach (($posts['data'] ?? []) as $post): ?>
        <div class="col-md-6 col-lg-4">
            <article class="blog-card card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <a href="<?= url('/blog/' . $post['slug']) ?>" class="text-decoration-none">
                    <img src="<?= e($post['featured_image'] ?? asset('images/blog-default.jpg')) ?>" alt="" class="w-100" style="height:200px;object-fit:cover" loading="lazy">
                    <div class="card-body p-4">
                        <span class="badge bg-primary bg-opacity-10 text-primary mb-2"><?= e($post['category_name'] ?? 'Blog') ?></span>
                        <h5 class="fw-semibold text-dark mb-2"><?= e($post['title']) ?></h5>
                        <p class="text-secondary small mb-3"><?= e(truncate($post['excerpt'] ?? strip_tags($post['content'] ?? ''), 100)) ?></p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="small text-muted"><?= timeAgo($post['published_at'] ?? $post['created_at']) ?></span>
                            <span class="small text-muted"><i class="bi bi-eye me-1"></i><?= $post['views_count'] ?? 0 ?></span>
                        </div>
                    </div>
                </a>
            </article>
        </div>
        <?php endforeach; ?>
    </div>
    
    <?php if (!empty($posts['last_page']) && $posts['last_page'] > 1): ?>
    <div class="mt-4">
        <?= pagination($posts) ?>
    </div>
    <?php endif; ?>
</div>
<?php View::endSection(); ?>
