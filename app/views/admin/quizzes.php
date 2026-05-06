<?php View::section('content'); ?>
<div class="admin-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Quiz</h4>
            <p class="text-secondary mb-0">Gestion des quiz et questions</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createQuizModal">
            <i class="bi bi-plus-lg me-2"></i>Nouveau Quiz
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
                            <th>Lecon</th>
                            <th>Score</th>
                            <th>XP</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (($quizzes['data'] ?? []) as $q): ?>
                        <tr>
                            <td><?= $q['id'] ?></td>
                            <td class="fw-semibold"><?= e($q['title']) ?></td>
                            <td class="small"><?= e($q['lesson_title'] ?? '-') ?></td>
                            <td><?= $q['passing_score'] ?>%</td>
                            <td><span class="badge bg-warning text-dark">+<?= $q['xp_reward'] ?> XP</span></td>
                            <td><span class="badge bg-<?= ($q['is_active'] ? 'success' : 'secondary') ?>"><?= $q['is_active'] ? 'Actif' : 'Inactif' ?></span></td>
                            <td>
                                <a href="<?= url('/lecon/' . ($q['lesson_slug'] ?? '')) ?>" target="_blank" class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($quizzes['data'])): ?>
                        <tr><td colspan="7" class="text-center text-muted py-4">Aucun quiz</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <?= pagination($quizzes ?? []) ?>
</div>
<?php View::endSection(); ?>
