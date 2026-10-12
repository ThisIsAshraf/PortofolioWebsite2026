<?php
// Update these values with the MySQL details shown in your InfinityFree control panel.
// Do not upload this file to a public code repository.
const DB_HOST = 'localhost';
const DB_NAME = 'updateportfolio';
const DB_USER = 'root';
const DB_PASS = '';
const SITE_BASE_PATH = ''; // Usually blank when installed directly in htdocs.

if (session_status() !== PHP_SESSION_ACTIVE) {
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    session_set_cookie_params([
        'httponly' => true,
        'secure' => $isHttps,
        'samesite' => 'Lax',
    ]);
    session_start();
}

function db(): PDO {
    static $pdo = null;
    if ($pdo instanceof PDO) return $pdo;
    if (DB_HOST === 'YOUR_MYSQL_HOST' || DB_NAME === 'YOUR_DATABASE_NAME') {
        http_response_code(500);
        exit('Database is not configured. Update config.php using your hosting control panel credentials.');
    }
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    return $pdo;
}

function e(?string $value): string { return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function url(string $path = ''): string { return rtrim(SITE_BASE_PATH, '/') . '/' . ltrim($path, '/'); }
function redirect(string $path): never { header('Location: ' . url($path)); exit; }
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf_token'];
}
function verify_csrf(): void {
    $sent = $_POST['csrf_token'] ?? '';
    if (!is_string($sent) || !hash_equals(csrf_token(), $sent)) {
        http_response_code(419); exit('Your form session expired. Please go back, refresh the page, and try again.');
    }
}
function flash(string $type, string $message): void { $_SESSION['flash'][] = ['type'=>$type, 'message'=>$message]; }
function take_flashes(): array { $items = $_SESSION['flash'] ?? []; unset($_SESSION['flash']); return $items; }
function require_admin(): void { if (empty($_SESSION['admin_id'])) { flash('error', 'Please sign in to access the admin panel.'); redirect('admin/login.php'); } }
function setting(string $key, string $default = ''): string {
    static $settings = null;
    if ($settings === null) {
        $settings = [];
        try { foreach (db()->query('SELECT setting_key, setting_value FROM site_settings') as $row) $settings[$row['setting_key']] = $row['setting_value']; }
        catch (Throwable $e) { return $default; }
    }
    return (string)($settings[$key] ?? $default);
}
function set_setting(string $key, string $value): void {
    $stmt = db()->prepare('INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value)');
    $stmt->execute([$key, $value]);
}
function preview_text(string $value, int $limit = 65): string {
    $chars = preg_split('//u', $value, -1, PREG_SPLIT_NO_EMPTY);
    if (!is_array($chars)) return substr($value, 0, $limit) . (strlen($value) > $limit ? '…' : '');
    return count($chars) > $limit ? implode('', array_slice($chars, 0, $limit - 1)) . '…' : $value;
}
function parse_json_list(?string $value): array {
    $decoded = json_decode((string)$value, true);
    if (is_array($decoded)) return $decoded;
    return array_values(array_filter(array_map('trim', explode(',', (string)$value))));
}
function content_config(): array {
    return [
        'skills' => ['title'=>'Skills','table'=>'skills','fields'=>[
            'name'=>['Skill name','text',true], 'group_name'=>['Category','text',true], 'level'=>['Level / proficiency note','text',true], 'sort_order'=>['Display order','number',true]], 'columns'=>['name','group_name','level']],
        'experience' => ['title'=>'Work Experience','table'=>'experience','fields'=>[
            'role'=>['Job title','text',true], 'company'=>['Company','text',true], 'period'=>['Date range','text',true], 'location'=>['Location / work mode','text',false], 'description'=>['Responsibilities (one per line)','textarea',true], 'sort_order'=>['Display order','number',true]], 'columns'=>['role','company','period']],
        'projects' => ['title'=>'Projects','table'=>'projects','fields'=>[
            'title'=>['Project title','text',true], 'project_type'=>['Category / type','text',true], 'description'=>['Description','textarea',true], 'tags'=>['Tags (comma separated)','text',false], 'sort_order'=>['Display order','number',true]], 'columns'=>['title','project_type','description']],
        'education' => ['title'=>'Education','table'=>'education','fields'=>[
            'degree'=>['Degree / qualification','text',true], 'institution'=>['Institution','text',true], 'period'=>['Date range','text',true], 'detail'=>['Additional details','text',false], 'sort_order'=>['Display order','number',true]], 'columns'=>['degree','institution','period']],
    ];
}
