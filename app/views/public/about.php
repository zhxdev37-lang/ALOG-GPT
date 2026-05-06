<?php View::section('content'); ?>
<section class="section-padding">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <nav aria-label="breadcrumb" class="mb-4">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= url('/') ?>">Accueil</a></li>
                        <li class="breadcrumb-item active">À Propos</li>
                    </ol>
                </nav>
                
                <h1 class="fw-bold mb-4">À Propos d'ALOG Academy</h1>
                
                <div class="row g-5">
                    <div class="col-lg-6">
                        <h3 class="fw-semibold mb-3">Notre Mission</h3>
                        <p class="text-secondary">ALOG Academy est née d'une vision simple : démocratiser l'accès à une éducation de qualité pour tous les étudiants marocains. Face aux défis de l'apprentissage traditionnel, nous avons créé une plateforme qui combine technologie et pédagogie pour offrir une expérience d'apprentissage moderne, engageante et efficace.</p>
                        
                        <h3 class="fw-semibold mb-3 mt-4">Notre Vision</h3>
                        <p class="text-secondary">Devenir la plateforme éducative de référence au Maroc et en Afrique du Nord, en offrant aux étudiants les outils nécessaires pour réussir leur parcours académique et professionnel.</p>
                    </div>
                    <div class="col-lg-6">
                        <div class="about-image rounded-4 bg-primary bg-opacity-10 p-5 text-center">
                            <i class="bi bi-mortarboard-fill display-1 text-primary"></i>
                            <h4 class="mt-3 fw-bold">ALOG Academy</h4>
                            <p class="text-secondary">Excellence Éducative depuis 2024</p>
                        </div>
                    </div>
                </div>
                
                <div class="row g-4 mt-5">
                    <div class="col-md-4">
                        <div class="text-center p-4">
                            <div class="display-4 fw-bold text-primary mb-2">100%</div>
                            <div class="fw-semibold">Gratuit pour Commencer</div>
                            <p class="text-secondary small">Accès basique sans engagement financier</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center p-4">
                            <div class="display-4 fw-bold text-primary mb-2">24/7</div>
                            <div class="fw-semibold">Accès Illimité</div>
                            <p class="text-secondary small">Apprenez à votre rythme, quand vous voulez</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center p-4">
                            <div class="display-4 fw-bold text-primary mb-2">12</div>
                            <div class="fw-semibold">Régions du Maroc</div>
                            <p class="text-secondary small">Une communauté nationale connectée</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php View::endSection(); ?>
