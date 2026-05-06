<?php
class PromoCode extends BaseModel {
    protected string $table = 'promo_codes';
    protected array $fillable = ['code', 'discount_percent', 'discount_amount', 'applies_to_plan_id', 'max_uses', 'used_count', 'expires_at', 'is_active'];
    protected array $casts = ['discount_percent' => 'int', 'discount_amount' => 'float', 'applies_to_plan_id' => 'int', 'max_uses' => 'int', 'used_count' => 'int', 'is_active' => 'bool'];
    
    public function validate(string $code, ?int $planId = null): ?array {
        $promo = Database::fetch(
            "SELECT * FROM promo_codes WHERE code = :code AND is_active = 1 AND (expires_at IS NULL OR expires_at > NOW()) AND (max_uses IS NULL OR used_count < max_uses) LIMIT 1",
            [':code' => strtoupper($code)]
        );
        
        if (!$promo) return null;
        if ($planId && $promo['applies_to_plan_id'] && $promo['applies_to_plan_id'] != $planId) return null;
        
        return $promo;
    }
    
    public function incrementUsage(int $id): void {
        Database::execute("UPDATE promo_codes SET used_count = used_count + 1 WHERE id = :id", [':id' => $id]);
    }
    
    public function calculateDiscount(float $price, array $promo): float {
        if ($promo['discount_percent'] > 0) {
            return round($price * ($promo['discount_percent'] / 100), 2);
        }
        if ($promo['discount_amount'] > 0) {
            return min($price, $promo['discount_amount']);
        }
        return 0;
    }
}
