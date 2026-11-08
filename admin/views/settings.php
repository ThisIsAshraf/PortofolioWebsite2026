<?php

declare(strict_types=1);
/**
 * @var array $settings
 */
?>
<div class="admin-panel">
    <h2>Global Site &amp; Content Settings</h2>
    <p class="panel-hint">Manage site copy, headlines, and highlighted learning cards displayed across your public portfolio.</p>

    <form method="post" action="<?= e(url('admin/settings')) ?>" class="admin-form">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="action" value="save_site_settings">

        <h3 style="margin: 1.5rem 0 0.5rem; font-size: 1.1rem; color: #0284c7;">Homepage Hero &amp; Intro</h3>
        <div class="form-grid">
            <label class="wide">Hero Headline
                <input name="headline" value="<?= e($settings['headline'] ?? '') ?>" placeholder="IT support, systems administration, and reliable technology experiences.">
            </label>
            <label class="wide">Introductory Summary
                <textarea name="intro" rows="3" placeholder="I help people and teams work confidently with technology..."><?= e($settings['intro'] ?? '') ?></textarea>
            </label>
        </div>

        <h3 style="margin: 2rem 0 0.5rem; font-size: 1.1rem; color: #0284c7;">Interactive Learning Cards</h3>
        <p class="panel-hint">Controls the green callout box in your Skills section and the Continuous Learning card in Education.</p>
        <div class="form-grid">
            <label>Skills Learning Heading
                <input name="learning_title" value="<?= e($settings['learning_title'] ?? 'Currently growing') ?>" placeholder="Currently growing">
            </label>
            <label class="wide">Skills Learning Details (Comma/Bullet separated)
                <textarea name="learning_text" rows="2" placeholder="Microsoft 365 Administration · Microsoft Entra ID · Azure Administrator (AZ-104 path)"><?= e($settings['learning_text'] ?? '') ?></textarea>
            </label>
            <label>Education Card Heading
                <input name="edu_learning_title" value="<?= e($settings['edu_learning_title'] ?? 'Building toward cloud & enterprise infrastructure') ?>" placeholder="Building toward cloud...">
            </label>
            <label class="wide">Education Card Description
                <textarea name="edu_learning_text" rows="2" placeholder="Focused on Microsoft 365, Entra ID, Azure administration, and lab-based deployments."><?= e($settings['edu_learning_text'] ?? '') ?></textarea>
            </label>
        </div>

        <h3 style="margin: 2rem 0 0.5rem; font-size: 1.1rem; color: #0284c7;">External Links &amp; Status</h3>
        <div class="form-grid">
            <label>Availability Badge
                <input name="availability_status" value="<?= e($settings['availability_status'] ?? 'IT SUPPORT · OPERATIONS · INFRASTRUCTURE') ?>" placeholder="AVAILABLE FOR ROLES">
            </label>
            <label>GitHub Profile URL
                <input type="url" name="github" value="<?= e($settings['github'] ?? '') ?>" placeholder="https://github.com/username">
            </label>
        </div>

        <button class="button button-primary" type="submit" style="margin-top: 1.5rem;">Save Site Settings ↗</button>
    </form>
</div>