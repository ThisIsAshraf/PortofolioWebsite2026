<?php

declare(strict_types=1);

require_once __DIR__ . '/config.php';

$pdo = db();

// 1. Granular Data
$name        = setting('cv_display_name', setting('name', 'Ashraful Alam Shihab'));
$role        = setting('cv_display_title', setting('role', 'Senior Desktop Support Engineer'));
$atsSubline  = setting('cv_ats_subline', 'Senior Desktop Support Engineer | IT Support | Service Desk | IT Operations');
$phone       = setting('cv_phone', setting('phone', '+8801982401992'));
$email       = setting('cv_email', setting('email', 'meet.ashraf.uk@gmail.com'));
$location    = setting('cv_location', setting('location', 'Dhaka, Bangladesh'));
$linkedin    = setting('cv_linkedin', 'linkedin.com/in/ashraful98');
$photo       = setting('photo', '');
$summary     = setting('cv_custom_summary', setting('intro', 'Experienced Technical Support Specialist with over 4+ years of expertise in resolving complex IT issues, managing IT hardware and software, and possessing a solid foundation in networking, Linux systems, and Windows Server. Demonstrated success in ensuring seamless operations, showcasing strong problem-solving skills, and employing effective communication to ensure end-user satisfaction.'));

// Photo Styling Settings
$photoShape  = setting('cv_photo_shape', 'rounded');
$photoSize   = setting('cv_photo_size', '110px');
$photoBorder = setting('cv_photo_border_color', '#ea580c');

// Dynamic Section Titles
$titleSummary      = setting('cv_sec_summary_title', 'Professional Summary');
$titleExpertise    = setting('cv_sec_expertise_title', 'Areas of Expertise');
$titleCompetencies = setting('cv_sec_competencies_title', 'Core Competencies');
$titleExperience   = setting('cv_sec_experience_title', 'Professional Experience');
$titleEducation    = setting('cv_sec_education_title', 'Education');
$titleMatrix       = setting('cv_sec_matrix_title', 'Technical Skills Matrix');
$titleTraining     = setting('cv_sec_training_title', 'Training & Certifications');
$titleLanguages    = setting('cv_sec_languages_title', 'Languages');

// Section Visibility Toggles
$showPhoto        = setting('cv_show_photo', '1') === '1';
$showSummary      = setting('cv_show_summary', '1') === '1';
$showCompetencies = setting('cv_show_competencies', '1') === '1';
$showMatrix       = setting('cv_show_matrix', '1') === '1';
$showExperience   = setting('cv_show_experience', '1') === '1';
$showEducation    = setting('cv_show_education', '1') === '1';
$showTraining     = setting('cv_show_training', '1') === '1';
$showLanguages    = setting('cv_show_languages', '1') === '1';

// Dynamic Matrix JSON
$matrixJson = setting('cv_matrix_json', '');
$matrixRows = json_decode($matrixJson, true);
if (!is_array($matrixRows) || empty($matrixRows)) {
    $matrixRows = [
        ['label' => 'IT Support & Operation', 'skills' => 'L1/L2 Support, Service Desk, Remote Support, Asset Management, IT Procurement, Vendor Coordination, Hardware/Software Troubleshooting'],
        ['label' => 'Ticketing & SaaS', 'skills' => 'Zendesk, Jira SM, ServiceNow, Google Workspace, M365 Administration'],
        ['label' => 'Networking & OS', 'skills' => 'TCP/IP, DNS, LAN/WAN, Windows Server, RHEL Administration'],
        ['label' => 'Cloud and Database', 'skills' => 'AWS, Azure, MySQL, MSSQL Server']
    ];
}

$languages = array_values(array_filter(array_map('trim', explode("\n", setting('cv_languages', "Bangla - Native\nEnglish - Fluent\nHindi - Fluent")))));
$trainings = array_values(array_filter(array_map('trim', explode("\n", setting('cv_trainings', "CCNA 200-301 — 2023\nLinux System Administration — 2022\nGoogle IT Support Professional — 2022\nWindows Server Administration — 2022\nMS SQL Server Administration — 2021\nNetworking Essential — 2021")))));

// 2. Active Template Resolution
$requestedLayout = (string)($_GET['layout'] ?? '');
$currentTpl = null;

if ($requestedLayout !== '') {
    $stmt = $pdo->prepare('SELECT * FROM cv_templates WHERE template_key = ?');
    $stmt->execute([$requestedLayout]);
    $currentTpl = $stmt->fetch();
}

if (!$currentTpl) {
    $currentTpl = $pdo->query('SELECT * FROM cv_templates WHERE is_default = 1 LIMIT 1')->fetch();
    if (!$currentTpl) {
        $currentTpl = $pdo->query('SELECT * FROM cv_templates WHERE is_enabled = 1 ORDER BY sort_order ASC LIMIT 1')->fetch();
    }
}

$layoutKey = $currentTpl['template_key'] ?? 'modern_orange';

// 3. Database Content Queries
$skills = $pdo->query('SELECT name FROM skills WHERE is_visible = 1 ORDER BY sort_order ASC, id ASC')->fetchAll(PDO::FETCH_COLUMN);
if (count($skills) < 3) {
    $skills = [
        'L1/L2 IT & Desktop Support',
        'Technical Support',
        'Hardware Support',
        'Troubleshooting',
        'Email Communication',
        'Customer Support',
        'Global Client Management',
        'Windows Server & Linux',
        'Ticketing Systems'
    ];
}

$experience = $pdo->query('SELECT * FROM experience WHERE is_visible = 1 ORDER BY sort_order ASC, id ASC')->fetchAll();

$expResponsibilities = [];
foreach ($pdo->query('SELECT * FROM experience_responsibilities ORDER BY sort_order ASC')->fetchAll() as $r) {
    $expResponsibilities[(int)$r['experience_id']][] = $r['responsibility'];
}

$education = $pdo->query('SELECT * FROM education WHERE is_visible = 1 ORDER BY sort_order ASC, id ASC')->fetchAll();

$borderRadius = match ($photoShape) {
    'circle' => '50%',
    'rounded' => '12px',
    default => '0px',
};
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($name) ?> — Curriculum Vitae</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400..700;1,9..40,400..700&family=Space+Grotesk:wght@500;600;700;800&family=Merriweather:ital,wght@0,300;0,400;0,700;1,300&display=swap" rel="stylesheet">
    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        @page {
            size: A4 portrait;
            margin: 8mm 10mm;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            background: #1e293b;
            color: #1e293b;
            font-family: 'DM Sans', -apple-system, sans-serif;
            font-size: 11.5px;
            line-height: 1.42;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .cv-action-bar {
            position: sticky;
            top: 0;
            z-index: 1000;
            background: #0f172a;
            color: #ffffff;
            padding: 10px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.3);
        }

        .cv-action-bar button,
        .cv-action-bar a {
            background: #b7f36b;
            color: #0f172a;
            font-weight: 700;
            border: none;
            padding: 6px 14px;
            border-radius: 6px;
            font-size: 12px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        /* Strict A4 Container with Smart Spacing */
        .cv-page {
            width: 210mm;
            margin: 18px auto;
            background: #ffffff;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.35);
            box-sizing: border-box;
            position: relative;
        }

        .cv-block,
        .job-item,
        .sec-title,
        .matrix-table,
        .competencies-grid {
            page-break-inside: avoid;
            break-inside: avoid;
        }

        .sec-title {
            page-break-after: avoid;
            break-after: avoid;
        }

        /* Photo Stylings */
        .cv-photo-dynamic {
            width: <?= $photoSize ?>;
            height: <?= $photoSize ?>;
            border-radius: <?= $borderRadius ?>;
            border: 2px solid <?= $photoBorder ?>;
            object-fit: cover;
            display: block;
            flex-shrink: 0;
        }

        .cv-photo-ph {
            width: <?= $photoSize ?>;
            height: <?= $photoSize ?>;
            border-radius: <?= $borderRadius ?>;
            border: 2px solid <?= $photoBorder ?>;
            background: #0f172a;
            color: #fff;
            display: grid;
            place-content: center;
            font-size: 30px;
            font-weight: 700;
            flex-shrink: 0;
        }

        /* =====================================================================
           LAYOUT 1: MODERN ORANGE BADGES (Compacted to Fit 1-2 Pages Seamlessly)
           ===================================================================== */
        .layout-modern_orange {
            padding: 24px 32px;
        }

        .layout-modern_orange .mo-header {
            display: flex;
            gap: 16px;
            align-items: center;
            margin-bottom: 12px;
        }

        .layout-modern_orange h1 {
            font-size: 26px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.03em;
            margin: 0 0 4px;
            font-family: 'Space Grotesk', sans-serif;
            line-height: 1.1;
        }

        .layout-modern_orange .mo-summary {
            font-size: 11px;
            color: #475569;
            line-height: 1.45;
            margin: 0 0 8px;
        }

        .layout-modern_orange .mo-contacts {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            font-size: 10.5px;
            color: #334155;
            font-weight: 600;
        }

        .layout-modern_orange .sec-title {
            font-size: 11.5px;
            font-weight: 800;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin: 14px 0 8px;
        }

        .layout-modern_orange .mo-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            margin-bottom: 10px;
        }

        .layout-modern_orange .mo-badge {
            background: #ea580c;
            color: #ffffff;
            padding: 3px 9px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 600;
            display: inline-block;
        }

        .layout-modern_orange .mo-job {
            border-left: 2px solid #ea580c;
            padding-left: 12px;
            margin-bottom: 12px;
        }

        .layout-modern_orange .mo-company {
            font-size: 13.5px;
            font-weight: 700;
            color: #dc2626;
            margin: 0 0 1px;
        }

        .layout-modern_orange .mo-role-line {
            display: flex;
            justify-content: space-between;
            font-size: 11.5px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 4px;
        }

        .layout-modern_orange .mo-date {
            color: #64748b;
            font-size: 10.5px;
            font-weight: 500;
        }

        .layout-modern_orange ul {
            margin: 0;
            padding-left: 15px;
            font-size: 11px;
            color: #334155;
            line-height: 1.45;
        }

        .layout-modern_orange li {
            margin-bottom: 2px;
        }

        .layout-modern_orange .bottom-two-col {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 10px;
        }

        /* =====================================================================
           LAYOUT 2: EXECUTIVE SPLIT (Novoresume Full Height)
           ===================================================================== */
        .layout-executive_split {
            display: grid;
            grid-template-columns: 31% 69%;
            min-height: 297mm;
        }

        .layout-executive_split .es-sidebar {
            background: #0f172a;
            color: #f8fafc;
            padding: 28px 18px;
            box-sizing: border-box;
        }

        .layout-executive_split .es-sidebar h2 {
            font-size: 18px;
            margin: 10px 0 2px;
            color: #fff;
            font-family: 'Space Grotesk', sans-serif;
        }

        .layout-executive_split .es-role {
            font-size: 11px;
            color: #94a3b8;
            margin-bottom: 16px;
        }

        .layout-executive_split .es-sidebar h3 {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #38bdf8;
            border-bottom: 1px solid #334155;
            padding-bottom: 4px;
            margin: 16px 0 8px;
        }

        .layout-executive_split .es-sidebar p,
        .layout-executive_split .es-sidebar li {
            font-size: 10.5px;
            color: #cbd5e1;
            line-height: 1.45;
        }

        .layout-executive_split .es-main {
            padding: 28px 24px;
            box-sizing: border-box;
        }

        .layout-executive_split .es-main h3 {
            font-size: 11.5px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #0284c7;
            border-bottom: 1.5px solid #e2e8f0;
            padding-bottom: 3px;
            margin: 16px 0 8px;
        }

        .layout-executive_split .es-main h3:first-child {
            margin-top: 0;
        }

        /* =====================================================================
           LAYOUT 3: CREATIVE BANNER (True Full-Width Edge-to-Edge)
           ===================================================================== */
        .layout-creative_banner {
            padding: 0;
        }

        .layout-creative_banner .cb-top {
            background: #0f172a;
            color: #ffffff;
            padding: 24px 30px;
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .layout-creative_banner .cb-body {
            padding: 24px 30px;
        }

        .layout-creative_banner .cb-sec-title {
            font-size: 11.5px;
            text-transform: uppercase;
            font-weight: 800;
            letter-spacing: 0.08em;
            color: #0f172a;
            border-bottom: 1.5px solid #0f172a;
            padding-bottom: 3px;
            margin: 16px 0 8px;
        }

        /* =====================================================================
           LAYOUT 4: NOVORESUME MODERN
           ===================================================================== */
        .layout-novoresume_modern {
            padding: 28px 32px;
            font-family: 'DM Sans', sans-serif;
        }

        .layout-novoresume_modern .nm-header-box {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            padding-bottom: 14px;
            border-bottom: 2px solid #2563eb;
            margin-bottom: 14px;
        }

        .layout-novoresume_modern .nm-name {
            font-size: 26px;
            font-weight: 800;
            color: #0f172a;
            font-family: 'Space Grotesk', sans-serif;
            margin: 0 0 3px;
        }

        .layout-novoresume_modern .nm-role {
            font-size: 12.5px;
            color: #2563eb;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .layout-novoresume_modern .nm-contacts {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            font-size: 10.5px;
            color: #475569;
            font-weight: 500;
        }

        .layout-novoresume_modern .sec-title {
            font-size: 11.5px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #2563eb;
            border-bottom: 1.5px solid #e2e8f0;
            padding-bottom: 3px;
            margin: 16px 0 8px;
        }

        .layout-novoresume_modern .nm-badge-chip {
            background: #eff6ff;
            color: #1d4ed8;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 600;
            display: inline-block;
            margin: 2px;
            border: 1px solid #bfdbfe;
        }

        /* =====================================================================
           LAYOUT 5: HYBRID TEAL EXECUTIVE
           ===================================================================== */
        .layout-hybrid_teal {
            padding: 26px 32px;
        }

        .layout-hybrid_teal .ht-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            background: #042f2e;
            color: #ffffff;
            padding: 16px 22px;
            border-radius: 8px;
            margin-bottom: 14px;
        }

        .layout-hybrid_teal .sec-title {
            font-size: 11.5px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #0f766e;
            border-bottom: 1.5px solid #ccfbf1;
            padding-bottom: 3px;
            margin: 14px 0 8px;
        }

        .layout-hybrid_teal .ht-badge {
            background: #f0fdfa;
            color: #0f766e;
            border: 1px solid #99f6e4;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 600;
            display: inline-block;
            margin: 2px;
        }

        .layout-hybrid_teal .ht-job-border {
            border-left: 2px solid #0d9488;
            padding-left: 10px;
            margin-bottom: 12px;
        }

        /* =====================================================================
           LAYOUT 6: IVY LEAGUE COMPACT
           ===================================================================== */
        .layout-ivy_compact {
            padding: 26px 32px;
            border-top: 4px solid #4338ca;
        }

        .layout-ivy_compact .ic-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            margin-bottom: 14px;
        }

        .layout-ivy_compact .sec-title {
            font-size: 11.5px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #4338ca;
            border-bottom: 1px solid #e0e7ff;
            padding-bottom: 3px;
            margin: 14px 0 8px;
        }

        .layout-ivy_compact .ic-badge {
            background: #eef2ff;
            color: #3730a3;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 600;
            display: inline-block;
            margin: 2px;
        }

        /* =====================================================================
           LAYOUT 7: CLASSIC CORPORATE ATS (No Photo)
           ===================================================================== */
        .layout-classic_ats {
            padding: 28px 36px;
            font-family: 'Merriweather', serif;
            color: #000000;
            line-height: 1.45;
        }

        .layout-classic_ats h1 {
            font-family: 'DM Sans', sans-serif;
            font-size: 26px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin: 0 0 2px;
            color: #000;
        }

        .layout-classic_ats .ats-subline {
            font-family: 'DM Sans', sans-serif;
            color: #1d4ed8;
            font-size: 12px;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .layout-classic_ats .ats-contact-bar {
            font-family: 'DM Sans', sans-serif;
            font-size: 10.5px;
            color: #334155;
            padding-bottom: 6px;
            border-bottom: 1px solid #000000;
            margin-bottom: 10px;
        }

        .layout-classic_ats .sec-title {
            font-family: 'DM Sans', sans-serif;
            font-size: 11.5px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            border-bottom: 1px solid #000000;
            padding-bottom: 2px;
            margin: 14px 0 6px;
            color: #000;
        }

        .layout-classic_ats .competencies-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 3px 10px;
            font-family: 'DM Sans', sans-serif;
            font-size: 10.5px;
            margin-bottom: 8px;
        }

        .layout-classic_ats .ats-comp-item::before {
            content: "•";
            margin-right: 4px;
            font-weight: bold;
        }

        .layout-classic_ats .ats-company {
            color: #b91c1c;
            font-size: 13.5px;
            font-weight: 700;
            margin: 8px 0 1px;
        }

        .layout-classic_ats .ats-role-line {
            color: #1d4ed8;
            font-size: 11.5px;
            font-weight: 700;
            font-family: 'DM Sans', sans-serif;
            margin-bottom: 3px;
        }

        .layout-classic_ats ul {
            margin: 2px 0 8px;
            padding-left: 16px;
            font-size: 11px;
        }

        .layout-classic_ats li {
            margin-bottom: 2px;
        }

        .layout-classic_ats .matrix-table {
            width: 100%;
            border-collapse: collapse;
            font-family: 'DM Sans', sans-serif;
            font-size: 10.5px;
            margin-top: 6px;
        }

        .layout-classic_ats .matrix-table td {
            border: 1px solid #000;
            padding: 4px 6px;
        }

        .layout-classic_ats .matrix-table td:first-child {
            font-weight: 700;
            width: 28%;
            background: #f8fafc;
        }

        @media print {
            body {
                background: transparent !important;
                color: #000 !important;
            }

            .cv-action-bar {
                display: none !important;
            }

            .cv-page {
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                box-shadow: none !important;
            }
        }

        @media print {

            html,
            body {
                background: #ffffff !important;
                color: #000000 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .cv-action-bar {
                display: none !important;
            }

            .cv-page {
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                box-shadow: none !important;
            }

            .mo-badge,
            .ht-badge,
            .ic-badge,
            .nm-badge-chip {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
    </style>
</head>

<body>

    <div class="cv-action-bar">
        <div>
            <strong>Curriculum Vitae Preview</strong>
            <span style="color: #94a3b8; font-size: 12px; margin-left: 12px;">
                Active Style: <strong><?= e($currentTpl['title']) ?></strong>
            </span>
        </div>
        <div style="display: flex; gap: 10px;">
            <button onclick="window.print()">🖨 Print / Save as PDF</button>
            <a href="<?= e(url('admin/cv')) ?>" style="background: #334155; color: #fff;">⚙ Admin CV Editor</a>
            <a href="<?= e(url('')) ?>" style="background: #334155; color: #fff;">Back to Portfolio ↗</a>
        </div>
    </div>

    <div class="cv-page layout-<?= e($layoutKey) ?>">

        <?php if ($layoutKey === 'modern_orange'): ?>
            <!-- ================= TEMPLATE 1: MODERN ORANGE BADGES ================= -->
            <div class="mo-header cv-block">
                <?php if ($showPhoto && $photo && is_file(__DIR__ . '/uploads/' . $photo)): ?>
                    <img src="<?= e(url('uploads/' . $photo)) ?>" alt="<?= e($name) ?>" class="cv-photo-dynamic">
                <?php elseif ($showPhoto): ?>
                    <div class="cv-photo-ph">AS</div>
                <?php endif; ?>
                <div>
                    <h1><?= e($name) ?></h1>
                    <?php if ($showSummary && $summary): ?>
                        <p class="mo-summary"><?= e($summary) ?></p>
                    <?php endif; ?>
                    <div class="mo-contacts">
                        <span>✉ <?= e($email) ?></span>
                        <span>☎ <?= e($phone) ?></span>
                        <span>📍 <?= e($location) ?></span>
                        <span>🔗 <?= e($linkedin) ?></span>
                    </div>
                </div>
            </div>

            <?php if ($showCompetencies && !empty($skills)): ?>
                <div class="cv-block">
                    <div class="sec-title"><?= e($titleExpertise) ?></div>
                    <div class="mo-badges">
                        <?php foreach ($skills as $s): ?>
                            <span class="mo-badge"><?= e($s) ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($showExperience && !empty($experience)): ?>
                <div class="sec-title"><?= e($titleExperience) ?></div>
                <?php foreach ($experience as $job): ?>
                    <div class="mo-job job-item">
                        <div class="mo-company"><?= e($job['company']) ?></div>
                        <div class="mo-role-line">
                            <span><?= e($job['role']) ?></span>
                            <span class="mo-date"><?= e($job['period']) ?></span>
                        </div>
                        <ul>
                            <?php foreach ($expResponsibilities[(int)$job['id']] ?? [] as $r): ?>
                                <li><?= e((string)$r) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <div class="bottom-two-col cv-block">
                <?php if ($showTraining && !empty($trainings)): ?>
                    <div>
                        <div class="sec-title" style="margin-top: 0;"><?= e($titleTraining) ?></div>
                        <ul style="padding-left: 14px; font-size: 10.5px;">
                            <?php foreach ($trainings as $t): ?>
                                <li style="margin-bottom: 2px;"><?= e($t) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <div>
                    <?php if ($showEducation && !empty($education)): ?>
                        <div class="sec-title" style="margin-top: 0;"><?= e($titleEducation) ?></div>
                        <?php foreach ($education as $edu): ?>
                            <div style="margin-bottom: 6px; font-size: 11px;">
                                <strong><?= e($edu['degree']) ?></strong> [<?= e($edu['period']) ?>]<br>
                                <span style="color: #475569;"><?= e($edu['institution']) ?></span>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <?php if ($showLanguages && !empty($languages)): ?>
                        <div class="sec-title"><?= e($titleLanguages) ?></div>
                        <ul style="padding-left: 14px; font-size: 10.5px;">
                            <?php foreach ($languages as $l): ?>
                                <li style="margin-bottom: 2px;"><?= e($l) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>

        <?php elseif ($layoutKey === 'executive_split'): ?>
            <!-- ================= TEMPLATE 2: NOVORESUME EXECUTIVE SPLIT ================= -->
            <aside class="es-sidebar">
                <?php if ($showPhoto && $photo && is_file(__DIR__ . '/uploads/' . $photo)): ?>
                    <img src="<?= e(url('uploads/' . $photo)) ?>" alt="Photo" class="cv-photo-dynamic" style="margin-bottom: 12px;">
                <?php elseif ($showPhoto): ?>
                    <div class="cv-photo-ph" style="margin-bottom: 12px;">AS</div>
                <?php endif; ?>
                <h2><?= e($name) ?></h2>
                <div class="es-role"><?= e($role) ?></div>

                <h3>Contact</h3>
                <p>
                    ✉ <?= e($email) ?><br>
                    ☎ <?= e($phone) ?><br>
                    📍 <?= e($location) ?><br>
                    🔗 <?= e($linkedin) ?>
                </p>

                <?php if ($showCompetencies && !empty($skills)): ?>
                    <h3><?= e($titleExpertise) ?></h3>
                    <ul style="padding-left: 14px; margin: 0;">
                        <?php foreach ($skills as $s): ?>
                            <li style="margin-bottom: 3px;"><?= e($s) ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <?php if ($showLanguages && !empty($languages)): ?>
                    <h3><?= e($titleLanguages) ?></h3>
                    <ul style="padding-left: 14px; margin: 0;">
                        <?php foreach ($languages as $l): ?>
                            <li style="margin-bottom: 3px;"><?= e($l) ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </aside>

            <main class="es-main">
                <?php if ($showSummary && $summary): ?>
                    <div class="cv-block">
                        <h3><?= e($titleSummary) ?></h3>
                        <p style="font-size: 11px; color: #475569; line-height: 1.45; margin-top: 0;"><?= e($summary) ?></p>
                    </div>
                <?php endif; ?>

                <?php if ($showExperience && !empty($experience)): ?>
                    <h3><?= e($titleExperience) ?></h3>
                    <?php foreach ($experience as $job): ?>
                        <div class="job-item" style="margin-bottom: 12px;">
                            <div style="display: flex; justify-content: space-between; font-weight: 700; font-size: 12.5px; color: #0f172a;">
                                <span><?= e($job['role']) ?></span>
                                <span style="font-size: 10.5px; color: #64748b; font-weight: 500;"><?= e($job['period']) ?></span>
                            </div>
                            <div style="color: #0284c7; font-weight: 600; font-size: 11.5px; margin: 1px 0 4px;"><?= e($job['company']) ?></div>
                            <ul style="margin: 0; padding-left: 14px; font-size: 11px; color: #334155;">
                                <?php foreach ($expResponsibilities[(int)$job['id']] ?? [] as $r): ?>
                                    <li style="margin-bottom: 2px;"><?= e((string)$r) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <?php if ($showEducation && !empty($education)): ?>
                    <div class="cv-block">
                        <h3><?= e($titleEducation) ?></h3>
                        <?php foreach ($education as $edu): ?>
                            <div style="margin-bottom: 6px; font-size: 11px;">
                                <strong><?= e($edu['degree']) ?></strong> &middot; <?= e($edu['institution']) ?> (<?= e($edu['period']) ?>)
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </main>

        <?php elseif ($layoutKey === 'creative_banner'): ?>
            <!-- ================= TEMPLATE 3: CREATIVE BANNER ================= -->
            <div class="cb-top cv-block">
                <?php if ($showPhoto && $photo && is_file(__DIR__ . '/uploads/' . $photo)): ?>
                    <img src="<?= e(url('uploads/' . $photo)) ?>" alt="Photo" class="cv-photo-dynamic">
                <?php elseif ($showPhoto): ?>
                    <div class="cv-photo-ph">AS</div>
                <?php endif; ?>
                <div>
                    <h1 style="font-size: 26px; margin: 0 0 3px; font-family: 'Space Grotesk', sans-serif;"><?= e($name) ?></h1>
                    <div style="color: #b7f36b; font-size: 12.5px; font-weight: 600; margin-bottom: 5px;"><?= e($role) ?></div>
                    <div style="font-size: 10.5px; color: #cbd5e1; display: flex; gap: 12px; flex-wrap: wrap;">
                        <span>✉ <?= e($email) ?></span>
                        <span>☎ <?= e($phone) ?></span>
                        <span>📍 <?= e($location) ?></span>
                        <span>🔗 <?= e($linkedin) ?></span>
                    </div>
                </div>
            </div>
            <div class="cb-body">
                <?php if ($showSummary && $summary): ?>
                    <p style="font-size: 11.5px; color: #475569; margin: 0 0 14px;"><?= e($summary) ?></p>
                <?php endif; ?>

                <?php if ($showCompetencies && !empty($skills)): ?>
                    <div class="cv-block">
                        <div class="cb-sec-title"><?= e($titleExpertise) ?></div>
                        <div style="display: flex; flex-wrap: wrap; gap: 5px; margin-bottom: 12px;">
                            <?php foreach ($skills as $s): ?>
                                <span style="background: #f1f5f9; padding: 3px 8px; border-radius: 4px; font-size: 10px; font-weight: 600;"><?= e($s) ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($showExperience && !empty($experience)): ?>
                    <div class="cb-sec-title"><?= e($titleExperience) ?></div>
                    <?php foreach ($experience as $job): ?>
                        <div class="job-item" style="margin-bottom: 12px;">
                            <strong style="font-size: 12px;"><?= e($job['role']) ?></strong> &middot; <span style="color: #0284c7;"><?= e($job['company']) ?></span> <small style="color: #64748b;">(<?= e($job['period']) ?>)</small>
                            <ul style="margin: 3px 0; padding-left: 15px; font-size: 11px;">
                                <?php foreach ($expResponsibilities[(int)$job['id']] ?? [] as $r): ?>
                                    <li><?= e((string)$r) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <?php if ($showEducation && !empty($education)): ?>
                    <div class="cb-sec-title"><?= e($titleEducation) ?></div>
                    <?php foreach ($education as $edu): ?>
                        <div style="margin-bottom: 5px; font-size: 11px;">
                            <strong><?= e($edu['degree']) ?></strong> &mdash; <?= e($edu['institution']) ?> (<?= e($edu['period']) ?>)
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

        <?php elseif ($layoutKey === 'novoresume_modern'): ?>
            <!-- ================= TEMPLATE 4: NOVORESUME MODERN ================= -->
            <div class="nm-header-box cv-block">
                <div>
                    <h1 class="nm-name"><?= e($name) ?></h1>
                    <div class="nm-role"><?= e($role) ?> &middot; <?= e($location) ?></div>
                    <div class="nm-contacts">
                        <span>✉ <?= e($email) ?></span>
                        <span>☎ <?= e($phone) ?></span>
                        <span>🔗 <?= e($linkedin) ?></span>
                    </div>
                </div>
                <?php if ($showPhoto && $photo && is_file(__DIR__ . '/uploads/' . $photo)): ?>
                    <img src="<?= e(url('uploads/' . $photo)) ?>" alt="<?= e($name) ?>" class="cv-photo-dynamic">
                <?php elseif ($showPhoto): ?>
                    <div class="cv-photo-ph">AS</div>
                <?php endif; ?>
            </div>

            <?php if ($showSummary && $summary): ?>
                <div class="cv-block">
                    <p style="font-size: 11.5px; color: #334155; margin-bottom: 12px; line-height: 1.45;"><?= e($summary) ?></p>
                </div>
            <?php endif; ?>

            <?php if ($showCompetencies && !empty($skills)): ?>
                <div class="cv-block">
                    <div class="sec-title"><?= e($titleExpertise) ?></div>
                    <div style="display: flex; flex-wrap: wrap; gap: 4px; margin-bottom: 12px;">
                        <?php foreach ($skills as $s): ?>
                            <span class="nm-badge-chip"><?= e($s) ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($showExperience && !empty($experience)): ?>
                <div class="sec-title"><?= e($titleExperience) ?></div>
                <?php foreach ($experience as $job): ?>
                    <div class="job-item" style="margin-bottom: 12px;">
                        <div style="display: flex; justify-content: space-between; font-weight: 700; font-size: 12.5px;">
                            <span><?= e($job['role']) ?></span>
                            <span style="font-size: 10.5px; color: #64748b; font-weight: 500;"><?= e($job['period']) ?></span>
                        </div>
                        <div style="color: #2563eb; font-weight: 600; font-size: 11.5px; margin-bottom: 2px;"><?= e($job['company']) ?></div>
                        <ul style="margin: 0; padding-left: 15px; font-size: 11px; color: #334155;">
                            <?php foreach ($expResponsibilities[(int)$job['id']] ?? [] as $r): ?>
                                <li><?= e((string)$r) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <?php if ($showEducation && !empty($education)): ?>
                    <div class="cv-block">
                        <div class="sec-title"><?= e($titleEducation) ?></div>
                        <?php foreach ($education as $edu): ?>
                            <div style="margin-bottom: 5px; font-size: 11px;">
                                <strong><?= e($edu['degree']) ?></strong><br>
                                <span style="color: #64748b;"><?= e($edu['institution']) ?> (<?= e($edu['period']) ?>)</span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if ($showLanguages && !empty($languages)): ?>
                    <div class="cv-block">
                        <div class="sec-title"><?= e($titleLanguages) ?></div>
                        <ul style="margin: 0; padding-left: 15px; font-size: 11px; color: #334155;">
                            <?php foreach ($languages as $l): ?>
                                <li><?= e($l) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>

        <?php elseif ($layoutKey === 'hybrid_teal'): ?>
            <!-- ================= TEMPLATE 5: HYBRID TEAL EXECUTIVE ================= -->
            <div class="ht-header cv-block">
                <div>
                    <h1 style="font-size: 26px; font-weight: 800; font-family: 'Space Grotesk', sans-serif; margin: 0 0 3px;"><?= e($name) ?></h1>
                    <div style="color: #5eead4; font-size: 12.5px; font-weight: 600; margin-bottom: 5px;"><?= e($role) ?></div>
                    <div style="font-size: 10.5px; color: #ccfbf1; display: flex; gap: 10px; flex-wrap: wrap;">
                        <span>✉ <?= e($email) ?></span>
                        <span>☎ <?= e($phone) ?></span>
                        <span>📍 <?= e($location) ?></span>
                    </div>
                </div>
                <?php if ($showPhoto && $photo && is_file(__DIR__ . '/uploads/' . $photo)): ?>
                    <img src="<?= e(url('uploads/' . $photo)) ?>" alt="<?= e($name) ?>" class="cv-photo-dynamic" style="border-color: #5eead4;">
                <?php elseif ($showPhoto): ?>
                    <div class="cv-photo-ph" style="border-color: #5eead4;">AS</div>
                <?php endif; ?>
            </div>

            <?php if ($showSummary && $summary): ?>
                <p style="font-size: 11.5px; color: #334155; line-height: 1.45; margin-bottom: 12px;"><?= e($summary) ?></p>
            <?php endif; ?>

            <?php if ($showCompetencies && !empty($skills)): ?>
                <div class="cv-block">
                    <div class="sec-title"><?= e($titleExpertise) ?></div>
                    <div style="margin-bottom: 12px;">
                        <?php foreach ($skills as $s): ?>
                            <span class="ht-badge"><?= e($s) ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($showExperience && !empty($experience)): ?>
                <div class="sec-title"><?= e($titleExperience) ?></div>
                <?php foreach ($experience as $job): ?>
                    <div class="ht-job-border job-item">
                        <div style="display: flex; justify-content: space-between; font-weight: 700; font-size: 12.5px;">
                            <span><?= e($job['role']) ?></span>
                            <span style="font-size: 10.5px; color: #64748b; font-weight: 500;"><?= e($job['period']) ?></span>
                        </div>
                        <div style="color: #0f766e; font-weight: 600; font-size: 11.5px; margin: 1px 0 3px;"><?= e($job['company']) ?></div>
                        <ul style="margin: 0; padding-left: 15px; font-size: 11px; color: #334155;">
                            <?php foreach ($expResponsibilities[(int)$job['id']] ?? [] as $r): ?>
                                <li><?= e((string)$r) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <?php if ($showEducation && !empty($education)): ?>
                <div class="cv-block">
                    <div class="sec-title"><?= e($titleEducation) ?></div>
                    <?php foreach ($education as $edu): ?>
                        <div style="margin-bottom: 5px; font-size: 11px;">
                            <strong><?= e($edu['degree']) ?></strong> &middot; <?= e($edu['institution']) ?> (<?= e($edu['period']) ?>)
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        <?php elseif ($layoutKey === 'ivy_compact'): ?>
            <!-- ================= TEMPLATE 6: IVY LEAGUE COMPACT ================= -->
            <div class="ic-header cv-block">
                <div>
                    <h1 style="font-size: 26px; font-weight: 800; font-family: 'Space Grotesk', sans-serif; color: #0f172a; margin: 0 0 3px;"><?= e($name) ?></h1>
                    <div style="font-size: 12.5px; color: #4338ca; font-weight: 700; margin-bottom: 5px;"><?= e($role) ?> &middot; <?= e($location) ?></div>
                    <div style="font-size: 10.5px; color: #475569; display: flex; gap: 10px; flex-wrap: wrap;">
                        <span>✉ <?= e($email) ?></span>
                        <span>☎ <?= e($phone) ?></span>
                        <span>🔗 <?= e($linkedin) ?></span>
                    </div>
                </div>
                <?php if ($showPhoto && $photo && is_file(__DIR__ . '/uploads/' . $photo)): ?>
                    <img src="<?= e(url('uploads/' . $photo)) ?>" alt="<?= e($name) ?>" class="cv-photo-dynamic" style="border-color: #4338ca;">
                <?php elseif ($showPhoto): ?>
                    <div class="cv-photo-ph" style="border-color: #4338ca;">AS</div>
                <?php endif; ?>
            </div>

            <?php if ($showSummary && $summary): ?>
                <p style="font-size: 11.5px; color: #334155; line-height: 1.45; margin-bottom: 12px;"><?= e($summary) ?></p>
            <?php endif; ?>

            <?php if ($showCompetencies && !empty($skills)): ?>
                <div class="cv-block">
                    <div class="sec-title"><?= e($titleExpertise) ?></div>
                    <div style="margin-bottom: 10px;">
                        <?php foreach ($skills as $s): ?>
                            <span class="ic-badge"><?= e($s) ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($showExperience && !empty($experience)): ?>
                <div class="sec-title"><?= e($titleExperience) ?></div>
                <?php foreach ($experience as $job): ?>
                    <div class="job-item" style="margin-bottom: 10px;">
                        <div style="display: flex; justify-content: space-between; font-weight: 700; font-size: 12px;">
                            <span><?= e($job['role']) ?></span>
                            <span style="font-size: 10.5px; color: #64748b; font-weight: 500;"><?= e($job['period']) ?></span>
                        </div>
                        <div style="color: #4338ca; font-weight: 600; font-size: 11.5px; margin-bottom: 2px;"><?= e($job['company']) ?></div>
                        <ul style="margin: 0; padding-left: 15px; font-size: 11px; color: #334155;">
                            <?php foreach ($expResponsibilities[(int)$job['id']] ?? [] as $r): ?>
                                <li><?= e((string)$r) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

        <?php else: ?>
            <!-- ================= TEMPLATE 7: CLASSIC CORPORATE ATS (No Photo) ================= -->
            <div class="cv-block">
                <h1><?= e($name) ?></h1>
                <div class="ats-subline"><?= e($atsSubline) ?></div>
                <div class="ats-contact-bar">
                    <?= e($email) ?> &nbsp;&middot;&nbsp; <?= e($phone) ?> &nbsp;&middot;&nbsp; <?= e($location) ?> &nbsp;&middot;&nbsp; <?= e($linkedin) ?>
                </div>
            </div>

            <?php if ($showSummary && $summary): ?>
                <div class="cv-block">
                    <p style="margin: 6px 0 10px; font-size: 11.5px; line-height: 1.45;"><?= e($summary) ?></p>
                </div>
            <?php endif; ?>

            <?php if ($showCompetencies && !empty($skills)): ?>
                <div class="competencies-grid cv-block">
                    <div class="sec-title" style="grid-column: 1/-1;"><?= e($titleCompetencies) ?></div>
                    <?php foreach (array_slice($skills, 0, 9) as $s): ?>
                        <div class="ats-comp-item"><?= e($s) ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if ($showExperience && !empty($experience)): ?>
                <div class="sec-title"><?= e($titleExperience) ?></div>
                <?php foreach ($experience as $job): ?>
                    <div class="job-item">
                        <div class="ats-company"><?= e($job['company']) ?></div>
                        <div class="ats-role-line"><?= e($job['role']) ?> &nbsp;|&nbsp; <?= e($job['period']) ?></div>
                        <ul>
                            <?php foreach ($expResponsibilities[(int)$job['id']] ?? [] as $r): ?>
                                <li><?= e((string)$r) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <?php if ($showEducation && !empty($education)): ?>
                <div class="cv-block">
                    <div class="sec-title"><?= e($titleEducation) ?></div>
                    <?php foreach ($education as $edu): ?>
                        <p style="margin: 3px 0; font-size: 11px;">
                            <strong><?= e($edu['degree']) ?></strong> &nbsp;|&nbsp; <?= e($edu['period']) ?><br>
                            <span style="font-style: italic; color: #334155;"><?= e($edu['institution']) ?> &middot; <?= e($edu['detail'] ?? '') ?></span>
                        </p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if ($showMatrix && !empty($matrixRows)): ?>
                <div class="cv-block">
                    <div class="sec-title"><?= e($titleMatrix) ?></div>
                    <table class="matrix-table">
                        <?php foreach ($matrixRows as $mRow): ?>
                            <?php if (!empty($mRow['label']) || !empty($mRow['skills'])): ?>
                                <tr>
                                    <td><?= e($mRow['label']) ?></td>
                                    <td><?= e($mRow['skills']) ?></td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </table>
                </div>
            <?php endif; ?>
        <?php endif; ?>

    </div>
</body>

</html>