<?php View::section('content'); ?>
<div class="admin-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Blog</h4>
            <p class="text-secondary mb-0">Gestion des articles SEO</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createPostModal">
            <i class="bi bi-plus-lg me-2"></i>Nouvel Article
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
                            <th>Catégorie</th>
                            <th>Auteur</th>
                            <th>Statut</th>
                            <th>Vues</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (($posts['data'] ?? []) as $p): ?>
                        <tr>
                            <td><?= $p['id'] ?></td>
                            <td class="fw-semibold"><?= e($p['title']) ?></td>
                            <td class="small"><?= e($p['category_name'] ?? '-') ?></td>
                            <td class="small"><?= e(($p['first_name'] ?? '') . ' ' . ($p['last_name'] ?? '')) ?></td>
                            <td><span class="badge bg-<?= (['published'=>'success','draft'=>'warning','archived'=>'secondary'][$p['status']] ?? 'secondary') ?>"><?= e($p['status']) ?></span></td>
                            <td><?= number_format($p['views_count'] ?? 0) ?></td>
                            <td class="small text-muted"><?= timeAgo($p['created_at']) ?></td>
                            <td>
                                <a href="<?= url('/blog/' . $p['slug']) ?>" target="_blank" class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <?php if (!empty($posts['last_page']) && $posts['last_page'] > 1): ?>
    <div class="mt-3">
        <?= pagination($posts) ?>
    </div>
    <?php endif; ?>
</div>

<!-- Create Modal -->
<div class="modal fade" id="createPostModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nouvel Article</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= url('/admin/blog/creer') ?>" method="POST">
                <?= $csrf ?>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Titre *</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Catégorie</label>
                            <select name="category_id" class="form-select">
                                <option value="">Aucune</option>
                                <?php foreach ($categories as $c): ?>
                                <option value="<?= $c['id'] ?>"><?= e($c['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Extrait</label>
                            <textarea name="excerpt" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Contenu *</label>
                            <textarea name="content" class="form-control" rows="8" required></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Image mise en avant URL</label>
                            <input type="url" name="featured_image" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tags (séparés par virgules)</label>
                            <input type="text" name="tags" class="form-control" placeholder="conseils, bac, etude">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Meta Title</label>
                            <input type="text" name="meta_title" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Meta Description</label>
                            <input type="text" name="meta_description" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">OG Image URL</label>
                            <input type="url" name="og_image" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Statut</label>
                            <select name="status" class="form-select">
                                <option value="draft">Brouillon</option>
                                <option value="published">Publié</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input type="checkbox" name="is_featured" class="form-check-input" id="featured">
                                <label class="form-check-label" for="featured">Article en vedette</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Publier</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php View::endSection(); ?>
