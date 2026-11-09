<?php

declare(strict_types=1);

http_response_code(403);

// Safe helper fallbacks in case config.php isn't required directly
$homeUrl = '/';
if (file_exists(__DIR__ . '/config.php')) {
    require_once __DIR__ . '/config.php';
    if (function_exists('url')) {
        $homeUrl = url('');
    }
}

$clientIp = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
    $clientIp = $_SERVER['HTTP_CF_CONNECTING_IP'];
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $clientIp = trim(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0]);
}

$traceId = strtoupper(bin2hex(random_bytes(8)));
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 — Access Forbidden</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&family=JetBrains+Mono:wght@400;500;600&family=Space+Grotesk:wght@600;700;800&display=swap" rel="stylesheet">
    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background-color: #060911;
            color: #f1f5f9;
            font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 24px;
            overflow-x: hidden;
            position: relative;
        }

        /* Ambient background glow */
        body::before {
            content: '';
            position: absolute;
            width: 420px;
            height: 420px;
            background: radial-gradient(circle, rgba(239, 68, 68, 0.08) 0%, rgba(6, 9, 17, 0) 70%);
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 0;
            pointer-events: none;
        }

        .security-container {
            position: relative;
            z-index: 1;
            max-width: 540px;
            width: 100%;
            background: #0d121f;
            border: 1px solid #1e293b;
            border-radius: 18px;
            padding: 44px 38px;
            box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.7), 0 0 0 1px rgba(255, 255, 255, 0.03);
            text-align: center;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.25);
            color: #f87171;
            padding: 5px 14px;
            border-radius: 9999px;
            font-size: 11.5px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            margin-bottom: 22px;
        }

        .status-badge::before {
            content: '';
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #ef4444;
            box-shadow: 0 0 8px #ef4444;
        }

        h1 {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 28px;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: -0.02em;
            margin-bottom: 12px;
            line-height: 1.2;
        }

        p.lead-copy {
            color: #94a3b8;
            font-size: 13.5px;
            line-height: 1.6;
            margin-bottom: 26px;
        }

        /* Telemetry Diagnostic Block */
        .telemetry-card {
            background: #080b14;
            border: 1px solid #172033;
            border-radius: 10px;
            padding: 14px 18px;
            margin-bottom: 30px;
            text-align: left;
            font-family: 'JetBrains Mono', monospace;
            font-size: 11.5px;
            color: #64748b;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px 16px;
        }

        .telemetry-card span {
            color: #94a3b8;
        }

        .telemetry-card strong {
            color: #cbd5e1;
            font-weight: 500;
        }

        .telemetry-card .full-width {
            grid-column: 1 / -1;
            border-top: 1px solid #141b2d;
            padding-top: 8px;
            margin-top: 2px;
        }

        .actions {
            display: flex;
            justify-content: center;
        }

        .btn-home {
            background: #ffffff;
            color: #090d16;
            font-weight: 700;
            font-size: 13px;
            padding: 12px 28px;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: none;
        }

        .btn-home:hover {
            background: #e2e8f0;
            transform: translateY(-1px);
            box-shadow: 0 4px 14px rgba(255, 255, 255, 0.15);
        }

        .brand-footer {
            margin-top: 32px;
            padding-top: 18px;
            border-top: 1px solid #172033;
            font-size: 11px;
            color: #475569;
            letter-spacing: 0.02em;
        }
    </style>
</head>

<body>
    <div class="security-container">
        <div class="status-badge">
            Security Policy Violation
        </div>

        <h1>403 Forbidden Access</h1>
        <p class="lead-copy">
            The requested object, configuration file, or system path is restricted by server-level access control policies. Direct public queries to this endpoint are strictly prohibited.
        </p>

        <div class="telemetry-card">
            <div><span>Status:</span> <strong>HTTP 403 Denied</strong></div>
            <div><span>Trace ID:</span> <strong><?= htmlspecialchars($traceId, ENT_QUOTES, 'UTF-8') ?></strong></div>
            <div><span>Client IP:</span> <strong><?= htmlspecialchars($clientIp, ENT_QUOTES, 'UTF-8') ?></strong></div>
            <div><span>Protocol:</span> <strong><?= htmlspecialchars($_SERVER['SERVER_PROTOCOL'] ?? 'HTTP/1.1', ENT_QUOTES, 'UTF-8') ?></strong></div>
            <div class="full-width"><span>Incident Disposition:</span> <strong style="color: #f87171;">PACKET_INTERCEPT_LOGGED</strong></div>
        </div>

        <div class="actions">
            <a href="<?= htmlspecialchars($homeUrl, ENT_QUOTES, 'UTF-8') ?>" class="btn-home">
                &larr; Return to Safe Area
            </a>
        </div>

        <div class="brand-footer">
            Automated Gateway Guard &middot; Security Perimeter Enforced
        </div>
    </div>
</body>

</html>