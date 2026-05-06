<?php View::section('content'); ?>
<div class="dashboard-content">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="<?= url('/matieres') ?>">Matières</a></li>
            <li class="breadcrumb-item"><a href="<?= url('/cours/' . ($lesson['subject_slug'] ?? '')) ?>"><?= e($lesson['subject_name'] ?? '') ?></a></li>
            <li class="breadcrumb-item active">Quiz</li>
        </ol>
    </nav>
    
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h4 class="fw-bold"><?= e($quiz['title'] ?? 'Quiz') ?></h4>
                    <p class="text-secondary mb-0"><?= e($lesson['title'] ?? '') ?></p>
                    <div class="d-flex gap-2 mt-2">
                        <span class="badge bg-primary bg-opacity-10 text-primary">Score minimum : <?= $quiz['passing_score'] ?? 60 ?>%</span>
                        <span class="badge bg-warning bg-opacity-10 text-warning">+<?= $quiz['xp_reward'] ?? 20 ?> XP</span>
                    </div>
                </div>
                
                <div class="card-body p-4">
                    <form id="quiz-form" action="<?= url('/quiz/soumettre') ?>" method="POST">
                        <?= $csrf ?>
                        <input type="hidden" name="quiz_id" value="<?= $quiz['id'] ?>">
                        <input type="hidden" name="lesson_id" value="<?= $lesson['id'] ?>">
                        
                        <?php foreach ($quiz['questions'] ?? [] as $i => $question): ?>
                        <div class="quiz-question mb-4 p-4 rounded-4 bg-light">
                            <h6 class="fw-semibold mb-3"><?= $i + 1 ?>. <?= e($question['question_text']) ?></h6>
                            
                            <?php if ($question['question_type'] === 'true_false'): ?>
                            <div class="d-flex gap-3">
                                <label class="quiz-option flex-grow-1">
                                    <input type="radio" name="answers[<?= $question['id'] ?>]" value="true" class="d-none" required>
                                    <div class="option-box p-3 rounded-3 border bg-white text-center">Vrai</div>
                                </label>
                                <label class="quiz-option flex-grow-1">
                                    <input type="radio" name="answers[<?= $question['id'] ?>]" value="false" class="d-none" required>
                                    <div class="option-box p-3 rounded-3 border bg-white text-center">Faux</div>
                                </label>
                            </div>
                            <?php else: ?>
                            <div class="d-flex flex-column gap-2">
                                <?php foreach (json_decode($question['options'] ?? '[]', true) as $opt): ?>
                                <label class="quiz-option">
                                    <input type="radio" name="answers[<?= $question['id'] ?>]" value="<?= e($opt) ?>" class="d-none" required>
                                    <div class="option-box p-3 rounded-3 border bg-white"><?= e($opt) ?></div>
                                </label>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                        
                        <button type="submit" class="btn btn-primary w-100 py-3">
                            <i class="bi bi-check-circle me-2"></i>Soumettre le Quiz
                        </button>
                    </form>
                    
                    <div id="quiz-result" class="d-none">
                        <div class="text-center py-4">
                            <div id="result-icon" class="display-1 mb-3"></div>
                            <h4 id="result-title" class="fw-bold"></h4>
                            <p id="result-score" class="lead"></p>
                            <p id="result-xp" class="text-muted"></p>
                            <a href="<?= url('/lecon/' . ($lesson['slug'] ?? '')) ?>" class="btn btn-primary mt-3">
                                <i class="bi bi-arrow-left me-2"></i>Retour à la leçon
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.quiz-option input:checked + .option-box { border-color: #0975e4 !important; background: rgba(9,117,228,0.05) !important; }
.quiz-option .option-box { cursor: pointer; transition: all 0.2s; }
.quiz-option .option-box:hover { border-color: #0975e4; }
</style>

<script>
document.getElementById('quiz-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const form = this;
    const resultDiv = document.getElementById('quiz-result');
    const formData = new FormData(form);
    
    fetch(form.action, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: new URLSearchParams(formData)
    })
    .then(r => r.json())
    .then(data => {
        form.classList.add('d-none');
        resultDiv.classList.remove('d-none');
        
        if (data.passed) {
            document.getElementById('result-icon').innerHTML = '<i class="bi bi-trophy-fill text-warning"></i>';
            document.getElementById('result-title').textContent = 'Félicitations !';
            document.getElementById('result-title').className = 'fw-bold text-success';
            document.getElementById('result-xp').innerHTML = '<span class="badge bg-warning text-dark">+' + data.xp_earned + ' XP gagnés</span>';
        } else {
            document.getElementById('result-icon').innerHTML = '<i class="bi bi-x-circle-fill text-danger"></i>';
            document.getElementById('result-title').textContent = 'Quiz non réussi';
            document.getElementById('result-title').className = 'fw-bold text-danger';
            document.getElementById('result-xp').textContent = 'N\'hésitez pas à revoir la leçon et réessayer.';
        }
        document.getElementById('result-score').textContent = 'Score : ' + data.score + '/' + data.total + ' (' + data.percentage + '%)';
    })
    .catch(() => alert('Erreur lors de la soumission. Veuillez réessayer.'));
});
</script>
<?php View::endSection(); ?>
