<?php
/**
 * AdminController - Panneau d'administration complet
 * RBAC, analytics, CRUD pour tous les modules
 */
class AdminController extends BaseController {
    
    public function __construct() {
        parent::__construct();
        $this->requirePermission('admin.access');
        $this->layout = 'admin';
    }
    
    public function dashboard(): void {
        $userModel = new User();
        $subModel = new Subscription();
        $contactModel = new Contact();
        $logModel = new AdminLog();
        
        $stats = [
            'users' => $userModel->getStats(),
            'revenue' => $subModel->getRevenueStats(),
            'contacts' => Database::fetch("SELECT COUNT(*) as c FROM contacts WHERE status = 'new'")['c'] ?? 0,
            'active_subs' => $subModel->count("status = 'active' AND expires_at > NOW()"),
            'total_lessons' => Database::fetch("SELECT COUNT(*) as c FROM lessons")['c'] ?? 0,
            'total_quizzes' => Database::fetch("SELECT COUNT(*) as c FROM quizzes")['c'] ?? 0,
            'total_posts' => Database::fetch("SELECT COUNT(*) as c FROM blog_posts")['c'] ?? 0,
        ];
        
        $recentUsers = $userModel->getStudents(1, 5)['data'] ?? [];
        $recentSubs = Database::query("SELECT s.*, u.first_name, u.last_name, p.name as plan_name FROM subscriptions s JOIN users u ON s.user_id = u.id JOIN plans p ON s.plan_id = p.id ORDER BY s.created_at DESC LIMIT 5");
        $recentLogs = $logModel->getRecent(10);
        $weeklyRevenue = Database::query(
            "SELECT DATE(created_at) as date, SUM(amount_paid) as revenue FROM subscriptions 
             WHERE payment_status = 'paid' AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) 
             GROUP BY DATE(created_at) ORDER BY date"
        );
        
        $this->view('admin/dashboard', [
            'stats' => $stats,
            'recentUsers' => $recentUsers,
            'recentSubs' => $recentSubs,
            'recentLogs' => $recentLogs,
            'weeklyRevenue' => $weeklyRevenue,
            'seo' => generateSeoMeta(['title' => 'Admin - Dashboard'])
        ]);
    }
    
    // ========== USERS ==========
    public function users(): void {
        $this->requirePermission('users.read');
        $page = max(1, Security::int('page', 'GET', 1));
        $users = (new User())->getStudents($page);
        $this->view('admin/users', ['users' => $users, 'seo' => generateSeoMeta(['title' => 'Admin - Utilisateurs'])]);
    }
    
    public function editUser(int $id): void {
        $this->requirePermission('users.update');
        $user = (new User())->find($id);
        if (!$user) Router::redirect('/admin/utilisateurs');
        
        $this->view('admin/user-edit', [
            'editUser' => $user,
            'roles' => (new Role())->all('level ASC'),
            'levels' => (new SchoolLevel())->getActive(),
            'plans' => (new Plan())->getActive(),
            'seo' => generateSeoMeta(['title' => 'Modifier Utilisateur'])
        ]);
    }
    
    public function updateUser(): void {
        $this->requirePermission('users.update');
        $id = Security::int('id');
        
        $old = (new User())->find($id);
        (new User())->update($id, [
            'first_name' => Security::input('first_name'),
            'last_name' => Security::input('last_name'),
            'email' => Security::email('email'),
            'phone' => Security::input('phone'),
            'role_id' => Security::int('role_id'),
            'school_level_id' => Security::int('school_level_id') ?: null,
            'filiere_id' => Security::int('filiere_id') ?: null,
            'plan_id' => Security::int('plan_id'),
            'status' => Security::input('status'),
            'xp_total' => Security::int('xp_total'),
            'xp_current' => Security::int('xp_current'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        
        $new = (new User())->find($id);
        $this->logAdmin('update', 'user', $id, $old, $new);
        
        Session::flash('success', 'Utilisateur mis à jour.');
        Router::redirect('/admin/utilisateurs');
    }
    
    // ========== SCHOOL LEVELS ==========
    public function levels(): void {
        $this->requirePermission('levels.read');
        $levels = (new SchoolLevel())->getWithFilieres();
        $this->view('admin/levels', ['levels' => $levels, 'seo' => generateSeoMeta(['title' => 'Admin - Niveaux'])]);
    }
    
    public function storeLevel(): void {
        $this->requirePermission('levels.create');
        (new SchoolLevel())->create([
            'name' => Security::input('name'),
            'slug' => slugify(Security::input('name')),
            'description' => Security::input('description'),
            'sort_order' => Security::int('sort_order')
        ]);
        Session::flash('success', 'Niveau créé.');
        Router::redirect('/admin/niveaux');
    }
    
    public function updateLevel(): void {
        $this->requirePermission('levels.update');
        $id = Security::int('id');
        (new SchoolLevel())->update($id, [
            'name' => Security::input('name'),
            'slug' => slugify(Security::input('name')),
            'description' => Security::input('description'),
            'sort_order' => Security::int('sort_order'),
            'is_active' => isset($_POST['is_active']) ? 1 : 0
        ]);
        Session::flash('success', 'Niveau mis à jour.');
        Router::redirect('/admin/niveaux');
    }
    
    // ========== FILIERES ==========
    public function storeFiliere(): void {
        $this->requirePermission('filieres.create');
        (new Filiere())->create([
            'school_level_id' => Security::int('school_level_id'),
            'name' => Security::input('name'),
            'slug' => slugify(Security::input('name')),
            'description' => Security::input('description'),
            'sort_order' => Security::int('sort_order')
        ]);
        Session::flash('success', 'Filière créée.');
        Router::redirect('/admin/niveaux');
    }
    
    // ========== SUBJECTS ==========
    public function subjects(): void {
        $this->requirePermission('subjects.read');
        $subjects = (new Subject())->getActive();
        $levels = (new SchoolLevel())->getWithFilieres();
        $this->view('admin/subjects', ['subjects' => $subjects, 'levels' => $levels, 'seo' => generateSeoMeta(['title' => 'Admin - Matières'])]);
    }
    
    public function storeSubject(): void {
        $this->requirePermission('subjects.create');
        $id = (new Subject())->create([
            'name' => Security::input('name'),
            'slug' => slugify(Security::input('name')),
            'description' => Security::input('description'),
            'icon' => Security::input('icon'),
            'color' => Security::input('color')
        ]);
        
        // Attach to levels/filieres
        $levelIds = $_POST['level_ids'] ?? [];
        foreach ($levelIds as $lid) {
            $filiereId = !empty($_POST['filiere_ids'][$lid]) ? (int)$_POST['filiere_ids'][$lid] : null;
            (new Subject())->attachToLevel($id, (int)$lid, $filiereId);
        }
        
        Session::flash('success', 'Matière créée.');
        Router::redirect('/admin/matieres');
    }
    
    // ========== LESSONS ==========
    public function lessons(): void {
        $this->requirePermission('lessons.read');
        $page = max(1, Security::int('page', 'GET', 1));
        $lessons = (new Lesson())->getAdminList($page);
        $subjects = (new Subject())->getActive();
        $levels = (new SchoolLevel())->getWithFilieres();
        $plans = (new Plan())->getActive();
        
        $this->view('admin/lessons', [
            'lessons' => $lessons,
            'subjects' => $subjects,
            'levels' => $levels,
            'plans' => $plans,
            'seo' => generateSeoMeta(['title' => 'Admin - Leçons'])
        ]);
    }
    
    public function storeLesson(): void {
        $this->requirePermission('lessons.create');
        $data = [
            'subject_id' => Security::int('subject_id'),
            'school_level_id' => Security::int('school_level_id'),
            'filiere_id' => Security::int('filiere_id') ?: null,
            'title' => Security::input('title'),
            'slug' => slugify(Security::input('title')),
            'description' => Security::input('description'),
            'youtube_url' => Security::input('youtube_url'),
            'youtube_duration' => Security::int('youtube_duration'),
            'pdf_course_url' => Security::input('pdf_course_url'),
            'pdf_exercises_url' => Security::input('pdf_exercises_url'),
            'image_url' => Security::input('image_url'),
            'xp_reward' => Security::int('xp_reward'),
            'xp_unlock_cost' => Security::int('xp_unlock_cost') ?: null,
            'plan_id' => Security::int('plan_id'),
            'sort_order' => Security::int('sort_order'),
            'is_active' => isset($_POST['is_active']) ? 1 : 0
        ];
        
        $id = (new Lesson())->create($data);
        $this->logAdmin('create', 'lesson', $id, [], $data);
        
        Session::flash('success', 'Leçon créée.');
        Router::redirect('/admin/lecons');
    }
    
    public function updateLesson(): void {
        $this->requirePermission('lessons.update');
        $id = Security::int('id');
        $old = (new Lesson())->find($id);
        
        $data = [
            'subject_id' => Security::int('subject_id'),
            'school_level_id' => Security::int('school_level_id'),
            'filiere_id' => Security::int('filiere_id') ?: null,
            'title' => Security::input('title'),
            'slug' => slugify(Security::input('title')),
            'description' => Security::input('description'),
            'youtube_url' => Security::input('youtube_url'),
            'youtube_duration' => Security::int('youtube_duration'),
            'pdf_course_url' => Security::input('pdf_course_url'),
            'pdf_exercises_url' => Security::input('pdf_exercises_url'),
            'image_url' => Security::input('image_url'),
            'xp_reward' => Security::int('xp_reward'),
            'xp_unlock_cost' => Security::int('xp_unlock_cost') ?: null,
            'plan_id' => Security::int('plan_id'),
            'sort_order' => Security::int('sort_order'),
            'is_active' => isset($_POST['is_active']) ? 1 : 0
        ];
        
        (new Lesson())->update($id, $data);
        $this->logAdmin('update', 'lesson', $id, $old ?? [], $data);
        
        Session::flash('success', 'Leçon mise à jour.');
        Router::redirect('/admin/lecons');
    }
    
    public function deleteLesson(): void {
        $this->requirePermission('lessons.delete');
        $id = Security::int('id');
        $old = (new Lesson())->find($id);
        (new Lesson())->delete($id);
        $this->logAdmin('delete', 'lesson', $id, $old ?? [], []);
        Session::flash('success', 'Leçon supprimée.');
        Router::redirect('/admin/lecons');
    }
    
    // ========== QUIZZES ==========
    public function quizzes(): void {
        $this->requirePermission('quizzes.read');
        $page = max(1, Security::int('page', 'GET', 1));
        $quizzes = (new Quiz())->getAdminList($page);
        $lessons = (new Lesson())->where('is_active', 1, '=', 'title ASC');
        
        $this->view('admin/quizzes', [
            'quizzes' => $quizzes,
            'lessons' => $lessons,
            'seo' => generateSeoMeta(['title' => 'Admin - Quiz'])
        ]);
    }
    
    public function storeQuiz(): void {
        $this->requirePermission('quizzes.create');
        $id = (new Quiz())->create([
            'lesson_id' => Security::int('lesson_id') ?: null,
            'title' => Security::input('title'),
            'description' => Security::input('description'),
            'passing_score' => Security::int('passing_score'),
            'xp_reward' => Security::int('xp_reward'),
            'time_limit_minutes' => Security::int('time_limit') ?: null,
            'is_active' => 1
        ]);
        
        // Save questions
        $this->saveQuestions($id);
        
        Session::flash('success', 'Quiz créé.');
        Router::redirect('/admin/quiz');
    }
    
    public function updateQuiz(): void {
        $this->requirePermission('quizzes.update');
        $id = Security::int('id');
        (new Quiz())->update($id, [
            'lesson_id' => Security::int('lesson_id') ?: null,
            'title' => Security::input('title'),
            'description' => Security::input('description'),
            'passing_score' => Security::int('passing_score'),
            'xp_reward' => Security::int('xp_reward'),
            'time_limit_minutes' => Security::int('time_limit') ?: null,
            'is_active' => isset($_POST['is_active']) ? 1 : 0
        ]);
        
        // Delete old questions and save new ones
        Database::execute("DELETE FROM questions WHERE quiz_id = :qid", [':qid' => $id]);
        $this->saveQuestions($id);
        
        Session::flash('success', 'Quiz mis à jour.');
        Router::redirect('/admin/quiz');
    }
    
    private function saveQuestions(int $quizId): void {
        $questions = $_POST['questions'] ?? [];
        foreach ($questions as $i => $q) {
            if (empty($q['text'])) continue;
            
            (new Question())->create([
                'quiz_id' => $quizId,
                'question_text' => $q['text'],
                'question_type' => $q['type'] ?? 'qcm',
                'options' => json_encode($q['options'] ?? []),
                'correct_answer' => $q['correct'] ?? '',
                'explanation' => $q['explanation'] ?? '',
                'points' => (int)($q['points'] ?? 1),
                'sort_order' => $i
            ]);
        }
    }
    
    // ========== PLANS ==========
    public function plans(): void {
        $this->requirePermission('plans.read');
        $plans = (new Plan())->all('sort_order ASC');
        $this->view('admin/plans', ['plans' => $plans, 'seo' => generateSeoMeta(['title' => 'Admin - Plans'])]);
    }
    
    public function storePlan(): void {
        $this->requirePermission('plans.create');
        (new Plan())->create([
            'name' => Security::input('name'),
            'slug' => slugify(Security::input('name')),
            'description' => Security::input('description'),
            'price_mad' => Security::input('price_mad'),
            'price_usd' => Security::input('price_usd') ?: null,
            'duration_days' => Security::int('duration_days'),
            'features' => json_encode($_POST['features'] ?? []),
            'lesson_access_type' => Security::input('lesson_access_type'),
            'max_lessons_per_day' => Security::int('max_lessons_per_day') ?: null,
            'support_level' => Security::input('support_level'),
            'color' => Security::input('color'),
            'sort_order' => Security::int('sort_order')
        ]);
        Session::flash('success', 'Plan créé.');
        Router::redirect('/admin/plans');
    }
    
    public function updatePlan(): void {
        $this->requirePermission('plans.update');
        $id = Security::int('id');
        (new Plan())->update($id, [
            'name' => Security::input('name'),
            'slug' => slugify(Security::input('name')),
            'description' => Security::input('description'),
            'price_mad' => Security::input('price_mad'),
            'price_usd' => Security::input('price_usd') ?: null,
            'duration_days' => Security::int('duration_days'),
            'features' => json_encode($_POST['features'] ?? []),
            'lesson_access_type' => Security::input('lesson_access_type'),
            'max_lessons_per_day' => Security::int('max_lessons_per_day') ?: null,
            'support_level' => Security::input('support_level'),
            'color' => Security::input('color'),
            'sort_order' => Security::int('sort_order'),
            'is_active' => isset($_POST['is_active']) ? 1 : 0
        ]);
        Session::flash('success', 'Plan mis à jour.');
        Router::redirect('/admin/plans');
    }
    
    // ========== SUBSCRIPTIONS ==========
    public function subscriptions(): void {
        $this->requirePermission('subscriptions.read');
        $page = max(1, Security::int('page', 'GET', 1));
        $subs = (new Subscription())->getAdminList($page);
        $this->view('admin/subscriptions', ['subscriptions' => $subs, 'seo' => generateSeoMeta(['title' => 'Admin - Abonnements'])]);
    }
    
    // ========== BLOG ==========
    public function blogPosts(): void {
        $this->requirePermission('blog.read');
        $page = max(1, Security::int('page', 'GET', 1));
        $posts = (new BlogPost())->getAdminList($page);
        $categories = (new BlogCategory())->getActive();
        $this->view('admin/blog-posts', ['posts' => $posts, 'categories' => $categories, 'seo' => generateSeoMeta(['title' => 'Admin - Blog'])]);
    }
    
    public function storeBlogPost(): void {
        $this->requirePermission('blog.create');
        $data = [
            'author_id' => Auth::id(),
            'category_id' => Security::int('category_id') ?: null,
            'title' => Security::input('title'),
            'slug' => slugify(Security::input('title')),
            'excerpt' => Security::input('excerpt'),
            'content' => Security::clean($_POST['content'] ?? ''),
            'featured_image' => Security::input('featured_image'),
            'meta_title' => Security::input('meta_title'),
            'meta_description' => Security::input('meta_description'),
            'meta_keywords' => Security::input('meta_keywords'),
            'og_image' => Security::input('og_image'),
            'status' => Security::input('status'),
            'published_at' => Security::input('status') === 'published' ? date('Y-m-d H:i:s') : null,
            'is_featured' => isset($_POST['is_featured']) ? 1 : 0
        ];
        
        $id = (new BlogPost())->create($data);
        
        // Tags
        $tagNames = array_filter(array_map('trim', explode(',', Security::input('tags'))));
        $tagModel = new BlogTag();
        $tagIds = [];
        foreach ($tagNames as $name) {
            $tagIds[] = $tagModel->findOrCreateByName($name);
        }
        $tagModel->syncForPost($id, $tagIds);
        
        $this->logAdmin('create', 'blog_post', $id, [], $data);
        Session::flash('success', 'Article créé.');
        Router::redirect('/admin/blog');
    }
    
    public function updateBlogPost(): void {
        $this->requirePermission('blog.update');
        $id = Security::int('id');
        $old = (new BlogPost())->find($id);
        
        $data = [
            'category_id' => Security::int('category_id') ?: null,
            'title' => Security::input('title'),
            'slug' => slugify(Security::input('title')),
            'excerpt' => Security::input('excerpt'),
            'content' => Security::clean($_POST['content'] ?? ''),
            'featured_image' => Security::input('featured_image'),
            'meta_title' => Security::input('meta_title'),
            'meta_description' => Security::input('meta_description'),
            'meta_keywords' => Security::input('meta_keywords'),
            'og_image' => Security::input('og_image'),
            'status' => Security::input('status'),
            'is_featured' => isset($_POST['is_featured']) ? 1 : 0
        ];
        
        if (Security::input('status') === 'published' && (!$old || !$old['published_at'])) {
            $data['published_at'] = date('Y-m-d H:i:s');
        }
        
        (new BlogPost())->update($id, $data);
        
        // Tags
        $tagNames = array_filter(array_map('trim', explode(',', Security::input('tags'))));
        $tagModel = new BlogTag();
        $tagIds = [];
        foreach ($tagNames as $name) {
            $tagIds[] = $tagModel->findOrCreateByName($name);
        }
        $tagModel->syncForPost($id, $tagIds);
        
        $this->logAdmin('update', 'blog_post', $id, $old ?? [], $data);
        Session::flash('success', 'Article mis à jour.');
        Router::redirect('/admin/blog');
    }
    
    // ========== EVENTS ==========
    public function events(): void {
        $this->requirePermission('events.read');
        $page = max(1, Security::int('page', 'GET', 1));
        $perPage = 20;
        $offset = ($page - 1) * $perPage;
        
        $events = Database::query("SELECT * FROM events ORDER BY event_date DESC LIMIT :limit OFFSET :offset", [':limit' => $perPage, ':offset' => $offset]);
        $count = Database::fetch("SELECT COUNT(*) as total FROM events")['total'] ?? 0;
        
        $this->view('admin/events', [
            'events' => $events,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => (int)$count,
                'last_page' => (int)ceil((int)$count / $perPage)
            ],
            'seo' => generateSeoMeta(['title' => 'Admin - Événements'])
        ]);
    }
    
    public function storeEvent(): void {
        $this->requirePermission('events.create');
        (new Event())->create([
            'title' => Security::input('title'),
            'slug' => slugify(Security::input('title')),
            'description' => Security::input('description'),
            'event_type' => Security::input('event_type'),
            'image_url' => Security::input('image_url'),
            'registration_url' => Security::input('registration_url'),
            'event_date' => Security::input('event_date'),
            'end_date' => Security::input('end_date') ?: null,
            'location' => Security::input('location'),
            'max_participants' => Security::int('max_participants') ?: null,
            'is_active' => 1
        ]);
        Session::flash('success', 'Événement créé.');
        Router::redirect('/admin/evenements');
    }
    
    // ========== CONTACTS ==========
    public function contacts(): void {
        $this->requirePermission('contacts.read');
        $page = max(1, Security::int('page', 'GET', 1));
        $contacts = (new Contact())->getAdminList($page);
        $this->view('admin/contacts', ['contacts' => $contacts, 'seo' => generateSeoMeta(['title' => 'Admin - Messages'])]);
    }
    
    public function updateContact(): void {
        $this->requirePermission('contacts.update');
        $id = Security::int('id');
        (new Contact())->update($id, ['status' => Security::input('status')]);
        Session::flash('success', 'Statut mis à jour.');
        Router::redirect('/admin/contacts');
    }
    
    // ========== SETTINGS ==========
    public function settings(): void {
        $this->requirePermission('settings.read');
        $groups = ['general', 'system', 'security', 'payment', 'seo', 'analytics', 'social'];
        $currentGroup = Security::input('group', 'GET') ?: 'general';
        
        $settings = (new Setting())->getByGroup($currentGroup);
        
        $this->view('admin/settings', [
            'settings' => $settings,
            'groups' => $groups,
            'currentGroup' => $currentGroup,
            'seo' => generateSeoMeta(['title' => 'Admin - Paramètres'])
        ]);
    }
    
    public function updateSettings(): void {
        $this->requirePermission('settings.update');
        $group = Security::input('group');
        
        foreach ($_POST['settings'] ?? [] as $key => $value) {
            Setting::set($key, $value);
        }
        
        Cache::flush();
        Session::flash('success', 'Paramètres enregistrés.');
        Router::redirect('/admin/parametres?group=' . $group);
    }
    
    // ========== LOGS ==========
    public function logs(): void {
        $this->requirePermission('admin.logs');
        $logs = (new AdminLog())->getRecent(100);
        $this->view('admin/logs', ['logs' => $logs, 'seo' => generateSeoMeta(['title' => 'Admin - Logs'])]);
    }
    
    // ========== ANALYTICS ==========
    public function analytics(): void {
        $this->requirePermission('analytics.read');
        
        $period = Security::input('period', 'GET') ?: '30';
        $interval = match($period) {
            '7' => '7 DAY',
            '30' => '30 DAY',
            '90' => '90 DAY',
            '365' => '1 YEAR',
            default => '30 DAY'
        };
        
        $stats = [
            'new_users' => Database::fetch("SELECT COUNT(*) as c FROM users WHERE role_id = 3 AND created_at >= DATE_SUB(NOW(), INTERVAL {$interval})")['c'] ?? 0,
            'active_users' => Database::fetch("SELECT COUNT(DISTINCT user_id) as c FROM lesson_progress WHERE updated_at >= DATE_SUB(NOW(), INTERVAL {$interval})")['c'] ?? 0,
            'completed_lessons' => Database::fetch("SELECT COUNT(*) as c FROM lesson_progress WHERE completed_at >= DATE_SUB(NOW(), INTERVAL {$interval})")['c'] ?? 0,
            'quiz_attempts' => Database::fetch("SELECT COUNT(*) as c FROM quiz_attempts WHERE completed_at >= DATE_SUB(NOW(), INTERVAL {$interval})")['c'] ?? 0,
        ];
        
        $daily = Database::query(
            "SELECT DATE(created_at) as date, COUNT(*) as count FROM users 
             WHERE role_id = 3 AND created_at >= DATE_SUB(NOW(), INTERVAL {$interval}) 
             GROUP BY DATE(created_at) ORDER BY date"
        );
        
        $this->view('admin/analytics', [
            'stats' => $stats,
            'daily' => $daily,
            'period' => $period,
            'seo' => generateSeoMeta(['title' => 'Admin - Analytics'])
        ]);
    }
}
