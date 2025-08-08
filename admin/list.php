<?php
require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../app/models/ContentModel.php';

$model = new ContentModel();
$content = $model->load();
$pageTitle = 'Lista stron';
?>

<?php include __DIR__ . '/../app/views/admin_layout_start.php'; ?>

<h1>📄 Lista stron</h1>
<table class="table table-striped">
    <thead>
        <tr>
            <th>Slug</th>
            <th>Tytuł</th>
            <th>W menu</th>
            <th>Akcja</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($content as $slug => $page): ?>
            <tr>
                <td><?= htmlspecialchars($slug) ?></td>
                <td><?= htmlspecialchars($page['title']) ?></td>
                <td><?= $page['visible_in_menu'] ? '✅' : '❌' ?></td>
                <td><a class="btn btn-sm btn-primary" href="edit.php?slug=<?= urlencode($slug) ?>">Edytuj</a></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include __DIR__ . '/../app/views/admin_layout_end.php'; ?>
