<?php
/**
 * GuestMiddleware - Vérifie que l'utilisateur n'est pas connecté
 */
class GuestMiddleware {
    public function handle(): void {
        if (Auth::check()) {
            header('Location: ' . Router::url('/tableau-de-bord'));
            exit;
        }
    }
}
