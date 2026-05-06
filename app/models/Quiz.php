<?php
class Quiz extends BaseModel {
    protected string $table = 'quizzes';
    protected array $fillable = ['lesson_id', 'title', 'description', 'passing_score', 'xp_reward', 'time_limit_minutes', 'is_active'];
    protected array $casts = ['lesson_id' => 'int', 'passing_score' => 'int', 'xp_reward' => 'int', 'time_limit_minutes' => 'int', 'is_active' => 'bool'];
    
    public function getByLesson(int $lessonId): ?array {
        return Database::fetch(
            "SELECT * FROM quizzes WHERE lesson_id = :lid AND is_active = 1 LIMIT 1",
            [':lid' => $lessonId]
        );
    }
    
    public function getWithQuestions(int $quizId): ?array {
        $quiz = $this->find($quizId);
        if (!$quiz) return null;
        
        $quiz['questions'] = (new Question())->where('quiz_id', $quizId, '=', 'sort_order ASC');
        return $quiz;
    }
    
    public function getAdminList(int $page = 1, int $perPage = 20): array {
        $offset = ($page - 1) * $perPage;
        $sql = "SELECT q.*, l.title as lesson_title FROM quizzes q 
                LEFT JOIN lessons l ON q.lesson_id = l.id 
                ORDER BY q.created_at DESC 
                LIMIT :limit OFFSET :offset";
        $rows = Database::query($sql, [':limit' => $perPage, ':offset' => $offset]);
        
        $count = Database::fetch("SELECT COUNT(*) as total FROM quizzes")['total'] ?? 0;
        
        return [
            'data' => $rows,
            'current_page' => $page,
            'per_page' => $perPage,
            'total' => (int)$count,
            'last_page' => (int)ceil((int)$count / $perPage)
        ];
    }
}
