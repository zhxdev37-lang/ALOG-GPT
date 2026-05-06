<?php
class BlogTag extends BaseModel {
    protected string $table = 'blog_tags';
    protected array $fillable = ['name', 'slug'];
    
    public function getPopular(int $limit = 20): array {
        return Database::query(
            "SELECT t.*, COUNT(pt.post_id) as usage_count FROM blog_tags t 
             JOIN blog_post_tag pt ON t.id = pt.tag_id 
             GROUP BY t.id 
             ORDER BY usage_count DESC 
             LIMIT :limit",
            [':limit' => $limit]
        );
    }
    
    public function attachToPost(int $tagId, int $postId): void {
        Database::execute(
            "INSERT INTO blog_post_tag (post_id, tag_id) VALUES (:pid, :tid) ON DUPLICATE KEY UPDATE post_id = post_id",
            [':pid' => $postId, ':tid' => $tagId]
        );
    }
    
    public function detachFromPost(int $tagId, int $postId): void {
        Database::execute(
            "DELETE FROM blog_post_tag WHERE post_id = :pid AND tag_id = :tid",
            [':pid' => $postId, ':tid' => $tagId]
        );
    }
    
    public function syncForPost(int $postId, array $tagIds): void {
        Database::execute("DELETE FROM blog_post_tag WHERE post_id = :pid", [':pid' => $postId]);
        foreach ($tagIds as $tid) {
            $this->attachToPost((int)$tid, $postId);
        }
    }
    
    public function findOrCreateByName(string $name): int {
        $slug = slugify($name);
        $existing = Database::fetch("SELECT id FROM blog_tags WHERE slug = :slug LIMIT 1", [':slug' => $slug]);
        if ($existing) return (int)$existing['id'];
        return $this->create(['name' => $name, 'slug' => $slug]);
    }
}
