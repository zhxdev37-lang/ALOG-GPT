<?php
/**
 * Cache - Système de cache fichier simple
 * Ultra-léger pour shared hosting
 */
class Cache {
    public static function get(string $key) {
        $file = CACHE_PATH . '/' . md5($key) . '.cache';
        if (!file_exists($file)) {
            return null;
        }
        
        $data = unserialize(file_get_contents($file));
        if ($data['expires'] < time()) {
            unlink($file);
            return null;
        }
        
        return $data['value'];
    }
    
    public static function set(string $key, $value, int $ttl = CACHE_TTL): void {
        if (!is_dir(CACHE_PATH)) {
            mkdir(CACHE_PATH, 0755, true);
        }
        
        $file = CACHE_PATH . '/' . md5($key) . '.cache';
        file_put_contents($file, serialize([
            'expires' => time() + $ttl,
            'value' => $value
        ]), LOCK_EX);
    }
    
    public static function forget(string $key): void {
        $file = CACHE_PATH . '/' . md5($key) . '.cache';
        if (file_exists($file)) {
            unlink($file);
        }
    }
    
    public static function flush(): void {
        foreach (glob(CACHE_PATH . '/*.cache') as $file) {
            unlink($file);
        }
    }
    
    public static function remember(string $key, callable $callback, int $ttl = CACHE_TTL) {
        $value = self::get($key);
        if ($value !== null) {
            return $value;
        }
        
        $value = $callback();
        self::set($key, $value, $ttl);
        return $value;
    }
}
