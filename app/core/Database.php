<?php
/**
 * Database - PDO Wrapper avec cache query
 * Compatible MySQL 8+, optimisé pour shared hosting
 */
class Database {
    private static ?PDO $instance = null;
    private static array $queryCache = [];
    
    public static function getInstance(): PDO {
        if (self::$instance === null) {
            try {
                $host = $_ENV['DB_HOST'] ?? 'localhost';
                $name = $_ENV['DB_NAME'] ?? 'alog_academy';
                $user = $_ENV['DB_USER'] ?? 'root';
                $pass = $_ENV['DB_PASS'] ?? '';
                
                $dsn = "mysql:host={$host};dbname={$name};charset=utf8mb4";
                self::$instance = new PDO($dsn, $user, $pass, [
                    PDO::ATTR_ERRMODE => DEBUG_MODE ? PDO::ERRMODE_EXCEPTION : PDO::ERRMODE_SILENT,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::ATTR_PERSISTENT => true
                ]);
            } catch (PDOException $e) {
                Logger::error('DB Connection failed: ' . $e->getMessage());
                die('Service temporairement indisponible. Veuillez réessayer plus tard.');
            }
        }
        return self::$instance;
    }
    
    public static function query(string $sql, array $params = [], bool $useCache = false): array {
        if ($useCache && CACHE_ENABLED) {
            $cacheKey = md5($sql . serialize($params));
            if (isset(self::$queryCache[$cacheKey])) {
                return self::$queryCache[$cacheKey];
            }
        }
        
        $stmt = self::getInstance()->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetchAll();
        
        if ($useCache && CACHE_ENABLED) {
            self::$queryCache[$cacheKey] = $result;
        }
        
        return $result;
    }
    
    public static function fetch(string $sql, array $params = []) {
        $stmt = self::getInstance()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }
    
    public static function execute(string $sql, array $params = []): int {
        self::$queryCache = []; // Clear cache on write
        $stmt = self::getInstance()->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }
    
    public static function lastInsertId(): string {
        return self::getInstance()->lastInsertId();
    }
    
    public static function beginTransaction(): bool {
        return self::getInstance()->beginTransaction();
    }
    
    public static function commit(): bool {
        return self::getInstance()->commit();
    }
    
    public static function rollback(): bool {
        return self::getInstance()->rollBack();
    }
}
