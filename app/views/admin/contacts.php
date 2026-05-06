<?php View::section('content'); ?>
<div class="admin-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Messages de Contact</h4>
            <p class="text-secondary mb-0">Demandes et suggestions des visiteurs</p>
        </div>
    </div>
    
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Email</th>
                            <th>Sujet</th>
                            <th>Statut</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (($contacts['data'] ?? []) as $c): ?>
                        <tr>
                            <td><?= $c['id'] ?></td>
                            <td class="fw-semibold"><?= e($c['name']) ?></td>
                            <td class="small"><?= e($c['email']) ?></td>
                            <td class="small"><?= e($c['subject']) ?></td>
                            <td><span class="badge bg-<?= ($c['status'] === 'new' ? 'warning' : ($c['status'] === 'replied' ? 'success' : 'secondary')) ?>"><?= e($c['status']) ?></span></td>
                            <td class="small text-muted"><?= timeAgo($c['created_at']) ?></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#contactModal<?= $c['id'] ?>"><i class="bi bi-eye"></i></button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($contacts['data'])): ?>
                        <tr><td colspan="7" class="text-center text-muted py-4">Aucun message</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <?= pagination($contacts ?? []) ?>
</div>
<?php View::endSection(); ?>
