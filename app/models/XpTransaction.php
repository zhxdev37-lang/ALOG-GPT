<?php
class XpTransaction extends BaseModel {
    protected string $table = 'xp_transactions';
    protected array $fillable = ['user_id', 'amount', 'type', 'source_id', 'source_type', 'description'];
    protected array $casts = ['user_id' => 'int', 'amount' => 'int', 'source_id' => 'int'];
    
    public function getForUser(int $userId, int $limit = 50): array {
        return Database::query(
            "SELECT * FROM xp_transactions WHERE user_id = :uid ORDER BY created_at DESC LIMIT :limit",
            [':uid' => $userId, ':limit' => $limit]
        );
    }
    
    public function getTodayTotal(int $userId): int {
        $row = Database::fetch(
            "SELECT COALESCE(SUM(amount),0) as total FROM xp_transactions WHERE user_id = :uid AND DATE(created_at) = CURDATE()",
            [':uid' => $userId]
        );
        return (int)($row['total'] ?? 0);
    }
    
    public function createTransaction(int $userId, int $amount, string $type, string $description, ?int $sourceId = null, ?string $sourceType = null): int {
        return $this->create([
            'user_id' => $userId,
            'amount' => $amount,
            'type' => $type,
            'source_id' => $sourceId,
            'source_type' => $sourceType,
            'description' => $description,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }
}
