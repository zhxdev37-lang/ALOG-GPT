<?php View::section('content'); ?>
<section class="section-padding">
    <div class="container">
        <div class="text-center mb-5">
            <h1 class="fw-bold">Événements</h1>
            <p class="text-secondary">Webinaires, examens blancs et rencontres éducatives</p>
        </div>
        
        <div class="row g-4">
            <?php foreach ($events as $event): ?>
            <div class="col-md-6 col-lg-4">
                <div class="event-card card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                    <div class="position-relative">
                        <img src="<?= e($event['image_url'] ?? asset('images/event-default.jpg')) ?>" alt="" class="w-100" style="height:200px;object-fit:cover" loading="lazy">
                        <div class="position-absolute top-0 start-0 m-2">
                            <span class="badge bg-primary"><?= e(ucfirst($event['event_type'] ?? 'Événement')) ?></span>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <h5 class="fw-semibold mb-2"><?= e($event['title']) ?></h5>
                        <p class="text-secondary small mb-3"><?= e(truncate($event['description'] ?? '', 100)) ?></p>
                        <div class="d-flex flex-column gap-2 mb-3">
                            <div class="small text-muted"><i class="bi bi-calendar me-2 text-primary"></i><?= formatDatetime($event['event_date']) ?></div>
                            <?php if ($event['location']): ?>
                            <div class="small text-muted"><i class="bi bi-geo-alt me-2 text-danger"></i><?= e($event['location']) ?></div>
                            <?php endif; ?>
                            <?php if ($event['max_participants']): ?>
                            <div class="small text-muted"><i class="bi bi-people me-2 text-success"></i><?= $event['current_participants'] ?> / <?= $event['max_participants'] ?> participants</div>
                            <?php endif; ?>
                        </div>
                        <a href="<?= url('/evenement/' . $event['slug']) ?>" class="btn btn-primary w-100">
                            <i class="bi bi-info-circle me-2"></i>Voir les Détails
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            
            <?php if (empty($events)): ?>
            <div class="col-12 text-center py-5">
                <div class="text-muted">
                    <i class="bi bi-calendar-x display-4"></i>
                    <p class="mt-3">Aucun événement programmé actuellement.</p>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <?php if (!empty($pagination['last_page']) && $pagination['last_page'] > 1): ?>
        <div class="mt-4">
            <?= pagination($pagination) ?>
        </div>
        <?php endif; ?>
    </div>
</section>
<?php View::endSection(); ?>
