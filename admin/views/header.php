<?php

declare(strict_types=1);
/**
 * @var string $title
 * @var string $page
 * @var array $flashes
 */
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?= e($title) ?> · Portfolio Admin</title>
    <link rel="stylesheet" href="<?= e(url('assets/css/style.css')) ?>">
    <link rel="stylesheet" href="<?= e(url('assets/css/admin.css')) ?>">
</head>

<body class="admin-body">
    <div class="admin-shell">
        <aside class="admin-sidebar">
            <a class="brand" href="<?= e(url('admin/')) ?>"><span class="brand-mark">S.</span><span>SHIHAB<span class="brand-dot">/</span></span></a>
            <p class="sidebar-label">WORKSPACE</p>
            <nav class="admin-nav">
                <a class="<?= $page === 'dashboard' ? 'active' : '' ?>" href="<?= e(url('admin/')) ?>">▦ &nbsp; Dashboard</a>
                <a class="<?= $page === 'profile' ? 'active' : '' ?>" href="<?= e(url('admin/profile')) ?>">◉ &nbsp; Profile &amp; Photo</a>
                <a class="<?= ($page === 'content' && ($_GET['type'] ?? '') === 'skills') ? 'active' : '' ?>" href="<?= e(url('admin/content/skills')) ?>">✳ &nbsp; Skills</a>
                <a class="<?= ($page === 'content' && ($_GET['type'] ?? '') === 'experience') ? 'active' : '' ?>" href="<?= e(url('admin/content/experience')) ?>">▤ &nbsp; Experience</a>
                <a class="<?= ($page === 'content' && ($_GET['type'] ?? '') === 'projects') ? 'active' : '' ?>" href="<?= e(url('admin/content/projects')) ?>">◇ &nbsp; Projects</a>
                <a class="<?= ($page === 'content' && ($_GET['type'] ?? '') === 'education') ? 'active' : '' ?>" href="<?= e(url('admin/content/education')) ?>">▣ &nbsp; Education</a>
                <a class="<?= $page === 'messages' ? 'active' : '' ?>" href="<?= e(url('admin/messages')) ?>">✉ &nbsp; Messages</a>
                <a class="<?= $page === 'smtp' ? 'active' : '' ?>" href="<?= e(url('admin/smtp')) ?>">⚙ &nbsp; SMTP &amp; Mail</a>
                <a class="<?= ($page ?? '') === 'cv' ? 'active' : '' ?>" href="<?= e(url('admin/cv')) ?>">
                    📄 &nbsp; CV Templates
                </a>
                <a class="<?= $page === 'settings' ? 'active' : '' ?>" href="<?= e(url('admin/settings')) ?>">🎛 &nbsp; Site Settings</a>
                <a class="<?= ($page ?? '') === 'analytics' ? 'active' : '' ?>" href="<?= e(url('admin/analytics')) ?>">
                    📊 &nbsp; Site Analytics
                </a>
            </nav>
            <div class="sidebar-bottom">
                <a href="<?= e(url('')) ?>" target="_blank" rel="noopener">↗ View live website</a>
                <a href="<?= e(url('download-cv.php')) ?>" target="_blank">📄 Download CV (PDF)</a>
                <form method="post" action="<?= e(url('admin/')) ?>">
                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                    <input type="hidden" name="action" value="logout">
                    <button type="submit">↪ Sign out</button>
                </form>
            </div>
        </aside>
        <main class="admin-main">
            <header class="admin-topbar">
                <div>
                    <span class="admin-top-kicker">CONTROL CENTER</span>
                    <h1><?= e($title) ?></h1>
                </div>
                <span class="admin-user">● &nbsp; <?= e($_SESSION['admin_username'] ?? 'Admin') ?></span>
            </header>
            <section class="admin-content">
                <?php foreach ($flashes as $f): ?>
                    <div class="flash flash-<?= e($f['type']) ?>" role="status"><?= e($f['message']) ?></div>
                <?php endforeach; ?>