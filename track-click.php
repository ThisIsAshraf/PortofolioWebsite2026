<?php

declare(strict_types=1);

require_once __DIR__ . '/config.php';

header('Content-Type: application/json; charset=utf-8');

$input = json_decode(file_get_contents('php://input'), true);
if (!$input || empty($input['target_url'])) {
    http_response_code(400);
    echo json_encode(['status' => 'ignored']);
    exit;
}

$ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
$sessionId = session_id();
$target = trim((string)$input['target_url']);
$text = trim((string)($input['element_text'] ?? ''));
$elementId = trim((string)($input['element_id'] ?? ''));

try {
    $stmt = db()->prepare('INSERT INTO click_logs (session_id, ip_address, element_id, element_text, target_url) VALUES (?, ?, ?, ?, ?)');
    $stmt->execute([$sessionId, $ip, $elementId ?: null, $text ?: null, $target]);
    echo json_encode(['status' => 'recorded']);
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error']);
}
