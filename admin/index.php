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

    // Clear Analytics Records
    if ($action === 'clear_analytics') {
        $pdo->exec('TRUNCATE TABLE visitor_logs');
        $pdo->exec('TRUNCATE TABLE click_logs');
        flash('success', 'All visitor telemetry and click records have been wiped clean.');
        redirect('admin/analytics');
    }

    // Profile & Photo Update
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

    // Global Site Settings, Section Headers & Dynamic Footer Handler
    if ($action === 'save_site_settings') {
        $keys = [
            'headline',
            'intro',
            'availability_status',
            'github',
            'sec_about_kicker',
            'sec_about_title',
            'sec_about_accent',
            'sec_skills_kicker',
            'sec_skills_title',
            'sec_skills_accent',
            'sec_skills_intro',
            'sec_exp_kicker',
            'sec_exp_title',
            'sec_exp_accent',
            'sec_exp_intro',
            'sec_proj_kicker',
            'sec_proj_title',
            'sec_proj_accent',
            'sec_proj_intro',
            'sec_edu_kicker',
            'sec_edu_title',
            'sec_edu_accent',
            'sec_contact_kicker',
            'sec_contact_title',
            'sec_contact_accent',
            'sec_contact_intro',
            'learning_title',
            'learning_text',
            'edu_learning_title',
            'edu_learning_text',
            'footer_badge_letter',
            'footer_brand_name',
            'footer_tagline',
            'footer_copyright',
            'footer_show_back_to_top',
            'footer_show_admin_link'
        ];

        foreach ($keys as $key) {
            if ($key === 'footer_show_back_to_top' || $key === 'footer_show_admin_link') {
                set_setting($key, isset($_POST[$key]) ? '1' : '0');
            } else {
                set_setting($key, trim((string)($_POST[$key] ?? '')));
            }
        }

        // Process Dynamic Add/Remove Footer Custom Links
        $linkLabels = $_POST['footer_link_label'] ?? [];
        $linkUrls   = $_POST['footer_link_url'] ?? [];
        $linkNewTab = $_POST['footer_link_new_tab'] ?? [];
        $footerLinks = [];

        if (is_array($linkLabels) && is_array($linkUrls)) {
            foreach ($linkLabels as $idx => $label) {
                $lbl = trim((string)$label);
                $url = trim((string)($linkUrls[$idx] ?? ''));
                if ($lbl !== '' || $url !== '') {
                    $footerLinks[] = [
                        'label'   => $lbl,
                        'url'     => $url,
                        'new_tab' => !empty($linkNewTab[$idx]) ? '1' : '0'
                    ];
                }
            }
        }
        set_setting('footer_links_json', json_encode($footerLinks, JSON_UNESCAPED_UNICODE));

        flash('success', 'Site settings, section copy, and complete footer configuration saved.');
        redirect('admin/settings');
    }

    // SMTP Configuration & Test Dispatch
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

            $fromEmail = $smtp['from_email'] ?: $smtp['username'];
            $fromName  = $smtp['from_name'] ?: setting('name', 'Portfolio Admin');
            $mail->setFrom($fromEmail, (string)$fromName);
            $mail->addAddress($to);
            $mail->addReplyTo($fromEmail, (string)$fromName);

            $mail->isHTML(true);
            $mail->Subject = 'SMTP Test Verification — Portfolio CMS';
            $mail->Body    = "Success! Your SMTP mail configuration is functional and ready to deliver contact form notifications.";

            $mail->send();
            flash('success', 'Test email dispatched successfully to ' . $to);
        } catch (\Throwable $e) {
            flash('error', 'SMTP Connection Test Failed: ' . $e->getMessage());
        }
        redirect('admin/smtp');
    }

    // Normalized Content CRUD Handlers
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
            $parentId = (!empty($_POST['parent_id']) && is_numeric($_POST['parent_id'])) ? (int)$_POST['parent_id'] : null;
            $rawBullets = trim((string)($_POST['description'] ?? ''));

            if ($role === '' || $period === '') {
                flash('error', 'Job title and date range are required.');
                redirect("admin/content/experience" . ($id ? "?edit={$id}" : '?new=1'));
            }

            if ($parentId !== null) {
                $pStmt = $pdo->prepare('SELECT company, location FROM experience WHERE id = ?');
                $pStmt->execute([$parentId]);
                if ($parentRow = $pStmt->fetch()) {
                    if ($company === '') $company = $parentRow['company'];
                    if ($location === '') $location = $parentRow['location'];
                }
            }

            if ($company === '') {
                flash('error', 'Company name is required for primary positions.');
                redirect("admin/content/experience" . ($id ? "?edit={$id}" : '?new=1'));
            }

            $pdo->beginTransaction();
            if ($id > 0) {
                $uStmt = $pdo->prepare('UPDATE experience SET parent_id = ?, role = ?, company = ?, period = ?, location = ?, sort_order = ? WHERE id = ?');
                $uStmt->execute([$parentId, $role, $company, $period, $location, $sort, $id]);
                $pdo->prepare('DELETE FROM experience_responsibilities WHERE experience_id = ?')->execute([$id]);
                $expId = $id;
                flash('success', 'Work experience updated.');
            } else {
                $iStmt = $pdo->prepare('INSERT INTO experience (parent_id, role, company, period, location, sort_order) VALUES (?, ?, ?, ?, ?, ?)');
                $iStmt->execute([$parentId, $role, $company, $period, $location, $sort]);
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

    // Send Direct Email Reply to Visitor
    if ($action === 'send_reply') {
        $messageId = (int)($_POST['message_id'] ?? 0);
        $toEmail = trim((string)($_POST['recipient_email'] ?? ''));
        $toName = trim((string)($_POST['recipient_name'] ?? ''));
        $replySubject = trim((string)($_POST['reply_subject'] ?? ''));
        $replyBody = trim((string)($_POST['reply_body'] ?? ''));
        $quoteOriginal = !empty($_POST['quote_original']);

        if ($messageId <= 0 || !filter_var($toEmail, FILTER_VALIDATE_EMAIL) || $replyBody === '') {
            flash('error', 'Valid recipient email and reply content are required.');
            redirect("admin/messages?view={$messageId}");
        }

        $smtp = get_smtp_config();
        if (empty($smtp['is_active']) || empty($smtp['host']) || empty($smtp['username']) || empty($smtp['password'])) {
            flash('error', 'SMTP is not active or configured. Please visit SMTP & Mail Settings first.');
            redirect("admin/messages?view={$messageId}");
        }

        // Fetch original message for quoting
        $mStmt = $pdo->prepare('SELECT * FROM contact_messages WHERE id = ?');
        $mStmt->execute([$messageId]);
        $orig = $mStmt->fetch();

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

            $fromEmail = $smtp['from_email'] ?: $smtp['username'];
            $fromName  = $smtp['from_name'] ?: setting('name', 'Portfolio Admin');
            $mail->setFrom($fromEmail, (string)$fromName);
            $mail->addAddress($toEmail, $toName);
            $mail->addReplyTo($fromEmail, (string)$fromName);

            $mail->isHTML(true);
            $mail->Subject = $replySubject;

            $htmlContent = nl2br(e($replyBody));
            if ($quoteOriginal && $orig) {
                $htmlContent .= '<br><br><hr style="border:none;border-top:1px solid #cbd5e1;margin:20px 0;">'
                    . '<p style="color:#64748b;font-size:12px;"><strong>Original Inquiry from ' . e($orig['name']) . ' (' . e($orig['created_at']) . '):</strong><br>'
                    . nl2br(e($orig['message'])) . '</p>';
            }

            $mail->Body    = $htmlContent;
            $mail->AltBody = strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", $htmlContent));

            $mail->send();

            // Automatically update message status to 'replied'
            $pdo->prepare("UPDATE contact_messages SET status = 'replied' WHERE id = ?")->execute([$messageId]);

            flash('success', 'Reply dispatched successfully to ' . $toEmail);
        } catch (\Throwable $e) {
            flash('error', 'Reply dispatch failed: ' . $e->getMessage());
        }

        redirect("admin/messages?view={$messageId}");
    }

    // CV Template Management Handlers
    if ($action === 'set_default_cv') {
        $id = (int)($_POST['id'] ?? 0);
        $pdo->beginTransaction();
        $pdo->exec('UPDATE cv_templates SET is_default = 0');
        $stmt = $pdo->prepare('UPDATE cv_templates SET is_default = 1, is_enabled = 1 WHERE id = ?');
        $stmt->execute([$id]);
        $pdo->commit();
        flash('success', 'Default public CV layout updated.');
        redirect('admin/cv');
    }

    if ($action === 'toggle_cv_template') {
        $id = (int)($_POST['id'] ?? 0);
        $stmt = $pdo->prepare('UPDATE cv_templates SET is_enabled = 1 - is_enabled WHERE id = ?');
        $stmt->execute([$id]);
        flash('success', 'CV layout visibility updated.');
        redirect('admin/cv');
    }

    // Save Granular CV Customization Settings
    if ($action === 'save_cv_settings') {
        $keys = [
            'cv_display_name',
            'cv_display_title',
            'cv_ats_subline',
            'cv_phone',
            'cv_email',
            'cv_location',
            'cv_linkedin',
            'cv_photo_shape',
            'cv_photo_size',
            'cv_photo_border_color',
            'cv_show_photo',
            'cv_show_summary',
            'cv_show_competencies',
            'cv_show_matrix',
            'cv_show_experience',
            'cv_show_education',
            'cv_show_training',
            'cv_show_languages',
            'cv_sec_summary_title',
            'cv_sec_expertise_title',
            'cv_sec_competencies_title',
            'cv_sec_experience_title',
            'cv_sec_education_title',
            'cv_sec_matrix_title',
            'cv_sec_training_title',
            'cv_sec_languages_title',
            'cv_custom_summary',
            'cv_languages',
            'cv_trainings'
        ];

        foreach ($keys as $k) {
            set_setting($k, trim((string)($_POST[$k] ?? '')));
        }

        // Process Dynamic Matrix Rows
        $matrixLabels = $_POST['matrix_label'] ?? [];
        $matrixSkills = $_POST['matrix_skills'] ?? [];
        $matrixData = [];

        if (is_array($matrixLabels) && is_array($matrixSkills)) {
            foreach ($matrixLabels as $idx => $label) {
                $lbl = trim((string)$label);
                $skl = trim((string)($matrixSkills[$idx] ?? ''));
                if ($lbl !== '' || $skl !== '') {
                    $matrixData[] = [
                        'label'  => $lbl,
                        'skills' => $skl
                    ];
                }
            }
        }
        set_setting('cv_matrix_json', json_encode($matrixData, JSON_UNESCAPED_UNICODE));

        flash('success', 'CV layout rules, dynamic matrix, and content saved.');
        redirect('admin/cv');
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

// Site Settings View
if ($page === 'settings') {
    $title = 'Site Settings';
    $settingKeys = [
        'headline',
        'intro',
        'availability_status',
        'github',
        'sec_about_kicker',
        'sec_about_title',
        'sec_about_accent',
        'sec_skills_kicker',
        'sec_skills_title',
        'sec_skills_accent',
        'sec_skills_intro',
        'sec_exp_kicker',
        'sec_exp_title',
        'sec_exp_accent',
        'sec_exp_intro',
        'sec_proj_kicker',
        'sec_proj_title',
        'sec_proj_accent',
        'sec_proj_intro',
        'sec_edu_kicker',
        'sec_edu_title',
        'sec_edu_accent',
        'sec_contact_kicker',
        'sec_contact_title',
        'sec_contact_accent',
        'sec_contact_intro',
        'learning_title',
        'learning_text',
        'edu_learning_title',
        'edu_learning_text',
        'footer_badge_letter',
        'footer_brand_name',
        'footer_tagline',
        'footer_copyright',
        'footer_show_back_to_top',
        'footer_show_admin_link'
    ];
    $settings = [];
    foreach ($settingKeys as $k) {
        $settings[$k] = setting($k);
    }

    $footerLinksJson = setting('footer_links_json', '');
    $footerLinks = json_decode($footerLinksJson, true);
    if (!is_array($footerLinks)) {
        $footerLinks = [
            ['label' => 'GitHub', 'url' => setting('github', 'https://github.com/ashraful98'), 'new_tab' => '1'],
            ['label' => 'LinkedIn', 'url' => setting('linkedin', 'https://linkedin.com/in/ashraful98'), 'new_tab' => '1']
        ];
    }
    $settings['footer_links'] = $footerLinks;

    require __DIR__ . '/views/header.php';
    require __DIR__ . '/views/settings.php';
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
    $companies = [];

    if ($type === 'experience') {
        $companies = $pdo->query('
            SELECT id, company, role 
            FROM experience 
            ORDER BY sort_order ASC, id ASC
        ')->fetchAll();
    }

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
        } elseif ($type === 'experience') {
            $rows = $pdo->query('
                SELECT e.*, p.company AS parent_company 
                FROM experience e 
                LEFT JOIN experience p ON e.parent_id = p.id 
                ORDER BY COALESCE(e.parent_id, e.id) ASC, e.parent_id ASC, e.sort_order ASC
            ')->fetchAll();
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

// CV Template Manager View
if ($page === 'cv') {
    $title = 'CV Layout & Granular Line Editor';
    $templates = $pdo->query('SELECT * FROM cv_templates ORDER BY sort_order ASC, id ASC')->fetchAll();

    $matrixJson = setting('cv_matrix_json', '');
    $matrixRows = json_decode($matrixJson, true);
    if (!is_array($matrixRows) || empty($matrixRows)) {
        $matrixRows = [
            ['label' => 'IT Support & Operation', 'skills' => 'L1/L2 Support, Service Desk, Remote Support, Asset Management, Troubleshooting'],
            ['label' => 'Ticketing & SaaS', 'skills' => 'Zendesk, Jira SM, ServiceNow, Google Workspace, M365 Administration'],
            ['label' => 'Networking & OS', 'skills' => 'TCP/IP, DNS, LAN/WAN, Windows Server, RHEL Administration'],
            ['label' => 'Cloud and Database', 'skills' => 'AWS, Azure, MySQL, MSSQL Server']
        ];
    }

    $cv = [
        'name'               => setting('cv_display_name', setting('name', 'Ashraful Alam Shihab')),
        'title'              => setting('cv_display_title', setting('role', 'Senior Desktop Support Engineer')),
        'ats_subline'        => setting('cv_ats_subline', 'Senior Desktop Support Engineer | IT Support | Service Desk | IT Operations'),
        'phone'              => setting('cv_phone', setting('phone', '+8801982401992')),
        'email'              => setting('cv_email', setting('email', 'meet.ashraf.uk@gmail.com')),
        'location'           => setting('cv_location', setting('location', 'Dhaka, Bangladesh')),
        'linkedin'           => setting('cv_linkedin', 'linkedin.com/in/ashraful98'),

        'sec_summary'        => setting('cv_sec_summary_title', 'Professional Summary'),
        'sec_expertise'      => setting('cv_sec_expertise_title', 'Areas of Expertise'),
        'sec_competencies'   => setting('cv_sec_competencies_title', 'Core Competencies'),
        'sec_experience'     => setting('cv_sec_experience_title', 'Professional Experience'),
        'sec_education'      => setting('cv_sec_education_title', 'Education'),
        'sec_matrix'         => setting('cv_sec_matrix_title', 'Technical Skills Matrix'),
        'sec_training'       => setting('cv_sec_training_title', 'Training & Certifications'),
        'sec_languages'      => setting('cv_sec_languages_title', 'Languages'),

        'summary'            => setting('cv_custom_summary', setting('intro', '')),
        'languages'          => setting('cv_languages', "Bangla - Native\nEnglish - Fluent\nHindi - Fluent"),
        'trainings'          => setting('cv_trainings', "CCNA 200-301 — 2023\nLinux System Administration — 2022\nGoogle IT Support Professional — 2022\nWindows Server Administration — 2022"),
        'matrix_rows'        => $matrixRows
    ];

    require __DIR__ . '/views/header.php';
    require __DIR__ . '/views/cv_manager.php';
    require __DIR__ . '/views/footer.php';
    exit;
}

// -----------------------------------------------------------------------------
// Site Analytics & Monitoring Controller
// -----------------------------------------------------------------------------
if ($page === 'analytics') {
    $period = (string)($_GET['period'] ?? '30d');
    $deviceFilter = (string)($_GET['device'] ?? 'all');
    $startDate = trim((string)($_GET['start_date'] ?? ''));
    $endDate = trim((string)($_GET['end_date'] ?? ''));

    // Compute Date Range (Y-m-d)
    if ($period === 'today') {
        $start = date('Y-m-d');
        $end = date('Y-m-d');
    } elseif ($period === '7d') {
        $start = date('Y-m-d', strtotime('-6 days'));
        $end = date('Y-m-d');
    } elseif ($period === '90d') {
        $start = date('Y-m-d', strtotime('-89 days'));
        $end = date('Y-m-d');
    } elseif ($period === 'custom' && $startDate !== '' && $endDate !== '') {
        $start = $startDate;
        $end = $endDate;
    } else {
        $period = '30d';
        $start = date('Y-m-d', strtotime('-29 days'));
        $end = date('Y-m-d');
    }

    $startTime = $start . ' 00:00:00';
    $endTime = $end . ' 23:59:59';

    $whereParts = ["created_at >= ? AND created_at <= ?"];
    $bindings = [$startTime, $endTime];

    if ($deviceFilter !== 'all' && in_array($deviceFilter, ['Desktop', 'Mobile', 'Tablet'], true)) {
        $whereParts[] = "device_type = ?";
        $bindings[] = $deviceFilter;
    }
    $whereSql = implode(' AND ', $whereParts);

    // CSV Exporter
    if (isset($_GET['export']) && $_GET['export'] === 'csv') {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="portfolio_analytics_' . $start . '_to_' . $end . '.csv"');
        $out = fopen('php://output', 'w');
        fputcsv($out, ['ID', 'IP Address', 'Device', 'Browser', 'Platform', 'Country', 'Page URL', 'Referrer', 'Timestamp']);

        $exportStmt = $pdo->prepare("SELECT id, ip_address, device_type, browser, platform, country, page_url, referrer, created_at FROM visitor_logs WHERE {$whereSql} ORDER BY id DESC LIMIT 10000");
        $exportStmt->execute($bindings);
        while ($row = $exportStmt->fetch(PDO::FETCH_ASSOC)) {
            fputcsv($out, $row);
        }
        fclose($out);
        exit;
    }

    $title = 'Site Analytics & Telemetry';

    $kpiViews = $pdo->prepare("SELECT COUNT(*) FROM visitor_logs WHERE {$whereSql}");
    $kpiViews->execute($bindings);
    $totalViews = (int)$kpiViews->fetchColumn();

    $kpiUniques = $pdo->prepare("SELECT COUNT(DISTINCT ip_address) FROM visitor_logs WHERE {$whereSql}");
    $kpiUniques->execute($bindings);
    $uniqueVisitors = (int)$kpiUniques->fetchColumn();

    $stats = [
        'total_views'      => $totalViews,
        'unique_visitors'  => $uniqueVisitors,
        'active_visitors'  => (int)$pdo->query('SELECT COUNT(DISTINCT ip_address) FROM visitor_logs WHERE last_activity >= (NOW() - INTERVAL 5 MINUTE)')->fetchColumn(),
        'total_clicks'     => (int)$pdo->query('SELECT COUNT(*) FROM click_logs')->fetchColumn()
    ];

    $chartStmt = $pdo->prepare("
        SELECT 
            DATE(created_at) AS log_date,
            COUNT(*) AS total_views,
            COUNT(DISTINCT ip_address) AS unique_visitors
        FROM visitor_logs
        WHERE {$whereSql}
        GROUP BY DATE(created_at)
        ORDER BY log_date ASC
    ");
    $chartStmt->execute($bindings);
    $dailyRaw = $chartStmt->fetchAll(PDO::FETCH_UNIQUE | PDO::FETCH_ASSOC);

    $chartData = [];
    $curr = new DateTime($start);
    $stop = new DateTime($end);
    $stop->modify('+1 day');

    while ($curr < $stop) {
        $d = $curr->format('Y-m-d');
        $shortLabel = $curr->format('M j');
        $v = isset($dailyRaw[$d]) ? (int)$dailyRaw[$d]['total_views'] : 0;
        $u = isset($dailyRaw[$d]) ? (int)$dailyRaw[$d]['unique_visitors'] : 0;
        $chartData[] = [
            'date'    => $d,
            'label'   => $shortLabel,
            'views'   => $v,
            'uniques' => $u
        ];
        $curr->modify('+1 day');
    }

    $recentStmt = $pdo->prepare("SELECT * FROM visitor_logs WHERE {$whereSql} ORDER BY id DESC LIMIT 25");
    $recentStmt->execute($bindings);
    $recentLogs = $recentStmt->fetchAll();

    $devStmt = $pdo->prepare("SELECT device_type, COUNT(*) as cnt FROM visitor_logs WHERE {$whereSql} GROUP BY device_type ORDER BY cnt DESC");
    $devStmt->execute($bindings);
    $devices = $devStmt->fetchAll();

    $brwStmt = $pdo->prepare("SELECT browser, COUNT(*) as cnt FROM visitor_logs WHERE {$whereSql} GROUP BY browser ORDER BY cnt DESC");
    $brwStmt->execute($bindings);
    $browsers = $brwStmt->fetchAll();

    $pageStmt = $pdo->prepare("SELECT page_url, COUNT(*) as visits FROM visitor_logs WHERE {$whereSql} GROUP BY page_url ORDER BY visits DESC LIMIT 8");
    $pageStmt->execute($bindings);
    $pages = $pageStmt->fetchAll();

    $clicks = $pdo->query('SELECT element_text, target_url, COUNT(*) as total_clicks FROM click_logs GROUP BY target_url, element_text ORDER BY total_clicks DESC LIMIT 8')->fetchAll();

    $filterQuery = http_build_query([
        'period'     => $period,
        'device'     => $deviceFilter,
        'start_date' => $start,
        'end_date'   => $end
    ]);

    require __DIR__ . '/views/header.php';
    require __DIR__ . '/views/analytics.php';
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
        <a href="<?= e(url('admin/settings')) ?>">🎛 &nbsp; Global site &amp; section copy <span>↗</span></a>
        <a href="<?= e(url('admin/cv')) ?>">📄 &nbsp; CV layouts &amp; engine <span>↗</span></a>
        <a href="<?= e(url('admin/content/skills')) ?>">✳ &nbsp; Edit skills <span>↗</span></a>
        <a href="<?= e(url('admin/content/experience')) ?>">▤ &nbsp; Update work experience <span>↗</span></a>
        <a href="<?= e(url('admin/content/projects')) ?>">◇ &nbsp; Manage projects <span>↗</span></a>
        <a href="<?= e(url('admin/content/education')) ?>">▣ &nbsp; Edit education <span>↗</span></a>
        <a href="<?= e(url('admin/smtp')) ?>">⚙ &nbsp; Configure SMTP server <span>↗</span></a>
    </div>
</div>
<?php
require __DIR__ . '/views/footer.php';
