<?php
/**
 * Router - Système de routage léger
 * URLs propres SEO-friendly
 */
class Router {
    private array $routes = [];
    private array $params = [];
    private string $currentRoute = '';
    
    public function add(string $route, string $controller, string $action = 'index', array $middleware = []): void {
        $route = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $route);
        $route = '#^' . $route . '$#';
        $this->routes[$route] = [
            'controller' => $controller,
            'action' => $action,
            'middleware' => $middleware
        ];
    }
    
    public function dispatch(string $url): void {
        $url = parse_url($url, PHP_URL_PATH);
        $url = trim($url, '/');
        
        // Supprimer le sous-dossier si existant
        $scriptName = dirname($_SERVER['SCRIPT_NAME']);
        if ($scriptName !== '/' && $scriptName !== '\\') {
            $url = str_replace(trim($scriptName, '/'), '', $url);
            $url = trim($url, '/');
        }
        
        $this->currentRoute = $url ?: 'home';
        
        foreach ($this->routes as $route => $info) {
            if (preg_match($route, $url, $matches)) {
                foreach ($matches as $key => $value) {
                    if (is_string($key)) {
                        $this->params[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
                    }
                }
                
                // Exécuter middleware
                foreach ($info['middleware'] as $middleware) {
                    $this->runMiddleware($middleware);
                }
                
                $this->executeController($info['controller'], $info['action']);
                return;
            }
        }
        
        $this->handle404();
    }
    
    private function runMiddleware(string $middleware): void {
        $file = APP_PATH . '/middleware/' . ucfirst($middleware) . 'Middleware.php';
        if (file_exists($file)) {
            require_once $file;
            $class = ucfirst($middleware) . 'Middleware';
            $instance = new $class();
            $instance->handle();
        }
    }
    
    private function executeController(string $controller, string $action): void {
        $file = APP_PATH . '/controllers/' . $controller . '.php';
        if (!file_exists($file)) {
            $this->handle404();
            return;
        }
        
        require_once $file;
        $instance = new $controller();
        
        if (!method_exists($instance, $action)) {
            $this->handle404();
            return;
        }
        
        call_user_func_array([$instance, $action], $this->params);
    }
    
    private function handle404(): void {
        http_response_code(404);
        require APP_PATH . '/views/public/404.php';
        exit;
    }
    
    public function getParam(string $key, string $default = ''): string {
        return $this->params[$key] ?? $default;
    }
    
    public static function url(string $path = ''): string {
        return APP_URL . '/' . ltrim($path, '/');
    }
    
    public static function redirect(string $path): void {
        header('Location: ' . self::url($path));
        exit;
    }
    
    public static function back(): void {
        $referer = $_SERVER['HTTP_REFERER'] ?? APP_URL;
        header('Location: ' . $referer);
        exit;
    }
}
