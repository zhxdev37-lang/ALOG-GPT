<?php
class WeeklyRanking extends BaseModel {
    protected string $table = 'weekly_rankings';
    protected array $fillable = ['user_id', 'week_start', 'week_end', 'xp_earned', 'lessons_completed', 'quizzes_completed', 'rank_position', 'region', 'school_level_id'];
    protected array $casts = ['user_id' => 'int', 'xp_earned' => 'int', 'lessons_completed' => 'int', 'quizzes_completed' => 'int', 'rank_position' => 'int', 'school_level_id' => 'int'];
    
    public function getCurrentWeek(): array {
        $weekStart = date('Y-m-d', strtotime('monday this week'));
        $weekEnd = date('Y-m-d', strtotime('sunday this week'));
        
        return Database::query(
            "SELECT wr.*, u.first_name, u.last_name, u.avatar, u.xp_total, u.level 
             FROM weekly_rankings wr 
             JOIN users u ON wr.user_id = u.id 
             WHERE wr.week_start = :start AND u.status = 'active' 
             ORDER BY wr.xp_earned DESC 
             LIMIT 50",
            [':start' => $weekStart]
        );
    }
    
    public function getForUser(int $userId, int $limit = 10): array {
        return Database::query(
            "SELECT * FROM weekly_rankings WHERE user_id = :uid ORDER BY week_start DESC LIMIT :limit",
            [':uid' => $userId, ':limit' => $limit]
        );
    }
    
    public function updateCurrentWeek(int $userId): void {
        $weekStart = date('Y-m-d', strtotime('monday this week'));
        $weekEnd = date('Y-m-d', strtotime('sunday this week'));
        
        $user = Database::fetch("SELECT region, school_level_id FROM users WHERE id = :uid", [':uid' => $userId]);
        if (!$user) return;
        
        $stats = Database::fetch(
            "SELECT 
                COALESCE(SUM(xp.amount),0) as xp_earned,
                COUNT(DISTINCT lp.lesson_id) as lessons_completed,
                COUNT(DISTINCT qa.id) as quizzes_completed
             FROM users u
             LEFT JOIN xp_transactions xp ON u.id = xp.user_id AND DATE(xp.created_at) BETWEEN :start AND :end
             LEFT JOIN lesson_progress lp ON u.id = lp.user_id AND DATE(lp.completed_at) BETWEEN :start AND :end
             LEFT JOIN quiz_attempts qa ON u.id = qa.user_id AND DATE(qa.completed_at) BETWEEN :start AND :end
             WHERE u.id = :uid",
            [':uid' => $userId, ':start' => $weekStart, ':end' => $weekEnd]
        );
        
        Database::execute(
            "INSERT INTO weekly_rankings (user_id, week_start, week_end, xp_earned, lessons_completed, quizzes_completed, region, school_level_id)
             VALUES (:uid, :ws, :we, :xp, :lc, :qc, :region, :slid)
             ON DUPLICATE KEY UPDATE 
             xp_earned = :xp, lessons_completed = :lc, quizzes_completed = :qc, updated_at = NOW()",
            [
                ':uid' => $userId,
                ':ws' => $weekStart,
                ':we' => $weekEnd,
                ':xp' => $stats['xp_earned'] ?? 0,
                ':lc' => $stats['lessons_completed'] ?? 0,
                ':qc' => $stats['quizzes_completed'] ?? 0,
                ':region' => $user['region'],
                ':slid' => $user['school_level_id']
            ]
        );
        
        // Recalculate ranks
        $this->recalculateRanks($weekStart);
    }
    
    private function recalculateRanks(string $weekStart): void {
        $users = Database::query(
            "SELECT id FROM weekly_rankings WHERE week_start = :start ORDER BY xp_earned DESC",
            [':start' => $weekStart]
        );
        
        $rank = 1;
        foreach ($users as $row) {
            Database::execute(
                "UPDATE weekly_rankings SET rank_position = :rank WHERE id = :id",
                [':rank' => $rank++, ':id' => $row['id']]
            );
        }
    }
    
    public function resetWeekly(): void {
        // Archive old data or just let it be overwritten
        // Current implementation auto-updates via ON DUPLICATE KEY
    }
}
