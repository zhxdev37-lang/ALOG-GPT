<?php View::section('content'); ?>
<!-- Hero Section -->
<section class="hero-section py-5 py-lg-7">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <div class="hero-badge mb-3">
                    <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2">
                        <i class="bi bi-stars me-1"></i>Plateforme #1 au Maroc
                    </span>
                </div>
                <h1 class="hero-title display-4 fw-bold text-dark mb-3">
                    Rejoignez les <span class="text-primary">meilleurs étudiants</span> du Maroc
                </h1>
                <p class="hero-subtitle lead text-secondary mb-4">
                    Cours vidéo premium, quiz interactifs et classements nationaux pour exceller dans vos études du Tronc Commun jusqu'au Master.
                </p>
                <div class="d-flex flex-wrap gap-3 mb-4">
                    <a href="<?= url('/inscription') ?>" class="btn btn-primary btn-lg px-4">
                        <i class="bi bi-rocket-takeoff me-2"></i>Commencer Gratuitement
                    </a>
                    <a href="<?= url('/tarifs') ?>" class="btn btn-outline-dark btn-lg px-4">
                        <i class="bi bi-play-circle me-2"></i>Voir les Tarifs
                    </a>
                </div>
                <div class="d-flex align-items-center gap-4 text-muted small">
                    <div class="d-flex align-items-center gap-1">
                        <i class="bi bi-check-circle-fill text-success"></i>
                        <span>Accès Gratuit</span>
                    </div>
                    <div class="d-flex align-items-center gap-1">
                        <i class="bi bi-check-circle-fill text-success"></i>
                        <span>Quiz Illimités</span>
                    </div>
                    <div class="d-flex align-items-center gap-1">
                        <i class="bi bi-check-circle-fill text-success"></i>
                        <span>Classement National</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="hero-image-wrapper">
                    <div class="hero-card floating-card-1">
                        <div class="d-flex align-items-center gap-3">
                            <div class="icon-box bg-success bg-opacity-10 text-success">
                                <i class="bi bi-trophy"></i>
                            </div>
                            <div>
                                <div class="fw-bold">Top 1 National</div>
                                <div class="small text-muted">Ce mois-ci</div>
                            </div>
                        </div>
                    </div>
                    <div class="hero-card floating-card-2">
                        <div class="d-flex align-items-center gap-3">
                            <div class="icon-box bg-warning bg-opacity-10 text-warning">
                                <i class="bi bi-lightning-charge"></i>
                            </div>
                            <div>
                                <div class="fw-bold">12,450 XP</div>
                                <div class="small text-muted">Record de la semaine</div>
                            </div>
                        </div>
                    </div>
                    <div class="hero-main-visual">
                        <div class="study-illustration">
                            <div class="d-flex flex-column align-items-center justify-content-center h-100 text-white">
                                <i class="bi bi-mortarboard-fill display-1 mb-3"></i>
                                <h3 class="fw-bold">ALOG Academy</h3>
                                <p class="mb-0 opacity-75">Excellence Éducative</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats Bar -->
<section class="stats-bar py-4 bg-primary text-white">
    <div class="container">
        <div class="row text-center g-4">
            <div class="col-6 col-lg-3">
                <div class="stat-number fw-bold display-6"><?= number_format($stats['students'] ?? 0) ?></div>
                <div class="stat-label opacity-75">Étudiants</div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="stat-number fw-bold display-6"><?= number_format($stats['lessons'] ?? 0) ?></div>
                <div class="stat-label opacity-75">Leçons</div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="stat-number fw-bold display-6"><?= number_format($stats['quizzes'] ?? 0) ?></div>
                <div class="stat-label opacity-75">Quiz</div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="stat-number fw-bold display-6">12</div>
                <div class="stat-label opacity-75">Régions</div>
            </div>
        </div>
    </div>
</section>

<!-- Leaderboard Preview -->
<section class="section-padding bg-light">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-5">
                <h2 class="fw-bold mb-3">Le Classement National</h2>
                <p class="text-secondary mb-4">Compétitionnez avec les meilleurs étudiants de toutes les régions du Maroc. Gagnez des XP, montez de niveau et débloquez des badges exclusifs.</p>
                <a href="<?= url('/classement') ?>" class="btn btn-primary">
                    <i class="bi bi-trophy me-2"></i>Voir le Classement
                </a>
            </div>
            <div class="col-lg-7">
                <div class="leaderboard-card">
                    <div class="leaderboard-header d-flex align-items-center justify-content-between p-3 border-bottom">
                        <span class="fw-semibold">Top Étudiants - Semaine</span>
                        <span class="badge bg-primary">Global</span>
                    </div>
                    <div class="leaderboard-body">
                        <?php foreach (array_slice($weeklyLeaders ?? [], 0, 5) as $i => $student): ?>
                        <div class="leaderboard-row d-flex align-items-center gap-3 p-3 <?= $i < 3 ? 'top-' . ($i + 1) : '' ?>">
                            <div class="rank-number fw-bold <?= $i < 3 ? 'text-primary' : 'text-muted' ?>">#<?= $i + 1 ?></div>
                            <img src="<?= avatar($student['avatar'] ?? 'avatar1.png') ?>" alt="" class="leaderboard-avatar rounded-circle">
                            <div class="flex-grow-1">
                                <div class="fw-semibold"><?= e(($student['first_name'] ?? '') . ' ' . ($student['last_name'] ?? '')) ?></div>
                                <div class="small text-muted">Niveau <?= $student['level'] ?? 1 ?> • <?= $student['region'] ?? 'Maroc' ?></div>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold text-primary"><?= number_format($student['xp_earned'] ?? 0) ?> XP</div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features -->
<section class="section-padding">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Pourquoi Choisir ALOG Academy ?</h2>
            <p class="text-secondary">Tout ce dont vous avez besoin pour réussir vos études</p>
        </div>
        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="feature-card h-100 p-4 rounded-4 border">
                    <div class="feature-icon bg-primary bg-opacity-10 text-primary mb-3">
                        <i class="bi bi-play-btn"></i>
                    </div>
                    <h5 class="fw-semibold">Cours Vidéo</h5>
                    <p class="text-secondary small mb-0">Des centaines de vidéos pédagogiques sur YouTube, organisées par niveau et matière.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="feature-card h-100 p-4 rounded-4 border">
                    <div class="feature-icon bg-success bg-opacity-10 text-success mb-3">
                        <i class="bi bi-clipboard-check"></i>
                    </div>
                    <h5 class="fw-semibold">Quiz Interactifs</h5>
                    <p class="text-secondary small mb-0">Testez vos connaissances avec des QCM et Vrai/Faux corrigés automatiquement.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="feature-card h-100 p-4 rounded-4 border">
                    <div class="feature-icon bg-warning bg-opacity-10 text-warning mb-3">
                        <i class="bi bi-trophy"></i>
                    </div>
                    <h5 class="fw-semibold">Gamification</h5>
                    <p class="text-secondary small mb-0">Gagnez des XP, montez de niveau et collectionnez des badges exclusifs.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="feature-card h-100 p-4 rounded-4 border">
                    <div class="feature-icon bg-danger bg-opacity-10 text-danger mb-3">
                        <i class="bi bi-file-earmark-pdf"></i>
                    </div>
                    <h5 class="fw-semibold">PDF & Exercices</h5>
                    <p class="text-secondary small mb-0">Accédez aux cours PDF et exercices via notre lecteur intégré optimisé.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Plans Preview -->
<section class="section-padding bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Choisissez Votre Plan</h2>
            <p class="text-secondary">Commencez gratuitement et évoluez selon vos besoins</p>
        </div>
        <div class="row g-4 justify-content-center">
            <?php foreach ($plans ?? [] as $plan): ?>
            <div class="col-md-4">
                <div class="pricing-card h-100 p-4 rounded-4 border <?= $plan['slug'] === 'pro' ? 'pricing-popular border-primary' : 'bg-white' ?>">
                    <?php if ($plan['slug'] === 'pro'): ?>
                    <div class="popular-badge">Populaire</div>
                    <?php endif; ?>
                    <div class="text-center mb-4">
                        <h4 class="fw-bold"><?= e($plan['name']) ?></h4>
                        <div class="pricing-price">
                            <span class="display-5 fw-bold" style="color:<?= e($plan['color']) ?>"><?= formatPrice((float)$plan['price_mad']) ?></span>
                            <span class="text-muted">/mois</span>
                        </div>
                    </div>
                    <ul class="list-unstyled mb-4">
                        <?php foreach (json_decode($plan['features'] ?? '[]', true) as $feature): ?>
                        <li class="d-flex align-items-center gap-2 mb-2">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <span class="small"><?= e($feature) ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <a href="<?= url('/tarifs') ?>" class="btn w-100 <?= $plan['slug'] === 'pro' ? 'btn-primary' : 'btn-outline-primary' ?>">
                        <?= $plan['price_mad'] > 0 ? 'Choisir ' . e($plan['name']) : 'Commencer Gratuitement' ?>
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Blog Preview -->
<section class="section-padding">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <h2 class="fw-bold">Conseils & Actualités</h2>
                <p class="text-secondary mb-0">Restez informé et optimisez vos méthodes d'étude</p>
            </div>
            <a href="<?= url('/blog') ?>" class="btn btn-outline-primary">Voir Tout</a>
        </div>
        <div class="row g-4">
            <?php foreach ($recentPosts ?? [] as $post): ?>
            <div class="col-md-4">
                <article class="blog-card h-100">
                    <a href="<?= url('/blog/' . $post['slug']) ?>" class="text-decoration-none">
                        <div class="blog-image-wrapper mb-3">
                            <img src="<?= e($post['featured_image'] ?? asset('images/blog-default.jpg')) ?>" alt="" class="blog-image rounded-3 w-100" loading="lazy">
                        </div>
                        <span class="badge bg-primary bg-opacity-10 text-primary mb-2"><?= e($post['category_name'] ?? 'Actualité') ?></span>
                        <h5 class="fw-semibold text-dark"><?= e($post['title']) ?></h5>
                        <p class="text-secondary small"><?= e(truncate($post['excerpt'] ?? strip_tags($post['content'] ?? ''), 100)) ?></p>
                        <div class="text-muted small"><?= timeAgo($post['published_at'] ?? $post['created_at']) ?></div>
                    </a>
                </article>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="section-padding bg-primary text-white">
    <div class="container text-center">
        <h2 class="fw-bold mb-3">Prêt à Exceller ?</h2>
        <p class="lead opacity-75 mb-4">Rejoignez des milliers d'étudiants marocains qui progressent chaque jour avec ALOG Academy.</p>
        <a href="<?= url('/inscription') ?>" class="btn btn-light btn-lg px-5">
            <i class="bi bi-rocket-takeoff me-2"></i>Créer un Compte Gratuit
        </a>
    </div>
</section>
<?php View::endSection(); ?>
