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
    <div class="admin-panel" style="max-width: 960px;">
        <a class="back-link" href="<?= e(url('admin/messages')) ?>">← Back to inbox</a>
        <div class="message-heading" style="margin-top: 12px; display: flex; justify-content: space-between; align-items: flex-start; gap: 20px;">
            <div>
                <h2 style="font-size: 22px; color: #0f172a; margin: 0 0 6px;"><?= e($message['subject'] ?: 'No Subject') ?></h2>
                <p class="panel-hint" style="margin: 0 0 4px;">From <strong><?= e($message['name']) ?></strong> &middot; <a href="mailto:<?= e($message['email']) ?>" style="color: #0284c7; text-decoration: underline;"><?= e($message['email']) ?></a></p>
                <p class="panel-hint" style="font-size: 11px; color: #94a3b8;"><?= e($message['created_at']) ?></p>
            </div>
            <span class="status-pill status-<?= e($message['status']) ?>" style="text-transform: capitalize; padding: 5px 12px; border-radius: 20px; font-weight: 600; font-size: 11px;">
                <?= e(ucfirst($message['status'])) ?>
            </span>
        </div>

        <div class="message-body" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 20px; font-size: 13.5px; line-height: 1.8; color: #334155; margin: 20px 0; white-space: pre-wrap;">
            <?= e($message['message']) ?>
        </div>

        <!-- Action Row -->
        <div class="message-actions" style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap; padding-bottom: 24px; border-bottom: 1px solid #e2e8f0;">
            <button type="button" class="button button-primary" id="toggleReplyBtn" onclick="document.getElementById('replyComposerBox').scrollIntoView({behavior: 'smooth'}); document.getElementById('replyBodyText').focus();" style="background: #b7f36b; color: #111; font-weight: 700; border: none; padding: 10px 18px; border-radius: 8px; cursor: pointer;">
                ✉ Write Direct Reply
            </button>

            <form method="post" action="<?= e(url('admin/messages')) ?>" class="inline-form" style="display: inline-flex; align-items: center; gap: 8px;">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="action" value="message_status">
                <input type="hidden" name="id" value="<?= $id ?>">
                <select name="status" style="padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 12px; background: #fff; color: #0f172a;">
                    <?php foreach (['new', 'read', 'replied', 'archived'] as $s): ?>
                        <option value="<?= $s ?>" <?= $message['status'] === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                    <?php endforeach; ?>
                </select>
                <button class="small-action" type="submit" style="padding: 8px 14px; border-radius: 8px; border: 1px solid #cbd5e1; background: #fff; cursor: pointer;">Update status</button>
            </form>

            <form method="post" action="<?= e(url('admin/messages')) ?>" onsubmit="return confirm('Delete message permanently?')" style="margin-left: auto;">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="action" value="delete_message">
                <input type="hidden" name="id" value="<?= $id ?>">
                <button class="small-action danger" type="submit" style="background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; padding: 8px 14px; border-radius: 8px; cursor: pointer;">Delete</button>
            </form>
        </div>

        <!-- Direct Email Reply Composer Form -->
        <div id="replyComposerBox" style="margin-top: 28px; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 12px; padding: 24px; box-shadow: 0 4px 15px rgba(0,0,0,0.03);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                <h3 style="margin: 0; font-size: 16px; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                    <span style="color: #16a34a;">●</span> Dispatch Reply to <?= e($message['name']) ?>
                </h3>
                <small style="color: #64748b; font-size: 11px;">Dispatched using SMTP Config</small>
            </div>

            <form method="post" action="<?= e(url('admin/messages?view=' . $id)) ?>" class="admin-form">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="action" value="send_reply">
                <input type="hidden" name="message_id" value="<?= $id ?>">
                <input type="hidden" name="recipient_email" value="<?= e($message['email']) ?>">
                <input type="hidden" name="recipient_name" value="<?= e($message['name']) ?>">

                <div class="form-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 14px;">
                    <label style="display: block; font-size: 11px; font-weight: 600; color: #475569;">
                        To Recipient
                        <input type="text" value="<?= e($message['name'] . ' <' . $message['email'] . '>') ?>" readonly style="background: #f1f5f9; color: #64748b; cursor: not-allowed; border: 1px solid #cbd5e1; border-radius: 8px; padding: 10px 12px; width: 100%; margin-top: 6px;">
                    </label>
                    <label style="display: block; font-size: 11px; font-weight: 600; color: #475569;">
                        Email Subject
                        <input type="text" name="reply_subject" value="Re: <?= e($message['subject'] ?: 'Inquiry follow-up') ?>" required style="background: #fff; color: #0f172a; border: 1px solid #cbd5e1; border-radius: 8px; padding: 10px 12px; width: 100%; margin-top: 6px;">
                    </label>
                </div>

                <label style="display: block; font-size: 11px; font-weight: 600; color: #475569; margin-bottom: 16px;">
                    Your Response Body
                    <textarea id="replyBodyText" name="reply_body" rows="7" required placeholder="Hi <?= e($message['name']) ?>,&#10;&#10;Thank you for reaching out..." style="background: #fff; color: #0f172a; border: 1px solid #cbd5e1; border-radius: 8px; padding: 12px; width: 100%; margin-top: 6px; font-family: inherit; font-size: 13px; line-height: 1.6;"></textarea>
                </label>

                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                    <label style="display: inline-flex; align-items: center; gap: 8px; font-size: 12px; color: #64748b; cursor: pointer; margin: 0;">
                        <input type="checkbox" name="quote_original" value="1" checked style="width: auto; margin: 0;">
                        Quote original message in email footer
                    </label>

                    <button type="submit" class="button button-primary" style="background: #0f172a; color: #ffffff; padding: 10px 22px; border-radius: 8px; font-weight: 600; border: none; cursor: pointer;">
                        Send Email Response ↗
                    </button>
                </div>
            </form>
        </div>
    </div>
<?php else: ?>
    <div class="admin-toolbar" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
        <p class="panel-hint" style="margin: 0;">Submissions captured through your public contact form.</p>
        <form method="get" action="<?= e(url('admin/messages')) ?>" class="filter-form">
            <select name="status" onchange="this.form.submit()" style="padding: 7px 12px; border: 1px solid #cbd5e1; border-radius: 8px; background: #fff; font-size: 12px;">
                <?php foreach (['all', 'new', 'read', 'replied', 'archived'] as $s): ?>
                    <option value="<?= $s ?>" <?= $filter === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>
    <div class="admin-panel table-panel" style="background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px 22px;">
        <div class="table-scroll">
            <table class="admin-table" style="width: 100%; border-collapse: collapse; font-size: 12px;">
                <thead>
                    <tr style="border-bottom: 1px solid #e2e8f0; text-align: left;">
                        <th style="padding: 10px 12px; color: #64748b; font-size: 11px;">Sender</th>
                        <th style="padding: 10px 12px; color: #64748b; font-size: 11px;">Subject / Preview</th>
                        <th style="padding: 10px 12px; color: #64748b; font-size: 11px;">Received</th>
                        <th style="padding: 10px 12px; color: #64748b; font-size: 11px;">Status</th>
                        <th style="padding: 10px 12px; color: #64748b; font-size: 11px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $row): ?>
                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            <td style="padding: 12px;"><strong><?= e($row['name']) ?></strong><small style="display: block; color: #64748b;"><?= e($row['email']) ?></small></td>
                            <td style="padding: 12px; color: #334155;"><?= e($row['subject'] ?: preview_text($row['message'], 65)) ?></td>
                            <td style="padding: 12px; color: #64748b; white-space: nowrap;"><?= e(date('M j, Y H:i', strtotime($row['created_at']))) ?></td>
                            <td style="padding: 12px;">
                                <span class="status-pill status-<?= e($row['status']) ?>" style="padding: 4px 10px; border-radius: 20px; font-size: 10px; font-weight: 600; text-transform: capitalize;">
                                    <?= e(ucfirst($row['status'])) ?>
                                </span>
                            </td>
                            <td style="padding: 12px;">
                                <a class="small-action" href="<?= e(url("admin/messages?view=" . $row['id'])) ?>" style="padding: 5px 12px; background: #0f172a; color: #fff; text-decoration: none; border-radius: 6px; font-size: 11px; font-weight: 600;">
                                    View &amp; Reply
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$rows): ?>
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 24px; color: #94a3b8;">No inquiries found in this status category.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>