<?php View::section('content'); ?>
<section class="section-padding">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <nav aria-label="breadcrumb" class="mb-3">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= url('/') ?>">Accueil</a></li>
                        <li class="breadcrumb-item"><a href="<?= url('/evenements') ?>">Événements</a></li>
                        <li class="breadcrumb-item active"><?= e($event['title'] ?? '') ?></li>
                    </ol>
                </nav>
                
                <div class="row g-5">
                    <div class="col-lg-8">
                        <?php if ($event['image_url']): ?>
                        <img src="<?= e($event['image_url']) ?>" alt="" class="w-100 rounded-4 mb-4" style="max-height:400px;object-fit:cover">
                        <?php endif; ?>
                        
                        <div class="d-flex gap-2 mb-3">
                            <span class="badge bg-primary"><?= e(ucfirst($event['event_type'] ?? '')) ?></span>
                            <?php if ($event['max_participants'] && ($event['current_participants'] ?? 0) >= $event['max_participants']): ?>
                            <span class="badge bg-danger">Complet</span>
                            <?php endif; ?>
                        </div>
                        
                        <h1 class="fw-bold mb-3"><?= e($event['title'] ?? '') ?></h1>
                        
                        <div class="event-meta d-flex flex-wrap gap-4 mb-4">
                            <div class="d-flex align-items-center gap-2 text-muted">
                                <i class="bi bi-calendar text-primary"></i>
                                <span><?= formatDatetime($event['event_date'] ?? '') ?></span>
                            </div>
                            <?php if ($event['end_date']): ?>
                            <div class="d-flex align-items-center gap-2 text-muted">
                                <i class="bi bi-calendar-check text-primary"></i>
                                <span>Fin : <?= formatDatetime($event['end_date']) ?></span>
                            </div>
                            <?php endif; ?>
                            <?php if ($event['location']): ?>
                            <div class="d-flex align-items-center gap-2 text-muted">
                                <i class="bi bi-geo-alt text-danger"></i>
                                <span><?= e($event['location']) ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="event-description">
                            <?= nl2br(e($event['description'] ?? '')) ?>
                        </div>
                        
                        <?php if ($event['registration_url']): ?>
                        <a href="<?= e($event['registration_url']) ?>" target="_blank" class="btn btn-primary mt-4">
                            <i class="bi bi-box-arrow-up-right me-2"></i>Lien d'Inscription Externe
                        </a>
                        <?php endif; ?>
                    </div>
                    
                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm rounded-4 sticky-top" style="top:100px">
                            <div class="card-body p-4">
                                <h5 class="fw-semibold mb-3">Inscription</h5>
                                
                                <?php if ($isRegistered): ?>
                                <div class="alert alert-success">
                                    <i class="bi bi-check-circle-fill me-2"></i>Vous êtes inscrit à cet événement !
                                </div>
                                <?php elseif ($event['max_participants'] && ($event['current_participants'] ?? 0) >= $event['max_participants']): ?>
                                <div class="alert alert-warning">
                                    <i class="bi bi-exclamation-triangle me-2"></i>Cet événement est complet.
                                </div>
                                <?php else: ?>
                                <form action="<?= url('/evenement/inscription') ?>" method="POST">
                                    <?= $csrf ?>
                                    <input type="hidden" name="event_id" value="<?= $event['id'] ?>">
                                    <div class="mb-3">
                                        <label class="form-label small">Vos informations seront utilisées :</label>
                                        <div class="small text-muted">
                                            <div><?= e(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?></div>
                                            <div><?= e($user['email'] ?? '') ?></div>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="bi bi-person-plus me-2"></i>S'inscrire
                                    </button>
                                </form>
                                <?php endif; ?>
                                
                                <?php if ($event['max_participants']): ?>
                                <div class="mt-3 pt-3 border-top">
                                    <div class="d-flex justify-content-between small">
                                        <span class="text-muted">Participants</span>
                                        <span class="fw-semibold"><?= $event['current_participants'] ?? 0 ?> / <?= $event['max_participants'] ?></span>
                                    </div>
                                    <div class="progress mt-2" style="height:6px">
                                        <div class="progress-bar bg-primary" style="width:<?= min(100, (($event['current_participants'] ?? 0) / max(1, $event['max_participants'])) * 100) ?>"></div>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php View::endSection(); ?>
