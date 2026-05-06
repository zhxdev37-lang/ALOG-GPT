<?php
/**
 * PublicController - Pages publiques du site
 * SEO optimisé, cache-friendly
 */
class PublicController extends BaseController {
    
    public function home(): void {
        $cacheKey = 'homepage_' . date('YmdH');
        $data = Cache::remember($cacheKey, function() {
            $userModel = new User();
            $blogModel = new BlogPost();
            $eventModel = new Event();
            $planModel = new Plan();
            
            return [
                'topStudents' => $userModel->getLeaderboard('global', 5),
                'weeklyLeaders' => $userModel->getLeaderboard('weekly', 5),
                'recentPosts' => $blogModel->getRecent(3),
                'upcomingEvents' => $eventModel->getUpcoming(3),
                'plans' => $planModel->getActive(),
                'stats' => [
                    'students' => Database::fetch("SELECT COUNT(*) as c FROM users WHERE role_id = 3")['c'] ?? 0,
                    'lessons' => Database::fetch("SELECT COUNT(*) as c FROM lessons WHERE is_active = 1")['c'] ?? 0,
                    'quizzes' => Database::fetch("SELECT COUNT(*) as c FROM quizzes WHERE is_active = 1")['c'] ?? 0
                ]
            ];
        }, 3600);
        
        $seo = generateSeoMeta([
            'title' => 'Accueil - Rejoignez les meilleurs étudiants du Maroc',
            'description' => 'ALOG Academy est la plateforme éducative premium pour les étudiants marocains. Cours, quiz, classements et préparation aux examens du Maroc.',
            'image' => asset('images/og-home.jpg')
        ]);
        
        $this->view('public/home', array_merge($data, ['seo' => $seo]));
    }
    
    public function about(): void {
        $seo = generateSeoMeta([
            'title' => 'À Propos',
            'description' => 'Découvrez ALOG Academy, la plateforme éducative qui révolutionne l\'apprentissage au Maroc.'
        ]);
        $this->view('public/about', ['seo' => $seo]);
    }
    
    public function services(): void {
        $seo = generateSeoMeta([
            'title' => 'Nos Services',
            'description' => 'Cours vidéo, quiz interactifs, classements et coaching pour exceller dans vos études.'
        ]);
        $this->view('public/services', ['seo' => $seo]);
    }
    
    public function pricing(): void {
        $plans = (new Plan())->getActive();
        $seo = generateSeoMeta([
            'title' => 'Tarifs',
            'description' => 'Comparez nos plans Gratuit, Pro et Ultra. Commencez gratuitement et passez à la vitesse supérieure.'
        ]);
        $this->view('public/pricing', ['plans' => $plans, 'seo' => $seo]);
    }
    
    public function faq(): void {
        $faqs = (new FAQ())->getActive();
        $seo = generateSeoMeta([
            'title' => 'FAQ',
            'description' => 'Trouvez les réponses à vos questions sur ALOG Academy.'
        ]);
        $this->view('public/faq', ['faqs' => $faqs, 'seo' => $seo]);
    }
    
    public function contact(): void {
        $seo = generateSeoMeta([
            'title' => 'Contact',
            'description' => 'Contactez l\'équipe ALOG Academy pour toute question ou suggestion.'
        ]);
        $this->view('public/contact', ['seo' => $seo]);
    }
    
    public function submitContact(): void {
        $validator = new Validator($_POST);
        $validator->required('name', 'Nom')->required('email', 'Email')->email('email')->required('subject', 'Sujet')->required('message', 'Message')->max('message', 2000);
        
        if ($validator->fails()) {
            Session::flash('errors', $validator->errors());
            Router::back();
        }
        
        (new Contact())->create([
            'name' => Security::input('name'),
            'email' => Security::email('email'),
            'phone' => Security::input('phone'),
            'subject' => Security::input('subject'),
            'message' => Security::input('message'),
            'status' => 'new'
        ]);
        
        Session::flash('success', 'Votre message a été envoyé avec succès. Nous vous répondrons rapidement.');
        Router::redirect('/contact');
    }
    
    public function sitemap(): void {
        header('Content-Type: application/xml');
        
        $urls = [
            ['loc' => APP_URL, 'priority' => '1.0'],
            ['loc' => APP_URL . '/a-propos', 'priority' => '0.8'],
            ['loc' => APP_URL . '/services', 'priority' => '0.8'],
            ['loc' => APP_URL . '/tarifs', 'priority' => '0.9'],
            ['loc' => APP_URL . '/blog', 'priority' => '0.9'],
            ['loc' => APP_URL . '/faq', 'priority' => '0.7'],
            ['loc' => APP_URL . '/contact', 'priority' => '0.7'],
            ['loc' => APP_URL . '/evenements', 'priority' => '0.8'],
        ];
        
        // Blog posts
        $posts = (new BlogPost())->getPublished(1, 1000)['data'] ?? [];
        foreach ($posts as $post) {
            $urls[] = ['loc' => APP_URL . '/blog/' . $post['slug'], 'priority' => '0.6'];
        }
        
        // Lessons
        $lessons = (new Lesson())->where('is_active', 1, '=', 'updated_at DESC');
        foreach ($lessons as $lesson) {
            $urls[] = ['loc' => APP_URL . '/cours/' . $lesson['slug'], 'priority' => '0.6'];
        }
        
        echo '<?xml version="1.0" encoding="UTF-8"?>';
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        foreach ($urls as $url) {
            echo '<url><loc>' . e($url['loc']) . '</loc><priority>' . $url['priority'] . '</priority></url>';
        }
        echo '</urlset>';
        exit;
    }
    
    public function robots(): void {
        header('Content-Type: text/plain');
        echo "User-agent: *\n";
        echo "Allow: /\n";
        echo "Disallow: /admin/\n";
        echo "Disallow: /tableau-de-bord/\n";
        echo "Disallow: /connexion\n";
        echo "Disallow: /inscription\n";
        echo "Sitemap: " . APP_URL . "/sitemap.xml\n";
        exit;
    }
    
    public function manifest(): void {
        header('Content-Type: application/json');
        echo json_encode([
            'name' => 'ALOG Academy',
            'short_name' => 'ALOG',
            'description' => 'Plateforme éducative pour étudiants marocains',
            'start_url' => '/',
            'display' => 'standalone',
            'background_color' => '#ffffff',
            'theme_color' => '#0975e4',
            'orientation' => 'portrait',
            'icons' => [
                ['src' => asset('images/icon-72x72.png'), 'sizes' => '72x72', 'type' => 'image/png'],
                ['src' => asset('images/icon-96x96.png'), 'sizes' => '96x96', 'type' => 'image/png'],
                ['src' => asset('images/icon-128x128.png'), 'sizes' => '128x128', 'type' => 'image/png'],
                ['src' => asset('images/icon-144x144.png'), 'sizes' => '144x144', 'type' => 'image/png'],
                ['src' => asset('images/icon-192x192.png'), 'sizes' => '192x192', 'type' => 'image/png'],
                ['src' => asset('images/icon-512x512.png'), 'sizes' => '512x512', 'type' => 'image/png']
            ],
            'shortcuts' => [
                ['name' => 'Cours', 'short_name' => 'Cours', 'url' => '/cours', 'icons' => [['src' => asset('images/icon-96x96.png'), 'sizes' => '96x96']]],
                ['name' => 'Classement', 'short_name' => 'Classement', 'url' => '/classement', 'icons' => [['src' => asset('images/icon-96x96.png'), 'sizes' => '96x96']]]
            ]
        ]);
        exit;
    }
    
    public function notFound(): void {
        http_response_code(404);
        $this->view('public/404');
    }
}
