<?php

declare(strict_types=1);
/**
 * @var array $profile
 */
?>
<div class="admin-panel">
    <h2>Public Profile Details</h2>
    <p class="panel-hint">Changes will reflect instantly on your public portfolio.</p>
    <form method="post" action="<?= e(url('admin/profile')) ?>" enctype="multipart/form-data" class="admin-form">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="action" value="save_profile">
        <div class="form-grid">
            <label>Name *
                <input name="name" required value="<?= e($profile['name']) ?>">
            </label>
            <label>Professional title *
                <input name="role" required value="<?= e($profile['role']) ?>">
            </label>
            <label>Location
                <input name="location" value="<?= e($profile['location']) ?>">
            </label>
            <label>Email address
                <input type="email" name="email" value="<?= e($profile['email']) ?>" placeholder="you@example.com">
            </label>
            <label>Phone number
                <input type="tel" name="phone" value="<?= e($profile['phone']) ?>" placeholder="+880 1XXXXXXXXX">
            </label>
            <label class="wide">LinkedIn URL
                <input type="url" name="linkedin" value="<?= e($profile['linkedin']) ?>">
            </label>
            <label class="wide">Headline
                <input name="headline" value="<?= e($profile['headline']) ?>">
            </label>
            <label class="wide">Introduction
                <textarea name="intro" rows="4"><?= e($profile['intro']) ?></textarea>
            </label>
            <label class="wide">Profile photo (JPG, PNG, WEBP; max 5 MB)
                <input type="file" name="photo" accept="image/jpeg,image/png,image/webp">
            </label>
        </div>
        <?php if (!empty($profile['photo']) && is_file(dirname(__DIR__, 2) . '/uploads/' . $profile['photo'])): ?>
            <p>Current profile photo:</p>
            <img class="admin-photo-preview" src="<?= e(url('uploads/' . $profile['photo'])) ?>" alt="Current photo preview">
        <?php endif; ?>
        <button class="button button-primary" type="submit">Save Profile ↗</button>
    </form>
</div>