<?php

declare(strict_types=1);
/**
 * @var array $settings
 */
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

        <button class="button button-primary" type="submit" style="margin-top: 1.5rem;">Save All Settings ↗</button>
    </form>
</div>