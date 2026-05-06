<?php
class AdminLog extends BaseModel {
    protected string $table = 'admin_logs';
    protected array $fillable = ['user_id', 'action', 'entity_type', 'entity_id', 'old_values', 'new_values', 'ip_address', 'user_agent'];
    protected array $casts = ['user_id' => 'int', 'entity_id' => 'int', 'old_values' => 'json', 'new_values' => 'json'];
    
    public function getRecent(int $limit = 50): array {
        return Database::query(
            "SELECT al.*, u.first_name, u.last_name, u.email FROM admin_logs al 
             JOIN users u ON al.user_id = u.id 
             ORDER BY al.created_at DESC 
             LIMIT :limit",
            [':limit' => $limit]
        );
    }
    
    public function getForEntity(string $type, int $id, int $limit = 20): array {
        return Database::query(
            "SELECT al.*, u.first_name, u.last_name FROM admin_logs al 
             JOIN users u ON al.user_id = u.id 
             WHERE al.entity_type = :type AND al.entity_id = :id 
             ORDER BY al.created_at DESC 
             LIMIT :limit",
            [':type' => $type, ':id' => $id, ':limit' => $limit]
        );
    }
}
