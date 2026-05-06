<?php View::section('content'); ?>
<div class="dashboard-content">
    <div class="mb-4">
        <h4 class="fw-bold mb-1">Paiement</h4>
        <p class="text-secondary mb-0">Finalisez votre abonnement <?= e($plan['name']) ?></p>
    </div>
    
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-semibold mb-3">Récapitulatif</h5>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span>Plan</span>
                        <span class="fw-semibold"><?= e($plan['name']) ?></span>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span>Prix</span>
                        <span><?= formatPrice((float)$plan['price_mad']) ?>/mois</span>
                    </div>
                    <?php if ($promo): ?>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span>Promo (<?= e($promo['code']) ?>)</span>
                        <span class="text-success">-<?= formatPrice($discount) ?></span>
                    </div>
                    <?php endif; ?>
                    <div class="d-flex justify-content-between py-2">
                        <span class="fw-bold">Total</span>
                        <span class="fw-bold text-primary fs-5"><?= formatPrice($finalPrice) ?></span>
                    </div>
                </div>
            </div>
            
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h5 class="fw-semibold mb-3">Choisir un Moyen de Paiement</h5>
                    
                    <form action="<?= url('/paiement/process') ?>" method="POST">
                        <?= $csrf ?>
                        <input type="hidden" name="plan_id" value="<?= $plan['id'] ?>">
                        <?php if ($promo): ?>
                        <input type="hidden" name="promo_code" value="<?= e($promo['code']) ?>">
                        <?php endif; ?>
                        
                        <div class="d-flex flex-column gap-2 mb-4">
                            <label class="payment-option">
                                <input type="radio" name="method" value="cmi" class="d-none" required>
                                <div class="option-box p-3 rounded-3 border d-flex align-items-center gap-3">
                                    <div class="payment-icon bg-primary bg-opacity-10 text-primary"><i class="bi bi-credit-card"></i></div>
                                    <div>
                                        <div class="fw-semibold">Carte Bancaire (CMI)</div>
                                        <div class="small text-muted">Paiement sécurisé via CMI</div>
                                    </div>
                                </div>
                            </label>
                            <label class="payment-option">
                                <input type="radio" name="method" value="paypal" class="d-none">
                                <div class="option-box p-3 rounded-3 border d-flex align-items-center gap-3">
                                    <div class="payment-icon bg-info bg-opacity-10 text-info"><i class="bi bi-paypal"></i></div>
                                    <div>
                                        <div class="fw-semibold">PayPal</div>
                                        <div class="small text-muted">Paiement international</div>
                                    </div>
                                </div>
                            </label>
                            <label class="payment-option">
                                <input type="radio" name="method" value="whatsapp" class="d-none">
                                <div class="option-box p-3 rounded-3 border d-flex align-items-center gap-3">
                                    <div class="payment-icon bg-success bg-opacity-10 text-success"><i class="bi bi-whatsapp"></i></div>
                                    <div>
                                        <div class="fw-semibold">WhatsApp</div>
                                        <div class="small text-muted">Paiement manuel assisté</div>
                                    </div>
                                </div>
                            </label>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 py-3">
                            <i class="bi bi-lock me-2"></i>Payer <?= formatPrice($finalPrice) ?>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.payment-option input:checked + .option-box { border-color: #0975e4 !important; background: rgba(9,117,228,0.05); }
.payment-option .option-box { cursor: pointer; transition: all 0.2s; }
.payment-icon { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; }
</style>
<?php View::endSection(); ?>
