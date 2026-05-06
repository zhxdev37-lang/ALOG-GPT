<?php
class Event extends BaseModel {
    protected string $table = 'events';
    protected array $fillable = ['title', 'slug', 'description', 'event_type', 'image_url', 'registration_url', 'event_date', 'end_date', 'location', 'max_participants', 'current_participants', 'is_active'];
    protected array $casts = ['max_participants' => 'int', 'current_participants' => 'int', 'is_active' => 'bool'];
    
    public function getUpcoming(int $limit = 10): array {
        return Database::query(
            "SELECT * FROM events WHERE event_date >= NOW() AND is_active = 1 ORDER BY event_date ASC LIMIT :limit",
            [':limit' => $limit]
        );
    }
    
    public function getBySlug(string $slug): ?array {
        return Database::fetch(
            "SELECT * FROM events WHERE slug = :slug AND is_active = 1 LIMIT 1",
            [':slug' => $slug]
        );
    }
    
    public function isUserRegistered(int $eventId, int $userId): bool {
        return (bool)Database::fetch(
            "SELECT 1 FROM event_registrations WHERE event_id = :eid AND user_id = :uid LIMIT 1",
            [':eid' => $eventId, ':uid' => $userId]
        );
    }
    
    public function registerUser(int $eventId, int $userId): bool {
        if ($this->isUserRegistered($eventId, $userId)) {
            return false;
        }
        
        Database::beginTransaction();
        
        Database::execute(
            "INSERT INTO event_registrations (event_id, user_id) VALUES (:eid, :uid)",
            [':eid' => $eventId, ':uid' => $userId]
        );
        
        Database::execute(
            "UPDATE events SET current_participants = current_participants + 1 WHERE id = :id",
            [':id' => $eventId]
        );
        
        Database::commit();
        
        return true;
    }
}
