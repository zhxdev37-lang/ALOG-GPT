<?php
/**
 * EventController - Événements et inscriptions
 */
class EventController extends BaseController {
    
    public function index(): void {
        $page = max(1, Security::int('page', 'GET', 1));
        $perPage = 12;
        $offset = ($page - 1) * $perPage;
        
        $events = Database::query(
            "SELECT * FROM events WHERE is_active = 1 ORDER BY event_date ASC LIMIT :limit OFFSET :offset",
            [':limit' => $perPage, ':offset' => $offset]
        );
        
        $count = Database::fetch("SELECT COUNT(*) as total FROM events WHERE is_active = 1")['total'] ?? 0;
        
        $seo = generateSeoMeta([
            'title' => 'Événements',
            'description' => 'Découvrez nos événements, examens blancs et webinaires pour étudiants marocains.'
        ]);
        
        $this->view('events/index', [
            'events' => $events,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => (int)$count,
                'last_page' => (int)ceil((int)$count / $perPage)
            ],
            'seo' => $seo
        ]);
    }
    
    public function show(string $slug): void {
        $event = (new Event())->getBySlug($slug);
        
        if (!$event) {
            http_response_code(404);
            $this->view('public/404');
            return;
        }
        
        $isRegistered = false;
        if (Auth::check()) {
            $isRegistered = (new Event())->isUserRegistered((int)$event['id'], Auth::id());
        }
        
        $seo = generateSeoMeta([
            'title' => $event['title'],
            'description' => strip_tags($event['description'] ?? ''),
            'image' => $event['image_url']
        ]);
        
        $this->view('events/show', [
            'event' => $event,
            'isRegistered' => $isRegistered,
            'seo' => $seo
        ]);
    }
    
    public function register(): void {
        $this->requireAuth();
        $userId = Auth::id();
        $eventId = Security::int('event_id');
        
        $event = (new Event())->find($eventId);
        if (!$event) {
            Session::flash('errors', ['global' => 'Événement introuvable.']);
            Router::back();
        }
        
        if ($event['max_participants'] && (int)$event['current_participants'] >= (int)$event['max_participants']) {
            Session::flash('errors', ['global' => 'Cet événement est complet.']);
            Router::back();
        }
        
        $result = (new Event())->registerUser($eventId, $userId);
        
        if ($result) {
            Session::flash('success', 'Inscription confirmée ! Vous recevrez les détails par email.');
        } else {
            Session::flash('errors', ['global' => 'Vous êtes déjà inscrit à cet événement.']);
        }
        
        Router::back();
    }
}
