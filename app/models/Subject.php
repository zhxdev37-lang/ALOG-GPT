<?php
class Subject extends BaseModel {
    protected string $table = 'subjects';
    protected array $fillable = ['name', 'slug', 'description', 'icon', 'color', 'is_active'];
    protected array $casts = ['is_active' => 'bool'];
    
    public function getActive(): array {
        return $this->where('is_active', 1, '=', 'name ASC');
    }
    
    public function getForLevel(int $levelId, ?int $filiereId = null): array {
        $sql = "SELECT s.* FROM subjects s 
                JOIN subject_level sl ON s.id = sl.subject_id 
                WHERE s.is_active = 1 AND sl.school_level_id = :level_id";
        $params = [':level_id' => $levelId];
        
        if ($filiereId) {
            $sql .= " AND (sl.filiere_id = :filiere_id OR sl.filiere_id IS NULL)";
            $params[':filiere_id'] = $filiereId;
        }
        
        $sql .= " GROUP BY s.id ORDER BY s.name";
        return Database::query($sql, $params);
    }
    
    public function attachToLevel(int $subjectId, int $levelId, ?int $filiereId = null): void {
        Database::execute(
            "INSERT INTO subject_level (subject_id, school_level_id, filiere_id) VALUES (:sid, :lid, :fid)
             ON DUPLICATE KEY UPDATE created_at = created_at",
            [':sid' => $subjectId, ':lid' => $levelId, ':fid' => $filiereId]
        );
    }
    
    public function detachFromLevel(int $subjectId, int $levelId, ?int $filiereId = null): void {
        $sql = "DELETE FROM subject_level WHERE subject_id = :sid AND school_level_id = :lid";
        $params = [':sid' => $subjectId, ':lid' => $levelId];
        if ($filiereId) {
            $sql .= " AND filiere_id = :fid";
            $params[':fid'] = $filiereId;
        }
        Database::execute($sql, $params);
    }
}
