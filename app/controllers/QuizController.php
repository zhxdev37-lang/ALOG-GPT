<?php
/**
 * QuizController - Passage des quiz et résultats
 * Détection vidéo, scoring, XP awards
 */
class QuizController extends BaseController {
    
    public function take(string $lessonSlug): void {
        $this->requireAuth();
        $userId = Auth::id();
        
        $lesson = (new Lesson())->getBySlug($lessonSlug);
        if (!$lesson) {
            Router::redirect('/matieres');
        }
        
        // Check video completion first
        $progress = (new LessonProgress())->getForUser($userId, (int)$lesson['id']);
        if (!$progress || !$progress['video_completed']) {
            Session::flash('errors', ['global' => 'Vous devez d\'abord regarder la vidéo complète avant de passer le quiz.']);
            Router::redirect('/lecon/' . $lessonSlug);
        }
        
        $quiz = (new Quiz())->getWithQuestions((int)$lesson['quiz_id']);
        if (!$quiz) {
            Session::flash('errors', ['global' => 'Quiz non disponible pour cette leçon.']);
            Router::redirect('/lecon/' . $lessonSlug);
        }
        
        $this->view('student/quiz', [
            'lesson' => $lesson,
            'quiz' => $quiz,
            'seo' => generateSeoMeta(['title' => 'Quiz - ' . $lesson['title'], 'description' => 'Testez vos connaissances sur ' . $lesson['title']])
        ]);
    }
    
    public function submit(): void {
        $this->requireAuth();
        $userId = Auth::id();
        
        $quizId = Security::int('quiz_id');
        $lessonId = Security::int('lesson_id');
        $answers = $_POST['answers'] ?? [];
        
        $quiz = (new Quiz())->getWithQuestions($quizId);
        if (!$quiz) {
            $this->json(['error' => 'Quiz invalide']);
        }
        
        $totalPoints = 0;
        $earnedPoints = 0;
        $results = [];
        
        foreach ($quiz['questions'] as $question) {
            $qid = $question['id'];
            $correct = $question['correct_answer'];
            $userAnswer = $answers[$qid] ?? '';
            $points = (int)$question['points'];
            
            $totalPoints += $points;
            $isCorrect = $userAnswer === $correct;
            
            if ($isCorrect) {
                $earnedPoints += $points;
            }
            
            // Store only answer, NOT correct answer
            $results[$qid] = [
                'user_answer' => $userAnswer,
                'points' => $isCorrect ? $points : 0
            ];
        }
        
        $percentage = $totalPoints > 0 ? round(($earnedPoints / $totalPoints) * 100) : 0;
        $passed = $percentage >= (int)$quiz['passing_score'];
        
        // Save attempt
        $attemptId = Database::execute(
            "INSERT INTO quiz_attempts (user_id, quiz_id, score, total_points, percentage, passed, answers, started_at, completed_at) 
             VALUES (:uid, :qid, :score, :total, :pct, :passed, :answers, NOW(), NOW())",
            [
                ':uid' => $userId,
                ':qid' => $quizId,
                ':score' => $earnedPoints,
                ':total' => $totalPoints,
                ':pct' => $percentage,
                ':passed' => $passed ? 1 : 0,
                ':answers' => json_encode($results)
            ]
        );
        
        // Update lesson progress
        (new LessonProgress())->completeQuiz($userId, $lessonId, $percentage, $passed);
        
        // Award XP if passed
        if ($passed) {
            $xpReward = (int)$quiz['xp_reward'];
            if ($xpReward > 0) {
                (new User())->addXp($userId, $xpReward);
                (new XpTransaction())->createTransaction(
                    $userId,
                    $xpReward,
                    'quiz',
                    'Quiz réussi: ' . $quiz['title'],
                    $quizId,
                    'quiz'
                );
                
                // Check quiz achievements
                $quizCount = Database::fetch(
                    "SELECT COUNT(*) as c FROM quiz_attempts WHERE user_id = :uid AND passed = 1",
                    [':uid' => $userId]
                )['c'] ?? 0;
                (new Achievement())->checkAndAward($userId, 'quizzes', (int)$quizCount);
            }
        }
        
        $this->json([
            'success' => true,
            'score' => $earnedPoints,
            'total' => $totalPoints,
            'percentage' => $percentage,
            'passed' => $passed,
            'xp_earned' => $passed ? ($quiz['xp_reward'] ?? 0) : 0
        ]);
    }
    
    public function result(int $attemptId): void {
        $this->requireAuth();
        $userId = Auth::id();
        
        $attempt = Database::fetch(
            "SELECT qa.*, q.title as quiz_title, l.title as lesson_title, l.slug as lesson_slug 
             FROM quiz_attempts qa 
             JOIN quizzes q ON qa.quiz_id = q.id 
             LEFT JOIN lessons l ON q.lesson_id = l.id 
             WHERE qa.id = :id AND qa.user_id = :uid LIMIT 1",
            [':id' => $attemptId, ':uid' => $userId]
        );
        
        if (!$attempt) {
            Router::redirect('/tableau-de-bord');
        }
        
        $this->view('student/quiz-result', [
            'attempt' => $attempt,
            'seo' => generateSeoMeta(['title' => 'Résultat du Quiz', 'description' => 'Votre résultat pour ' . ($attempt['quiz_title'] ?? 'Quiz')])
        ]);
    }
}
