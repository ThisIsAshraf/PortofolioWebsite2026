<?php

declare(strict_types=1);
/**
 * @var array $templates
 * @var array $cv
 */
$photoShape = setting('cv_photo_shape', 'rounded');
$photoSize  = setting('cv_photo_size', '130px');
$photoBorder = setting('cv_photo_border_color', '#ea580c');
?>
<div class="admin-panel" style="margin-top: 0; padding: 24px 28px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px; margin-bottom: 24px;">
        <div>
            <h2>Master CV Template &amp; Granular Line Manager</h2>
            <p class="panel-hint" style="margin: 0;">Switch layouts, toggle sections, adjust photo framing, and add/remove unlimited skills matrix rows.</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="<?= e(url('download-cv.php?preview=1')) ?>" target="_blank" class="button button-primary" style="background: #b7f36b; color: #111; font-weight: 700; text-decoration: none; padding: 10px 18px; border-radius: 8px;">
                📄 View Active CV Live ↗
            </a>
        </div>
    </div>

    <!-- 1. Layout Archetypes -->
    <h3 style="font-size: 15px; color: #0f172a; margin: 0 0 14px; font-weight: 700;">1. Available Layout Styles (7 Archetypes)</h3>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(310px, 1fr)); gap: 18px; margin-bottom: 35px;">
        <?php foreach ($templates as $tpl): ?>
            <div style="background: #ffffff; border: 2px solid <?= $tpl['is_default'] ? '#16a34a' : '#e2e8f0' ?>; border-radius: 12px; padding: 20px; display: flex; flex-direction: column; justify-content: space-between; position: relative; box-shadow: 0 4px 14px rgba(0,0,0,0.03);">
                <?php if ($tpl['is_default']): ?>
                    <span style="position: absolute; top: 12px; right: 12px; background: #ecfdf5; color: #15803d; border: 1px solid #a7f3d0; font-size: 10px; font-weight: 700; padding: 3px 8px; border-radius: 20px;">
                        ★ ACTIVE DEFAULT
                    </span>
                <?php endif; ?>

                <div>
                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
                        <span style="width: 14px; height: 14px; border-radius: 4px; background: <?= e($tpl['accent_color']) ?>; display: inline-block;"></span>
                        <h4 style="margin: 0; font-size: 15.5px; color: #0f172a; font-weight: 700;"><?= e($tpl['title']) ?></h4>
                    </div>
                    <p style="font-size: 12px; color: #64748b; line-height: 1.5; margin: 0 0 14px;"><?= e($tpl['description']) ?></p>

                    <div style="display: flex; gap: 8px; font-size: 11px; margin-bottom: 16px;">
                        <span style="background: <?= $tpl['has_photo'] ? '#eff6ff; color: #1d4ed8;' : '#f1f5f9; color: #475569;' ?> padding: 3px 8px; border-radius: 6px; font-weight: 600;">
                            <?= $tpl['has_photo'] ? '📷 Photo Template' : '📄 Pure ATS (No Photo)' ?>
                        </span>
                        <span style="background: <?= $tpl['is_enabled'] ? '#ecfdf5; color: #15803d;' : '#fef2f2; color: #b91c1c;' ?> padding: 3px 8px; border-radius: 6px; font-weight: 600;">
                            <?= $tpl['is_enabled'] ? 'Enabled' : 'Disabled' ?>
                        </span>
                    </div>
                </div>

                <div style="display: flex; gap: 8px; align-items: center; border-top: 1px solid #f1f5f9; padding-top: 14px;">
                    <a href="<?= e(url('download-cv.php?layout=' . urlencode($tpl['template_key']) . '&preview=1')) ?>" target="_blank" style="padding: 7px 12px; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 11px; color: #334155; text-decoration: none; font-weight: 600;">
                        Preview Layout ↗
                    </a>

                    <form method="post" action="<?= e(url('admin/cv')) ?>" style="margin: 0;">
                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                        <input type="hidden" name="action" value="toggle_cv_template">
                        <input type="hidden" name="id" value="<?= $tpl['id'] ?>">
                        <button type="submit" style="padding: 7px 12px; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 11px; color: #475569; cursor: pointer;">
                            <?= $tpl['is_enabled'] ? 'Disable' : 'Enable' ?>
                        </button>
                    </form>

                    <?php if (!$tpl['is_default']): ?>
                        <form method="post" action="<?= e(url('admin/cv')) ?>" style="margin: 0; margin-left: auto;">
                            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                            <input type="hidden" name="action" value="set_default_cv">
                            <input type="hidden" name="id" value="<?= $tpl['id'] ?>">
                            <button type="submit" style="padding: 7px 14px; background: #0f172a; color: #fff; border: none; border-radius: 6px; font-size: 11px; font-weight: 600; cursor: pointer;">
                                Set As Default
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- 2. Master Settings Form -->
    <form method="post" action="<?= e(url('admin/cv')) ?>" class="admin-form">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="action" value="save_cv_settings">

        <!-- Photo Styling Controls -->
        <h3 style="font-size: 15px; color: #0f172a; margin: 24px 0 10px; border-top: 1px solid #e2e8f0; padding-top: 20px; font-weight: 700;">
            2. Photo Framing &amp; Styling Controls
        </h3>
        <div class="form-grid" style="grid-template-columns: repeat(3, 1fr); gap: 14px; margin-bottom: 24px;">
            <label>Photo Shape
                <select name="cv_photo_shape">
                    <option value="rounded" <?= $photoShape === 'rounded' ? 'selected' : '' ?>>Rounded Square (Soft Corners)</option>
                    <option value="circle" <?= $photoShape === 'circle' ? 'selected' : '' ?>>Circular Avatar (Novoresume Style)</option>
                    <option value="square" <?= $photoShape === 'square' ? 'selected' : '' ?>>Sharp Square</option>
                </select>
            </label>
            <label>Photo Size (Width/Height)
                <select name="cv_photo_size">
                    <option value="110px" <?= $photoSize === '110px' ? 'selected' : '' ?>>Compact (110px)</option>
                    <option value="130px" <?= $photoSize === '130px' ? 'selected' : '' ?>>Standard (130px)</option>
                    <option value="150px" <?= $photoSize === '150px' ? 'selected' : '' ?>>Large (150px)</option>
                </select>
            </label>
            <label>Photo Border Color
                <input type="color" name="cv_photo_border_color" value="<?= e($photoBorder) ?>" style="height: 42px; padding: 2px;">
            </label>
        </div>

        <!-- Section Visibility Toggles -->
        <h3 style="font-size: 15px; color: #0f172a; margin: 24px 0 10px; border-top: 1px solid #e2e8f0; padding-top: 20px; font-weight: 700;">
            3. Section Visibility Toggles
        </h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 10px; background: #f8fafc; padding: 16px; border-radius: 8px; border: 1px solid #e2e8f0; margin-bottom: 24px;">
            <label style="display: flex; align-items: center; gap: 8px; margin: 0; font-size: 12px; cursor: pointer;">
                <input type="checkbox" name="cv_show_photo" value="1" <?= setting('cv_show_photo', '1') === '1' ? 'checked' : '' ?> style="width: auto; margin: 0;">
                Display Profile Photo
            </label>
            <label style="display: flex; align-items: center; gap: 8px; margin: 0; font-size: 12px; cursor: pointer;">
                <input type="checkbox" name="cv_show_summary" value="1" <?= setting('cv_show_summary', '1') === '1' ? 'checked' : '' ?> style="width: auto; margin: 0;">
                Summary / Objective
            </label>
            <label style="display: flex; align-items: center; gap: 8px; margin: 0; font-size: 12px; cursor: pointer;">
                <input type="checkbox" name="cv_show_competencies" value="1" <?= setting('cv_show_competencies', '1') === '1' ? 'checked' : '' ?> style="width: auto; margin: 0;">
                Areas of Expertise / Badges
            </label>
            <label style="display: flex; align-items: center; gap: 8px; margin: 0; font-size: 12px; cursor: pointer;">
                <input type="checkbox" name="cv_show_matrix" value="1" <?= setting('cv_show_matrix', '1') === '1' ? 'checked' : '' ?> style="width: auto; margin: 0;">
                Technical Matrix Table
            </label>
            <label style="display: flex; align-items: center; gap: 8px; margin: 0; font-size: 12px; cursor: pointer;">
                <input type="checkbox" name="cv_show_experience" value="1" <?= setting('cv_show_experience', '1') === '1' ? 'checked' : '' ?> style="width: auto; margin: 0;">
                Work Experience
            </label>
            <label style="display: flex; align-items: center; gap: 8px; margin: 0; font-size: 12px; cursor: pointer;">
                <input type="checkbox" name="cv_show_education" value="1" <?= setting('cv_show_education', '1') === '1' ? 'checked' : '' ?> style="width: auto; margin: 0;">
                Education
            </label>
            <label style="display: flex; align-items: center; gap: 8px; margin: 0; font-size: 12px; cursor: pointer;">
                <input type="checkbox" name="cv_show_training" value="1" <?= setting('cv_show_training', '1') === '1' ? 'checked' : '' ?> style="width: auto; margin: 0;">
                Training &amp; Certifications
            </label>
            <label style="display: flex; align-items: center; gap: 8px; margin: 0; font-size: 12px; cursor: pointer;">
                <input type="checkbox" name="cv_show_languages" value="1" <?= setting('cv_show_languages', '1') === '1' ? 'checked' : '' ?> style="width: auto; margin: 0;">
                Languages
            </label>
        </div>

        <!-- Header & Contact Details -->
        <h3 style="font-size: 15px; color: #0f172a; margin: 26px 0 6px; border-top: 1px solid #e2e8f0; padding-top: 20px; font-weight: 700;">
            4. Header Identity &amp; Contact Line
        </h3>
        <div class="form-grid">
            <label>Full Display Name
                <input name="cv_display_name" value="<?= e($cv['name']) ?>">
            </label>
            <label>Primary Role Title
                <input name="cv_display_title" value="<?= e($cv['title']) ?>">
            </label>
            <label class="wide">ATS Sub-line Banner (Classic ATS Layout)
                <input name="cv_ats_subline" value="<?= e($cv['ats_subline']) ?>">
            </label>
            <label>Email Address
                <input name="cv_email" value="<?= e($cv['email']) ?>">
            </label>
            <label>Phone Number (with country code)
                <input name="cv_phone" value="<?= e($cv['phone']) ?>">
            </label>
            <label>Location (City, Country)
                <input name="cv_location" value="<?= e($cv['location']) ?>">
            </label>
            <label>LinkedIn Handle / URL
                <input name="cv_linkedin" value="<?= e($cv['linkedin']) ?>">
            </label>
        </div>

        <!-- Dynamic Section Titles -->
        <h3 style="font-size: 15px; color: #0f172a; margin: 26px 0 6px; border-top: 1px solid #e2e8f0; padding-top: 20px; font-weight: 700;">
            5. Section Title Headings
        </h3>
        <div class="form-grid">
            <label>Summary Section Title
                <input name="cv_sec_summary_title" value="<?= e($cv['sec_summary']) ?>">
            </label>
            <label>Areas of Expertise Title
                <input name="cv_sec_expertise_title" value="<?= e($cv['sec_expertise']) ?>">
            </label>
            <label>Core Competencies Title
                <input name="cv_sec_competencies_title" value="<?= e($cv['sec_competencies']) ?>">
            </label>
            <label>Experience Section Title
                <input name="cv_sec_experience_title" value="<?= e($cv['sec_experience']) ?>">
            </label>
            <label>Education Section Title
                <input name="cv_sec_education_title" value="<?= e($cv['sec_education']) ?>">
            </label>
            <label>Skills Matrix Section Title
                <input name="cv_sec_matrix_title" value="<?= e($cv['sec_matrix']) ?>">
            </label>
            <label>Trainings Section Title
                <input name="cv_sec_training_title" value="<?= e($cv['sec_training']) ?>">
            </label>
            <label>Languages Section Title
                <input name="cv_sec_languages_title" value="<?= e($cv['sec_languages']) ?>">
            </label>
        </div>

        <!-- Content Bodies -->
        <h3 style="font-size: 15px; color: #0f172a; margin: 26px 0 6px; border-top: 1px solid #e2e8f0; padding-top: 20px; font-weight: 700;">
            6. Content Bodies, Trainings &amp; Languages
        </h3>
        <label class="wide">Professional Summary / Objective
            <textarea name="cv_custom_summary" rows="4"><?= e($cv['summary']) ?></textarea>
        </label>
        <div class="form-grid" style="margin-top: 14px;">
            <label>Training &amp; Certifications (1 per line)
                <textarea name="cv_trainings" rows="6"><?= e($cv['trainings']) ?></textarea>
            </label>
            <label>Languages &amp; Fluency (1 per line)
                <textarea name="cv_languages" rows="6"><?= e($cv['languages']) ?></textarea>
            </label>
        </div>

        <!-- 7. Dynamic Technical Skills Matrix (Unlimited Rows) -->
        <div style="border-top: 1px solid #e2e8f0; padding-top: 24px; margin-top: 26px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                <div>
                    <h3 style="font-size: 15px; color: #0f172a; margin: 0; font-weight: 700;">
                        7. Technical Skills Matrix Rows (Dynamic Add / Remove)
                    </h3>
                    <p class="panel-hint" style="margin: 4px 0 0;">Add as many categories or skill groups as needed for the ATS table.</p>
                </div>
                <button type="button" onclick="addMatrixRow()" style="background: #16a34a; color: #fff; border: none; padding: 7px 14px; border-radius: 6px; font-size: 11.5px; font-weight: 600; cursor: pointer;">
                    + Add New Row
                </button>
            </div>

            <div id="matrixRowsContainer" style="display: flex; flex-direction: column; gap: 12px; margin-top: 14px;">
                <?php foreach ($cv['matrix_rows'] as $idx => $mRow): ?>
                    <div class="matrix-row-item" style="display: grid; grid-template-columns: 240px 1fr 40px; gap: 10px; align-items: center; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 14px;">
                        <div>
                            <span style="font-size: 10px; color: #64748b; font-weight: 600; display: block; margin-bottom: 4px;">Category Label</span>
                            <input name="matrix_label[]" value="<?= e($mRow['label'] ?? '') ?>" placeholder="e.g. Ticketing & SaaS" style="width: 100%; padding: 8px 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 12px; background: #fff;">
                        </div>
                        <div>
                            <span style="font-size: 10px; color: #64748b; font-weight: 600; display: block; margin-bottom: 4px;">Skills List (Comma separated)</span>
                            <input name="matrix_skills[]" value="<?= e($mRow['skills'] ?? '') ?>" placeholder="e.g. Zendesk, Jira SM, ServiceNow" style="width: 100%; padding: 8px 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 12px; background: #fff;">
                        </div>
                        <div style="padding-top: 16px;">
                            <button type="button" onclick="removeMatrixRow(this)" title="Delete Row" style="background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; width: 34px; height: 34px; border-radius: 6px; cursor: pointer; display: grid; place-content: center; font-size: 14px;">
                                🗑
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <button type="submit" class="button button-primary" style="margin-top: 26px; background: #0f172a; color: #fff; padding: 12px 28px; border-radius: 8px; font-weight: 700; border: none; cursor: pointer; font-size: 13.5px;">
            Save All CV Rules &amp; Content ↗
        </button>
    </form>
</div>

<script>
    function addMatrixRow() {
        const container = document.getElementById('matrixRowsContainer');
        const newDiv = document.createElement('div');
        newDiv.className = 'matrix-row-item';
        newDiv.style = 'display: grid; grid-template-columns: 240px 1fr 40px; gap: 10px; align-items: center; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 14px;';
        newDiv.innerHTML = `
        <div>
            <span style="font-size: 10px; color: #64748b; font-weight: 600; display: block; margin-bottom: 4px;">Category Label</span>
            <input name="matrix_label[]" placeholder="Category Name" style="width: 100%; padding: 8px 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 12px; background: #fff;">
        </div>
        <div>
            <span style="font-size: 10px; color: #64748b; font-weight: 600; display: block; margin-bottom: 4px;">Skills List (Comma separated)</span>
            <input name="matrix_skills[]" placeholder="Skill 1, Skill 2, Skill 3..." style="width: 100%; padding: 8px 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 12px; background: #fff;">
        </div>
        <div style="padding-top: 16px;">
            <button type="button" onclick="removeMatrixRow(this)" title="Delete Row" style="background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; width: 34px; height: 34px; border-radius: 6px; cursor: pointer; display: grid; place-content: center; font-size: 14px;">
                🗑
            </button>
        </div>
    `;
        container.appendChild(newDiv);
    }

    function removeMatrixRow(btn) {
        const row = btn.closest('.matrix-row-item');
        if (row) {
            row.remove();
        }
    }
</script>