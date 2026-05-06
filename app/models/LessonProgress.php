<?php
class LessonProgress extends BaseModel {
    protected string $table = 'lesson_progress';
    protected array $fillable = ['user_id', 'lesson_id', 'video_watched_seconds', 'video_completed', 'quiz_completed', 'quiz_score', 'quiz_passed', 'xp_earned', 'completed_at'];
    protected array $casts = ['user_id' => 'int', 'lesson_id' => 'int', 'video_watched_seconds' => 'int', 'video_completed' => 'bool', 'quiz_completed' => 'bool', 'quiz_score' => 'int', 'quiz_passed' => 'bool', 'xp_earned' => 'int'];
    
    public function getForUser(int $userId, int $lessonId): ?array {
        return Database::fetch(
            "SELECT * FROM lesson_progress WHERE user_id = :uid AND lesson_id = :lid LIMIT 1",
            [':uid' => $userId, ':lid' => $lessonId]
        );
    }
    
    public function updateVideoProgress(int $userId, int $lessonId, int $seconds, bool $completed = false): void {
        $progress = $this->getForUser($userId, $lessonId);
        
        if (!$progress) {
            $this->create([
                'user_id' => $userId,
                'lesson_id' => $lessonId,
                'video_watched_seconds' => $seconds,
                'video_completed' => $completed ? 1 : 0,
                'started_at' => date('Y-m-d H:i:s')
            ]);
        } else {
            $data = [
                'video_watched_seconds' => max((int)$progress['video_watched_seconds'], $seconds),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            if ($completed) {
                $data['video_completed'] = 1;
            }
            $this->update((int)$progress['id'], $data);
        }
        
        if ($completed) {
            $this->checkAndCompleteLesson($userId, $lessonId);
        }
    }
    
    public function completeQuiz(int $userId, int $lessonId, int $score, bool $passed): void {
        $progress = $this->getForUser($userId, $lessonId);
        
        if (!$progress) {
            $this->create([
                'user_id' => $userId,
                'lesson_id' => $lessonId,
                'quiz_completed' => 1,
                'quiz_score' => $score,
                'quiz_passed' => $passed ? 1 : 0,
                'started_at' => date('Y-m-d H:i:s')
            ]);
        } else {
            $this->update((int)$progress['id'], [
                'quiz_completed' => 1,
                'quiz_score' => $score,
                'quiz_passed' => $passed ? 1 : 0,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }
        
        if ($passed) {
            $this->checkAndCompleteLesson($userId, $lessonId);
        }
    }
    
    public function checkAndCompleteLesson(int $userId, int $lessonId): void {
        $progress = $this->getForUser($userId, $lessonId);
        if (!$progress || !$progress['video_completed'] || !$progress['quiz_passed']) {
            return;
        }
        if ($progress['completed_at']) {
            return;
        }
        
        $lesson = (new Lesson())->find($lessonId);
        if (!$lesson) return;
        
        $xpReward = (int)$lesson['xp_reward'];
        
        // Award XP
        if ($xpReward > 0 && $progress['xp_earned'] == 0) {
            (new User())->addXp($userId, $xpReward);
            (new XpTransaction())->createTransaction($userId, $xpReward, 'lesson', 'Leçon complétée: ' . $lesson['title'], $lessonId, 'lesson');
            
            $this->update((int)$progress['id'], [
                'xp_earned' => $xpReward,
                'completed_at' => date('Y-m-d H:i:s')
            ]);
            
            // Check streak
            $this->updateStreak($userId);
            
            // Check achievements
            $completedCount = Database::fetch("SELECT COUNT(*) as c FROM lesson_progress WHERE user_id = :uid AND completed_at IS NOT NULL", [':uid' => $userId])['c'] ?? 0;
            (new Achievement())->checkAndAward($userId, 'lessons', (int)$completedCount);
            
            // Update weekly ranking
            (new WeeklyRanking())->updateCurrentWeek($userId);
        }
    }
    
    private function updateStreak(int $userId): void {
        $user = (new User())->find($userId);
        if (!$user) return;
        
        $lastDate = $user['streak_last_date'];
        $today = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        
        $streak = (int)$user['streak_days'];
        
        if ($lastDate === $today) {
            return; // Already updated today
        } elseif ($lastDate === $yesterday) {
            $streak++;
        } else {
            $streak = 1;
        }
        
        Database::execute(
            "UPDATE users SET streak_days = :streak, streak_last_date = :today WHERE id = :id",
            [':streak' => $streak, ':today' => $today, ':id' => $userId]
        );
        
        if ($streak >= 7) {
            (new Achievement())->checkAndAward($userId, 'streak', $streak);
        }
    }
    
    public function getCompletedCount(int $userId): int {
        $row = Database::fetch(
            "SELECT COUNT(*) as c FROM lesson_progress WHERE user_id = :uid AND completed_at IS NOT NULL",
            [':uid' => $userId]
        );
        return (int)($row['c'] ?? 0);
    }
    
    public function getInProgressCount(int $userId): int {
        $row = Database::fetch(
            "SELECT COUNT(*) as c FROM lesson_progress WHERE user_id = :uid AND completed_at IS NULL",
            [':uid' => $userId]
        );
        return (int)($row['c'] ?? 0);
    }
}
