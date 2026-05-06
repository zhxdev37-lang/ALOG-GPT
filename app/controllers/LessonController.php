<?php
/**
 * LessonController - Parcours des cours et leçons
 * Visionnage vidéo, lecteur PDF, progression
 */
class LessonController extends BaseController {
    
    public function subjects(): void {
        $this->requireAuth();
        $user = Auth::user();
        
        $subjects = (new Subject())->getForLevel(
            (int)$user['school_level_id'],
            $user['filiere_id'] ? (int)$user['filiere_id'] : null
        );
        
        $this->view('student/subjects', [
            'subjects' => $subjects,
            'seo' => generateSeoMeta(['title' => 'Matières', 'description' => 'Parcourez les matières disponibles pour votre niveau.'])
        ]);
    }
    
    public function lessons(string $subjectSlug): void {
        $this->requireAuth();
        $user = Auth::user();
        $subject = (new Subject())->findBy('slug', $subjectSlug);
        
        if (!$subject) {
            Router::redirect('/matieres');
        }
        
        $activeSub = (new Subscription())->getActiveForUser((int)$user['id']);
        $planId = $activeSub ? (int)$activeSub['plan_id'] : 1;
        
        $lessons = (new Lesson())->getForUser(
            (int)$user['id'],
            (int)$subject['id'],
            (int)$user['school_level_id'],
            $user['filiere_id'] ? (int)$user['filiere_id'] : null,
            $planId
        );
        
        $this->view('student/lessons', [
            'subject' => $subject,
            'lessons' => $lessons,
            'planId' => $planId,
            'seo' => generateSeoMeta([
                'title' => $subject['name'] . ' - Cours',
                'description' => 'Cours de ' . $subject['name'] . ' pour ' . ($user['level_name'] ?? 'votre niveau')
            ])
        ]);
    }
    
    public function show(string $slug): void {
        $this->requireAuth();
        $user = Auth::user();
        $lesson = (new Lesson())->getBySlug($slug);
        
        if (!$lesson) {
            http_response_code(404);
            $this->view('public/404');
            return;
        }
        
        // Check access
        $activeSub = (new Subscription())->getActiveForUser((int)$user['id']);
        $planId = $activeSub ? (int)$activeSub['plan_id'] : 1;
        
        $isLocked = (int)$lesson['plan_id'] > $planId;
        $isUnlockedByXp = false;
        
        if ($isLocked && $lesson['xp_unlock_cost']) {
            $isUnlockedByXp = (int)$user['xp_current'] >= (int)$lesson['xp_unlock_cost'];
        }
        
        if ($isLocked && !$isUnlockedByXp) {
            Session::flash('errors', ['global' => 'Cette leçon nécessite un abonnement supérieur ou des XP pour être débloquée.']);
            Router::redirect('/cours/' . (new Subject())->find($lesson['subject_id'])['slug']);
        }
        
        $progress = (new LessonProgress())->getForUser((int)$user['id'], (int)$lesson['id']);
        $quiz = (new Quiz())->getByLesson((int)$lesson['id']);
        
        $this->view('student/lesson-detail', [
            'lesson' => $lesson,
            'progress' => $progress,
            'quiz' => $quiz,
            'isLocked' => false,
            'seo' => generateSeoMeta([
                'title' => $lesson['title'],
                'description' => strip_tags($lesson['description'] ?? ''),
                'image' => $lesson['image_url']
            ])
        ]);
    }
    
    public function updateVideoProgress(): void {
        $this->requireAuth();
        
        $lessonId = Security::int('lesson_id');
        $seconds = Security::int('seconds');
        $completed = isset($_POST['completed']);
        
        if (!$lessonId) {
            $this->json(['error' => 'Leçon invalide']);
        }
        
        (new LessonProgress())->updateVideoProgress(Auth::id(), $lessonId, $seconds, $completed);
        
        $this->json(['success' => true, 'completed' => $completed]);
    }
    
    public function unlockWithXp(): void {
        $this->requireAuth();
        $userId = Auth::id();
        $lessonId = Security::int('lesson_id');
        
        $lesson = (new Lesson())->find($lessonId);
        if (!$lesson || !$lesson['xp_unlock_cost']) {
            Session::flash('errors', ['global' => 'Leçon non débloquable avec des XP.']);
            Router::back();
        }
        
        $user = (new User())->find($userId);
        if ((int)$user['xp_current'] < (int)$lesson['xp_unlock_cost']) {
            Session::flash('errors', ['global' => 'XP insuffisants pour débloquer cette leçon.']);
            Router::back();
        }
        
        Database::beginTransaction();
        
        // Deduct XP
        Database::execute(
            "UPDATE users SET xp_current = xp_current - :cost WHERE id = :uid",
            [':cost' => $lesson['xp_unlock_cost'], ':uid' => $userId]
        );
        
        (new XpTransaction())->createTransaction(
            $userId,
            -(int)$lesson['xp_unlock_cost'],
            'purchase',
            'Déblocage leçon: ' . $lesson['title'],
            $lessonId,
            'lesson_unlock'
        );
        
        Database::commit();
        
        Session::flash('success', 'Leçon débloquée avec succès !');
        Router::redirect('/lecon/' . $lesson['slug']);
    }
    
    public function search(): void {
        $this->requireAuth();
        $query = Security::input('q');
        $user = Auth::user();
        
        $results = [];
        if (strlen($query) >= 2) {
            $results = (new Lesson())->search($query, (int)$user['school_level_id']);
        }
        
        $this->view('student/search', [
            'query' => $query,
            'results' => $results,
            'seo' => generateSeoMeta(['title' => 'Recherche: ' . $query, 'description' => 'Résultats de recherche pour ' . $query])
        ]);
    }
}
