<?php

declare(strict_types=1);
/**
 * @var array $stats
 * @var array $chartData
 * @var array $recentLogs
 * @var array $devices
 * @var array $browsers
 * @var array $pages
 * @var array $clicks
 * @var string $period
 * @var string $deviceFilter
 * @var string $start
 * @var string $end
 * @var string $filterQuery
 */

// Dynamic Graph Ceiling
$chartWidth    = 1000;
$chartHeight   = 280;
$paddingTop    = 35;
$paddingBottom = 45;
$paddingLeft   = 50;
$paddingRight  = 25;

$plotWidth  = $chartWidth - $paddingLeft - $paddingRight;
$plotHeight = $chartHeight - $paddingTop - $paddingBottom;

$peak = 0;
foreach ($chartData as $pt) {
    if ($pt['views'] > $peak) $peak = $pt['views'];
    if ($pt['uniques'] > $peak) $peak = $pt['uniques'];
}

if ($peak <= 5) {
    $maxVal = 5;
} elseif ($peak <= 20) {
    $maxVal = (int)(ceil($peak / 5) * 5);
} elseif ($peak <= 100) {
    $maxVal = (int)(ceil($peak / 10) * 10);
} else {
    $maxVal = (int)(ceil($peak / 50) * 50);
}

$count = count($chartData);
$stepX = $count > 1 ? ($plotWidth / ($count - 1)) : $plotWidth;

$viewCoords = [];
$uniqueCoords = [];

foreach ($chartData as $idx => $pt) {
    $x = $paddingLeft + ($idx * $stepX);
    $yView = $paddingTop + $plotHeight - (($pt['views'] / $maxVal) * $plotHeight);
    $yUnique = $paddingTop + $plotHeight - (($pt['uniques'] / $maxVal) * $plotHeight);
    $viewCoords[] = ['x' => $x, 'y' => $yView, 'data' => $pt];
    $uniqueCoords[] = ['x' => $x, 'y' => $yUnique, 'data' => $pt];
}

$viewPolyline = implode(' ', array_map(fn($c) => "{$c['x']},{$c['y']}", $viewCoords));
$uniquePolyline = implode(' ', array_map(fn($c) => "{$c['x']},{$c['y']}", $uniqueCoords));

$baselineY = $paddingTop + $plotHeight;
$viewArea = "{$paddingLeft},{$baselineY} " . $viewPolyline . " " . ($paddingLeft + max(0, $count - 1) * $stepX) . ",{$baselineY}";

$labelModulo = max(1, (int)ceil($count / 8));
?>

<!-- Filter Toolbar -->
<div class="admin-panel" style="margin-top: 0; margin-bottom: 22px; padding: 16px 20px;">
    <form method="get" action="<?= e(url('admin/analytics')) ?>" style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 14px;">
        <!-- Left: Quick Filters & Device Selector -->
        <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 10px;">
            <div style="display: inline-flex; background: #e2e8f0; border-radius: 8px; padding: 3px;">
                <a href="<?= e(url('admin/analytics?period=today&device=' . urlencode($deviceFilter))) ?>"
                    style="padding: 6px 14px; font-size: 11.5px; text-decoration: none; border-radius: 6px; <?= $period === 'today' ? 'background: #0f172a; color: #fff; font-weight: 700;' : 'color: #475569;' ?>">
                    Today
                </a>
                <a href="<?= e(url('admin/analytics?period=7d&device=' . urlencode($deviceFilter))) ?>"
                    style="padding: 6px 14px; font-size: 11.5px; text-decoration: none; border-radius: 6px; <?= $period === '7d' ? 'background: #0f172a; color: #fff; font-weight: 700;' : 'color: #475569;' ?>">
                    7 Days
                </a>
                <a href="<?= e(url('admin/analytics?period=30d&device=' . urlencode($deviceFilter))) ?>"
                    style="padding: 6px 14px; font-size: 11.5px; text-decoration: none; border-radius: 6px; <?= $period === '30d' ? 'background: #0f172a; color: #fff; font-weight: 700;' : 'color: #475569;' ?>">
                    30 Days
                </a>
                <a href="<?= e(url('admin/analytics?period=90d&device=' . urlencode($deviceFilter))) ?>"
                    style="padding: 6px 14px; font-size: 11.5px; text-decoration: none; border-radius: 6px; <?= $period === '90d' ? 'background: #0f172a; color: #fff; font-weight: 700;' : 'color: #475569;' ?>">
                    90 Days
                </a>
            </div>

            <select name="device" onchange="this.form.submit()" style="background: #ffffff; border: 1px solid #cbd5e1; color: #0f172a; border-radius: 8px; padding: 7px 12px; font-size: 12px; cursor: pointer;">
                <option value="all" <?= $deviceFilter === 'all' ? 'selected' : '' ?>>All Devices</option>
                <option value="Desktop" <?= $deviceFilter === 'Desktop' ? 'selected' : '' ?>>Desktop</option>
                <option value="Mobile" <?= $deviceFilter === 'Mobile' ? 'selected' : '' ?>>Mobile</option>
                <option value="Tablet" <?= $deviceFilter === 'Tablet' ? 'selected' : '' ?>>Tablet</option>
            </select>
        </div>

        <!-- Right: Date Range Inputs & High-Contrast Buttons -->
        <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 8px;">
            <input type="hidden" name="period" value="custom">
            <input type="date" name="start_date" value="<?= e($start) ?>" style="background: #ffffff; border: 1px solid #cbd5e1; color: #0f172a; border-radius: 7px; padding: 6px 10px; font-size: 12px;">
            <span style="color: #64748b; font-size: 11px;">to</span>
            <input type="date" name="end_date" value="<?= e($end) ?>" style="background: #ffffff; border: 1px solid #cbd5e1; color: #0f172a; border-radius: 7px; padding: 6px 10px; font-size: 12px;">

            <button type="submit" style="background: #0f172a; border: none; color: #ffffff; padding: 7px 15px; font-size: 12px; border-radius: 7px; cursor: pointer; font-weight: 600;">
                Apply Filter
            </button>

            <a href="<?= e(url('admin/analytics?' . $filterQuery)) ?>" style="background: #ffffff; border: 1px solid #cbd5e1; color: #334155; padding: 7px 14px; font-size: 12px; border-radius: 7px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 4px;" title="Reload dataset">
                🔄 Refresh
            </a>

            <a href="<?= e(url('admin/analytics?export=csv&' . $filterQuery)) ?>" style="background: #16a34a; border: none; color: #ffffff; padding: 7px 15px; font-size: 12px; border-radius: 7px; text-decoration: none; font-weight: 700;" target="_blank">
                📥 Export CSV
            </a>
        </div>
    </form>
</div>

<!-- KPI Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <span>Page Views</span>
        <strong><?= number_format($stats['total_views']) ?></strong>
        <small><?= e($start) ?> to <?= e($end) ?></small>
    </div>
    <div class="stat-card">
        <span>Unique Visitors</span>
        <strong><?= number_format($stats['unique_visitors']) ?></strong>
        <small>Distinct IP footprints</small>
    </div>
    <div class="stat-card">
        <span>Active Right Now</span>
        <strong style="color: #16a34a;">● <?= number_format($stats['active_visitors']) ?></strong>
        <small>Active in last 5 min</small>
    </div>
    <div class="stat-card">
        <span>Tracked Link Clicks</span>
        <strong><?= number_format($stats['total_clicks']) ?></strong>
        <small>Buttons, CV, Socials</small>
    </div>
</div>

<!-- Traffic Graph -->
<div class="admin-panel" style="margin-top: 0; margin-bottom: 22px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 0.75rem;">
        <div>
            <h2>Traffic Telemetry (<?= (int)$count ?> Days)</h2>
            <p class="panel-hint" style="margin: 0;">Showing volume from <?= e(date('M j, Y', strtotime($start))) ?> to <?= e(date('M j, Y', strtotime($end))) ?> (<?= e(ucfirst($deviceFilter)) ?> traffic).</p>
        </div>
        <div style="display: flex; align-items: center; gap: 1.25rem; font-size: 0.85rem;">
            <span style="display: inline-flex; align-items: center; gap: 6px; color: #334155;">
                <span style="width: 12px; height: 12px; background: #84cc16; border-radius: 3px; display: inline-block;"></span>
                <strong>Page Views</strong>
            </span>
            <span style="display: inline-flex; align-items: center; gap: 6px; color: #334155;">
                <span style="width: 12px; height: 12px; background: #0284c7; border-radius: 3px; display: inline-block;"></span>
                <strong>Unique Visitors</strong>
            </span>
        </div>
    </div>

    <div style="position: relative; width: 100%; overflow-x: auto; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 10px 0;">
        <svg viewBox="0 0 <?= $chartWidth ?> <?= $chartHeight ?>" style="width: 100%; height: auto; min-width: 650px; display: block; font-family: monospace;">
            <defs>
                <linearGradient id="viewsFillLight" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" stop-color="#84cc16" stop-opacity="0.35" />
                    <stop offset="100%" stop-color="#84cc16" stop-opacity="0.0" />
                </linearGradient>
            </defs>

            <!-- Gridlines & Y-Axis Scale Values -->
            <?php for ($gridStep = 0; $gridStep <= 4; $gridStep++):
                $yVal = ($maxVal / 4) * $gridStep;
                $lineY = $paddingTop + $plotHeight - (($yVal / $maxVal) * $plotHeight);
            ?>
                <line x1="<?= $paddingLeft ?>" y1="<?= $lineY ?>" x2="<?= $chartWidth - $paddingRight ?>" y2="<?= $lineY ?>" stroke="#e2e8f0" stroke-dasharray="3 3" />
                <text x="<?= $paddingLeft - 10 ?>" y="<?= $lineY + 4 ?>" text-anchor="end" fill="#64748b" font-size="11"><?= (int)round($yVal) ?></text>
            <?php endfor; ?>

            <!-- Filled Area for Page Views -->
            <polygon points="<?= $viewArea ?>" fill="url(#viewsFillLight)" />

            <!-- Unique Visitors Line (Blue) -->
            <polyline fill="none" stroke="#0284c7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" points="<?= $uniquePolyline ?>" />

            <!-- Page Views Line (Green) -->
            <polyline fill="none" stroke="#65a30d" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" points="<?= $viewPolyline ?>" />

            <!-- Interactive Data Nodes & Tooltips -->
            <?php foreach ($viewCoords as $i => $pt): ?>
                <circle cx="<?= $pt['x'] ?>" cy="<?= $pt['y'] ?>" r="4.5" fill="#65a30d" stroke="#ffffff" stroke-width="2">
                    <title><?= e((string)$pt['data']['label']) ?>: <?= (int)$pt['data']['views'] ?> views, <?= (int)$pt['data']['uniques'] ?> uniques</title>
                </circle>
            <?php endforeach; ?>

            <!-- X-Axis Date Ticks -->
            <?php foreach ($chartData as $i => $pt): ?>
                <?php if ($i % $labelModulo === 0 || $i === $count - 1): ?>
                    <text x="<?= $paddingLeft + ($i * $stepX) ?>" y="<?= $chartHeight - 12 ?>" text-anchor="middle" fill="#64748b" font-size="11">
                        <?= e((string)$pt['label']) ?>
                    </text>
                <?php endif; ?>
            <?php endforeach; ?>
        </svg>
    </div>
</div>

<!-- Breakdown Columns -->
<div class="dashboard-columns">
    <!-- Top Pages -->
    <div class="admin-panel">
        <h2>Top Visited Pages</h2>
        <div class="table-scroll">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Page URL</th>
                        <th>Visits</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pages as $p): ?>
                        <tr>
                            <td><code><?= e((string)$p['page_url']) ?></code></td>
                            <td><strong style="color: #15803d;"><?= (int)$p['visits'] ?></strong></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$pages): ?>
                        <tr>
                            <td colspan="2" style="color: #94a3b8; text-align: center; padding: 16px;">No tracking records for this filter period.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Top Link Clicks -->
    <div class="admin-panel">
        <h2>Top Link &amp; Button Clicks</h2>
        <div class="table-scroll">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Element / Action</th>
                        <th>Target URL</th>
                        <th>Clicks</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($clicks as $c): ?>
                        <tr>
                            <td><strong><?= e((string)($c['element_text'] ?: 'Click')) ?></strong></td>
                            <td><small><?= e(preview_text((string)$c['target_url'], 32)) ?></small></td>
                            <td><strong style="color: #0284c7;"><?= (int)$c['total_clicks'] ?></strong></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$clicks): ?>
                        <tr>
                            <td colspan="3" style="color: #94a3b8; text-align: center; padding: 16px;">No click events logged yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="dashboard-columns" style="margin-top: 20px;">
    <div class="admin-panel">
        <h2>Devices (Filtered)</h2>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Device Type</th>
                    <th>Share</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($devices as $d): ?>
                    <tr>
                        <td><?= e((string)$d['device_type']) ?></td>
                        <td><strong><?= (int)$d['cnt'] ?></strong></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$devices): ?>
                    <tr>
                        <td colspan="2" style="color: #94a3b8; text-align: center; padding: 16px;">No records.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="admin-panel">
        <h2>Browsers (Filtered)</h2>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Browser</th>
                    <th>Visitors</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($browsers as $b): ?>
                    <tr>
                        <td><?= e((string)$b['browser']) ?></td>
                        <td><strong><?= (int)$b['cnt'] ?></strong></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$browsers): ?>
                    <tr>
                        <td colspan="2" style="color: #94a3b8; text-align: center; padding: 16px;">No records.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Live Telemetry Stream with Clear Records Action -->
<div class="admin-panel" style="margin-top: 20px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.75rem;">
        <div>
            <h2>Visitor Audit Log (Filtered Period)</h2>
            <p class="panel-hint" style="margin: 0;">Detailed log of recent visits within the selected time window.</p>
        </div>

        <form method="post" action="<?= e(url('admin/analytics')) ?>" onsubmit="return confirm('⚠️ Are you sure you want to permanently clear ALL visitor and click telemetry records? This cannot be undone.')">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="action" value="clear_analytics">
            <button type="submit" style="background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; padding: 7px 15px; font-size: 11.5px; border-radius: 7px; cursor: pointer; font-weight: 600;">
                🗑 Clear All Records
            </button>
        </form>
    </div>

    <div class="table-scroll">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Timestamp</th>
                    <th>IP Address</th>
                    <th>Device</th>
                    <th>Platform</th>
                    <th>Browser</th>
                    <th>Location</th>
                    <th>Target Page</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recentLogs as $log): ?>
                    <tr>
                        <td><small><?= e(date('M j, H:i:s', strtotime((string)$log['created_at']))) ?></small></td>
                        <td><code><?= e((string)$log['ip_address']) ?></code></td>
                        <td><?= e((string)$log['device_type']) ?></td>
                        <td><?= e((string)$log['platform']) ?></td>
                        <td><?= e((string)$log['browser']) ?></td>
                        <td><small><?= e((string)$log['country']) ?></small></td>
                        <td><code><?= e((string)$log['page_url']) ?></code></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$recentLogs): ?>
                    <tr>
                        <td colspan="7" style="color: #94a3b8; text-align: center; padding: 24px;">No visitor traffic captured for this filter selection.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>