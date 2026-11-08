<?php

declare(strict_types=1);
/**
 * @var string $type
 * @var bool $isForm
 * @var int $id
 * @var array $item
 * @var string $responsibilities
 * @var string $tags
 * @var array $rows
 * @var array $companies
 */
?>
<?php if ($isForm): ?>
    <div class="admin-panel">
        <a class="back-link" href="<?= e(url("admin/content/{$type}")) ?>">← Back to <?= e(ucfirst($type)) ?></a>
        <h2><?= $id ? 'Edit item' : 'Add new entry' ?></h2>
        <form method="post" action="<?= e(url("admin/content/{$type}")) ?>" class="admin-form">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="action" value="save_content">
            <input type="hidden" name="type" value="<?= e($type) ?>">
            <input type="hidden" name="id" value="<?= $id ?>">

            <div class="form-grid">
                <?php if ($type === 'skills'): ?>
                    <label>Skill Name *
                        <input name="name" required value="<?= e($item['name'] ?? '') ?>" placeholder="e.g., Google Workspace">
                    </label>
                    <label>Category (Group) *
                        <input name="group_name" required value="<?= e($item['group_name'] ?? 'Support') ?>" placeholder="e.g., Administration, Operations">
                    </label>
                    <label>Proficiency Level
                        <input name="level" value="<?= e($item['level'] ?? 'Core') ?>" placeholder="e.g., Core, Working knowledge">
                    </label>
                    <label>Sort Order
                        <input type="number" name="sort_order" value="<?= e((string)($item['sort_order'] ?? 0)) ?>">
                    </label>

                <?php elseif ($type === 'experience'): ?>
                    <label class="wide">Position Type / Promotion Grouping
                        <select name="parent_id">
                            <option value="">Primary Company Role (Standard)</option>
                            <?php foreach ($companies as $comp): ?>
                                <?php if ($id && $comp['id'] == $id) continue; ?>
                                <option value="<?= $comp['id'] ?>" <?= (!empty($item['parent_id']) && $item['parent_id'] == $comp['id']) ? 'selected' : '' ?>>
                                    ↳ Promotion at <?= e($comp['company']) ?> (Parent: <?= e($comp['role']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </label>

                    <label>Job Title *
                        <input name="role" required value="<?= e($item['role'] ?? '') ?>" placeholder="e.g., Senior Desktop Support Engineer">
                    </label>
                    <label>Company Name
                        <input name="company" value="<?= e($item['company'] ?? '') ?>" placeholder="e.g., Noor Staffing Group (optional if promotion is linked above)">
                    </label>
                    <label>Date Range *
                        <input name="period" required value="<?= e($item['period'] ?? '') ?>" placeholder="e.g., Mar 2026 – Present">
                    </label>
                    <label>Location / Mode
                        <input name="location" value="<?= e($item['location'] ?? '') ?>" placeholder="e.g., Bangladesh / Remote">
                    </label>
                    <label>Sort Order
                        <input type="number" name="sort_order" value="<?= e((string)($item['sort_order'] ?? 0)) ?>">
                    </label>
                    <label class="wide">Responsibilities (one per line) *
                        <textarea name="description" rows="6" required placeholder="Managed Zendesk queue&#10;Handled Google Workspace user lifecycle"><?= e($responsibilities) ?></textarea>
                    </label>

                <?php elseif ($type === 'projects'): ?>
                    <label>Project Title *
                        <input name="title" required value="<?= e($item['title'] ?? '') ?>">
                    </label>
                    <label>Type / Domain *
                        <input name="project_type" required value="<?= e($item['project_type'] ?? '') ?>" placeholder="e.g., Infrastructure · Networking">
                    </label>
                    <label>Sort Order
                        <input type="number" name="sort_order" value="<?= e((string)($item['sort_order'] ?? 0)) ?>">
                    </label>
                    <label class="wide">Tags (comma separated)
                        <input name="tags" value="<?= e($tags) ?>" placeholder="Cat6, Cabling, Infrastructure">
                    </label>
                    <label class="wide">Description *
                        <textarea name="description" rows="5" required><?= e($item['description'] ?? '') ?></textarea>
                    </label>

                <?php elseif ($type === 'education'): ?>
                    <label>Degree / Qualification *
                        <input name="degree" required value="<?= e($item['degree'] ?? '') ?>">
                    </label>
                    <label>Institution *
                        <input name="institution" required value="<?= e($item['institution'] ?? '') ?>">
                    </label>
                    <label>Period *
                        <input name="period" required value="<?= e($item['period'] ?? '') ?>">
                    </label>
                    <label>Sort Order
                        <input type="number" name="sort_order" value="<?= e((string)($item['sort_order'] ?? 0)) ?>">
                    </label>
                    <label class="wide">Additional Detail
                        <input name="detail" value="<?= e($item['detail'] ?? '') ?>" placeholder="e.g., CGPA: 3.91 / 4.00">
                    </label>
                <?php endif; ?>
            </div>

            <button class="button button-primary" type="submit">Save Changes ↗</button>
        </form>
    </div>
<?php else: ?>
    <div class="admin-toolbar">
        <p class="panel-hint">Manage entries shown on your portfolio. Hidden items remain in MySQL.</p>
        <a class="button button-primary" href="<?= e(url("admin/content/{$type}?new=1")) ?>">+ Add Entry</a>
    </div>

    <div class="admin-panel table-panel">
        <div class="table-scroll">
            <table class="admin-table">
                <thead>
                    <tr>
                        <?php if ($type === 'skills'): ?>
                            <th>Skill</th>
                            <th>Group</th>
                            <th>Level</th>
                        <?php elseif ($type === 'experience'): ?>
                            <th>Role / Position</th>
                            <th>Company</th>
                            <th>Period</th>
                        <?php elseif ($type === 'projects'): ?>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Description</th>
                        <?php elseif ($type === 'education'): ?>
                            <th>Degree</th>
                            <th>Institution</th>
                            <th>Period</th>
                        <?php endif; ?>
                        <th>Visibility</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <?php if ($type === 'skills'): ?>
                                <td><strong><?= e($row['name']) ?></strong></td>
                                <td><?= e($row['group_name']) ?></td>
                                <td><?= e($row['level']) ?></td>
                            <?php elseif ($type === 'experience'): ?>
                                <td>
                                    <?php if (!empty($row['parent_id'])): ?>
                                        <span style="color:#0284c7; margin-right: 6px;">↳ Promotion:</span>
                                    <?php endif; ?>
                                    <strong><?= e($row['role']) ?></strong>
                                </td>
                                <td><?= e($row['company']) ?></td>
                                <td><?= e($row['period']) ?></td>
                            <?php elseif ($type === 'projects'): ?>
                                <td><strong><?= e($row['title']) ?></strong></td>
                                <td><?= e($row['project_type']) ?></td>
                                <td><?= e(preview_text($row['description'], 65)) ?></td>
                            <?php elseif ($type === 'education'): ?>
                                <td><strong><?= e($row['degree']) ?></strong></td>
                                <td><?= e($row['institution']) ?></td>
                                <td><?= e($row['period']) ?></td>
                            <?php endif; ?>
                            <td>
                                <span class="status-pill <?= $row['is_visible'] ? 'status-live' : 'status-hidden' ?>">
                                    <?= $row['is_visible'] ? 'Visible' : 'Hidden' ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-row">
                                    <a class="small-action" href="<?= e(url("admin/content/{$type}?edit=" . $row['id'])) ?>">Edit</a>
                                    <form method="post" action="<?= e(url("admin/content/{$type}")) ?>">
                                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                        <input type="hidden" name="action" value="visibility">
                                        <input type="hidden" name="type" value="<?= e($type) ?>">
                                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                        <button class="small-action" type="submit"><?= $row['is_visible'] ? 'Hide' : 'Show' ?></button>
                                    </form>
                                    <form method="post" action="<?= e(url("admin/content/{$type}")) ?>" onsubmit="return confirm('Delete permanently?')">
                                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                        <input type="hidden" name="action" value="delete_content">
                                        <input type="hidden" name="type" value="<?= e($type) ?>">
                                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                        <button class="small-action danger" type="submit">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$rows): ?>
                        <tr>
                            <td colspan="5">No entries found. Click "+ Add Entry" to create one.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>