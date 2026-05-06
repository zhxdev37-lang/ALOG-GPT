<?php View::section('content'); ?>
<div class="admin-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Utilisateurs</h4>
            <p class="text-secondary mb-0">Gestion des comptes étudiants</p>
        </div>
    </div>
    
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>ID</th>
                            <th>Utilisateur</th>
                            <th>Email</th>
                            <th>Niveau</th>
                            <th>XP</th>
                            <th>Plan</th>
                            <th>Statut</th>
                            <th>Inscription</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (($users['data'] ?? []) as $u): ?>
                        <tr>
                            <td><?= $u['id'] ?></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="<?= avatar($u['avatar'] ?? 'avatar1.png') ?>" alt="" class="rounded-circle" style="width:32px;height:32px">
                                    <span class="fw-semibold"><?= e(($u['first_name'] ?? '') . ' ' . ($u['last_name'] ?? '')) ?></span>
                                </div>
                            </td>
                            <td class="small"><?= e($u['email']) ?></td>
                            <td class="small"><?= e($u['level_name'] ?? '-') ?> <?= $u['filiere_name'] ? '- ' . e($u['filiere_name']) : '' ?></td>
                            <td><span class="badge bg-warning text-dark"><?= number_format($u['xp_total'] ?? 0) ?></span></td>
                            <td><span class="badge bg-primary bg-opacity-10 text-primary"><?= e($u['plan_name'] ?? 'Free') ?></span></td>
                            <td><span class="badge bg-<?= match($u['status']){'active'=>'success','suspended'=>'danger','pending'=>'warning',default=>'secondary'} ?>"><?= e($u['status']) ?></span></td>
                            <td class="small text-muted"><?= timeAgo($u['created_at']) ?></td>
                            <td>
                                <a href="<?= url('/admin/utilisateurs/' . $u['id']) ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <?php if (!empty($users['last_page']) && $users['last_page'] > 1): ?>
    <div class="mt-3">
        <?= pagination($users) ?>
    </div>
    <?php endif; ?>
</div>
<?php View::endSection(); ?>
