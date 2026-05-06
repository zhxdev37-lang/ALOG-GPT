<?php
/**
 * PaymentController - Système de paiement
 * CMI, PayPal, WhatsApp
 */
class PaymentController extends BaseController {
    
    public function checkout(string $planSlug): void {
        $this->requireAuth();
        
        $plan = (new Plan())->getBySlug($planSlug);
        if (!$plan) {
            Router::redirect('/tarifs');
        }
        
        $promoCode = Security::input('promo', 'GET');
        $discount = 0;
        $promo = null;
        
        if ($promoCode) {
            $promo = (new PromoCode())->validate($promoCode, (int)$plan['id']);
            if ($promo) {
                $discount = (new PromoCode())->calculateDiscount((float)$plan['price_mad'], $promo);
            }
        }
        
        $finalPrice = max(0, (float)$plan['price_mad'] - $discount);
        
        $this->view('payment/checkout', [
            'plan' => $plan,
            'promo' => $promo,
            'discount' => $discount,
            'finalPrice' => $finalPrice,
            'seo' => generateSeoMeta([
                'title' => 'Paiement - ' . $plan['name'],
                'description' => 'Finalisez votre abonnement ' . $plan['name']
            ])
        ]);
    }
    
    public function process(): void {
        $this->requireAuth();
        $userId = Auth::id();
        
        $planId = Security::int('plan_id');
        $method = Security::input('method');
        $promoCode = Security::input('promo_code');
        
        $plan = (new Plan())->find($planId);
        if (!$plan) {
            $this->json(['error' => 'Plan invalide']);
        }
        
        $amount = (float)$plan['price_mad'];
        $discount = 0;
        
        if ($promoCode) {
            $promo = (new PromoCode())->validate($promoCode, $planId);
            if ($promo) {
                $discount = (new PromoCode())->calculateDiscount($amount, $promo);
                $amount = max(0, $amount - $discount);
                (new PromoCode())->incrementUsage((int)$promo['id']);
            }
        }
        
        // For free plan or zero amount
        if ($amount <= 0) {
            $this->activateSubscription($userId, $planId, 'free', 0, $promoCode);
            $this->json(['success' => true, 'redirect' => '/tableau-de-bord?payment=success']);
        }
        
        switch ($method) {
            case 'cmi':
                $this->processCMI($userId, $plan, $amount, $promoCode);
                break;
            case 'paypal':
                $this->processPayPal($userId, $plan, $amount, $promoCode);
                break;
            case 'whatsapp':
                $this->processWhatsApp($userId, $plan, $amount, $promoCode);
                break;
            default:
                $this->json(['error' => 'Méthode de paiement invalide']);
        }
    }
    
    private function processCMI(int $userId, array $plan, float $amount, string $promoCode): void {
        $merchantId = Setting::get('cmi_merchant_id');
        $apiKey = Setting::get('cmi_api_key');
        
        if (!$merchantId || !$apiKey) {
            $this->json(['error' => 'Paiement CMI non configuré.']);
        }
        
        // Create pending subscription
        $subId = (new Subscription())->create([
            'user_id' => $userId,
            'plan_id' => $plan['id'],
            'status' => 'pending',
            'payment_method' => 'cmi',
            'payment_status' => 'pending',
            'amount_paid' => $amount,
            'currency' => 'MAD',
            'promo_code' => $promoCode ?: null
        ]);
        
        // Redirect to CMI payment page
        // This is a simplified placeholder - actual CMI integration requires their API
        $this->json([
            'success' => true,
            'redirect' => '/paiement/confirmation?method=cmi&sub=' . $subId
        ]);
    }
    
    private function processPayPal(int $userId, array $plan, float $amount, string $promoCode): void {
        $clientId = Setting::get('paypal_client_id');
        
        if (!$clientId) {
            $this->json(['error' => 'Paiement PayPal non configuré.']);
        }
        
        $subId = (new Subscription())->create([
            'user_id' => $userId,
            'plan_id' => $plan['id'],
            'status' => 'pending',
            'payment_method' => 'paypal',
            'payment_status' => 'pending',
            'amount_paid' => $amount,
            'currency' => 'USD',
            'promo_code' => $promoCode ?: null
        ]);
        
        $this->json([
            'success' => true,
            'redirect' => '/paiement/confirmation?method=paypal&sub=' . $subId
        ]);
    }
    
    private function processWhatsApp(int $userId, array $plan, float $amount, string $promoCode): void {
        $whatsappNumber = Setting::get('whatsapp_number', '+212600000000');
        
        $subId = (new Subscription())->create([
            'user_id' => $userId,
            'plan_id' => $plan['id'],
            'status' => 'pending',
            'payment_method' => 'whatsapp',
            'payment_status' => 'pending',
            'amount_paid' => $amount,
            'currency' => 'MAD',
            'promo_code' => $promoCode ?: null
        ]);
        
        $user = Auth::user();
        $message = urlencode("Bonjour, je souhaite souscrire au plan {$plan['name']} ({$amount} MAD).\nNom: {$user['first_name']} {$user['last_name']}\nEmail: {$user['email']}\nRéf: SUB-{$subId}");
        
        $this->json([
            'success' => true,
            'redirect' => "https://wa.me/{$whatsappNumber}?text={$message}"
        ]);
    }
    
    private function activateSubscription(int $userId, int $planId, string $method, float $amount, string $promoCode = ''): void {
        $plan = (new Plan())->find($planId);
        $expires = date('Y-m-d H:i:s', strtotime('+' . ($plan['duration_days'] ?? 30) . ' days'));
        
        Database::execute(
            "UPDATE subscriptions SET status = 'active', starts_at = NOW(), expires_at = :expires, 
             payment_status = 'paid', transaction_id = :tx WHERE user_id = :uid AND plan_id = :pid AND status = 'pending'",
            [':expires' => $expires, ':tx' => 'MANUAL-' . time(), ':uid' => $userId, ':pid' => $planId]
        );
        
        Database::execute(
            "UPDATE users SET plan_id = :pid, plan_expires_at = :expires WHERE id = :uid",
            [':pid' => $planId, ':expires' => $expires, ':uid' => $userId]
        );
    }
    
    public function confirmation(): void {
        $this->requireAuth();
        $method = Security::input('method');
        $subId = Security::int('sub');
        
        $this->view('payment/confirmation', [
            'method' => $method,
            'subId' => $subId,
            'seo' => generateSeoMeta(['title' => 'Confirmation de Paiement', 'description' => 'Confirmation de votre abonnement ALOG Academy.'])
        ]);
    }
    
    public function webhookCMI(): void {
        // CMI webhook handler
        // Verify signature, update subscription status
        // Implementation depends on CMI API specification
        
        $data = $_POST;
        Logger::info('CMI Webhook received', $data);
        
        $this->json(['status' => 'received']);
    }
    
    public function webhookPayPal(): void {
        // PayPal webhook handler
        $data = json_decode(file_get_contents('php://input'), true);
        Logger::info('PayPal Webhook received', $data ?? []);
        
        $this->json(['status' => 'received']);
    }
}
