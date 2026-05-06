<?php View::section('content'); ?>
<div class="admin-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Logs Admin</h4>
            <p class="text-secondary mb-0">Journal des actions administratives</p>
        </div>
    </div>
    
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Date</th>
                            <th>Utilisateur</th>
                            <th>Action</th>
                            <th>Entite</th>
                            <th>ID</th>
                            <th>IP</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($logs as $log): ?>
                        <tr>
                            <td class="small text-muted"><?= formatDatetime($log['created_at']) ?></td>
                            <td class="small"><?= e(($log['first_name'] ?? '') . ' ' . ($log['last_name'] ?? '')) ?></td>
                            <td><span class="badge bg-<?= ($log['action'] === 'create' ? 'success' : ($log['action'] === 'update' ? 'primary' : ($log['action'] === 'delete' ? 'danger' : 'secondary'))) ?>"><?= e($log['action']) ?></span></td>
                            <td class="small"><?= e($log['entity_type']) ?></td>
                            <td><?= $log['entity_id'] ?></td>
                            <td class="small text-muted"><?= e($log['ip_address'] ?? '-') ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($logs)): ?>
                        <tr><td colspan="6" class="text-center text-muted py-4">Aucun log</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php View::endSection(); ?>
