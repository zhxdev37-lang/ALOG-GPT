<?php
/**
 * AuthMiddleware - Vérifie l'authentification
 */
class AuthMiddleware {
    public function handle(): void {
        if (!Auth::check()) {
            Session::flash('redirect_after_login', $_SERVER['REQUEST_URI']);
            header('Location: ' . Router::url('/connexion'));
            exit;
        }
    }
}
