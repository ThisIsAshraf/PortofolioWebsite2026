<?php

declare(strict_types=1);

// Fetch Dynamic Footer Values
$footerBadge    = setting('footer_badge_letter', 'S.');
$footerBrand    = setting('footer_brand_name', 'SHIHAB/');
$footerTagline  = setting('footer_tagline', 'Designed with purpose. Built to keep systems moving.');
$footerCopy     = setting('footer_copyright', '© ' . date('Y') . ' Ashraful Alam Shihab');
$showBackToTop  = setting('footer_show_back_to_top', '1') === '1';
$showAdminLink  = setting('footer_show_admin_link', '1') === '1';

// Dynamic Custom Links Array
$footerLinksJson = setting('footer_links_json', '');
$footerLinks = json_decode($footerLinksJson, true);
if (!is_array($footerLinks)) {
    $footerLinks = [];
    if ($gh = setting('github')) {
        $footerLinks[] = ['label' => 'GitHub', 'url' => $gh, 'new_tab' => '1'];
    }
    if ($li = setting('linkedin')) {
        $footerLinks[] = ['label' => 'LinkedIn', 'url' => $li, 'new_tab' => '1'];
    }
}
?>
<!-- Public Site Footer -->
<footer class="site-footer" style="background: #090d16; border-top: 1px solid #1e293b; padding: 28px 40px; color: #94a3b8; font-size: 13px;">
    <div style="max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">

        <!-- Left Branding & Tagline -->
        <div style="display: flex; align-items: center; gap: 14px; flex-wrap: wrap;">
            <?php if ($footerBadge !== ''): ?>
                <span style="display: inline-grid; place-content: center; width: 28px; height: 28px; background: #b7f36b; color: #090d16; font-weight: 800; font-size: 13px; border-radius: 6px;">
                    <?= e($footerBadge) ?>
                </span>
            <?php endif; ?>

            <?php if ($footerBrand !== ''): ?>
                <strong style="color: #ffffff; letter-spacing: 0.05em; font-size: 14px; font-family: 'Space Grotesk', sans-serif;">
                    <?= e($footerBrand) ?>
                </strong>
            <?php endif; ?>

            <?php if ($footerTagline !== ''): ?>
                <span style="color: #64748b; font-size: 12.5px;">
                    <?= e($footerTagline) ?>
                </span>
            <?php endif; ?>
        </div>

        <!-- Center & Right Navigation -->
        <div style="display: flex; align-items: center; gap: 20px; flex-wrap: wrap;">
            <?php if ($showBackToTop): ?>
                <a href="#top" style="color: #cbd5e1; text-decoration: none; font-size: 12px; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase;">
                    BACK TO TOP &uarr;
                </a>
            <?php endif; ?>

            <!-- Custom Dynamic Links (GitHub, LinkedIn, or anything added) -->
            <?php foreach ($footerLinks as $fLink): ?>
                <?php if (!empty($fLink['label']) && !empty($fLink['url'])): ?>
                    <a href="<?= e($fLink['url']) ?>" <?= (!empty($fLink['new_tab']) && $fLink['new_tab'] === '1') ? 'target="_blank" rel="noopener"' : '' ?> style="color: #cbd5e1; text-decoration: none; font-size: 12.5px; transition: color 0.2s;">
                        <?= e($fLink['label']) ?>
                    </a>
                <?php endif; ?>
            <?php endforeach; ?>

            <?php if ($footerCopy !== ''): ?>
                <span style="color: #64748b; font-size: 12.5px;">
                    <?= e($footerCopy) ?>
                </span>
            <?php endif; ?>

            <?php if ($showAdminLink): ?>
                <a href="<?= e(url('admin/login')) ?>" style="color: #475569; text-decoration: none; font-size: 12px;">
                    Admin
                </a>
            <?php endif; ?>
        </div>

    </div>
</footer>
</body>

</html>