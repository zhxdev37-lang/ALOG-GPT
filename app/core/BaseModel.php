<?php
/**
 * BaseModel - Modèle de base ORM léger
 * CRUD + relations + pagination
 */
class BaseModel {
    protected string $table = '';
    protected string $primaryKey = 'id';
    protected array $fillable = [];
    protected array $hidden = [];
    protected array $casts = [];
    
    public function find(int $id): ?array {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id LIMIT 1";
        $row = Database::fetch($sql, [':id' => $id]);
        return $row ? $this->castRow($row) : null;
    }
    
    public function findBy(string $column, $value): ?array {
        $sql = "SELECT * FROM {$this->table} WHERE {$column} = :val LIMIT 1";
        $row = Database::fetch($sql, [':val' => $value]);
        return $row ? $this->castRow($row) : null;
    }
    
    public function all(string $orderBy = 'id DESC'): array {
        $sql = "SELECT * FROM {$this->table} ORDER BY {$orderBy}";
        $rows = Database::query($sql);
        return array_map([$this, 'castRow'], $rows);
    }
    
    public function where(string $column, $value, string $operator = '=', string $orderBy = 'id DESC'): array {
        $sql = "SELECT * FROM {$this->table} WHERE {$column} {$operator} :val ORDER BY {$orderBy}";
        $rows = Database::query($sql, [':val' => $value]);
        return array_map([$this, 'castRow'], $rows);
    }
    
    public function paginate(int $page = 1, int $perPage = 20, string $orderBy = 'id DESC'): array {
        $offset = ($page - 1) * $perPage;
        $sql = "SELECT * FROM {$this->table} ORDER BY {$orderBy} LIMIT :limit OFFSET :offset";
        $rows = Database::query($sql, [':limit' => $perPage, ':offset' => $offset]);
        
        $countSql = "SELECT COUNT(*) as total FROM {$this->table}";
        $count = Database::fetch($countSql);
        $total = (int)($count['total'] ?? 0);
        
        return [
            'data' => array_map([$this, 'castRow'], $rows),
            'current_page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'last_page' => (int)ceil($total / $perPage),
            'from' => $offset + 1,
            'to' => min($offset + $perPage, $total)
        ];
    }
    
    public function create(array $data): int {
        $data = array_intersect_key($data, array_flip($this->fillable));
        
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        
        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        Database::execute($sql, $data);
        
        return (int)Database::lastInsertId();
    }
    
    public function update(int $id, array $data): bool {
        $data = array_intersect_key($data, array_flip($this->fillable));
        
        $sets = [];
        foreach (array_keys($data) as $key) {
            $sets[] = "{$key} = :{$key}";
        }
        
        $sql = "UPDATE {$this->table} SET " . implode(', ', $sets) . " WHERE {$this->primaryKey} = :id";
        $data[':id'] = $id;
        
        return Database::execute($sql, $data) > 0;
    }
    
    public function delete(int $id): bool {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id";
        return Database::execute($sql, [':id' => $id]) > 0;
    }
    
    public function exists(string $column, $value): bool {
        $sql = "SELECT 1 FROM {$this->table} WHERE {$column} = :val LIMIT 1";
        return (bool)Database::fetch($sql, [':val' => $value]);
    }
    
    protected function castRow(array $row): array {
        foreach ($this->casts as $column => $type) {
            if (isset($row[$column])) {
                $row[$column] = $this->castValue($row[$column], $type);
            }
        }
        
        foreach ($this->hidden as $key) {
            unset($row[$key]);
        }
        
        return $row;
    }
    
    protected function castValue($value, string $type) {
        return match($type) {
            'int' => (int)$value,
            'float' => (float)$value,
            'bool' => (bool)$value,
            'array' => json_decode($value, true),
            'json' => json_decode($value, true),
            'datetime' => $value ? new DateTime($value) : null,
            default => $value
        };
    }
    
    public function raw(string $sql, array $params = []): array {
        return Database::query($sql, $params);
    }
    
    public function count(string $where = '', array $params = []): int {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        if ($where) {
            $sql .= " WHERE {$where}";
        }
        $row = Database::fetch($sql, $params);
        return (int)($row['total'] ?? 0);
    }
}
