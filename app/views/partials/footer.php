<footer class="footer bg-dark text-white py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <div class="brand-icon-sm">
                        <i class="bi bi-mortarboard-fill text-white"></i>
                    </div>
                    <span class="fw-bold fs-5">ALOG Academy</span>
                </div>
                <p class="text-white-50 mb-3">La plateforme éducative premium pour les étudiants marocains. Excellons ensemble vers l'avenir.</p>
                <div class="d-flex gap-2">
                    <a href="<?= e(Setting::get('facebook_page', '#')) ?>" class="social-link" target="_blank" rel="noopener"><i class="bi bi-facebook"></i></a>
                    <a href="<?= e(Setting::get('instagram_page', '#')) ?>" class="social-link" target="_blank" rel="noopener"><i class="bi bi-instagram"></i></a>
                    <a href="<?= e(Setting::get('youtube_channel', '#')) ?>" class="social-link" target="_blank" rel="noopener"><i class="bi bi-youtube"></i></a>
                    <a href="<?= e(Setting::get('discord_invite', '#')) ?>" class="social-link" target="_blank" rel="noopener"><i class="bi bi-discord"></i></a>
                </div>
            </div>
            
            <div class="col-lg-2 col-6">
                <h6 class="fw-semibold mb-3">Navigation</h6>
                <ul class="list-unstyled footer-links">
                    <li><a href="<?= url('/a-propos') ?>">À Propos</a></li>
                    <li><a href="<?= url('/services') ?>">Services</a></li>
                    <li><a href="<?= url('/tarifs') ?>">Tarifs</a></li>
                    <li><a href="<?= url('/blog') ?>">Blog</a></li>
                </ul>
            </div>
            
            <div class="col-lg-2 col-6">
                <h6 class="fw-semibold mb-3">Support</h6>
                <ul class="list-unstyled footer-links">
                    <li><a href="<?= url('/faq') ?>">FAQ</a></li>
                    <li><a href="<?= url('/contact') ?>">Contact</a></li>
                    <li><a href="<?= url('/connexion') ?>">Connexion</a></li>
                    <li><a href="<?= url('/inscription') ?>">Inscription</a></li>
                </ul>
            </div>
            
            <div class="col-lg-4">
                <h6 class="fw-semibold mb-3">Newsletter</h6>
                <p class="text-white-50 small">Recevez nos conseils et actualités éducatives.</p>
                <form class="d-flex gap-2" action="<?= url('/newsletter') ?>" method="POST">
                    <?= $csrf ?? '' ?>
                    <input type="email" name="email" class="form-control form-control-sm" placeholder="Votre email" required>
                    <button type="submit" class="btn btn-primary btn-sm">S'abonner</button>
                </form>
            </div>
        </div>
        
        <hr class="border-secondary my-4">
        
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
            <p class="mb-0 text-white-50 small">© <?= date('Y') ?> ALOG Academy. Tous droits réservés.</p>
            <div class="d-flex gap-3 small">
                <a href="<?= url('/confidentialite') ?>" class="text-white-50">Confidentialité</a>
                <a href="<?= url('/conditions') ?>" class="text-white-50">Conditions</a>
                <a href="<?= url('/sitemap.xml') ?>" class="text-white-50">Sitemap</a>
            </div>
        </div>
    </div>
</footer>
