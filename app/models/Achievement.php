<?php
class Achievement extends BaseModel {
    protected string $table = 'achievements';
    protected array $fillable = ['name', 'slug', 'description', 'icon', 'color', 'xp_bonus', 'requirement_type', 'requirement_value', 'is_active'];
    protected array $casts = ['xp_bonus' => 'int', 'requirement_value' => 'int', 'is_active' => 'bool'];
    
    public function getForUser(int $userId): array {
        return Database::query(
            "SELECT a.*, ua.earned_at FROM achievements a 
             JOIN user_achievements ua ON a.id = ua.achievement_id 
             WHERE ua.user_id = :uid AND a.is_active = 1 
             ORDER BY ua.earned_at DESC",
            [':uid' => $userId]
        );
    }
    
    public function getAvailableForUser(int $userId): array {
        return Database::query(
            "SELECT a.* FROM achievements a 
             WHERE a.is_active = 1 AND a.id NOT IN (
               SELECT achievement_id FROM user_achievements WHERE user_id = :uid
             ) ORDER BY a.requirement_value ASC",
            [':uid' => $userId]
        );
    }
    
    public function award(int $userId, int $achievementId): bool {
        $exists = Database::fetch(
            "SELECT 1 FROM user_achievements WHERE user_id = :uid AND achievement_id = :aid LIMIT 1",
            [':uid' => $userId, ':aid' => $achievementId]
        );
        if ($exists) return false;
        
        Database::execute(
            "INSERT INTO user_achievements (user_id, achievement_id) VALUES (:uid, :aid)",
            [':uid' => $userId, ':aid' => $achievementId]
        );
        
        $ach = $this->find($achievementId);
        if ($ach && $ach['xp_bonus'] > 0) {
            (new User())->addXp($userId, $ach['xp_bonus']);
            (new XpTransaction())->createTransaction($userId, $ach['xp_bonus'], 'achievement', 'Bonus: ' . $ach['name']);
        }
        
        return true;
    }
    
    public function checkAndAward(int $userId, string $type, int $value): void {
        $achievements = Database::query(
            "SELECT * FROM achievements WHERE requirement_type = :type AND requirement_value <= :val AND is_active = 1",
            [':type' => $type, ':val' => $value]
        );
        foreach ($achievements as $ach) {
            $this->award($userId, (int)$ach['id']);
        }
    }
}
