<?php
class Role extends BaseModel {
    protected string $table = 'roles';
    protected array $fillable = ['name', 'slug', 'description', 'permissions', 'level'];
    protected array $casts = ['permissions' => 'json', 'level' => 'int'];
}
