<?php
/**
 * functions.php - Helpers globaux
 */

function e(string $text): string {
    return Security::e($text);
}

function url(string $path = ''): string {
    return Router::url($path);
}

function asset(string $path): string {
    return APP_URL . '/assets/' . ltrim($path, '/');
}

function avatar(string $avatar): string {
    return asset('avatars/' . $avatar);
}

function old(string $key, string $default = ''): string {
    return e($_POST[$key] ?? $default);
}

function formatDate(string $date, string $format = 'd/m/Y'): string {
    if (!$date) return '';
    $d = new DateTime($date);
    return $d->format($format);
}

function formatDatetime(string $date): string {
    return formatDate($date, 'd/m/Y H:i');
}

function formatPrice(float $amount, string $currency = 'MAD'): string {
    return number_format($amount, 2, ',', ' ') . ' ' . $currency;
}

function truncate(string $text, int $length = 100, string $suffix = '...'): string {
    if (strlen($text) <= $length) return $text;
    return substr($text, 0, $length) . $suffix;
}

function slugify(string $text): string {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    return strtolower($text) ?: 'n-a';
}

function activeRoute(string $route): string {
    $current = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
    return $current === trim($route, '/') ? 'active' : '';
}

function containsRoute(string $route): string {
    $current = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
    return str_starts_with($current, trim($route, '/')) ? 'active' : '';
}

function timeAgo(string $date): string {
    $time = strtotime($date);
    $diff = time() - $time;
    
    if ($diff < 60) return 'À l\'instant';
    if ($diff < 3600) return 'Il y a ' . floor($diff / 60) . ' min';
    if ($diff < 86400) return 'Il y a ' . floor($diff / 3600) . ' h';
    if ($diff < 604800) return 'Il y a ' . floor($diff / 86400) . ' j';
    return formatDate($date);
}

function xpLevel(int $xp): int {
    return max(1, floor($xp / 1000) + 1);
}

function xpProgress(int $xp): int {
    $level = xpLevel($xp);
    $base = ($level - 1) * 1000;
    $next = $level * 1000;
    return min(100, round((($xp - $base) / ($next - $base)) * 100));
}

function pagination(array $paginator): string {
    if ($paginator['last_page'] <= 1) return '';
    
    $html = '<nav class="pagination-nav"><ul class="pagination">';
    $current = $paginator['current_page'];
    $last = $paginator['last_page'];
    
    $url = strtok($_SERVER['REQUEST_URI'], '?');
    $query = $_GET;
    
    // Previous
    $disabled = $current <= 1 ? ' disabled' : '';
    $html .= "<li class='page-item{$disabled}'><a class='page-link' href='" . buildPageUrl($url, $query, $current - 1) . "'>‹</a></li>";
    
    // Pages
    $start = max(1, $current - 2);
    $end = min($last, $current + 2);
    
    if ($start > 1) {
        $html .= "<li class='page-item'><a class='page-link' href='" . buildPageUrl($url, $query, 1) . "'>1</a></li>";
        if ($start > 2) $html .= "<li class='page-item disabled'><span>...</span></li>";
    }
    
    for ($i = $start; $i <= $end; $i++) {
        $active = $i === $current ? ' active' : '';
        $html .= "<li class='page-item{$active}'><a class='page-link' href='" . buildPageUrl($url, $query, $i) . "'>{$i}</a></li>";
    }
    
    if ($end < $last) {
        if ($end < $last - 1) $html .= "<li class='page-item disabled'><span>...</span></li>";
        $html .= "<li class='page-item'><a class='page-link' href='" . buildPageUrl($url, $query, $last) . "'>{$last}</a></li>";
    }
    
    // Next
    $disabled = $current >= $last ? ' disabled' : '';
    $html .= "<li class='page-item{$disabled}'><a class='page-link' href='" . buildPageUrl($url, $query, $current + 1) . "'>›</a></li>";
    
    $html .= '</ul></nav>';
    return $html;
}

function buildPageUrl(string $base, array $query, int $page): string {
    $query['page'] = $page;
    return $base . '?' . http_build_query($query);
}

function generateSeoMeta(array $data): array {
    $defaults = [
        'title' => 'ALOG Academy',
        'description' => 'Plateforme éducative premium pour les étudiants marocains. Cours, quiz, classements et préparation aux examens.',
        'image' => asset('images/og-default.jpg'),
        'url' => APP_URL . $_SERVER['REQUEST_URI'],
        'type' => 'website'
    ];
    return array_merge($defaults, $data);
}

function getRegions(): array {
    return [
        'Tanger-Tétouan-Al Hoceïma',
        'L\'Oriental',
        'Fès-Meknès',
        'Rabat-Salé-Kénitra',
        'Béni Mellal-Khénifra',
        'Casablanca-Settat',
        'Marrakech-Safi',
        'Drâa-Tafilalet',
        'Souss-Massa',
        'Guelmim-Oued Noun',
        'Laâyoune-Sakia El Hamra',
        'Dakhla-Oued Ed-Dahab'
    ];
}

function getAvatarOptions(): array {
    return [
        'avatar1.png' => 'Étudiant 1',
        'avatar2.png' => 'Étudiant 2',
        'avatar3.png' => 'Étudiant 3',
        'avatar4.png' => 'Étudiant 4',
        'avatar5.png' => 'Étudiant 5'
    ];
}
