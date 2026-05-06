<?php
class Plan extends BaseModel {
    protected string $table = 'plans';
    protected array $fillable = ['name', 'slug', 'description', 'price_mad', 'price_usd', 'duration_days', 'features', 'lesson_access_type', 'max_lessons_per_day', 'support_level', 'badge', 'color', 'is_active', 'sort_order'];
    protected array $casts = ['price_mad' => 'float', 'price_usd' => 'float', 'duration_days' => 'int', 'max_lessons_per_day' => 'int', 'features' => 'json', 'is_active' => 'bool', 'sort_order' => 'int'];
    
    public function getActive(): array {
        return $this->where('is_active', 1, '=', 'sort_order ASC');
    }
    
    public function getBySlug(string $slug): ?array {
        return $this->findBy('slug', $slug);
    }
}
