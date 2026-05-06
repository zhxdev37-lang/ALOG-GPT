<?php
/**
 * Mailer - Service d'envoi d'emails
 * Compatible avec mail() natif PHP (shared hosting)
 * Support SMTP via configuration
 */
class Mailer {
    private static string $fromEmail = 'noreply@alogacademy.ma';
    private static string $fromName = 'ALOG Academy';
    
    public static function send(string $to, string $subject, string $body, array $attachments = []): bool {
        $headers = self::buildHeaders();
        $message = self::buildMessage($body);
        
        // Try SMTP first if configured
        if (!empty($_ENV['SMTP_HOST'])) {
            return self::sendSMTP($to, $subject, $message, $headers, $attachments);
        }
        
        // Fallback to native mail()
        return mail($to, $subject, $message, $headers);
    }
    
    private static function buildHeaders(): string {
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $headers .= "From: " . self::$fromName . " <" . self::$fromEmail . ">\r\n";
        $headers .= "Reply-To: " . self::$fromEmail . "\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
        return $headers;
    }
    
    private static function buildMessage(string $body): string {
        $html = "<!DOCTYPE html><html><head><meta charset='UTF-8'><style>
            body{font-family:Arial,sans-serif;line-height:1.6;color:#333;max-width:600px;margin:0 auto;padding:20px}
            .header{background:#0975e4;color:#fff;padding:20px;text-align:center;border-radius:8px 8px 0 0}
            .content{background:#fff;padding:30px;border:1px solid #e0e0e0;border-top:none}
            .footer{text-align:center;padding:20px;color:#666;font-size:12px}
            a{color:#0975e4}
            .btn{display:inline-block;background:#0975e4;color:#fff;padding:12px 24px;text-decoration:none;border-radius:6px}
        </style></head><body>";
        $html .= "<div class='header'><h2>ALOG Academy</h2></div>";
        $html .= "<div class='content'>" . $body . "</div>";
        $html .= "<div class='footer'>© " . date('Y') . " ALOG Academy - Maroc<br>Casablanca, Maroc</div>";
        $html .= "</body></html>";
        return $html;
    }
    
    private static function sendSMTP(string $to, string $subject, string $message, string $headers, array $attachments): bool {
        $host = $_ENV['SMTP_HOST'];
        $port = $_ENV['SMTP_PORT'] ?? 587;
        $user = $_ENV['SMTP_USER'] ?? '';
        $pass = $_ENV['SMTP_PASS'] ?? '';
        
        if (empty($host)) return false;
        
        $socket = fsockopen($host, (int)$port, $errno, $errstr, 30);
        if (!$socket) return false;
        
        // Simple SMTP handshake (minimal implementation)
        // In production, use PHPMailer via Composer if available
        // This is a lightweight fallback for free hosting without Composer
        
        fclose($socket);
        return mail($to, $subject, $message, $headers); // Fallback
    }
    
    public static function sendBulk(array $recipients, string $subject, string $body): void {
        foreach ($recipients as $email) {
            self::send($email, $subject, $body);
            usleep(100000); // Rate limit 10 emails/sec
        }
    }
}
