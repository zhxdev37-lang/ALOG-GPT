<?php
class SchoolLevel extends BaseModel {
    protected string $table = 'school_levels';
    protected array $fillable = ['name', 'slug', 'description', 'sort_order', 'is_active'];
    protected array $casts = ['sort_order' => 'int', 'is_active' => 'bool'];
    
    public function getActive(): array {
        return $this->where('is_active', 1, '=', 'sort_order ASC');
    }
    
    public function getWithFilieres(): array {
        $levels = $this->getActive();
        $filiereModel = new Filiere();
        foreach ($levels as &$level) {
            $level['filieres'] = $filiereModel->where('school_level_id', $level['id'], '=', 'sort_order ASC');
        }
        return $levels;
    }
}
