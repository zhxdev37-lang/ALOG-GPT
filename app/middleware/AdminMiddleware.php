<?php
/**
 * AdminMiddleware - Vérifie les droits d'administration
 */
class AdminMiddleware {
    public function handle(): void {
        if (!Auth::check()) {
            Session::flash('redirect_after_login', $_SERVER['REQUEST_URI']);
            header('Location: ' . Router::url('/connexion'));
            exit;
        }
        
        $user = Auth::user();
        $allowed = ['super_admin', 'content_manager', 'blogger', 'moderator', 'support'];
        
        if (!in_array($user['role']['slug'] ?? '', $allowed)) {
            http_response_code(403);
            require APP_PATH . '/views/public/403.php';
            exit;
        }
    }
}
