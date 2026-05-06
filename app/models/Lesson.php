<?php
class Lesson extends BaseModel {
    protected string $table = 'lessons';
    protected array $fillable = ['subject_id', 'school_level_id', 'filiere_id', 'title', 'slug', 'description', 'youtube_url', 'youtube_duration', 'pdf_course_url', 'pdf_exercises_url', 'image_url', 'xp_reward', 'xp_unlock_cost', 'plan_id', 'quiz_id', 'sort_order', 'is_active'];
    protected array $casts = ['subject_id' => 'int', 'school_level_id' => 'int', 'filiere_id' => 'int', 'xp_reward' => 'int', 'xp_unlock_cost' => 'int', 'plan_id' => 'int', 'quiz_id' => 'int', 'sort_order' => 'int', 'is_active' => 'bool'];
    
    public function getBySubject(int $subjectId, int $levelId, ?int $filiereId = null): array {
        $sql = "SELECT l.*, s.name as subject_name FROM lessons l 
                JOIN subjects s ON l.subject_id = s.id 
                WHERE l.subject_id = :sid AND l.school_level_id = :lid AND l.is_active = 1";
        $params = [':sid' => $subjectId, ':lid' => $levelId];
        
        if ($filiereId) {
            $sql .= " AND (l.filiere_id = :fid OR l.filiere_id IS NULL)";
            $params[':fid'] = $filiereId;
        }
        
        $sql .= " ORDER BY l.sort_order, l.title";
        return Database::query($sql, $params);
    }
    
    public function getBySlug(string $slug): ?array {
        $sql = "SELECT l.*, s.name as subject_name, sl.name as level_name, f.name as filiere_name, p.name as plan_name
                FROM lessons l 
                JOIN subjects s ON l.subject_id = s.id 
                LEFT JOIN school_levels sl ON l.school_level_id = sl.id 
                LEFT JOIN filieres f ON l.filiere_id = f.id 
                LEFT JOIN plans p ON l.plan_id = p.id 
                WHERE l.slug = :slug AND l.is_active = 1 
                LIMIT 1";
        return Database::fetch($sql, [':slug' => $slug]);
    }
    
    public function getForUser(int $userId, int $subjectId, int $levelId, ?int $filiereId = null, int $planId = 1): array {
        $lessons = $this->getBySubject($subjectId, $levelId, $filiereId);
        
        foreach ($lessons as &$lesson) {
            $progress = Database::fetch(
                "SELECT * FROM lesson_progress WHERE user_id = :uid AND lesson_id = :lid LIMIT 1",
                [':uid' => $userId, ':lid' => $lesson['id']]
            );
            
            $lesson['progress'] = $progress ?: null;
            $lesson['is_locked'] = $lesson['plan_id'] > $planId;
            $lesson['is_unlocked_by_xp'] = false;
            
            if ($lesson['is_locked'] && $lesson['xp_unlock_cost']) {
                $user = Database::fetch("SELECT xp_current FROM users WHERE id = :uid", [':uid' => $userId]);
                $lesson['is_unlocked_by_xp'] = ($user['xp_current'] ?? 0) >= $lesson['xp_unlock_cost'];
            }
            
            $lesson['video_progress'] = $progress ? min(100, round(($progress['video_watched_seconds'] / max(1, $lesson['youtube_duration'])) * 100)) : 0;
        }
        
        return $lessons;
    }
    
    public function search(string $query, int $levelId = 0, int $limit = 20): array {
        $sql = "SELECT l.*, s.name as subject_name FROM lessons l 
                JOIN subjects s ON l.subject_id = s.id 
                WHERE l.is_active = 1 AND (l.title LIKE :q OR l.description LIKE :q)";
        $params = [':q' => '%' . $query . '%'];
        
        if ($levelId > 0) {
            $sql .= " AND l.school_level_id = :lid";
            $params[':lid'] = $levelId;
        }
        
        $sql .= " ORDER BY l.title LIMIT :limit";
        $params[':limit'] = $limit;
        
        return Database::query($sql, $params);
    }
    
    public function getAdminList(int $page = 1, int $perPage = 20): array {
        $offset = ($page - 1) * $perPage;
        $sql = "SELECT l.*, s.name as subject_name, sl.name as level_name, f.name as filiere_name, p.name as plan_name
                FROM lessons l 
                JOIN subjects s ON l.subject_id = s.id 
                LEFT JOIN school_levels sl ON l.school_level_id = sl.id 
                LEFT JOIN filieres f ON l.filiere_id = f.id 
                LEFT JOIN plans p ON l.plan_id = p.id 
                ORDER BY l.created_at DESC 
                LIMIT :limit OFFSET :offset";
        $rows = Database::query($sql, [':limit' => $perPage, ':offset' => $offset]);
        
        $count = Database::fetch("SELECT COUNT(*) as total FROM lessons")['total'] ?? 0;
        
        return [
            'data' => $rows,
            'current_page' => $page,
            'per_page' => $perPage,
            'total' => (int)$count,
            'last_page' => (int)ceil((int)$count / $perPage)
        ];
    }
}
