<?php
class Subscription extends BaseModel {
    protected string $table = 'subscriptions';
    protected array $fillable = ['user_id', 'plan_id', 'status', 'payment_method', 'payment_status', 'amount_paid', 'currency', 'starts_at', 'expires_at', 'cancelled_at', 'transaction_id', 'promo_code'];
    protected array $casts = ['user_id' => 'int', 'plan_id' => 'int', 'amount_paid' => 'float'];
    
    public function getActiveForUser(int $userId): ?array {
        return Database::fetch(
            "SELECT s.*, p.name as plan_name, p.slug as plan_slug, p.features, p.lesson_access_type, p.max_lessons_per_day, p.support_level
             FROM subscriptions s 
             JOIN plans p ON s.plan_id = p.id 
             WHERE s.user_id = :uid AND s.status = 'active' AND s.expires_at > NOW() 
             ORDER BY s.expires_at DESC LIMIT 1",
            [':uid' => $userId]
        );
    }
    
    public function getUserHistory(int $userId, int $page = 1, int $perPage = 10): array {
        $offset = ($page - 1) * $perPage;
        $sql = "SELECT s.*, p.name as plan_name FROM subscriptions s 
                JOIN plans p ON s.plan_id = p.id 
                WHERE s.user_id = :uid 
                ORDER BY s.created_at DESC 
                LIMIT :limit OFFSET :offset";
        $rows = Database::query($sql, [':uid' => $userId, ':limit' => $perPage, ':offset' => $offset]);
        
        $count = Database::fetch("SELECT COUNT(*) as total FROM subscriptions WHERE user_id = :uid", [':uid' => $userId])['total'] ?? 0;
        
        return [
            'data' => $rows,
            'current_page' => $page,
            'per_page' => $perPage,
            'total' => (int)$count,
            'last_page' => (int)ceil((int)$count / $perPage)
        ];
    }
    
    public function getAdminList(int $page = 1, int $perPage = 20): array {
        $offset = ($page - 1) * $perPage;
        $sql = "SELECT s.*, p.name as plan_name, u.first_name, u.last_name, u.email 
                FROM subscriptions s 
                JOIN plans p ON s.plan_id = p.id 
                JOIN users u ON s.user_id = u.id 
                ORDER BY s.created_at DESC 
                LIMIT :limit OFFSET :offset";
        $rows = Database::query($sql, [':limit' => $perPage, ':offset' => $offset]);
        
        $count = Database::fetch("SELECT COUNT(*) as total FROM subscriptions")['total'] ?? 0;
        
        return [
            'data' => $rows,
            'current_page' => $page,
            'per_page' => $perPage,
            'total' => (int)$count,
            'last_page' => (int)ceil((int)$count / $perPage)
        ];
    }
    
    public function getRevenueStats(): array {
        $today = Database::fetch("SELECT COALESCE(SUM(amount_paid),0) as revenue FROM subscriptions WHERE payment_status = 'paid' AND DATE(created_at) = CURDATE()")['revenue'] ?? 0;
        $month = Database::fetch("SELECT COALESCE(SUM(amount_paid),0) as revenue FROM subscriptions WHERE payment_status = 'paid' AND MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())")['revenue'] ?? 0;
        $total = Database::fetch("SELECT COALESCE(SUM(amount_paid),0) as revenue FROM subscriptions WHERE payment_status = 'paid'")['revenue'] ?? 0;
        $active = Database::fetch("SELECT COUNT(*) as c FROM subscriptions WHERE status = 'active' AND expires_at > NOW()")['c'] ?? 0;
        
        return ['today' => (float)$today, 'month' => (float)$month, 'total' => (float)$total, 'active' => (int)$active];
    }
}
