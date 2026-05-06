<?php
/**
 * CsrfMiddleware - Protection CSRF automatique sur POST
 */
class CsrfMiddleware {
    public function handle(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST[CSRF_TOKEN_NAME] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
            if (!Security::validateToken($token)) {
                http_response_code(403);
                if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
                    header('Content-Type: application/json');
                    echo json_encode(['error' => 'Token CSRF invalide']);
                    exit;
                }
                die('Accès refusé : Token CSRF invalide');
            }
        }
    }
}
