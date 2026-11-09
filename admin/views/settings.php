<?php

declare(strict_types=1);
/**
 * @var array $settings
 */
$footerLinks = $settings['footer_links'] ?? [];
?>
<div class="admin-panel">
    <h2>Global Site &amp; Section Settings</h2>
    <p class="panel-hint">Manage hero copy, section titles, kickers, and intro text displayed across your public portfolio.</p>

    <form method="post" action="<?= e(url('admin/settings')) ?>" class="admin-form">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="action" value="save_site_settings">

        <!-- 1. Hero & Intro -->
        <h3 style="margin: 1.5rem 0 0.5rem; font-size: 1.1rem; color: #0284c7;">Homepage Hero &amp; Intro</h3>
        <div class="form-grid">
            <label class="wide">Hero Headline
                <input name="headline" value="<?= e($settings['headline'] ?? 'IT support, systems administration, and reliable technology experiences.') ?>" placeholder="Headline">
            </label>
            <label class="wide">Introductory Summary
                <textarea name="intro" rows="3" placeholder="Summary"><?= e($settings['intro'] ?? 'I help people and teams work confidently with technology—from end-user support and SaaS administration to IT assets, vendor coordination, and office infrastructure.') ?></textarea>
            </label>
            <label>Availability Badge
                <input name="availability_status" value="<?= e($settings['availability_status'] ?? 'IT SUPPORT · OPERATIONS · INFRASTRUCTURE') ?>">
            </label>
            <label>GitHub Profile URL
                <input type="url" name="github" value="<?= e($settings['github'] ?? '') ?>" placeholder="https://github.com/username">
            </label>
        </div>

        <!-- 2. Section 01: About -->
        <h3 style="margin: 2rem 0 0.5rem; font-size: 1.1rem; color: #0284c7;">01 / About Section</h3>
        <div class="form-grid">
            <label>Section Kicker
                <input name="sec_about_kicker" value="<?= e($settings['sec_about_kicker'] ?? '01 / ABOUT') ?>">
            </label>
            <label>Main Title (Line 1)
                <input name="sec_about_title" value="<?= e($settings['sec_about_title'] ?? 'Making technology') ?>">
            </label>
            <label class="wide">Accent Title (Green Text)
                <input name="sec_about_accent" value="<?= e($settings['sec_about_accent'] ?? 'work for people.') ?>">
            </label>
        </div>

        <!-- 3. Section 02: Toolkit / Skills -->
        <h3 style="margin: 2rem 0 0.5rem; font-size: 1.1rem; color: #0284c7;">02 / Toolkit (Skills) Section</h3>
        <div class="form-grid">
            <label>Section Kicker
                <input name="sec_skills_kicker" value="<?= e($settings['sec_skills_kicker'] ?? '02 / TOOLKIT') ?>">
            </label>
            <label>Main Title
                <input name="sec_skills_title" value="<?= e($settings['sec_skills_title'] ?? 'Skills &') ?>">
            </label>
            <label>Accent Title (Green Text)
                <input name="sec_skills_accent" value="<?= e($settings['sec_skills_accent'] ?? 'strengths.') ?>">
            </label>
            <label class="wide">Right-side Subtitle / Description
                <textarea name="sec_skills_intro" rows="2"><?= e($settings['sec_skills_intro'] ?? 'A practical mix of user support, SaaS administration, IT operations, and developing infrastructure skills.') ?></textarea>
            </label>
        </div>

        <!-- 4. Section 03: Career / Experience -->
        <h3 style="margin: 2rem 0 0.5rem; font-size: 1.1rem; color: #0284c7;">03 / Career (Experience) Section</h3>
        <div class="form-grid">
            <label>Section Kicker
                <input name="sec_exp_kicker" value="<?= e($settings['sec_exp_kicker'] ?? '03 / CAREER') ?>">
            </label>
            <label>Main Title
                <input name="sec_exp_title" value="<?= e($settings['sec_exp_title'] ?? 'Work') ?>">
            </label>
            <label>Accent Title (Green Text)
                <input name="sec_exp_accent" value="<?= e($settings['sec_exp_accent'] ?? 'experience.') ?>">
            </label>
            <label class="wide">Right-side Subtitle / Description
                <textarea name="sec_exp_intro" rows="2"><?= e($settings['sec_exp_intro'] ?? 'The roles, responsibilities, and practical experience that have shaped my approach to IT.') ?></textarea>
            </label>
        </div>

        <!-- 5. Section 04: Selected Work -->
        <h3 style="margin: 2rem 0 0.5rem; font-size: 1.1rem; color: #0284c7;">04 / Selected Work (Projects) Section</h3>
        <div class="form-grid">
            <label>Section Kicker
                <input name="sec_proj_kicker" value="<?= e($settings['sec_proj_kicker'] ?? '04 / SELECTED WORK') ?>">
            </label>
            <label>Main Title
                <input name="sec_proj_title" value="<?= e($settings['sec_proj_title'] ?? 'Work that') ?>">
            </label>
            <label>Accent Title (Green Text)
                <input name="sec_proj_accent" value="<?= e($settings['sec_proj_accent'] ?? 'matters.') ?>">
            </label>
            <label class="wide">Right-side Subtitle / Description
                <textarea name="sec_proj_intro" rows="2"><?= e($settings['sec_proj_intro'] ?? 'Experience highlights and initiatives.') ?></textarea>
            </label>
        </div>

        <!-- 6. Section 05: Education -->
        <h3 style="margin: 2rem 0 0.5rem; font-size: 1.1rem; color: #0284c7;">05 / Education Section</h3>
        <div class="form-grid">
            <label>Section Kicker
                <input name="sec_edu_kicker" value="<?= e($settings['sec_edu_kicker'] ?? '05 / EDUCATION') ?>">
            </label>
            <label>Main Title
                <input name="sec_edu_title" value="<?= e($settings['sec_edu_title'] ?? 'Built on a') ?>">
            </label>
            <label class="wide">Accent Title (Green Text)
                <input name="sec_edu_accent" value="<?= e($settings['sec_edu_accent'] ?? 'strong foundation.') ?>">
            </label>
        </div>

        <!-- 7. Section 06: Contact -->
        <h3 style="margin: 2rem 0 0.5rem; font-size: 1.1rem; color: #0284c7;">06 / Get In Touch (Contact) Section</h3>
        <div class="form-grid">
            <label>Section Kicker
                <input name="sec_contact_kicker" value="<?= e($settings['sec_contact_kicker'] ?? '06 / GET IN TOUCH') ?>">
            </label>
            <label>Main Title (Line 1)
                <input name="sec_contact_title" value="<?= e($settings['sec_contact_title'] ?? 'Have a challenge?') ?>">
            </label>
            <label>Accent Title (Green Text)
                <input name="sec_contact_accent" value="<?= e($settings['sec_contact_accent'] ?? 'Let’s talk.') ?>">
            </label>
            <label class="wide">Intro Text Below Title
                <textarea name="sec_contact_intro" rows="2"><?= e($settings['sec_contact_intro'] ?? 'For IT support opportunities, collaboration, or a professional conversation, send me a message.') ?></textarea>
            </label>
        </div>

        <!-- 8. Highlight & Learning Callout Cards -->
        <h3 style="margin: 2rem 0 0.5rem; font-size: 1.1rem; color: #0284c7;">Highlight &amp; Learning Callout Cards</h3>
        <div class="form-grid">
            <label>Skills Card Heading
                <input name="learning_title" value="<?= e($settings['learning_title'] ?? 'Currently growing') ?>">
            </label>
            <label class="wide">Skills Card Details
                <textarea name="learning_text" rows="2"><?= e($settings['learning_text'] ?? 'Microsoft 365 Administration · Microsoft Entra ID · Azure Administrator (AZ-104 path)') ?></textarea>
            </label>
            <label>Education Card Heading
                <input name="edu_learning_title" value="<?= e($settings['edu_learning_title'] ?? 'Building toward cloud & enterprise infrastructure') ?>">
            </label>
            <label class="wide">Education Card Description
                <textarea name="edu_learning_text" rows="2"><?= e($settings['edu_learning_text'] ?? 'Focused on Microsoft 365, Entra ID, Azure administration, and lab-based deployments.') ?></textarea>
            </label>
        </div>

        <!-- 9. Complete Website Footer Settings -->
        <h3 style="margin: 2rem 0 0.5rem; font-size: 1.1rem; color: #0284c7;">07 / Website Footer Settings</h3>
        <p class="panel-hint" style="margin-bottom: 1rem;">Customize every element of your public footer: brand initials badge, title, tagline, copyright, and custom navigation links.</p>

        <div class="form-grid">
            <label>Logo Monogram Letter
                <input name="footer_badge_letter" value="<?= e($settings['footer_badge_letter'] ?? 'S.') ?>" placeholder="e.g. S.">
                <small style="color: #64748b; font-size: 11px;">Displayed inside the lime badge block</small>
            </label>

            <label>Brand Name / Suffix
                <input name="footer_brand_name" value="<?= e($settings['footer_brand_name'] ?? 'SHIHAB/') ?>" placeholder="e.g. SHIHAB/">
                <small style="color: #64748b; font-size: 11px;">Bold logo title next to the badge</small>
            </label>

            <label class="wide">Tagline / Mission Statement
                <input name="footer_tagline" value="<?= e($settings['footer_tagline'] ?? 'Designed with purpose. Built to keep systems moving.') ?>" placeholder="Tagline text">
            </label>

            <label class="wide">Copyright Notice
                <input name="footer_copyright" value="<?= e($settings['footer_copyright'] ?? '© ' . date('Y') . ' Ashraful Alam Shihab') ?>">
            </label>

            <div class="wide" style="display: flex; gap: 24px; flex-wrap: wrap; margin-top: 8px;">
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                    <input type="checkbox" name="footer_show_back_to_top" value="1" <?= (!empty($settings['footer_show_back_to_top']) && $settings['footer_show_back_to_top'] === '1') ? 'checked' : '' ?> style="width: auto; margin: 0;">
                    <span>Display "BACK TO TOP ↑" Button</span>
                </label>

                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                    <input type="checkbox" name="footer_show_admin_link" value="1" <?= (!empty($settings['footer_show_admin_link']) && $settings['footer_show_admin_link'] === '1') ? 'checked' : '' ?> style="width: auto; margin: 0;">
                    <span>Display "Admin" Quick Access Link</span>
                </label>
            </div>
        </div>

        <!-- Dynamic Add/Remove Footer Custom Links -->
        <div style="border-top: 1px solid #e2e8f0; padding-top: 20px; margin-top: 24px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                <div>
                    <h4 style="margin: 0; font-size: 14.5px; color: #0f172a; font-weight: 700;">Footer Navigation &amp; Social Links</h4>
                    <p class="panel-hint" style="margin: 2px 0 0;">Add, remove, or modify links shown in your website footer.</p>
                </div>
                <button type="button" onclick="addFooterLinkRow()" style="background: #16a34a; color: #fff; border: none; padding: 7px 14px; border-radius: 6px; font-size: 11.5px; font-weight: 600; cursor: pointer;">
                    + Add New Link
                </button>
            </div>

            <div id="footerLinksContainer" style="display: flex; flex-direction: column; gap: 10px; margin-top: 12px;">
                <?php foreach ($footerLinks as $idx => $link): ?>
                    <div class="footer-link-row" style="display: grid; grid-template-columns: 200px 1fr 140px 40px; gap: 10px; align-items: center; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 14px;">
                        <div>
                            <span style="font-size: 10px; color: #64748b; font-weight: 600; display: block; margin-bottom: 4px;">Link Label</span>
                            <input name="footer_link_label[]" value="<?= e($link['label'] ?? '') ?>" placeholder="e.g. GitHub" style="width: 100%; padding: 8px 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 12px; background: #fff;">
                        </div>
                        <div>
                            <span style="font-size: 10px; color: #64748b; font-weight: 600; display: block; margin-bottom: 4px;">Destination URL</span>
                            <input name="footer_link_url[]" value="<?= e($link['url'] ?? '') ?>" placeholder="https://..." style="width: 100%; padding: 8px 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 12px; background: #fff;">
                        </div>
                        <div style="padding-top: 16px;">
                            <label style="display: flex; align-items: center; gap: 6px; font-size: 11px; color: #475569; margin: 0; cursor: pointer;">
                                <input type="checkbox" name="footer_link_new_tab[<?= $idx ?>]" value="1" <?= (!empty($link['new_tab']) && $link['new_tab'] === '1') ? 'checked' : '' ?> style="width: auto; margin: 0;">
                                New Tab ↗
                            </label>
                        </div>
                        <div style="padding-top: 14px;">
                            <button type="button" onclick="removeFooterLinkRow(this)" title="Delete Link" style="background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; width: 34px; height: 34px; border-radius: 6px; cursor: pointer; display: grid; place-content: center; font-size: 14px;">
                                🗑
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <button class="button button-primary" type="submit" style="margin-top: 2rem;">Save All Settings ↗</button>
    </form>
</div>

<script>
    function addFooterLinkRow() {
        const container = document.getElementById('footerLinksContainer');
        const newIdx = container.querySelectorAll('.footer-link-row').length;
        const newDiv = document.createElement('div');
        newDiv.className = 'footer-link-row';
        newDiv.style = 'display: grid; grid-template-columns: 200px 1fr 140px 40px; gap: 10px; align-items: center; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 14px;';
        newDiv.innerHTML = `
        <div>
            <span style="font-size: 10px; color: #64748b; font-weight: 600; display: block; margin-bottom: 4px;">Link Label</span>
            <input name="footer_link_label[]" placeholder="e.g. LinkedIn" style="width: 100%; padding: 8px 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 12px; background: #fff;">
        </div>
        <div>
            <span style="font-size: 10px; color: #64748b; font-weight: 600; display: block; margin-bottom: 4px;">Destination URL</span>
            <input name="footer_link_url[]" placeholder="https://..." style="width: 100%; padding: 8px 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 12px; background: #fff;">
        </div>
        <div style="padding-top: 16px;">
            <label style="display: flex; align-items: center; gap: 6px; font-size: 11px; color: #475569; margin: 0; cursor: pointer;">
                <input type="checkbox" name="footer_link_new_tab[${newIdx}]" value="1" checked style="width: auto; margin: 0;">
                New Tab ↗
            </label>
        </div>
        <div style="padding-top: 14px;">
            <button type="button" onclick="removeFooterLinkRow(this)" title="Delete Link" style="background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; width: 34px; height: 34px; border-radius: 6px; cursor: pointer; display: grid; place-content: center; font-size: 14px;">
                🗑
            </button>
        </div>
    `;
        container.appendChild(newDiv);
    }

    function removeFooterLinkRow(btn) {
        const row = btn.closest('.footer-link-row');
        if (row) {
            row.remove();
        }
    }
</script>