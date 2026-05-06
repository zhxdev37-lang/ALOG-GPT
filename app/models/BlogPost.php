<?php
class BlogPost extends BaseModel {
    protected string $table = 'blog_posts';
    protected array $fillable = ['author_id', 'category_id', 'title', 'slug', 'excerpt', 'content', 'featured_image', 'meta_title', 'meta_description', 'meta_keywords', 'og_image', 'status', 'published_at', 'views_count', 'is_featured'];
    protected array $casts = ['author_id' => 'int', 'category_id' => 'int', 'views_count' => 'int', 'is_featured' => 'bool'];
    
    public function getPublished(int $page = 1, int $perPage = 12): array {
        $offset = ($page - 1) * $perPage;
        $sql = "SELECT bp.*, u.first_name as author_name, u.last_name as author_lastname, bc.name as category_name, bc.slug as category_slug
                FROM blog_posts bp 
                JOIN users u ON bp.author_id = u.id 
                LEFT JOIN blog_categories bc ON bp.category_id = bc.id 
                WHERE bp.status = 'published' AND bp.published_at <= NOW()
                ORDER BY bp.is_featured DESC, bp.published_at DESC 
                LIMIT :limit OFFSET :offset";
        $rows = Database::query($sql, [':limit' => $perPage, ':offset' => $offset]);
        
        $count = Database::fetch("SELECT COUNT(*) as total FROM blog_posts WHERE status = 'published' AND published_at <= NOW()")['total'] ?? 0;
        
        return [
            'data' => $rows,
            'current_page' => $page,
            'per_page' => $perPage,
            'total' => (int)$count,
            'last_page' => (int)ceil((int)$count / $perPage)
        ];
    }
    
    public function getBySlug(string $slug): ?array {
        $sql = "SELECT bp.*, u.first_name as author_name, u.last_name as author_lastname, u.avatar as author_avatar,
                       bc.name as category_name, bc.slug as category_slug
                FROM blog_posts bp 
                JOIN users u ON bp.author_id = u.id 
                LEFT JOIN blog_categories bc ON bp.category_id = bc.id 
                WHERE bp.slug = :slug AND bp.status = 'published' 
                LIMIT 1";
        $post = Database::fetch($sql, [':slug' => $slug]);
        
        if ($post) {
            $this->incrementViews((int)$post['id']);
            $post['tags'] = $this->getTags((int)$post['id']);
        }
        
        return $post;
    }
    
    public function getByCategory(string $slug, int $page = 1, int $perPage = 12): array {
        $offset = ($page - 1) * $perPage;
        $sql = "SELECT bp.*, u.first_name as author_name, u.last_name as author_lastname, bc.name as category_name
                FROM blog_posts bp 
                JOIN users u ON bp.author_id = u.id 
                JOIN blog_categories bc ON bp.category_id = bc.id 
                WHERE bc.slug = :slug AND bp.status = 'published'
                ORDER BY bp.published_at DESC 
                LIMIT :limit OFFSET :offset";
        $rows = Database::query($sql, [':slug' => $slug, ':limit' => $perPage, ':offset' => $offset]);
        
        $count = Database::fetch(
            "SELECT COUNT(*) as total FROM blog_posts bp JOIN blog_categories bc ON bp.category_id = bc.id WHERE bc.slug = :slug AND bp.status = 'published'",
            [':slug' => $slug]
        )['total'] ?? 0;
        
        return [
            'data' => $rows,
            'current_page' => $page,
            'per_page' => $perPage,
            'total' => (int)$count,
            'last_page' => (int)ceil((int)$count / $perPage)
        ];
    }
    
    public function getRelated(int $postId, int $categoryId, int $limit = 4): array {
        return Database::query(
            "SELECT id, title, slug, excerpt, featured_image, published_at FROM blog_posts 
             WHERE category_id = :cid AND id != :pid AND status = 'published' 
             ORDER BY published_at DESC LIMIT :limit",
            [':cid' => $categoryId, ':pid' => $postId, ':limit' => $limit]
        );
    }
    
    public function getTags(int $postId): array {
        return Database::query(
            "SELECT t.* FROM blog_tags t 
             JOIN blog_post_tag pt ON t.id = pt.tag_id 
             WHERE pt.post_id = :pid",
            [':pid' => $postId]
        );
    }
    
    public function getRecent(int $limit = 5): array {
        return Database::query(
            "SELECT bp.id, bp.title, bp.slug, bp.published_at, bc.name as category_name 
             FROM blog_posts bp 
             LEFT JOIN blog_categories bc ON bp.category_id = bc.id 
             WHERE bp.status = 'published' 
             ORDER BY bp.published_at DESC LIMIT :limit",
            [':limit' => $limit]
        );
    }
    
    public function search(string $query, int $page = 1, int $perPage = 12): array {
        $offset = ($page - 1) * $perPage;
        $sql = "SELECT bp.*, u.first_name as author_name, u.last_name as author_lastname, bc.name as category_name
                FROM blog_posts bp 
                JOIN users u ON bp.author_id = u.id 
                LEFT JOIN blog_categories bc ON bp.category_id = bc.id 
                WHERE bp.status = 'published' AND (bp.title LIKE :q OR bp.content LIKE :q OR bp.excerpt LIKE :q)
                ORDER BY bp.published_at DESC 
                LIMIT :limit OFFSET :offset";
        $rows = Database::query($sql, [':q' => '%' . $query . '%', ':limit' => $perPage, ':offset' => $offset]);
        
        $count = Database::fetch(
            "SELECT COUNT(*) as total FROM blog_posts WHERE status = 'published' AND (title LIKE :q OR content LIKE :q OR excerpt LIKE :q)",
            [':q' => '%' . $query . '%']
        )['total'] ?? 0;
        
        return [
            'data' => $rows,
            'current_page' => $page,
            'per_page' => $perPage,
            'total' => (int)$count,
            'last_page' => (int)ceil((int)$count / $perPage)
        ];
    }
    
    public function incrementViews(int $postId): void {
        Database::execute("UPDATE blog_posts SET views_count = views_count + 1 WHERE id = :id", [':id' => $postId]);
    }
    
    public function getAdminList(int $page = 1, int $perPage = 20): array {
        $offset = ($page - 1) * $perPage;
        $sql = "SELECT bp.*, u.first_name, u.last_name, bc.name as category_name
                FROM blog_posts bp 
                JOIN users u ON bp.author_id = u.id 
                LEFT JOIN blog_categories bc ON bp.category_id = bc.id 
                ORDER BY bp.created_at DESC 
                LIMIT :limit OFFSET :offset";
        $rows = Database::query($sql, [':limit' => $perPage, ':offset' => $offset]);
        
        $count = Database::fetch("SELECT COUNT(*) as total FROM blog_posts")['total'] ?? 0;
        
        return [
            'data' => $rows,
            'current_page' => $page,
            'per_page' => $perPage,
            'total' => (int)$count,
            'last_page' => (int)ceil((int)$count / $perPage)
        ];
    }
}
