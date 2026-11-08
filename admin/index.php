<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/config.php';
require_admin();

$pdo = db();
$page = (string)($_GET['page'] ?? 'dashboard');

// -----------------------------------------------------------------------------
// 1. Session & Auth Handlers
// -----------------------------------------------------------------------------
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
    redirect('admin/login');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = (string)($_POST['action'] ?? '');

    if ($action === 'logout') {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
        redirect('admin/login');
    }

    // -------------------------------------------------------------------------
    // Profile & Photo Update
    // -------------------------------------------------------------------------
    if ($action === 'save_profile') {
        $keys = ['name', 'role', 'location', 'headline', 'intro', 'linkedin', 'email', 'phone'];
        $values = [];
        foreach ($keys as $key) {
            $values[$key] = trim((string)($_POST[$key] ?? ''));
        }

        if ($values['name'] === '' || $values['role'] === '') {
            flash('error', 'Name and professional title are required.');
            redirect('admin/profile');
        }

        if ($values['email'] !== '' && !filter_var($values['email'], FILTER_VALIDATE_EMAIL)) {
            flash('error', 'Please enter a valid email address.');
            redirect('admin/profile');
        }

        foreach ($values as $key => $value) {
            set_setting($key, $value);
        }

        if (!empty($_FILES['photo']['name'])) {
            $file = $_FILES['photo'];
            $max = 5 * 1024 * 1024;
            $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
            $mime = '';

            if (is_uploaded_file($file['tmp_name'] ?? '')) {
                $finfo = new finfo(FILEINFO_MIME_TYPE);
                $mime = $finfo->file($file['tmp_name']);
            }

            if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK || ($file['size'] ?? 0) > $max || !isset($allowed[$mime])) {
                flash('error', 'Upload a valid JPG, PNG, or WEBP image under 5 MB.');
                redirect('admin/profile');
            }

            $filename = 'profile_' . bin2hex(random_bytes(8)) . '.' . $allowed[$mime];
            if (move_uploaded_file($file['tmp_name'], dirname(__DIR__) . '/uploads/' . $filename)) {
                set_setting('photo', $filename);
            } else {
                flash('error', 'Photo upload failed. Check upload directory permissions.');
                redirect('admin/profile');
            }
        }

        flash('success', 'Profile updated successfully.');
        redirect('admin/profile');
    }

    // -------------------------------------------------------------------------
    // SMTP Configuration & Test Dispatch
    // -------------------------------------------------------------------------
    if ($action === 'save_smtp') {
        save_smtp_config([
            'host'       => $_POST['host'] ?? '',
            'port'       => (int)($_POST['port'] ?? 587),
            'encryption' => $_POST['encryption'] ?? 'tls',
            'username'   => $_POST['username'] ?? '',
            'password'   => $_POST['password'] ?? '',
            'from_email' => $_POST['from_email'] ?? '',
            'from_name'  => $_POST['from_name'] ?? 'Portfolio Bot',
            'is_active'  => isset($_POST['is_active']) ? 1 : 0
        ]);
        flash('success', 'SMTP settings updated successfully.');
        redirect('admin/smtp');
    }

    if ($action === 'test_email') {
        $to = trim((string)($_POST['test_recipient'] ?? ''));
        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
            flash('error', 'Please enter a valid recipient email address.');
            redirect('admin/smtp');
        }

        $smtp = get_smtp_config();
        try {
            $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
            $mail->isSMTP();
            $mail->Host       = $smtp['host'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $smtp['username'];
            $mail->Password   = $smtp['password'];
            $mail->SMTPSecure = ($smtp['encryption'] === 'ssl')
                ? \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS
                : \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = (int)$smtp['port'];

            $mail->setFrom($smtp['from_email'] ?: $smtp['username'], (string)$smtp['from_name']);
            $mail->addAddress($to);
            $mail->Subject = 'SMTP Test Verification — Portfolio CMS';
            $mail->Body    = "Success! Your SMTP mail configuration is functional and ready to deliver contact form notifications.";

            $mail->send();
            flash('success', 'Test email dispatched successfully to ' . $to);
        } catch (\Throwable $e) {
            flash('error', 'SMTP Connection Test Failed: ' . $e->getMessage());
        }
        redirect('admin/smtp');
    }

    // -------------------------------------------------------------------------
    // Normalized Content CRUD Handlers
    // -------------------------------------------------------------------------
    if ($action === 'save_content') {
        $type = (string)($_POST['type'] ?? '');
        $id = (int)($_POST['id'] ?? 0);

        if ($type === 'skills') {
            $name = trim((string)($_POST['name'] ?? ''));
            $groupName = trim((string)($_POST['group_name'] ?? 'Support'));
            $level = trim((string)($_POST['level'] ?? 'Core'));
            $sort = (int)($_POST['sort_order'] ?? 0);

            if ($name === '') {
                flash('error', 'Skill name is required.');
                redirect("admin/content/skills" . ($id ? "?edit={$id}" : '?new=1'));
            }

            $gStmt = $pdo->prepare('SELECT id FROM skill_groups WHERE name = ? LIMIT 1');
            $gStmt->execute([$groupName]);
            $groupId = $gStmt->fetchColumn();

            if (!$groupId) {
                $gIns = $pdo->prepare('INSERT INTO skill_groups (name, sort_order) VALUES (?, 0)');
                $gIns->execute([$groupName]);
                $groupId = (int)$pdo->lastInsertId();
            }

            if ($id > 0) {
                $uStmt = $pdo->prepare('UPDATE skills SET group_id = ?, name = ?, level = ?, sort_order = ? WHERE id = ?');
                $uStmt->execute([$groupId, $name, $level, $sort, $id]);
                flash('success', 'Skill updated.');
            } else {
                $iStmt = $pdo->prepare('INSERT INTO skills (group_id, name, level, sort_order) VALUES (?, ?, ?, ?)');
                $iStmt->execute([$groupId, $name, $level, $sort]);
                flash('success', 'Skill created.');
            }
            redirect('admin/content/skills');
        }

        if ($type === 'experience') {
            $role = trim((string)($_POST['role'] ?? ''));
            $company = trim((string)($_POST['company'] ?? ''));
            $period = trim((string)($_POST['period'] ?? ''));
            $location = trim((string)($_POST['location'] ?? ''));
            $sort = (int)($_POST['sort_order'] ?? 0);
            $rawBullets = trim((string)($_POST['description'] ?? ''));

            if ($role === '' || $company === '' || $period === '') {
                flash('error', 'Job title, company, and period are required.');
                redirect("admin/content/experience" . ($id ? "?edit={$id}" : '?new=1'));
            }

            $pdo->beginTransaction();
            if ($id > 0) {
                $uStmt = $pdo->prepare('UPDATE experience SET role = ?, company = ?, period = ?, location = ?, sort_order = ? WHERE id = ?');
                $uStmt->execute([$role, $company, $period, $location, $sort, $id]);
                $pdo->prepare('DELETE FROM experience_responsibilities WHERE experience_id = ?')->execute([$id]);
                $expId = $id;
                flash('success', 'Work experience updated.');
            } else {
                $iStmt = $pdo->prepare('INSERT INTO experience (role, company, period, location, sort_order) VALUES (?, ?, ?, ?, ?)');
                $iStmt->execute([$role, $company, $period, $location, $sort]);
                $expId = (int)$pdo->lastInsertId();
                flash('success', 'Work experience created.');
            }

            $bullets = array_values(array_filter(array_map('trim', preg_split('/\R/u', $rawBullets))));
            $respStmt = $pdo->prepare('INSERT INTO experience_responsibilities (experience_id, responsibility, sort_order) VALUES (?, ?, ?)');
            foreach ($bullets as $idx => $bullet) {
                $respStmt->execute([$expId, $bullet, $idx + 1]);
            }
            $pdo->commit();
            redirect('admin/content/experience');
        }

        if ($type === 'projects') {
            $title = trim((string)($_POST['title'] ?? ''));
            $projectType = trim((string)($_POST['project_type'] ?? ''));
            $desc = trim((string)($_POST['description'] ?? ''));
            $sort = (int)($_POST['sort_order'] ?? 0);
            $rawTags = trim((string)($_POST['tags'] ?? ''));

            if ($title === '' || $projectType === '' || $desc === '') {
                flash('error', 'Title, category, and description are required.');
                redirect("admin/content/projects" . ($id ? "?edit={$id}" : '?new=1'));
            }

            $pdo->beginTransaction();
            if ($id > 0) {
                $uStmt = $pdo->prepare('UPDATE projects SET title = ?, project_type = ?, description = ?, sort_order = ? WHERE id = ?');
                $uStmt->execute([$title, $projectType, $desc, $sort, $id]);
                $pdo->prepare('DELETE FROM project_tags WHERE project_id = ?')->execute([$id]);
                $projId = $id;
                flash('success', 'Project updated.');
            } else {
                $iStmt = $pdo->prepare('INSERT INTO projects (title, project_type, description, sort_order) VALUES (?, ?, ?, ?)');
                $iStmt->execute([$title, $projectType, $desc, $sort]);
                $projId = (int)$pdo->lastInsertId();
                flash('success', 'Project created.');
            }

            $tags = array_values(array_filter(array_map('trim', explode(',', $rawTags))));
            $tagStmt = $pdo->prepare('INSERT INTO project_tags (project_id, tag_name) VALUES (?, ?)');
            foreach ($tags as $tag) {
                $tagStmt->execute([$projId, $tag]);
            }
            $pdo->commit();
            redirect('admin/content/projects');
        }

        if ($type === 'education') {
            $degree = trim((string)($_POST['degree'] ?? ''));
            $institution = trim((string)($_POST['institution'] ?? ''));
            $period = trim((string)($_POST['period'] ?? ''));
            $detail = trim((string)($_POST['detail'] ?? ''));
            $sort = (int)($_POST['sort_order'] ?? 0);

            if ($degree === '' || $institution === '' || $period === '') {
                flash('error', 'Degree, institution, and period are required.');
                redirect("admin/content/education" . ($id ? "?edit={$id}" : '?new=1'));
            }

            if ($id > 0) {
                $uStmt = $pdo->prepare('UPDATE education SET degree = ?, institution = ?, period = ?, detail = ?, sort_order = ? WHERE id = ?');
                $uStmt->execute([$degree, $institution, $period, $detail, $sort, $id]);
                flash('success', 'Education record updated.');
            } else {
                $iStmt = $pdo->prepare('INSERT INTO education (degree, institution, period, detail, sort_order) VALUES (?, ?, ?, ?, ?)');
                $iStmt->execute([$degree, $institution, $period, $detail, $sort]);
                flash('success', 'Education record created.');
            }
            redirect('admin/content/education');
        }
    }

    if ($action === 'delete_content') {
        $type = (string)($_POST['type'] ?? '');
        $id = (int)($_POST['id'] ?? 0);
        $allowed = ['skills' => 'skills', 'experience' => 'experience', 'projects' => 'projects', 'education' => 'education'];
        if (isset($allowed[$type])) {
            $stmt = $pdo->prepare("DELETE FROM `{$allowed[$type]}` WHERE id = ?");
            $stmt->execute([$id]);
            flash('success', 'Item deleted.');
        }
        redirect("admin/content/{$type}");
    }

    if ($action === 'visibility') {
        $type = (string)($_POST['type'] ?? '');
        $id = (int)($_POST['id'] ?? 0);
        $allowed = ['skills' => 'skills', 'experience' => 'experience', 'projects' => 'projects', 'education' => 'education'];
        if (isset($allowed[$type])) {
            $table = $allowed[$type];
            $stmt = $pdo->prepare("UPDATE `{$table}` SET is_visible = 1 - is_visible WHERE id = ?");
            $stmt->execute([$id]);
            flash('success', 'Visibility status updated.');
        }
        redirect("admin/content/{$type}");
    }

    if ($action === 'message_status') {
        $id = (int)($_POST['id'] ?? 0);
        $status = (string)($_POST['status'] ?? 'new');
        if (in_array($status, ['new', 'read', 'replied', 'archived'], true)) {
            $stmt = $pdo->prepare('UPDATE contact_messages SET status = ? WHERE id = ?');
            $stmt->execute([$status, $id]);
            flash('success', 'Message status updated.');
        }
        redirect("admin/messages?view={$id}");
    }

    if ($action === 'delete_message') {
        $stmt = $pdo->prepare('DELETE FROM contact_messages WHERE id = ?');
        $stmt->execute([(int)($_POST['id'] ?? 0)]);
        flash('success', 'Message deleted.');
        redirect('admin/messages');
    }
}

$flashes = take_flashes();

// -----------------------------------------------------------------------------
// 2. View Router (Delegating to admin/views/)
// -----------------------------------------------------------------------------

// SMTP Configuration View
if ($page === 'smtp') {
    $title = 'SMTP & Mail Settings';
    $smtp = get_smtp_config();
    require __DIR__ . '/views/header.php';
    require __DIR__ . '/views/smtp.php';
    require __DIR__ . '/views/footer.php';
    exit;
}

// Profile View
if ($page === 'profile') {
    $title = 'Profile & Photo';
    $profile = [];
    foreach (['name', 'role', 'location', 'headline', 'intro', 'linkedin', 'email', 'phone', 'photo'] as $k) {
        $profile[$k] = setting($k);
    }
    require __DIR__ . '/views/header.php';
    require __DIR__ . '/views/profile.php';
    require __DIR__ . '/views/footer.php';
    exit;
}

// Normalized Content CRUD View
if ($page === 'content') {
    $type = (string)($_GET['type'] ?? 'skills');
    if (!in_array($type, ['skills', 'experience', 'projects', 'education'], true)) {
        http_response_code(404);
        exit('Content section not found.');
    }

    $id = (int)($_GET['edit'] ?? 0);
    $isForm = isset($_GET['new']) || $id > 0;
    $item = [];
    $responsibilities = '';
    $tags = '';
    $rows = [];

    if ($isForm) {
        if ($id > 0) {
            if ($type === 'skills') {
                $stmt = $pdo->prepare('SELECT s.*, g.name AS group_name FROM skills s JOIN skill_groups g ON s.group_id = g.id WHERE s.id = ?');
                $stmt->execute([$id]);
                $item = $stmt->fetch() ?: [];
            } elseif ($type === 'experience') {
                $stmt = $pdo->prepare('SELECT * FROM experience WHERE id = ?');
                $stmt->execute([$id]);
                $item = $stmt->fetch() ?: [];
                $rStmt = $pdo->prepare('SELECT responsibility FROM experience_responsibilities WHERE experience_id = ? ORDER BY sort_order ASC');
                $rStmt->execute([$id]);
                $responsibilities = implode("\n", $rStmt->fetchAll(PDO::FETCH_COLUMN));
            } elseif ($type === 'projects') {
                $stmt = $pdo->prepare('SELECT * FROM projects WHERE id = ?');
                $stmt->execute([$id]);
                $item = $stmt->fetch() ?: [];
                $tStmt = $pdo->prepare('SELECT tag_name FROM project_tags WHERE project_id = ?');
                $tStmt->execute([$id]);
                $tags = implode(', ', $tStmt->fetchAll(PDO::FETCH_COLUMN));
            } elseif ($type === 'education') {
                $stmt = $pdo->prepare('SELECT * FROM education WHERE id = ?');
                $stmt->execute([$id]);
                $item = $stmt->fetch() ?: [];
            }

            if (!$item) {
                http_response_code(404);
                exit('Record not found.');
            }
        }
        $title = ($id ? 'Edit ' : 'Add ') . ucfirst($type);
    } else {
        if ($type === 'skills') {
            $rows = $pdo->query('SELECT s.*, g.name AS group_name FROM skills s JOIN skill_groups g ON s.group_id = g.id ORDER BY s.sort_order ASC, s.id ASC')->fetchAll();
        } else {
            $rows = $pdo->query("SELECT * FROM `{$type}` ORDER BY sort_order ASC, id ASC")->fetchAll();
        }
        $title = ucfirst($type);
    }

    require __DIR__ . '/views/header.php';
    require __DIR__ . '/views/content.php';
    require __DIR__ . '/views/footer.php';
    exit;
}

// Messages View
if ($page === 'messages') {
    $isDetail = isset($_GET['view']);
    $message = [];
    $rows = [];
    $filter = 'all';

    if ($isDetail) {
        $id = (int)$_GET['view'];
        $stmt = $pdo->prepare('SELECT * FROM contact_messages WHERE id = ?');
        $stmt->execute([$id]);
        $message = $stmt->fetch();

        if (!$message) {
            http_response_code(404);
            exit('Message not found.');
        }

        if ($message['status'] === 'new') {
            $pdo->prepare("UPDATE contact_messages SET status = 'read' WHERE id = ?")->execute([$id]);
            $message['status'] = 'read';
        }
        $title = 'Message Details';
    } else {
        $filter = (string)($_GET['status'] ?? 'all');
        if (!in_array($filter, ['all', 'new', 'read', 'replied', 'archived'], true)) {
            $filter = 'all';
        }

        if ($filter === 'all') {
            $rows = $pdo->query('SELECT * FROM contact_messages ORDER BY created_at DESC')->fetchAll();
        } else {
            $stmt = $pdo->prepare('SELECT * FROM contact_messages WHERE status = ? ORDER BY created_at DESC');
            $stmt->execute([$filter]);
            $rows = $stmt->fetchAll();
        }
        $title = 'Inbox';
    }

    require __DIR__ . '/views/header.php';
    require __DIR__ . '/views/messages.php';
    require __DIR__ . '/views/footer.php';
    exit;
}

// -----------------------------------------------------------------------------
// 3. Default View: Dashboard
// -----------------------------------------------------------------------------
$title = 'Dashboard';
$stats = [
    'messages'   => (int)$pdo->query("SELECT COUNT(*) FROM contact_messages")->fetchColumn(),
    'unread'     => (int)$pdo->query("SELECT COUNT(*) FROM contact_messages WHERE status = 'new'")->fetchColumn(),
    'skills'     => (int)$pdo->query("SELECT COUNT(*) FROM skills")->fetchColumn(),
    'projects'   => (int)$pdo->query("SELECT COUNT(*) FROM projects")->fetchColumn(),
    'experience' => (int)$pdo->query("SELECT COUNT(*) FROM experience")->fetchColumn(),
    'education'  => (int)$pdo->query("SELECT COUNT(*) FROM education")->fetchColumn(),
];

$recent = $pdo->query('SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 5')->fetchAll();

require __DIR__ . '/views/header.php';
?>
<div class="stats-grid">
    <div class="stat-card"><span>Total Inquiries</span><strong><?= $stats['messages'] ?></strong><small>Saved messages</small></div>
    <div class="stat-card"><span>Unread</span><strong><?= $stats['unread'] ?></strong><small>Pending action</small></div>
    <div class="stat-card"><span>Skills</span><strong><?= $stats['skills'] ?></strong><small>Normalized skills</small></div>
    <div class="stat-card"><span>Projects</span><strong><?= $stats['projects'] ?></strong><small>Portfolio projects</small></div>
</div>
<div class="dashboard-columns">
    <div class="admin-panel">
        <div class="panel-title-row">
            <h2>Recent Messages</h2>
            <a class="small-action" href="<?= e(url('admin/messages')) ?>">View All ↗</a>
        </div>
        <?php if (!$recent): ?>
            <p class="panel-hint">No messages in inbox.</p>
        <?php else: ?>
            <div class="recent-list">
                <?php foreach ($recent as $m): ?>
                    <a class="recent-message" href="<?= e(url("admin/messages?view=" . $m['id'])) ?>">
                        <span class="recent-dot <?= $m['status'] === 'new' ? 'unread' : '' ?>"></span>
                        <span class="recent-message-copy">
                            <strong><?= e($m['name']) ?></strong>
                            <small><?= e($m['subject'] ?: preview_text($m['message'], 60)) ?></small>
                        </span>
                        <span class="recent-date"><?= e(date('M j', strtotime($m['created_at']))) ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    <div class="admin-panel quick-links">
        <h2>Quick Shortcuts</h2>
        <a href="<?= e(url('admin/profile')) ?>">◉ &nbsp; Update profile &amp; photo <span>↗</span></a>
        <a href="<?= e(url('admin/content/skills')) ?>">✳ &nbsp; Edit skills <span>↗</span></a>
        <a href="<?= e(url('admin/content/experience')) ?>">▤ &nbsp; Update work experience <span>↗</span></a>
        <a href="<?= e(url('admin/content/projects')) ?>">◇ &nbsp; Manage projects <span>↗</span></a>
        <a href="<?= e(url('admin/content/education')) ?>">▣ &nbsp; Edit education <span>↗</span></a>
        <a href="<?= e(url('admin/smtp')) ?>">⚙ &nbsp; Configure SMTP server <span>↗</span></a>
    </div>
</div>
<?php
require __DIR__ . '/views/footer.php';
