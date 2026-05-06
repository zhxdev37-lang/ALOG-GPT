<?php
class Question extends BaseModel {
    protected string $table = 'questions';
    protected array $fillable = ['quiz_id', 'question_text', 'question_type', 'options', 'correct_answer', 'explanation', 'points', 'sort_order'];
    protected array $casts = ['quiz_id' => 'int', 'points' => 'int', 'sort_order' => 'int', 'options' => 'json'];
    
    public function getByQuiz(int $quizId): array {
        return $this->where('quiz_id', $quizId, '=', 'sort_order ASC');
    }
}
