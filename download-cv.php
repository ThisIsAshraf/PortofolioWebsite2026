<?php

declare(strict_types=1);

require_once __DIR__ . '/config.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$pdo = db();

// Fetch Profile Details
$profile = [];
foreach (['name', 'role', 'location', 'headline', 'intro', 'linkedin', 'email', 'phone'] as $k) {
    $profile[$k] = setting($k);
}

// Fetch Normalized Skills grouped by category (Joined with skill_groups)
$skillsStmt = $pdo->query('
    SELECT s.name, g.name AS group_name 
    FROM skills s 
    JOIN skill_groups g ON s.group_id = g.id 
    WHERE s.is_visible = 1 
    ORDER BY g.sort_order ASC, s.sort_order ASC, s.id ASC
');
$skills = $skillsStmt->fetchAll();

$groupedSkills = [];
foreach ($skills as $s) {
    $groupedSkills[$s['group_name']][] = $s['name'];
}

// Fetch Normalized Work Experience
$expStmt = $pdo->query('SELECT * FROM experience WHERE is_visible = 1 ORDER BY sort_order ASC, id ASC');
$experience = $expStmt->fetchAll();

$respStmt = $pdo->query('SELECT * FROM experience_responsibilities ORDER BY sort_order ASC, id ASC');
$allResponsibilities = $respStmt->fetchAll();

$expResponsibilities = [];
foreach ($allResponsibilities as $r) {
    $expResponsibilities[$r['experience_id']][] = $r['responsibility'];
}

// Fetch Education
$education = $pdo->query('SELECT * FROM education WHERE is_visible = 1 ORDER BY sort_order ASC, id ASC')->fetchAll();

// Clear any accidental whitespace or buffer pollution
while (ob_get_level() > 0) {
    ob_end_clean();
}
ob_start();
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: DejaVu Sans, Helvetica, sans-serif;
            font-size: 10px;
            line-height: 1.4;
            color: #222;
        }

        h1 {
            margin: 0 0 3px 0;
            font-size: 18px;
            color: #111;
            text-transform: uppercase;
        }

        .title {
            font-size: 11.5px;
            font-weight: bold;
            color: #0284c7;
            margin-bottom: 4px;
        }

        .contact {
            font-size: 8.5px;
            color: #555;
            margin-bottom: 10px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }

        .section-title {
            font-size: 11px;
            font-weight: bold;
            border-bottom: 1.5px solid #0284c7;
            padding-bottom: 2px;
            margin: 10px 0 5px 0;
            text-transform: uppercase;
            color: #111;
        }

        .entry {
            margin-bottom: 7px;
        }

        .entry-header {
            font-weight: bold;
            font-size: 10px;
        }

        .entry-sub {
            font-size: 9px;
            color: #555;
            margin-bottom: 2px;
        }

        ul {
            margin: 2px 0 0 14px;
            padding: 0;
        }

        li {
            margin-bottom: 1px;
        }

        .skills-grid {
            margin-top: 2px;
        }

        .skills-cat {
            font-weight: bold;
            color: #333;
        }
    </style>
</head>

<body>
    <h1><?= e($profile['name'] ?: 'Ashraful Alam Shihab') ?></h1>
    <div class="title"><?= e($profile['role'] ?: 'Senior Desktop Support Engineer') ?></div>
    <div class="contact">
        <?= e($profile['location']) ?> &middot;
        <?= e($profile['email']) ?> &middot;
        <?= e($profile['phone']) ?> &middot;
        <?= e($profile['linkedin']) ?>
    </div>

    <div class="section-title">Professional Summary</div>
    <p><?= e($profile['intro']) ?></p>

    <div class="section-title">Work Experience</div>
    <?php foreach ($experience as $exp): ?>
        <div class="entry">
            <div class="entry-header"><?= e($exp['role']) ?> &mdash; <?= e($exp['company']) ?></div>
            <div class="entry-sub"><?= e($exp['period']) ?> | <?= e($exp['location'] ?? '') ?></div>
            <ul>
                <?php
                $bullets = $expResponsibilities[$exp['id']] ?? [];
                foreach ($bullets as $bullet):
                ?>
                    <li><?= e((string)$bullet) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endforeach; ?>

    <div class="section-title">Technical Skills</div>
    <div class="skills-grid">
        <?php foreach ($groupedSkills as $cat => $items): ?>
            <div style="margin-bottom: 2px;"><span class="skills-cat"><?= e($cat) ?>:</span> <?= e(implode(', ', $items)) ?></div>
        <?php endforeach; ?>
    </div>

    <div class="section-title">Education</div>
    <?php foreach ($education as $edu): ?>
        <div class="entry">
            <div class="entry-header"><?= e($edu['degree']) ?></div>
            <div class="entry-sub"><?= e($edu['institution']) ?> &middot; <?= e($edu['period']) ?> (<?= e($edu['detail'] ?? '') ?>)</div>
        </div>
    <?php endforeach; ?>
</body>

</html>
<?php
$html = ob_get_clean();

$options = new Options();
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

$filename = 'Resume_' . preg_replace('/[^A-Za-z0-9_-]/', '_', $profile['name'] ?: 'Shihab') . '.pdf';
$dompdf->stream($filename, ['Attachment' => false]);
exit;
