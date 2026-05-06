<?php
class FAQ extends BaseModel {
    protected string $table = 'faqs';
    protected array $fillable = ['question', 'answer', 'category', 'sort_order', 'is_active'];
    protected array $casts = ['sort_order' => 'int', 'is_active' => 'bool'];
    
    public function getActive(): array {
        $faqs = $this->where('is_active', 1, '=', 'sort_order ASC');
        $grouped = [];
        foreach ($faqs as $faq) {
            $grouped[$faq['category']][] = $faq;
        }
        return $grouped;
    }
    
    public function getCategories(): array {
        return Database::query("SELECT DISTINCT category FROM faqs WHERE is_active = 1 ORDER BY category");
    }
}
