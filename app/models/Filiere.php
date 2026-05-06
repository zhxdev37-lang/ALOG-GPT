<?php
class Filiere extends BaseModel {
    protected string $table = 'filieres';
    protected array $fillable = ['school_level_id', 'name', 'slug', 'description', 'sort_order', 'is_active'];
    protected array $casts = ['school_level_id' => 'int', 'sort_order' => 'int', 'is_active' => 'bool'];
    
    public function getByLevel(int $levelId): array {
        return $this->where('school_level_id', $levelId, '=', 'sort_order ASC');
    }
    
    public function getActive(): array {
        return $this->where('is_active', 1, '=', 'sort_order ASC');
    }
}
