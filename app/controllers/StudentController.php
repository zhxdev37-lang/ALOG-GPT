<?php
/**
 * StudentController - Espace étudiant
 * Dashboard, profil, progression, classements
 */
class StudentController extends BaseController {
    
    public function __construct() {
        parent::__construct();
        $this->layout = 'dashboard';
    }
    
    public function dashboard(): void {
        $this->requireAuth();
        $userId = Auth::id();
        
        $progressModel = new LessonProgress();
        $subModel = new Subscription();
        $eventModel = new Event();
        $achievementModel = new Achievement();
        $rankingModel = new WeeklyRanking();
        
        $activeSub = $subModel->getActiveForUser($userId);
        $completedLessons = $progressModel->getCompletedCount($userId);
        $inProgressLessons = $progressModel->getInProgressCount($userId);
        
        // Recommended lessons based on level
        $recommended = [];
        if ($this->user['school_level_id']) {
            $recommended = Database::query(
                "SELECT l.*, s.name as subject_name FROM lessons l 
                 JOIN subjects s ON l.subject_id = s.id 
                 WHERE l.school_level_id = :lid AND l.is_active = 1 
                 AND l.id NOT IN (SELECT lesson_id FROM lesson_progress WHERE user_id = :uid AND completed_at IS NOT NULL)
                 ORDER BY l.sort_order LIMIT 5",
                [':lid' => $this->user['school_level_id'], ':uid' => $userId]
            );
        }
        
        $data = [
            'completedLessons' => $completedLessons,
            'inProgressLessons' => $inProgressLessons,
            'activeSubscription' => $activeSub,
            'upcomingEvents' => $eventModel->getUpcoming(3),
            'achievements' => $achievementModel->getForUser($userId),
            'weeklyRank' => $rankingModel->getForUser($userId, 1)[0] ?? null,
            'recommendedLessons' => $recommended,
            'xpToday' => (new XpTransaction())->getTodayTotal($userId),
            'seo' => generateSeoMeta(['title' => 'Tableau de Bord', 'description' => 'Votre espace personnel ALOG Academy.'])
        ];
        
        $this->view('student/dashboard', $data);
    }
    
    public function profile(): void {
        $this->requireAuth();
        
        $levels = (new SchoolLevel())->getWithFilieres();
        $regions = getRegions();
        $avatars = getAvatarOptions();
        
        $this->view('student/profile', [
            'levels' => $levels,
            'regions' => $regions,
            'avatars' => $avatars,
            'seo' => generateSeoMeta(['title' => 'Mon Profil', 'description' => 'Gérez votre profil ALOG Academy.'])
        ]);
    }
    
    public function updateProfile(): void {
        $this->requireAuth();
        $userId = Auth::id();
        
        $validator = new Validator($_POST);
        $validator->required('first_name', 'Prénom')->max('first_name', 100)
                  ->required('last_name', 'Nom')->max('last_name', 100)
                  ->required('region', 'Région')->in('region', getRegions())
                  ->required('avatar', 'Avatar')->in('avatar', array_keys(getAvatarOptions()));
        
        if ($validator->fails()) {
            Session::flash('errors', $validator->errors());
            Router::back();
        }
        
        $updateData = [
            'first_name' => Security::input('first_name'),
            'last_name' => Security::input('last_name'),
            'phone' => Security::input('phone'),
            'region' => Security::input('region'),
            'avatar' => Security::input('avatar'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        $newLevelId = Security::int('school_level_id');
        $newFiliereId = Security::int('filiere_id') ?: null;
        
        if ($newLevelId && $newLevelId != ($this->user['school_level_id'] ?? 0)) {
            // Check 3 months constraint
            $lastChange = $this->user['level_changed_at'] ?? null;
            if ($lastChange) {
                $last = new DateTime($lastChange);
                $now = new DateTime();
                if ($now->diff($last)->days < 90) {
                    Session::flash('errors', ['level' => 'Vous ne pouvez changer de niveau que tous les 3 mois.']);
                    Router::back();
                }
            }
            $updateData['school_level_id'] = $newLevelId;
            $updateData['filiere_id'] = $newFiliereId;
            $updateData['level_changed_at'] = date('Y-m-d H:i:s');
            
            // Reset lesson progress
            Database::execute("DELETE FROM lesson_progress WHERE user_id = :uid", [':uid' => $userId]);
        }
        
        (new User())->update($userId, $updateData);
        
        Session::flash('success', 'Profil mis à jour avec succès.');
        Router::redirect('/profil');
    }
    
    public function leaderboard(): void {
        $type = $_GET['type'] ?? 'global';
        $validTypes = ['global', 'weekly', 'regional', 'level'];
        
        if (!in_array($type, $validTypes)) {
            $type = 'global';
        }
        
        $leaders = (new User())->getLeaderboard($type, 50);
        
        if ($type === 'regional' && Auth::check()) {
            $userRegion = Auth::user()['region'] ?? '';
            $leaders = array_filter($leaders, fn($u) => ($u['region'] ?? '') === $userRegion);
        }
        
        if ($type === 'level' && Auth::check()) {
            $userLevel = Auth::user()['school_level_id'] ?? 0;
            $leaders = array_filter($leaders, fn($u) => ($u['school_level_id'] ?? 0) == $userLevel);
        }
        
        $this->view('student/leaderboard', [
            'leaders' => $leaders,
            'type' => $type,
            'seo' => generateSeoMeta(['title' => 'Classement - ' . ucfirst($type), 'description' => 'Découvrez le classement des meilleurs étudiants du Maroc sur ALOG Academy.'])
        ]);
    }
    
    public function achievements(): void {
        $this->requireAuth();
        $userId = Auth::id();
        
        $achievementModel = new Achievement();
        
        $this->view('student/achievements', [
            'earned' => $achievementModel->getForUser($userId),
            'available' => $achievementModel->getAvailableForUser($userId),
            'seo' => generateSeoMeta(['title' => 'Mes Badges', 'description' => 'Vos succès et badges sur ALOG Academy.'])
        ]);
    }
    
    public function subscriptions(): void {
        $this->requireAuth();
        $userId = Auth::id();
        
        $history = (new Subscription())->getUserHistory($userId);
        $active = (new Subscription())->getActiveForUser($userId);
        $plans = (new Plan())->getActive();
        
        $this->view('student/subscriptions', [
            'history' => $history,
            'active' => $active,
            'plans' => $plans,
            'seo' => generateSeoMeta(['title' => 'Mes Abonnements', 'description' => 'Gérez vos abonnements ALOG Academy.'])
        ]);
    }
    
    public function myLessons(): void {
        $this->requireAuth();
        $userId = Auth::id();
        
        $tab = $_GET['tab'] ?? 'in-progress';
        
        if ($tab === 'completed') {
            $lessons = Database::query(
                "SELECT l.*, s.name as subject_name, lp.completed_at, lp.xp_earned 
                 FROM lesson_progress lp 
                 JOIN lessons l ON lp.lesson_id = l.id 
                 JOIN subjects s ON l.subject_id = s.id 
                 WHERE lp.user_id = :uid AND lp.completed_at IS NOT NULL 
                 ORDER BY lp.completed_at DESC",
                [':uid' => $userId]
            );
        } else {
            $lessons = Database::query(
                "SELECT l.*, s.name as subject_name, lp.started_at, lp.video_watched_seconds, lp.video_completed 
                 FROM lesson_progress lp 
                 JOIN lessons l ON lp.lesson_id = l.id 
                 JOIN subjects s ON l.subject_id = s.id 
                 WHERE lp.user_id = :uid AND lp.completed_at IS NULL 
                 ORDER BY lp.started_at DESC",
                [':uid' => $userId]
            );
        }
        
        $this->view('student/my-lessons', [
            'lessons' => $lessons,
            'tab' => $tab,
            'seo' => generateSeoMeta(['title' => 'Mes Cours', 'description' => 'Suivez votre progression dans vos cours.'])
        ]);
    }
}
