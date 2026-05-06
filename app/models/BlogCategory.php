<?php
class BlogCategory extends BaseModel {
    protected string $table = 'blog_categories';
    protected array $fillable = ['name', 'slug', 'description', 'meta_title', 'meta_description', 'sort_order'];
    protected array $casts = ['sort_order' => 'int'];
    
    public function getActive(): array {
        return Database::query("SELECT bc.*, COUNT(bp.id) as posts_count FROM blog_categories bc LEFT JOIN blog_posts bp ON bc.id = bp.category_id AND bp.status = 'published' GROUP BY bc.id ORDER BY bc.sort_order");
    }
}
