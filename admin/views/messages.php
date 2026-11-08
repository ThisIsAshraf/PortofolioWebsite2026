<?php

declare(strict_types=1);
/**
 * @var bool $isDetail
 * @var array $message
 * @var int $id
 * @var string $filter
 * @var array $rows
 */
?>
<?php if ($isDetail): ?>
    <div class="admin-panel">
        <a class="back-link" href="<?= e(url('admin/messages')) ?>">← Back to inbox</a>
        <div class="message-heading">
            <div>
                <h2><?= e($message['subject'] ?: 'No Subject') ?></h2>
                <p class="panel-hint">From <strong><?= e($message['name']) ?></strong> &middot; <?= e($message['email']) ?></p>
                <p class="panel-hint"><?= e($message['created_at']) ?></p>
            </div>
            <span class="status-pill status-<?= e($message['status']) ?>"><?= e(ucfirst($message['status'])) ?></span>
        </div>
        <div class="message-body"><?= nl2br(e($message['message'])) ?></div>
        <div class="message-actions">
            <a class="button button-primary" href="mailto:<?= e($message['email']) ?>?subject=<?= rawurlencode('Re: ' . ($message['subject'] ?: 'Your inquiry')) ?>">Reply by Email ↗</a>
            <form method="post" action="<?= e(url('admin/messages')) ?>" class="inline-form">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="action" value="message_status">
                <input type="hidden" name="id" value="<?= $id ?>">
                <select name="status">
                    <?php foreach (['new', 'read', 'replied', 'archived'] as $s): ?>
                        <option value="<?= $s ?>" <?= $message['status'] === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                    <?php endforeach; ?>
                </select>
                <button class="small-action" type="submit">Update status</button>
            </form>
            <form method="post" action="<?= e(url('admin/messages')) ?>" onsubmit="return confirm('Delete message permanently?')">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="action" value="delete_message">
                <input type="hidden" name="id" value="<?= $id ?>">
                <button class="small-action danger" type="submit">Delete</button>
            </form>
        </div>
    </div>
<?php else: ?>
    <div class="admin-toolbar">
        <p class="panel-hint">Submissions captured through your public contact form.</p>
        <form method="get" action="<?= e(url('admin/messages')) ?>" class="filter-form">
            <select name="status" onchange="this.form.submit()">
                <?php foreach (['all', 'new', 'read', 'replied', 'archived'] as $s): ?>
                    <option value="<?= $s ?>" <?= $filter === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>
    <div class="admin-panel table-panel">
        <div class="table-scroll">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Sender</th>
                        <th>Subject / Preview</th>
                        <th>Received</th>
                        <th>Status</th>
                        <th>Open</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <td><strong><?= e($row['name']) ?></strong><small class="table-subline"><?= e($row['email']) ?></small></td>
                            <td><?= e($row['subject'] ?: preview_text($row['message'], 65)) ?></td>
                            <td><?= e(date('M j, Y H:i', strtotime($row['created_at']))) ?></td>
                            <td><span class="status-pill status-<?= e($row['status']) ?>"><?= e(ucfirst($row['status'])) ?></span></td>
                            <td><a class="small-action" href="<?= e(url("admin/messages?view=" . $row['id'])) ?>">View</a></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$rows): ?>
                        <tr>
                            <td colspan="5">No inquiries found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>