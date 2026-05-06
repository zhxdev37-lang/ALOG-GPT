<?php View::section('content'); ?>
<div class="admin-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Evenements</h4>
            <p class="text-secondary mb-0">Gestion des evenements et webinaires</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createEventModal">
            <i class="bi bi-plus-lg me-2"></i>Nouvel Evenement
        </button>
    </div>
    
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>ID</th>
                            <th>Titre</th>
                            <th>Type</th>
                            <th>Date</th>
                            <th>Lieu</th>
                            <th>Participants</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($events as $e): ?>
                        <tr>
                            <td><?= $e['id'] ?></td>
                            <td class="fw-semibold"><?= e($e['title']) ?></td>
                            <td class="small"><?= e($e['event_type'] ?? '-') ?></td>
                            <td class="small"><?= formatDate($e['event_date']) ?></td>
                            <td class="small"><?= e($e['location'] ?? '-') ?></td>
                            <td><?= ($e['current_participants'] ?? 0) ?> / <?= ($e['max_participants'] ?? 'Infini') ?></td>
                            <td>
                                <a href="<?= url('/evenement/' . $e['slug']) ?>" target="_blank" class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($events)): ?>
                        <tr><td colspan="7" class="text-center text-muted py-4">Aucun evenement</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <?= pagination($pagination ?? []) ?>
</div>
<?php View::endSection(); ?>
