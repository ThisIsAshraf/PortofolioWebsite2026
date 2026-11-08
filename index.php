<?php

declare(strict_types=1);

require_once __DIR__ . '/config.php';

use PHPMailer\PHPMailer\PHPMailer;

$pdo = db();

// Handle Contact Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'contact') {
    $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
        || (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json'));

    $respond = function (bool $success, string $message, int $statusCode = 200) use ($isAjax) {
        if ($isAjax) {
            while (ob_get_level() > 0) {
                ob_end_clean();
            }
            http_response_code($statusCode);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'success' => $success,
                'message' => $message,
                'csrf_token' => csrf_token()
            ]);
            exit;
        }

        flash($success ? 'success' : 'error', $message);
        redirect('index.php#contact');
    };

    // CSRF Check
    $sentToken = $_POST['csrf_token'] ?? '';
    if (!is_string($sentToken) || !hash_equals(csrf_token(), $sentToken)) {
        $respond(false, 'Your session expired. Please reload the page and try again.', 419);
    }

    $name     = trim((string)($_POST['name'] ?? ''));
    $email    = trim((string)($_POST['email'] ?? ''));
    $subject  = trim((string)($_POST['subject'] ?? ''));
    $message  = trim((string)($_POST['message'] ?? ''));
    $honeypot = trim((string)($_POST['website'] ?? ''));

    // Bot honeypot filter
    if ($honeypot !== '') {
        $respond(true, 'Thanks for your message.');
    }

    if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $message === '') {
        $respond(false, 'Please provide your name, a valid email address, and your message.', 422);
    }

    if (strlen($name) > 120 || strlen($email) > 254 || strlen($subject) > 180 || strlen($message) > 5000) {
        $respond(false, 'One or more form fields exceeded character limits.', 422);
    }

    // 1. Store in Database
    $stmt = $pdo->prepare('INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)');
    $stmt->execute([$name, $email, $subject !== '' ? $subject : null, $message]);

    // 2. Dispatch Email Notification using DB/Config SMTP Settings
    $smtp = get_smtp_config();
    $adminEmail = setting('email', $smtp['username'] ?? '');

    if (!empty($smtp['is_active']) && !empty($smtp['host']) && !empty($smtp['username']) && !empty($smtp['password']) && !empty($adminEmail)) {
        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Timeout    = 5;
            $mail->Host       = $smtp['host'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $smtp['username'];
            $mail->Password   = $smtp['password'];
            $mail->SMTPSecure = ($smtp['encryption'] === 'ssl')
                ? PHPMailer::ENCRYPTION_SMTPS
                : PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = (int)$smtp['port'];

            $mail->setFrom($smtp['from_email'] ?: $smtp['username'], (string)($smtp['from_name'] ?: 'Portfolio Bot'));
            $mail->addAddress($adminEmail);
            $mail->addReplyTo($email, $name);

            $mail->isHTML(true);
            $mail->Subject = 'New Portfolio Inquiry: ' . ($subject ?: 'General Message');
            $mail->Body    = '<h3>New Contact Message</h3>'
                . '<p><strong>Sender:</strong> ' . e($name) . '</p>'
                . '<p><strong>Email:</strong> ' . e($email) . '</p>'
                . '<p><strong>Subject:</strong> ' . e($subject ?: 'None') . '</p>'
                . '<p><strong>Message:</strong><br>' . nl2br(e($message)) . '</p>';
            $mail->AltBody = "Sender: {$name}\nEmail: {$email}\nSubject: {$subject}\n\nMessage:\n{$message}";

            $mail->send();
        } catch (\Throwable $e) {
            error_log('PHPMailer error: ' . $e->getMessage());
        }
    }

    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    $respond(true, 'Thank you! Your message has been sent successfully.');
}

// Fetch Profile Details, Dynamic Section Copy & Learning Badges
$profile = [
    'name'                => setting('name', 'Ashraful Alam Shihab'),
    'role'                => setting('role', 'Senior Desktop Support Engineer'),
    'location'            => setting('location', 'Dhaka, Bangladesh'),
    'headline'            => setting('headline', 'IT support, systems administration, and reliable technology experiences.'),
    'intro'               => setting('intro', 'I help people and teams work confidently with technology—from end-user support and SaaS administration to IT assets, vendor coordination, and office infrastructure.'),
    'linkedin'            => setting('linkedin', 'https://www.linkedin.com/in/ashraful98/'),
    'email'               => setting('email', ''),
    'phone'               => setting('phone', ''),
    'photo'               => setting('photo', ''),
    'availability_status' => setting('availability_status', 'IT SUPPORT · OPERATIONS · INFRASTRUCTURE'),
    'github'              => setting('github', ''),

    // Dynamic Section 01: About
    'sec_about_kicker'    => setting('sec_about_kicker', '01 / ABOUT'),
    'sec_about_title'     => setting('sec_about_title', 'Making technology'),
    'sec_about_accent'    => setting('sec_about_accent', 'work for people.'),

    // Dynamic Section 02: Skills
    'sec_skills_kicker'   => setting('sec_skills_kicker', '02 / TOOLKIT'),
    'sec_skills_title'    => setting('sec_skills_title', 'Skills &'),
    'sec_skills_accent'   => setting('sec_skills_accent', 'strengths.'),
    'sec_skills_intro'    => setting('sec_skills_intro', 'A practical mix of user support, SaaS administration, IT operations, and developing infrastructure skills.'),

    // Dynamic Section 03: Experience
    'sec_exp_kicker'      => setting('sec_exp_kicker', '03 / CAREER'),
    'sec_exp_title'       => setting('sec_exp_title', 'Work'),
    'sec_exp_accent'      => setting('sec_exp_accent', 'experience.'),
    'sec_exp_intro'       => setting('sec_exp_intro', 'The roles, responsibilities, and practical experience that have shaped my approach to IT.'),

    // Dynamic Section 04: Projects
    'sec_proj_kicker'     => setting('sec_proj_kicker', '04 / SELECTED WORK'),
    'sec_proj_title'      => setting('sec_proj_title', 'Work that'),
    'sec_proj_accent'     => setting('sec_proj_accent', 'matters.'),
    'sec_proj_intro'      => setting('sec_proj_intro', 'Experience highlights and initiatives.'),

    // Dynamic Section 05: Education
    'sec_edu_kicker'      => setting('sec_edu_kicker', '05 / EDUCATION'),
    'sec_edu_title'       => setting('sec_edu_title', 'Built on a'),
    'sec_edu_accent'      => setting('sec_edu_accent', 'strong foundation.'),

    // Dynamic Section 06: Contact
    'sec_contact_kicker'  => setting('sec_contact_kicker', '06 / GET IN TOUCH'),
    'sec_contact_title'   => setting('sec_contact_title', 'Have a challenge?'),
    'sec_contact_accent'  => setting('sec_contact_accent', 'Let’s talk.'),
    'sec_contact_intro'   => setting('sec_contact_intro', 'For IT support opportunities, collaboration, or a professional conversation, send me a message.'),

    // Callout Badges
    'learning_title'      => setting('learning_title', 'Currently growing'),
    'learning_text'       => setting('learning_text', 'Microsoft 365 Administration · Microsoft Entra ID · Azure Administrator (AZ-104 path)'),
    'edu_learning_title'  => setting('edu_learning_title', 'Building toward cloud & enterprise infrastructure'),
    'edu_learning_text'   => setting('edu_learning_text', 'Focused on Microsoft 365, Entra ID, Azure administration, and lab-based deployments.')
];

// Fetch Normalized Skills & Groups
$skillsStmt = $pdo->query('
    SELECT s.*, g.name AS group_name 
    FROM skills s 
    JOIN skill_groups g ON s.group_id = g.id 
    WHERE s.is_visible = 1 
    ORDER BY g.sort_order ASC, s.sort_order ASC, s.id ASC
');
$skills = $skillsStmt->fetchAll();

$groups = [];
foreach ($skills as $skill) {
    $groups[$skill['group_name']] = true;
}

// Fetch Normalized Work Experience (Current Primary Roles)
$expStmt = $pdo->query('
    SELECT * FROM experience 
    WHERE is_visible = 1 AND parent_id IS NULL 
    ORDER BY sort_order ASC, id ASC
');
$experience = $expStmt->fetchAll();

// Fetch Promotions / Prior Roles under same company
$childStmt = $pdo->query('
    SELECT * FROM experience 
    WHERE is_visible = 1 AND parent_id IS NOT NULL 
    ORDER BY sort_order ASC, id ASC
');
$childRoles = [];
foreach ($childStmt->fetchAll() as $child) {
    $childRoles[(int)$child['parent_id']][] = $child;
}

$respStmt = $pdo->query('SELECT * FROM experience_responsibilities ORDER BY sort_order ASC, id ASC');
$allResponsibilities = $respStmt->fetchAll();

$expResponsibilities = [];
foreach ($allResponsibilities as $r) {
    $expResponsibilities[(int)$r['experience_id']][] = $r['responsibility'];
}

// Fetch Normalized Projects with Tags
$projStmt = $pdo->query('SELECT * FROM projects WHERE is_visible = 1 ORDER BY sort_order ASC, id ASC');
$projects = $projStmt->fetchAll();

$tagStmt = $pdo->query('SELECT * FROM project_tags');
$allTags = $tagStmt->fetchAll();

$projectTags = [];
foreach ($allTags as $t) {
    $projectTags[(int)$t['project_id']][] = $t['tag_name'];
}

// Fetch Education
$education = $pdo->query('SELECT * FROM education WHERE is_visible = 1 ORDER BY sort_order ASC, id ASC')->fetchAll();

$flashes = take_flashes();
$nameParts = explode(' ', trim($profile['name']));
$firstName = array_shift($nameParts);
$remainingName = implode(' ', $nameParts);
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?= e($profile['name']) ?> — <?= e($profile['role']) ?> Portfolio">
    <meta name="theme-color" content="#0b0d12">
    <title><?= e($profile['name']) ?> — <?= e($profile['role']) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400..700;1,9..40,400..700&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= e(url('assets/css/style.css')) ?>">
    <script defer src="<?= e(url('assets/js/main.js')) ?>"></script>
</head>

<body>
    <div class="page-glow glow-one"></div>
    <div class="page-glow glow-two"></div>

    <header class="site-header" id="top">
        <nav class="nav container" aria-label="Main navigation">
            <a class="brand" href="<?= e(url('#top')) ?>"><span class="brand-mark">S.</span><span>SHIHAB<span class="brand-dot">/</span></span></a>
            <button class="menu-toggle" id="menuToggle" aria-label="Open navigation" aria-expanded="false">
                <span></span><span></span>
            </button>
            <div class="nav-links" id="navLinks">
                <a href="#about">About</a>
                <a href="#skills">Skills</a>
                <a href="#experience">Experience</a>
                <a href="#projects">Projects</a>
                <a href="#education">Education</a>
                <a href="<?= e(url('download-cv.php')) ?>" target="_blank" class="nav-cv-btn">Download CV 📄</a>
                <a class="nav-contact" href="#contact">Let's talk <span>↗</span></a>
                <button class="theme-toggle" id="themeToggle" type="button" aria-label="Toggle theme">☼</button>
            </div>
        </nav>
    </header>

    <main>
        <section class="hero container" id="home">
            <div class="hero-copy">
                <div class="availability"><span class="status-dot"></span> <?= e($profile['availability_status']) ?></div>
                <p class="eyebrow">HELLO, I'M</p>
                <h1><?= e($firstName) ?><br><span><?= e($remainingName) ?>.</span></h1>
                <p class="hero-role"><?= e($profile['role']) ?> <span class="role-divider">/</span> <?= e($profile['location']) ?></p>
                <p class="hero-description"><?= e($profile['intro']) ?></p>

                <div class="hero-actions">
                    <a class="button button-primary" href="#experience">Explore my experience <span>↘</span></a>
                    <a class="button button-quiet" href="<?= e(url('download-cv.php')) ?>" target="_blank">Download CV <span>↗</span></a>
                </div>

                <div class="hero-socials">
                    <?php if ($profile['linkedin']): ?>
                        <a href="<?= e($profile['linkedin']) ?>" target="_blank" rel="noopener noreferrer">LinkedIn <span>↗</span></a>
                    <?php endif; ?>
                    <?php if (!empty($profile['github'])): ?>
                        <span class="social-separator"></span>
                        <a href="<?= e($profile['github']) ?>" target="_blank" rel="noopener noreferrer">GitHub <span>↗</span></a>
                    <?php endif; ?>
                    <span class="social-separator"></span>
                    <a href="#projects">Selected work <span>↘</span></a>
                </div>

                <?php if ($profile['phone'] || $profile['email']): ?>
                    <div class="hero-contact-details" aria-label="Contact details">
                        <?php if ($profile['phone']): ?>
                            <a href="tel:<?= e(preg_replace('/[^0-9+]/', '', $profile['phone'])) ?>">
                                <span class="detail-label">☎ </span><span><?= e($profile['phone']) ?></span>
                            </a>
                        <?php endif; ?>
                        <?php if ($profile['email']): ?>
                            <a href="mailto:<?= e($profile['email']) ?>">
                                <span class="detail-label"> | 📧 </span><span><?= e($profile['email']) ?></span>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="hero-visual">
                <div class="visual-orbit orbit-a"></div>
                <div class="visual-orbit orbit-b"></div>
                <div class="portrait-card">
                    <div class="portrait-topline"><span>PROFILE / 001</span><span>IT · SYSTEMS</span></div>
                    <div class="portrait-placeholder" role="img" aria-label="Profile portrait">
                        <?php if ($profile['photo'] && is_file(__DIR__ . '/uploads/' . $profile['photo'])): ?>
                            <img src="<?= e(url('uploads/' . $profile['photo'])) ?>" alt="<?= e($profile['name']) ?>" class="profile-photo">
                        <?php else: ?>
                            <div class="portrait-grid"></div>
                            <div class="portrait-initials">AS<span>.</span></div>
                            <div class="portrait-hint">PORTFOLIO PROFILE</div>
                        <?php endif; ?>
                    </div>
                    <div class="portrait-caption">
                        <div><strong><?= e($profile['name']) ?></strong><span>Support. Systems. Solutions.</span></div>
                        <span class="caption-arrow">↗</span>
                    </div>
                </div>
                <div class="floating-chip chip-top"><span class="chip-icon">✳</span><span>Reliable IT support</span></div>
                <div class="floating-chip chip-bottom"><span class="chip-pulse"></span><span>Always learning</span></div>
            </div>
            <a class="scroll-cue" href="#about"><span class="scroll-line"></span> SCROLL TO EXPLORE</a>
        </section>

        <!-- 01 / ABOUT -->
        <section class="section about-section" id="about">
            <div class="container section-grid">
                <div class="section-heading reveal">
                    <p class="eyebrow"><?= e($profile['sec_about_kicker']) ?></p>
                    <h2><?= e($profile['sec_about_title']) ?><br><span><?= e($profile['sec_about_accent']) ?></span></h2>
                </div>
                <div class="about-content reveal">
                    <p class="lead"><?= e($profile['headline']) ?></p>
                    <p><?= e($profile['intro']) ?></p>
                    <div class="stat-grid">
                        <div class="stat"><strong>L1/L2</strong><span>End-user support</span></div>
                        <div class="stat"><strong>IT</strong><span>Support & operations</span></div>
                        <div class="stat"><strong>4+</strong><span>Years in IT support roles</span></div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 02 / TOOLKIT (SKILLS) -->
        <section class="section skills-section" id="skills">
            <div class="container">
                <div class="section-heading section-heading-wide reveal">
                    <div>
                        <p class="eyebrow"><?= e($profile['sec_skills_kicker']) ?></p>
                        <h2><?= e($profile['sec_skills_title']) ?> <span><?= e($profile['sec_skills_accent']) ?></span></h2>
                    </div>
                    <?php if (!empty($profile['sec_skills_intro'])): ?>
                        <p class="section-intro"><?= e($profile['sec_skills_intro']) ?></p>
                    <?php endif; ?>
                </div>
                <div class="skill-filters reveal">
                    <button class="filter-button active" data-filter="all">All skills</button>
                    <?php foreach (array_keys($groups) as $group): ?>
                        <button class="filter-button" data-filter="<?= e(strtolower(str_replace(' ', '-', (string)$group))) ?>"><?= e((string)$group) ?></button>
                    <?php endforeach; ?>
                </div>
                <div class="skills-grid">
                    <?php foreach ($skills as $skill): ?>
                        <article class="skill-card reveal" data-group="<?= e(strtolower(str_replace(' ', '-', (string)$skill['group_name']))) ?>">
                            <div class="skill-card-top">
                                <span class="skill-symbol">✳</span>
                                <span class="skill-status"><?= e($skill['level']) ?></span>
                            </div>
                            <h3><?= e($skill['name']) ?></h3>
                            <p><?= e($skill['group_name']) ?></p>
                        </article>
                    <?php endforeach; ?>
                </div>

                <?php if (!empty($profile['learning_text'])): ?>
                    <div class="learning-note reveal">
                        <span class="learning-icon">↗</span>
                        <div>
                            <strong><?= e($profile['learning_title'] ?: 'Currently growing') ?></strong>
                            <p><?= e($profile['learning_text']) ?></p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- 03 / CAREER (EXPERIENCE) -->
        <section class="section experience-section" id="experience">
            <div class="container">
                <div class="section-heading section-heading-wide reveal">
                    <div>
                        <p class="eyebrow"><?= e($profile['sec_exp_kicker']) ?></p>
                        <h2><?= e($profile['sec_exp_title']) ?> <span><?= e($profile['sec_exp_accent']) ?></span></h2>
                    </div>
                    <?php if (!empty($profile['sec_exp_intro'])): ?>
                        <p class="section-intro"><?= e($profile['sec_exp_intro']) ?></p>
                    <?php endif; ?>
                </div>
                <div class="timeline">
                    <?php foreach ($experience as $i => $job): ?>
                        <article class="timeline-item reveal">
                            <div class="timeline-marker"><span></span></div>
                            <div class="timeline-date"><?= e($job['period']) ?></div>
                            <div class="timeline-content">
                                <div class="job-heading">
                                    <div>
                                        <h3><?= e($job['role']) ?></h3>
                                        <p class="company"><?= e($job['company']) ?> <span>·</span> <?= e($job['location'] ?? '') ?></p>
                                    </div>
                                    <span class="job-index"><?= e(str_pad((string)($i + 1), 2, '0', STR_PAD_LEFT)) ?></span>
                                </div>
                                <ul>
                                    <?php
                                    $bullets = $expResponsibilities[(int)$job['id']] ?? [];
                                    foreach ($bullets as $point):
                                    ?>
                                        <li><?= e((string)$point) ?></li>
                                    <?php endforeach; ?>
                                </ul>

                                <!-- Career Progression: Earlier Roles at this Same Company -->
                                <?php if (!empty($childRoles[(int)$job['id']])): ?>
                                    <div class="nested-promotions" style="margin-top: 1.5rem; padding-top: 1.25rem; border-top: 1px dashed rgba(255, 255, 255, 0.15);">
                                        <div style="font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.08em; color: #38bdf8; margin-bottom: 0.9rem; font-weight: 600;">
                                            Previous Role at <?= e($job['company']) ?>
                                        </div>
                                        <?php foreach ($childRoles[(int)$job['id']] as $subRole): ?>
                                            <div class="promotion-subitem" style="margin-bottom: 1rem; padding-left: 0.85rem; border-left: 2px solid #0284c7;">
                                                <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 0.35rem;">
                                                    <h4 style="font-size: 1.05rem; margin: 0; color: #cbd5e1; font-weight: 600;">
                                                        <?= e($subRole['role']) ?>
                                                    </h4>
                                                    <span style="font-size: 0.85rem; color: #94a3b8; font-family: monospace;"><?= e($subRole['period']) ?></span>
                                                </div>
                                                <ul style="margin-top: 0.35rem;">
                                                    <?php foreach ($expResponsibilities[(int)$subRole['id']] ?? [] as $subPoint): ?>
                                                        <li><?= e((string)$subPoint) ?></li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- 04 / SELECTED WORK (PROJECTS) -->
        <section class="section projects-section" id="projects">
            <div class="container">
                <div class="section-heading section-heading-wide reveal">
                    <div>
                        <p class="eyebrow"><?= e($profile['sec_proj_kicker']) ?></p>
                        <h2><?= e($profile['sec_proj_title']) ?> <span><?= e($profile['sec_proj_accent']) ?></span></h2>
                    </div>
                    <?php if (!empty($profile['sec_proj_intro'])): ?>
                        <p class="section-intro"><?= e($profile['sec_proj_intro']) ?></p>
                    <?php endif; ?>
                </div>
                <div class="projects-grid">
                    <?php foreach ($projects as $i => $project): ?>
                        <article class="project-card reveal">
                            <div class="project-art project-art-<?= ($i % 3) + 1 ?>">
                                <span class="project-number"><?= e(str_pad((string)($i + 1), 2, '0', STR_PAD_LEFT)) ?></span>
                                <div class="network-art"><i></i><i></i><i></i><i></i><b></b><b></b><b></b></div>
                                <span class="project-art-label"><?= e($project['project_type']) ?></span>
                            </div>
                            <div class="project-info">
                                <p class="project-type"><?= e($project['project_type']) ?></p>
                                <h3><?= e($project['title']) ?></h3>
                                <p><?= e($project['description']) ?></p>
                                <div class="tag-list">
                                    <?php
                                    $tags = $projectTags[(int)$project['id']] ?? [];
                                    foreach ($tags as $tag):
                                    ?>
                                        <span><?= e((string)$tag) ?></span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- 05 / EDUCATION -->
        <section class="section education-section" id="education">
            <div class="container section-grid">
                <div class="section-heading reveal">
                    <p class="eyebrow"><?= e($profile['sec_edu_kicker']) ?></p>
                    <h2><?= e($profile['sec_edu_title']) ?><br><span><?= e($profile['sec_edu_accent']) ?></span></h2>
                </div>
                <div class="education-list">
                    <?php foreach ($education as $item): ?>
                        <article class="education-card reveal">
                            <div class="education-icon">↗</div>
                            <div>
                                <p class="education-period"><?= e($item['period']) ?></p>
                                <h3><?= e($item['degree']) ?></h3>
                                <p class="education-institution"><?= e($item['institution']) ?></p>
                                <p class="education-detail"><?= e($item['detail'] ?? '') ?></p>
                            </div>
                        </article>
                    <?php endforeach; ?>

                    <?php if (!empty($profile['edu_learning_text'])): ?>
                        <div class="learning-card reveal">
                            <div class="learning-icon">◎</div>
                            <div>
                                <p class="eyebrow">CONTINUOUS LEARNING</p>
                                <h3><?= e($profile['edu_learning_title']) ?></h3>
                                <p><?= e($profile['edu_learning_text']) ?></p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- 06 / GET IN TOUCH (CONTACT) -->
        <section class="section contact-section" id="contact">
            <div class="container contact-layout">
                <div class="contact-copy reveal">
                    <p class="eyebrow"><?= e($profile['sec_contact_kicker']) ?></p>
                    <h2><?= e($profile['sec_contact_title']) ?><br><span><?= e($profile['sec_contact_accent']) ?></span></h2>
                    <p><?= e($profile['sec_contact_intro']) ?></p>

                    <?php if ($profile['linkedin']): ?>
                        <a class="contact-link" href="<?= e($profile['linkedin']) ?>" target="_blank" rel="noopener noreferrer">
                            <span class="contact-link-icon">in</span>
                            <span><small>CONNECT ON</small><strong>LinkedIn</strong></span>
                            <span class="contact-arrow">↗</span>
                        </a>
                    <?php endif; ?>
                    <?php if ($profile['email']): ?>
                        <a class="contact-link" href="mailto:<?= e($profile['email']) ?>">
                            <span class="contact-link-icon">@</span>
                            <span><small>EMAIL ME</small><strong><?= e($profile['email']) ?></strong></span>
                            <span class="contact-arrow">↗</span>
                        </a>
                    <?php endif; ?>
                    <?php if ($profile['phone']): ?>
                        <a class="contact-link" href="tel:<?= e(preg_replace('/[^0-9+]/', '', $profile['phone'])) ?>">
                            <span class="contact-link-icon">☎</span>
                            <span><small>CALL ME</small><strong><?= e($profile['phone']) ?></strong></span>
                            <span class="contact-arrow">↗</span>
                        </a>
                    <?php endif; ?>

                    <div class="contact-location"><span class="status-dot"></span> Located in <?= e($profile['location']) ?></div>
                </div>

                <div class="contact-form-wrap reveal">
                    <div class="form-header"><span>INQUIRY FORM</span><span class="form-asterisk">✳</span></div>

                    <div id="formAlertBox">
                        <?php foreach ($flashes as $f): ?>
                            <div class="flash flash-<?= e($f['type']) ?>" role="status"><?= e($f['message']) ?></div>
                        <?php endforeach; ?>
                    </div>

                    <form action="<?= e(url('index.php')) ?>" method="post" id="contactForm">
                        <input type="hidden" name="action" value="contact">
                        <input type="hidden" name="csrf_token" id="csrfTokenField" value="<?= e(csrf_token()) ?>">

                        <!-- Anti-bot Honeypot Field -->
                        <div class="honeypot" aria-hidden="true" style="display:none !important;">
                            <label for="website">Leave blank</label>
                            <input id="website" name="website" tabindex="-1" autocomplete="off">
                        </div>

                        <div class="form-row">
                            <label for="name">Your Name <span>*</span>
                                <input id="name" name="name" type="text" placeholder="Jane Doe" maxlength="120" required>
                            </label>
                            <label for="email">Email Address <span>*</span>
                                <input id="email" name="email" type="email" placeholder="jane@example.com" maxlength="254" required>
                            </label>
                        </div>
                        <label for="subject">Subject
                            <input id="subject" name="subject" type="text" placeholder="Opportunity / Consultation" maxlength="180">
                        </label>
                        <label for="message">Message <span>*</span>
                            <textarea id="message" name="message" rows="5" maxlength="5000" placeholder="How can I assist your team?" required></textarea>
                        </label>
                        <div class="form-bottom">
                            <p><span>*</span> Required fields</p>
                            <button class="button button-primary submit-button" id="submitBtn" type="submit">
                                <span class="btn-text">Send message</span> <span>↗</span>
                            </button>
                        </div>
                        <p class="privacy-note">Messages are received directly into my inbox and admin panel.</p>
                    </form>
                </div>
            </div>
        </section>
    </main>

    <footer class="site-footer">
        <div class="container footer-inner">
            <a class="brand" href="#top"><span class="brand-mark">S.</span><span>SHIHAB<span class="brand-dot">/</span></span></a>
            <p>Designed with purpose. Built to keep systems moving.</p>
            <a href="#top" class="back-top">BACK TO TOP ↑</a>
            <span class="copyright">&copy; <?= date('Y') ?> <?= e($profile['name']) ?></span>
            <a class="admin-footer-link" href="<?= e(url('admin/login')) ?>">Admin</a>
        </div>
    </footer>
</body>

</html>