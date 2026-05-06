<?php View::section('content'); ?>
<section class="section-padding">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="text-center mb-5">
                    <h1 class="fw-bold">Questions Fréquentes</h1>
                    <p class="text-secondary">Trouvez rapidement les réponses à vos questions</p>
                </div>
                
                <?php foreach ($faqs as $category => $items): ?>
                <div class="faq-category mb-4">
                    <h4 class="fw-semibold mb-3 text-primary text-capitalize"><?= e($category) ?></h4>
                    <div class="accordion" id="faq-<?= e($category) ?>">
                        <?php foreach ($items as $i => $faq): ?>
                        <div class="accordion-item border-0 mb-2 shadow-sm rounded-3 overflow-hidden">
                            <h2 class="accordion-header">
                                <button class="accordion-button <?= $i > 0 ? 'collapsed' : '' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#faq-collapse-<?= $faq['id'] ?>">
                                    <i class="bi bi-question-circle text-primary me-2"></i>
                                    <?= e($faq['question']) ?>
                                </button>
                            </h2>
                            <div id="faq-collapse-<?= $faq['id'] ?>" class="accordion-collapse collapse <?= $i === 0 ? 'show' : '' ?>" data-bs-parent="#faq-<?= e($category) ?>">
                                <div class="accordion-body text-secondary">
                                    <?= nl2br(e($faq['answer'])) ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
                
                <div class="text-center mt-5 p-4 bg-light rounded-4">
                    <h5 class="fw-semibold">Vous ne trouvez pas votre réponse ?</h5>
                    <p class="text-secondary">Contactez notre équipe de support, nous vous répondrons rapidement.</p>
                    <a href="<?= url('/contact') ?>" class="btn btn-primary">
                        <i class="bi bi-envelope me-2"></i>Nous Contacter
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
<?php View::endSection(); ?>
