<?php
class Setting extends BaseModel {
    protected string $table = 'settings';
    protected array $fillable = ['setting_key', 'setting_value', 'group_name'];
    
    public static function get(string $key, string $default = ''): string {
        $row = Database::fetch("SELECT setting_value FROM settings WHERE setting_key = :key LIMIT 1", [':key' => $key]);
        return $row['setting_value'] ?? $default;
    }
    
    public static function set(string $key, string $value): void {
        Database::execute(
            "INSERT INTO settings (setting_key, setting_value) VALUES (:key, :val) 
             ON DUPLICATE KEY UPDATE setting_value = :val",
            [':key' => $key, ':val' => $value]
        );
    }
    
    public function getByGroup(string $group): array {
        return $this->where('group_name', $group);
    }
}
