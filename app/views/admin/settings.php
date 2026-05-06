<?php View::section('content'); ?>
<div class="admin-content">
    <div class="mb-4">
        <h4 class="fw-bold mb-1">Paramètres</h4>
        <p class="text-secondary mb-0">Configuration de la plateforme</p>
    </div>
    
    <div class="row g-4">
        <div class="col-lg-3">
            <div class="list-group list-group-flush rounded-4 border shadow-sm">
                <?php foreach ($groups as $g): ?>
                <a href="<?= url('/admin/parametres?group=' . $g) ?>" class="list-group-item list-group-item-action <?= $currentGroup === $g ? 'active' : '' ?>">
                    <i class="bi bi-<?= match($g){'general'=>'gear','system'=>'cpu','security'=>'shield-lock','payment'=>'credit-card','seo'=>'search','analytics'=>'bar-chart','social'=>'share',default=>'circle'} ?> me-2"></i>
                    <?= ucfirst($g) ?>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        
        <div class="col-lg-9">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h5 class="fw-semibold mb-4"><?= ucfirst($currentGroup) ?></h5>
                    
                    <form action="<?= url('/admin/parametres/enregistrer') ?>" method="POST">
                        <?= $csrf ?>
                        <input type="hidden" name="group" value="<?= e($currentGroup) ?>">
                        
                        <?php foreach ($settings as $setting): ?>
                        <div class="mb-3">
                            <label class="form-label text-capitalize"><?= e(str_replace('_', ' ', $setting['setting_key'])) ?></label>
                            <?php if (strlen($setting['setting_value'] ?? '') > 100): ?>
                            <textarea name="settings[<?= e($setting['setting_key']) ?>]" class="form-control" rows="3"><?= e($setting['setting_value'] ?? '') ?></textarea>
                            <?php else: ?>
                            <input type="text" name="settings[<?= e($setting['setting_key']) ?>]" class="form-control" value="<?= e($setting['setting_value'] ?? '') ?>">
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i>Enregistrer
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php View::endSection(); ?>
