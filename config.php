<?php

declare(strict_types=1);

// 1. Load Composer Autoloader
$composerAutoload = __DIR__ . '/vendor/autoload.php';
if (file_exists($composerAutoload)) {
    require_once $composerAutoload;
}

// 2. Load Environment Variables via vlucas/phpdotenv
if (class_exists(\Dotenv\Dotenv::class) && file_exists(__DIR__ . '/.env')) {
    $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->safeLoad();
}

// 3. Environment & Error Reporting
$appEnv = $_ENV['APP_ENV'] ?? 'production';
if ($appEnv === 'development') {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(0);
}

// 4. Session Security (Strict cookies & protection)
if (session_status() !== PHP_SESSION_ACTIVE) {
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');

    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'domain'   => '',
        'secure'   => $isHttps,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

// 5. Database Connection (PDO)
function db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $host = $_ENV['DB_HOST'] ?? 'localhost';
    $name = $_ENV['DB_NAME'] ?? '';
    $user = $_ENV['DB_USER'] ?? 'root';
    $pass = $_ENV['DB_PASS'] ?? '';

    if (empty($name)) {
        http_response_code(500);
        exit('Database configuration missing. Please verify your .env file.');
    }

    $dsn = "mysql:host={$host};dbname={$name};charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
        $pdo = new PDO($dsn, $user, $pass, $options);
        return $pdo;
    } catch (PDOException $e) {
        if (($_ENV['APP_ENV'] ?? 'production') === 'development') {
            exit('Database Connection Error: ' . $e->getMessage());
        }
        http_response_code(500);
        exit('Unable to connect to database. Please check back shortly.');
    }
}

// 6. Automatic URL Base Detection (Fixes Local /portfolio/ vs InfinityFree root)
function url(string $path = ''): string
{
    $envPath = $_ENV['SITE_BASE_PATH'] ?? null;
    if ($envPath !== null && $envPath !== '') {
        $base = rtrim($envPath, '/');
    } else {
        // Auto-detect subdirectory based on document root vs current directory
        $docRoot = realpath($_SERVER['DOCUMENT_ROOT'] ?? '') ?: '';
        $dir = realpath(__DIR__) ?: '';
        $docRoot = str_replace('\\', '/', $docRoot);
        $dir = str_replace('\\', '/', $dir);

        if ($docRoot !== '' && str_starts_with($dir, $docRoot)) {
            $base = substr($dir, strlen($docRoot));
        } else {
            $base = '';
        }
    }

    $base = '/' . trim($base, '/');
    $normalized = ($base === '/') ? '' : $base;

    return $normalized . '/' . ltrim($path, '/');
}

function redirect(string $path): never
{
    header('Location: ' . url($path));
    exit;
}

// 7. Security Helpers (XSS, CSRF)
function e(?string $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf(): void
{
    $sent = $_POST['csrf_token'] ?? '';
    if (!is_string($sent) || !hash_equals(csrf_token(), $sent)) {
        http_response_code(419);
        exit('Session expired or invalid submission token. Please reload and try again.');
    }
}

// 8. Session Flash Messages
function flash(string $type, string $message): void
{
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

function take_flashes(): array
{
    $items = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $items;
}

function require_admin(): void
{
    if (empty($_SESSION['admin_id'])) {
        flash('error', 'Please sign in to access the admin dashboard.');
        redirect('admin/login.php');
    }
}

// 9. Site Settings Retrieval & Update
function setting(string $key, string $default = ''): string
{
    static $settings = null;
    if ($settings === null) {
        $settings = [];
        try {
            foreach (db()->query('SELECT setting_key, setting_value FROM site_settings') as $row) {
                $settings[$row['setting_key']] = $row['setting_value'];
            }
        } catch (Throwable $e) {
            return $default;
        }
    }
    return (string)($settings[$key] ?? $default);
}

function set_setting(string $key, string $value): void
{
    $stmt = db()->prepare('INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value)');
    $stmt->execute([$key, $value]);
}

// 10. Data Parsers
function parse_json_list(?string $value): array
{
    if (empty($value)) return [];
    $decoded = json_decode((string)$value, true);
    if (is_array($decoded)) {
        return $decoded;
    }
    return array_values(array_filter(array_map('trim', explode("\n", (string)$value))));
}

function preview_text(string $value, int $limit = 65): string
{
    $chars = preg_split('//u', $value, -1, PREG_SPLIT_NO_EMPTY);
    if (!is_array($chars)) return substr($value, 0, $limit) . (strlen($value) > $limit ? '…' : '');
    return count($chars) > $limit ? implode('', array_slice($chars, 0, $limit - 1)) . '…' : $value;
}

// 11. Schema Configurations for Admin CRUD
function content_config(): array
{
    return [
        'skills' => [
            'title' => 'Skills',
            'table' => 'skills',
            'fields' => [
                'name' => ['Skill name', 'text', true],
                'group_name' => ['Category', 'text', true],
                'level' => ['Level / proficiency note', 'text', true],
                'sort_order' => ['Display order', 'number', true]
            ],
            'columns' => ['name', 'group_name', 'level']
        ],
        'experience' => [
            'title' => 'Work Experience',
            'table' => 'experience',
            'fields' => [
                'role' => ['Job title', 'text', true],
                'company' => ['Company', 'text', true],
                'period' => ['Date range', 'text', true],
                'location' => ['Location / work mode', 'text', false],
                'description' => ['Responsibilities (one per line or JSON)', 'textarea', true],
                'sort_order' => ['Display order', 'number', true]
            ],
            'columns' => ['role', 'company', 'period']
        ],
        'projects' => [
            'title' => 'Projects',
            'table' => 'projects',
            'fields' => [
                'title' => ['Project title', 'text', true],
                'project_type' => ['Category / type', 'text', true],
                'description' => ['Description', 'textarea', true],
                'tags' => ['Tags (comma separated or JSON)', 'text', false],
                'sort_order' => ['Display order', 'number', true]
            ],
            'columns' => ['title', 'project_type', 'description']
        ],
        'education' => [
            'title' => 'Education',
            'table' => 'education',
            'fields' => [
                'degree' => ['Degree / qualification', 'text', true],
                'institution' => ['Institution', 'text', true],
                'period' => ['Date range', 'text', true],
                'detail' => ['Additional details', 'text', false],
                'sort_order' => ['Display order', 'number', true]
            ],
            'columns' => ['degree', 'institution', 'period']
        ],
    ];
}

// Retrieve Active SMTP Configuration
function get_smtp_config(): array
{
    static $smtp = null;
    if ($smtp !== null) return $smtp;

    try {
        $stmt = db()->query('SELECT * FROM smtp_settings WHERE id = 1 LIMIT 1');
        $row = $stmt->fetch();
        if ($row && !empty($row['host'])) {
            $smtp = $row;
            return $smtp;
        }
    } catch (Throwable $e) {
        // Fallback to .env below if table not yet migrated
    }

    $smtp = [
        'host'       => $_ENV['SMTP_HOST'] ?? '',
        'port'       => (int)($_ENV['SMTP_PORT'] ?? 587),
        'encryption' => $_ENV['SMTP_SECURE'] ?? 'tls',
        'username'   => $_ENV['SMTP_USER'] ?? '',
        'password'   => $_ENV['SMTP_PASS'] ?? '',
        'from_email' => $_ENV['SMTP_USER'] ?? '',
        'from_name'  => $_ENV['MAIL_FROM_NAME'] ?? 'Portfolio Contact',
        'is_active'  => 1
    ];

    return $smtp;
}

// Save SMTP Configuration to DB
function save_smtp_config(array $data): void
{
    $stmt = db()->prepare('
        INSERT INTO smtp_settings (id, host, port, encryption, username, password, from_email, from_name, is_active)
        VALUES (1, :host, :port, :encryption, :username, :password, :from_email, :from_name, :is_active)
        ON DUPLICATE KEY UPDATE
            host = VALUES(host),
            port = VALUES(port),
            encryption = VALUES(encryption),
            username = VALUES(username),
            password = IF(VALUES(password) = "", password, VALUES(password)),
            from_email = VALUES(from_email),
            from_name = VALUES(from_name),
            is_active = VALUES(is_active)
    ');

    $stmt->execute([
        ':host'       => trim($data['host']),
        ':port'       => (int)$data['port'],
        ':encryption' => in_array($data['encryption'], ['tls', 'ssl', 'none'], true) ? $data['encryption'] : 'tls',
        ':username'   => trim($data['username']),
        ':password'   => trim($data['password']),
        ':from_email' => trim($data['from_email']),
        ':from_name'  => trim($data['from_name']),
        ':is_active'  => !empty($data['is_active']) ? 1 : 0
    ]);
}
