<?php View::section('content'); ?>
<section class="section-padding">
    <div class="container">
        <div class="text-center mb-5">
            <h1 class="fw-bold">Nos Tarifs</h1>
            <p class="text-secondary">Choisissez le plan adapté à vos ambitions</p>
        </div>
        
        <div class="row g-4 justify-content-center">
            <?php foreach ($plans as $plan): ?>
            <div class="col-md-4">
                <div class="pricing-card h-100 p-4 rounded-4 border bg-white <?= $plan['slug'] === 'pro' ? 'pricing-popular border-primary shadow-lg' : '' ?>">
                    <?php if ($plan['slug'] === 'pro'): ?>
                    <div class="popular-badge">Le plus populaire</div>
                    <?php endif; ?>
                    
                    <div class="text-center mb-4">
                        <h4 class="fw-bold"><?= e($plan['name']) ?></h4>
                        <div class="pricing-price mt-3">
                            <span class="display-4 fw-bold" style="color:<?= e($plan['color']) ?>"><?= formatPrice((float)$plan['price_mad']) ?></span>
                            <span class="text-muted">/<?= $plan['duration_days'] >= 365 ? 'an' : 'mois' ?></span>
                        </div>
                        <p class="text-secondary small mt-2"><?= e($plan['description']) ?></p>
                    </div>
                    
                    <ul class="list-unstyled mb-4">
                        <?php foreach (json_decode($plan['features'] ?? '[]', true) as $feature): ?>
                        <li class="d-flex align-items-center gap-2 mb-2">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <span class="small"><?= e($feature) ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    
                    <a href="<?= $plan['price_mad'] > 0 ? url('/checkout/' . $plan['slug']) : url('/inscription') ?>" class="btn w-100 py-2 <?= $plan['slug'] === 'pro' ? 'btn-primary' : 'btn-outline-primary' ?>">
                        <?= $plan['price_mad'] > 0 ? 'Choisir ce Plan' : 'Commencer Gratuitement' ?>
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-5">
            <h5 class="fw-semibold mb-3">Moyens de Paiement Acceptés</h5>
            <div class="d-flex justify-content-center gap-3">
                <span class="badge bg-light text-dark border">CMI</span>
                <span class="badge bg-light text-dark border">PayPal</span>
                <span class="badge bg-light text-dark border">WhatsApp</span>
            </div>
            <p class="text-muted small mt-2">Paiement sécurisé et crypté. Annulation possible à tout moment.</p>
        </div>
    </div>
</section>
<?php View::endSection(); ?>
