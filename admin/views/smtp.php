<?php

declare(strict_types=1);
$smtp = get_smtp_config();
?>
<div class="admin-panel">
    <h2>SMTP &amp; Email Delivery Configuration</h2>
    <p class="panel-hint">Configure your mail server so contact messages send directly to your personal email inbox.</p>

    <form method="post" action="<?= e(url('admin/smtp')) ?>" class="admin-form">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="action" value="save_smtp">

        <div class="form-grid">
            <label>SMTP Host *
                <input name="host" required value="<?= e($smtp['host']) ?>" placeholder="smtp.gmail.com">
            </label>
            <label>SMTP Port *
                <input type="number" name="port" required value="<?= e((string)$smtp['port']) ?>" placeholder="587">
            </label>
            <label>Encryption
                <select name="encryption">
                    <option value="tls" <?= ($smtp['encryption'] === 'tls') ? 'selected' : '' ?>>TLS (Port 587)</option>
                    <option value="ssl" <?= ($smtp['encryption'] === 'ssl') ? 'selected' : '' ?>>SSL (Port 465)</option>
                    <option value="none" <?= ($smtp['encryption'] === 'none') ? 'selected' : '' ?>>None</option>
                </select>
            </label>
            <label>SMTP Username / Email *
                <input type="email" name="username" required value="<?= e($smtp['username']) ?>" placeholder="you@gmail.com">
            </label>
            <label>SMTP Password / App Password
                <input type="password" name="password" placeholder="<?= empty($smtp['password']) ? 'Enter password' : '•••••••••••• (Leave blank to keep current)' ?>">
            </label>
            <label>Sender From Email *
                <input type="email" name="from_email" required value="<?= e($smtp['from_email'] ?: $smtp['username']) ?>" placeholder="you@gmail.com">
            </label>
            <label class="wide">Sender Name
                <input name="from_name" value="<?= e($smtp['from_name']) ?>" placeholder="Portfolio Contact Bot">
            </label>
            <label class="wide">
                <input type="checkbox" name="is_active" value="1" <?= !empty($smtp['is_active']) ? 'checked' : '' ?>> Enable SMTP Delivery
            </label>
        </div>

        <button class="button button-primary" type="submit">Save SMTP Settings ↗</button>
    </form>
</div>

<div class="admin-panel" style="margin-top: 24px;">
    <h2>Send Test Email</h2>
    <p class="panel-hint">Send a live test message to verify that your mail server credentials work without issues.</p>
    <form method="post" action="<?= e(url('admin/smtp')) ?>" class="admin-form">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="action" value="test_email">
        <div class="form-grid">
            <label class="wide">Recipient Test Address *
                <input type="email" name="test_recipient" required value="<?= e(setting('email')) ?>" placeholder="your-personal@email.com">
            </label>
        </div>
        <button class="button button-quiet" type="submit">Send Test Email ↗</button>
    </form>
</div>