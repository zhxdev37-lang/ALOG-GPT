<?php View::section('content'); ?>
<section class="section-padding">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-5">
                <h1 class="fw-bold mb-4">Contactez-Nous</h1>
                <p class="text-secondary mb-4">Une question, une suggestion ou un problème ? Notre équipe est là pour vous aider.</p>
                
                <div class="contact-info mb-4">
                    <div class="d-flex align-items-start gap-3 mb-3">
                        <div class="icon-box-sm bg-primary bg-opacity-10 text-primary"><i class="bi bi-envelope"></i></div>
                        <div>
                            <div class="fw-semibold">Email</div>
                            <a href="mailto:<?= e(Setting::get('site_email')) ?>" class="text-secondary text-decoration-none"><?= e(Setting::get('site_email')) ?></a>
                        </div>
                    </div>
                    <div class="d-flex align-items-start gap-3 mb-3">
                        <div class="icon-box-sm bg-primary bg-opacity-10 text-primary"><i class="bi bi-telephone"></i></div>
                        <div>
                            <div class="fw-semibold">Téléphone</div>
                            <span class="text-secondary"><?= e(Setting::get('site_phone')) ?></span>
                        </div>
                    </div>
                    <div class="d-flex align-items-start gap-3">
                        <div class="icon-box-sm bg-primary bg-opacity-10 text-primary"><i class="bi bi-geo-alt"></i></div>
                        <div>
                            <div class="fw-semibold">Adresse</div>
                            <span class="text-secondary"><?= e(Setting::get('site_address')) ?></span>
                        </div>
                    </div>
                </div>
                
                <div class="social-links">
                    <h6 class="fw-semibold mb-2">Suivez-nous</h6>
                    <div class="d-flex gap-2">
                        <a href="<?= e(Setting::get('facebook_page', '#')) ?>" class="btn btn-outline-primary btn-sm" target="_blank"><i class="bi bi-facebook"></i></a>
                        <a href="<?= e(Setting::get('instagram_page', '#')) ?>" class="btn btn-outline-primary btn-sm" target="_blank"><i class="bi bi-instagram"></i></a>
                        <a href="<?= e(Setting::get('youtube_channel', '#')) ?>" class="btn btn-outline-primary btn-sm" target="_blank"><i class="bi bi-youtube"></i></a>
                        <a href="<?= e(Setting::get('discord_invite', '#')) ?>" class="btn btn-outline-primary btn-sm" target="_blank"><i class="bi bi-discord"></i></a>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4 p-lg-5">
                        <h4 class="fw-semibold mb-4">Envoyez un Message</h4>
                        
                        <?php if (!empty($success)): ?>
                        <div class="alert alert-success"><?= e($success) ?></div>
                        <?php endif; ?>
                        
                        <form action="<?= url('/contact') ?>" method="POST">
                            <?= $csrf ?>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Nom *</label>
                                    <input type="text" name="name" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" value="<?= old('name') ?>" required>
                                    <?php if (isset($errors['name'])): ?><div class="invalid-feedback"><?= e($errors['name']) ?></div><?php endif; ?>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email *</label>
                                    <input type="email" name="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" value="<?= old('email') ?>" required>
                                    <?php if (isset($errors['email'])): ?><div class="invalid-feedback"><?= e($errors['email']) ?></div><?php endif; ?>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Téléphone</label>
                                    <input type="tel" name="phone" class="form-control" value="<?= old('phone') ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Sujet *</label>
                                    <input type="text" name="subject" class="form-control <?= isset($errors['subject']) ? 'is-invalid' : '' ?>" value="<?= old('subject') ?>" required>
                                    <?php if (isset($errors['subject'])): ?><div class="invalid-feedback"><?= e($errors['subject']) ?></div><?php endif; ?>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Message *</label>
                                    <textarea name="message" rows="5" class="form-control <?= isset($errors['message']) ? 'is-invalid' : '' ?>" required maxlength="2000"><?= old('message') ?></textarea>
                                    <?php if (isset($errors['message'])): ?><div class="invalid-feedback"><?= e($errors['message']) ?></div><?php endif; ?>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="bi bi-send me-2"></i>Envoyer le Message
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php View::endSection(); ?>
