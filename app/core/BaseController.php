<?php
/**
 * BaseController - Contrôleur de base
 * Injection de services communs
 */
class BaseController {
    protected array $data = [];
    protected ?array $user = null;
    protected string $layout = 'main';
    
    public function __construct() {
        $this->user = Auth::user();
        $this->data['user'] = $this->user;
        $this->data['csrf'] = Security::tokenField();
        $this->data['app_url'] = APP_URL;
        $this->data['notifications'] = Session::flash('notifications') ?? [];
        $this->data['errors'] = Session::flash('errors') ?? [];
        $this->data['success'] = Session::flash('success') ?? '';
    }
    
    protected function view(string $view, array $data = []): void {
        $merged = array_merge($this->data, $data);
        View::render($view, $merged, $this->layout);
    }
    
    protected function json(array $data, int $status = 200): void {
        View::json($data, $status);
    }
    
    protected function redirect(string $path): void {
        Router::redirect($path);
    }
    
    protected function back(): void {
        Router::back();
    }
    
    protected function setFlash(string $key, $value): void {
        Session::flash($key, $value);
    }
    
    protected function validateCSRF(): bool {
        $token = $_POST[CSRF_TOKEN_NAME] ?? '';
        if (!Security::validateToken($token)) {
            http_response_code(403);
            $this->json(['error' => 'Token CSRF invalide.']);
            return false;
        }
        return true;
    }
    
    protected function requireAuth(): void {
        if (!Auth::check()) {
            Session::flash('redirect_after_login', $_SERVER['REQUEST_URI']);
            Router::redirect('/connexion');
        }
    }
    
    protected function requireGuest(): void {
        if (Auth::check()) {
            Router::redirect('/tableau-de-bord');
        }
    }
    
    protected function requireRole(string $role): void {
        $this->requireAuth();
        if (!Auth::hasRole($role)) {
            http_response_code(403);
            $this->view('public/403', ['message' => 'Accès interdit.']);
            exit;
        }
    }
    
    protected function requirePermission(string $permission): void {
        $this->requireAuth();
        if (!Auth::can($permission)) {
            http_response_code(403);
            $this->view('public/403', ['message' => 'Permission insuffisante.']);
            exit;
        }
    }
    
    protected function logAdmin(string $action, string $entityType, ?int $entityId = null, array $old = [], array $new = []): void {
        if (!$this->user) return;
        
        Database::execute(
            "INSERT INTO admin_logs (user_id, action, entity_type, entity_id, old_values, new_values, ip_address, user_agent) 
             VALUES (:uid, :action, :entity, :eid, :old, :new, :ip, :ua)",
            [
                ':uid' => $this->user['id'],
                ':action' => $action,
                ':entity' => $entityType,
                ':eid' => $entityId,
                ':old' => json_encode($old),
                ':new' => json_encode($new),
                ':ip' => Security::getIp(),
                ':ua' => Security::getDevice()
            ]
        );
    }
}
