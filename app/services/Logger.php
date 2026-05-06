<?php
/**
 * Logger - Journalisation légère
 * Fichiers quotidiens pour shared hosting
 */
class Logger {
    private static string $logPath = STORAGE_PATH . '/logs';
    
    public static function info(string $message, array $context = []): void {
        self::write('INFO', $message, $context);
    }
    
    public static function error(string $message, array $context = []): void {
        self::write('ERROR', $message, $context);
    }
    
    public static function warning(string $message, array $context = []): void {
        self::write('WARNING', $message, $context);
    }
    
    public static function loginAttempt(string $email, bool $success): void {
        self::write('AUTH', ($success ? 'SUCCESS' : 'FAIL') . ' login attempt', [
            'email' => $email,
            'ip' => Security::getIp(),
            'user_agent' => Security::getDevice()
        ]);
    }
    
    private static function write(string $level, string $message, array $context = []): void {
        if (!is_dir(self::$logPath)) {
            mkdir(self::$logPath, 0755, true);
        }
        
        $file = self::$logPath . '/' . date('Y-m-d') . '.log';
        $line = '[' . date('Y-m-d H:i:s') . '] [' . $level . '] ' . $message;
        
        if (!empty($context)) {
            $line .= ' | ' . json_encode($context, JSON_UNESCAPED_UNICODE);
        }
        
        $line .= PHP_EOL;
        
        file_put_contents($file, $line, FILE_APPEND | LOCK_EX);
    }
    
    public static function read(string $date = null, int $lines = 100): array {
        $file = self::$logPath . '/' . ($date ?? date('Y-m-d')) . '.log';
        if (!file_exists($file)) return [];
        
        $content = file($file);
        return array_slice($content, -$lines);
    }
}
