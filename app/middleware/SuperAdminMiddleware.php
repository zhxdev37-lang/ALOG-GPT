<?php
/**
 * SuperAdminMiddleware - Super admin uniquement
 */
class SuperAdminMiddleware {
    public function handle(): void {
        if (!Auth::check() || !Auth::hasRole('super_admin')) {
            http_response_code(403);
            require APP_PATH . '/views/public/403.php';
            exit;
        }
    }
}
