<?php
require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../app/models/ContentModel.php';

$slug = $_GET['slug'] ?? null;
if (!$slug) {
    header("Location: list.php");
    exit;
}

$model = new ContentModel();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $model->save($slug, [
        'title' => $_POST['title'],
        'body'  => $_POST['body'],
        'menu_label' => $_POST['menu_label'],
        'visible_in_menu' => isset($_POST['visible_in_menu']) ? 1 : 0
    ]);

    header("Location: edit.php?slug=$slug&saved=1");
    exit;
}

$content = $model->load();
$page = $content[$slug] ?? ['title' => '', 'body' => '', 'menu_label' => '', 'visible_in_menu' => 1];
?>

<?php include __DIR__ . '/../app/views/admin_layout_start.php'; ?>


<div class="container py-4">
    <h1 class="mb-4">Edycja: <?= htmlspecialchars($slug) ?></h1>

    <?php if (isset($_GET['saved'])): ?>
        <div class="alert alert-success">✅ Zapisano zmiany</div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="post">
                <div class="mb-3">
                    <label class="form-label">Tytuł</label>
                    <input type="text" name="title" value="<?= htmlspecialchars($page['title']) ?>" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Treść</label>
                    <textarea name="body" class="form-control" rows="8"><?= htmlspecialchars($page['body']) ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nazwa w menu</label>
                    <input type="text" name="menu_label" value="<?= htmlspecialchars($page['menu_label']) ?>" class="form-control">
                </div>

                <div class="form-check form-switch mb-4">
                    <input class="form-check-input" type="checkbox" name="visible_in_menu" id="visible" value="1" <?= $page['visible_in_menu'] ? 'checked' : '' ?>>
                    <label class="form-check-label" for="visible">Widoczna w menu</label>
                </div>

                <div class="d-flex gap-2">
                    <button class="btn btn-success" type="submit">💾 Zapisz</button>
                    <a href="list.php" class="btn btn-secondary">↩️ Wróć</a>
                </div>
            </form>
        </div>
    </div>
</div>


<?php include __DIR__ . '/../app/views/admin_layout_end.php'; ?>
