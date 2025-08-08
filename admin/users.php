<?php
require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../app/models/UserModel.php';

$model = new UserModel($db);
$users = $model->all();

$pageTitle = 'Użytkownicy';
?>

<?php include __DIR__ . '/../app/views/admin_layout_start.php'; ?>

<h1>👤 Użytkownicy</h1>

<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nazwa użytkownika</th>
            <th>Hasło (hash)</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?= $user['id'] ?></td>
                <td><?= htmlspecialchars($user['username']) ?></td>
                <td><code><?= htmlspecialchars($user['password']) ?></code></td>
				<td><?= htmlspecialchars($user['role']) ?></td>
				<td>
				<a class="btn btn-sm btn-outline-primary" href="user_edit.php?username=<?= urlencode($user['username']) ?>">
					✏️ Edytuj
				</a>
				</td>				
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<a href="user_add.php" class="btn btn-success mb-3">
    ➕ Dodaj użytkownika
</a>


<?php include __DIR__ . '/../app/views/admin_layout_end.php'; ?>
