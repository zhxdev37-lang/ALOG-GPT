<?php
/**
 * View - Moteur de vues simple avec layout
 * Support sections, partials, et cache de rendu
 */
class View {
    private static string $layout = 'main';
    private static array $sections = [];
    private static string $currentSection = '';
    private static array $globals = [];
    
    public static function setGlobal(string $key, $value): void {
        self::$globals[$key] = $value;
    }
    
    public static function render(string $view, array $data = [], string $layout = null): void {
        extract(array_merge(self::$globals, $data));
        
        $viewFile = APP_PATH . '/views/' . $view . '.php';
        if (!file_exists($viewFile)) {
            throw new Exception("Vue introuvable: {$view}");
        }
        
        ob_start();
        require $viewFile;
        $content = ob_get_clean();
        
        if ($layout !== null) {
            self::$layout = $layout;
        }
        
        if (self::$layout) {
            $layoutFile = APP_PATH . '/views/layouts/' . self::$layout . '.php';
            if (file_exists($layoutFile)) {
                require $layoutFile;
            } else {
                echo $content;
            }
        } else {
            echo $content;
        }
        
        self::$sections = [];
        self::$currentSection = '';
    }
    
    public static function section(string $name): void {
        self::$currentSection = $name;
        ob_start();
    }
    
    public static function endSection(): void {
        if (self::$currentSection) {
            self::$sections[self::$currentSection] = ob_get_clean();
            self::$currentSection = '';
        }
    }
    
    public static function yield(string $name, string $default = ''): void {
        echo self::$sections[$name] ?? $default;
    }
    
    public static function partial(string $name, array $data = []): void {
        extract($data);
        $file = APP_PATH . '/views/partials/' . $name . '.php';
        if (file_exists($file)) {
            require $file;
        }
    }
    
    public static function json(array $data, int $status = 200): void {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
}
