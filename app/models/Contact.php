<?php
class Contact extends BaseModel {
    protected string $table = 'contacts';
    protected array $fillable = ['name', 'email', 'phone', 'subject', 'message', 'status'];
    
    public function getPending(): array {
        return $this->where('status', 'new', '=', 'created_at DESC');
    }
    
    public function getAdminList(int $page = 1, int $perPage = 20): array {
        $offset = ($page - 1) * $perPage;
        $sql = "SELECT * FROM contacts ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
        $rows = Database::query($sql, [':limit' => $perPage, ':offset' => $offset]);
        
        $count = Database::fetch("SELECT COUNT(*) as total FROM contacts")['total'] ?? 0;
        
        return [
            'data' => $rows,
            'current_page' => $page,
            'per_page' => $perPage,
            'total' => (int)$count,
            'last_page' => (int)ceil((int)$count / $perPage)
        ];
    }
}
