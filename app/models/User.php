<?php
class User extends BaseModel {
    protected string $table = 'users';
    protected array $fillable = ['role_id', 'first_name', 'last_name', 'email', 'phone', 'password_hash', 'avatar', 'birth_date', 'region', 'school_level_id', 'filiere_id', 'xp_total', 'xp_current', 'level', 'streak_days', 'streak_last_date', 'plan_id', 'plan_expires_at', 'email_verified_at', 'google_id', 'status', 'last_login_at', 'last_login_ip'];
    protected array $casts = ['xp_total' => 'int', 'xp_current' => 'int', 'level' => 'int', 'streak_days' => 'int', 'school_level_id' => 'int', 'filiere_id' => 'int', 'plan_id' => 'int'];
    
    public function findByEmail(string $email): ?array {
        return $this->findBy('email', $email);
    }
    
    public function findByGoogleId(string $id): ?array {
        return $this->findBy('google_id', $id);
    }
    
    public function getStudents(int $page = 1, int $perPage = 20): array {
        $offset = ($page - 1) * $perPage;
        $sql = "SELECT u.*, r.name as role_name, sl.name as level_name, f.name as filiere_name, p.name as plan_name 
                FROM users u 
                LEFT JOIN roles r ON u.role_id = r.id 
                LEFT JOIN school_levels sl ON u.school_level_id = sl.id 
                LEFT JOIN filieres f ON u.filiere_id = f.id 
                LEFT JOIN plans p ON u.plan_id = p.id 
                WHERE u.role_id = 3 
                ORDER BY u.created_at DESC 
                LIMIT :limit OFFSET :offset";
        $rows = Database::query($sql, [':limit' => $perPage, ':offset' => $offset]);
        
        $count = Database::fetch("SELECT COUNT(*) as total FROM users WHERE role_id = 3");
        return [
            'data' => $rows,
            'current_page' => $page,
            'per_page' => $perPage,
            'total' => (int)($count['total'] ?? 0),
            'last_page' => (int)ceil((int)($count['total'] ?? 0) / $perPage)
        ];
    }
    
    public function updateLevelAndFiliere(int $id, int $levelId, ?int $filiereId): bool {
        // Check 3 months constraint
        $user = $this->find($id);
        if ($user && $user['level_changed_at']) {
            $lastChange = new DateTime($user['level_changed_at']);
            $now = new DateTime();
            $diff = $now->diff($lastChange);
            if ($diff->days < 90) {
                return false;
            }
        }
        
        return $this->update($id, [
            'school_level_id' => $levelId,
            'filiere_id' => $filiereId,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }
    
    public function addXp(int $userId, int $amount): void {
        Database::execute(
            "UPDATE users SET xp_total = xp_total + :amount, xp_current = xp_current + :amount WHERE id = :id",
            [':amount' => $amount, ':id' => $userId]
        );
        
        // Update level
        $user = $this->find($userId);
        if ($user) {
            $newLevel = xpLevel((int)$user['xp_total']);
            Database::execute("UPDATE users SET level = :lvl WHERE id = :id", [':lvl' => $newLevel, ':id' => $userId]);
        }
    }
    
    public function getLeaderboard(string $type = 'global', int $limit = 50): array {
        $sql = "SELECT u.id, u.first_name, u.last_name, u.avatar, u.xp_total, u.level, 
                       u.region, sl.name as level_name, f.name as filiere_name
                FROM users u 
                LEFT JOIN school_levels sl ON u.school_level_id = sl.id 
                LEFT JOIN filieres f ON u.filiere_id = f.id 
                WHERE u.role_id = 3 AND u.status = 'active'
                ORDER BY u.xp_total DESC 
                LIMIT :limit";
        
        if ($type === 'weekly') {
            $sql = "SELECT u.id, u.first_name, u.last_name, u.avatar, u.level, u.region,
                           wr.xp_earned, wr.lessons_completed, wr.rank_position
                    FROM weekly_rankings wr
                    JOIN users u ON wr.user_id = u.id
                    WHERE wr.week_start = DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY)
                    ORDER BY wr.xp_earned DESC
                    LIMIT :limit";
        }
        
        return Database::query($sql, [':limit' => $limit]);
    }
    
    public function getStats(): array {
        $total = Database::fetch("SELECT COUNT(*) as c FROM users WHERE role_id = 3")['c'] ?? 0;
        $active = Database::fetch("SELECT COUNT(*) as c FROM users WHERE role_id = 3 AND status = 'active'")['c'] ?? 0;
        $today = Database::fetch("SELECT COUNT(*) as c FROM users WHERE role_id = 3 AND DATE(created_at) = CURDATE()")['c'] ?? 0;
        $week = Database::fetch("SELECT COUNT(*) as c FROM users WHERE role_id = 3 AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)")['c'] ?? 0;
        
        return ['total' => (int)$total, 'active' => (int)$active, 'today' => (int)$today, 'week' => (int)$week];
    }
}
