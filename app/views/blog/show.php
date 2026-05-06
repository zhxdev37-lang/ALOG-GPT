<?php View::section('content'); ?>
<article class="section-padding">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <nav aria-label="breadcrumb" class="mb-3">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= url('/') ?>">Accueil</a></li>
                        <li class="breadcrumb-item"><a href="<?= url('/blog') ?>">Blog</a></li>
                        <li class="breadcrumb-item active"><?= e($post['title'] ?? '') ?></li>
                    </ol>
                </nav>
                
                <header class="mb-4">
                    <span class="badge bg-primary bg-opacity-10 text-primary mb-2"><?= e($post['category_name'] ?? 'Blog') ?></span>
                    <h1 class="fw-bold"><?= e($post['title'] ?? '') ?></h1>
                    <div class="d-flex align-items-center gap-3 text-muted small">
                        <div class="d-flex align-items-center gap-2">
                            <img src="<?= avatar($post['author_avatar'] ?? 'avatar1.png') ?>" alt="" class="rounded-circle" style="width:32px;height:32px">
                            <span><?= e(($post['author_name'] ?? '') . ' ' . ($post['author_lastname'] ?? '')) ?></span>
                        </div>
                        <span>•</span>
                        <span><?= formatDate($post['published_at'] ?? $post['created_at']) ?></span>
                        <span>•</span>
                        <span><i class="bi bi-eye me-1"></i><?= $post['views_count'] ?? 0 ?> vues</span>
                    </div>
                </header>
                
                <?php if ($post['featured_image']): ?>
                <img src="<?= e($post['featured_image']) ?>" alt="" class="w-100 rounded-4 mb-4" style="max-height:400px;object-fit:cover" loading="lazy">
                <?php endif; ?>
                
                <div class="blog-content">
                    <?= $post['content'] ?? '' ?>
                </div>
                
                <?php if (!empty($post['tags'])): ?>
                <div class="mt-4 pt-4 border-top">
                    <div class="d-flex flex-wrap gap-2">
                        <?php foreach ($post['tags'] as $tag): ?>
                        <span class="badge bg-light text-dark border"><?= e($tag['name']) ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($related)): ?>
                <div class="mt-5 pt-4 border-top">
                    <h5 class="fw-semibold mb-3">Articles Similaires</h5>
                    <div class="row g-3">
                        <?php foreach ($related as $r): ?>
                        <div class="col-md-4">
                            <a href="<?= url('/blog/' . $r['slug']) ?>" class="text-decoration-none">
                                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                                    <img src="<?= e($r['featured_image'] ?? asset('images/blog-default.jpg')) ?>" alt="" class="w-100" style="height:140px;object-fit:cover">
                                    <div class="card-body p-3">
                                        <h6 class="fw-semibold text-dark small mb-1"><?= e($r['title']) ?></h6>
                                        <span class="text-muted small"><?= timeAgo($r['published_at'] ?? $r['created_at']) ?></span>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</article>
<?php View::endSection(); ?>
