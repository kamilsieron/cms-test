<?php
require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../app/models/UserModel.php';
require_once __DIR__ . '/../app/models/ContentModel.php';
require_once __DIR__ . '/../app/models/PermissionModel.php';

$username = $_GET['username'] ?? '';
if (!$username) {
    header("Location: users.php");
    exit;
}

$userModel = new UserModel($db);
$contentModel = new ContentModel($db);
$permissionModel = new PermissionModel($db);

$user = $userModel->find($username);
if (!$user) {
    die("Użytkownik nie istnieje.");
}

$allPages = $contentModel->allPages(); // Zakładamy, że zwraca slug i tytuł
$currentPermissions = $permissionModel->getByUser($username);

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newRole = $_POST['role'] ?? $user['role'];
    $newPassword = $_POST['password'] ?? '';
    $currentUser = $_SESSION['user'] ?? 'admin'; // fallback

    // Aktualizacja roli
    if (!in_array($newRole, ['user', 'admin'])) {
        $errors[] = 'Nieprawidłowa rola.';
    } else {
        $userModel->updateRole($username, $newRole);
    }

    // Aktualizacja hasła (jeśli podano)
    if ($newPassword !== '') {
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        $userModel->updatePassword($username, $hash);
    }

    // Uprawnienia (tylko jeśli rola to admin)
    if ($newRole === 'admin') {
        $selectedPages = $_POST['permissions'] ?? [];
        $permissionModel->replacePermissions($username, $selectedPages, $currentUser);
    } else {
        $permissionModel->clearPermissions($username);
    }

    $success = true;
    $user = $userModel->find($username); // odśwież dane
    $currentPermissions = $permissionModel->getByUser($username);
}

require_once __DIR__ . '/../app/views/admin_layout_start.php';
?>

<h2>Edycja użytkownika: <?= htmlspecialchars($username) ?></h2>

<?php if ($success): ?>
  <div class="alert alert-success">Dane użytkownika zostały zaktualizowane.</div>
<?php endif; ?>

<form method="post" class="mb-4">
  <div class="mb-3">
    <label class="form-label">Nowe hasło (opcjonalnie)</label>
    <input type="password" class="form-control" name="password">
  </div>

  <div class="mb-3">
    <label class="form-label">Rola</label>
    <select name="role" class="form-select">
      <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>user</option>
      <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>admin</option>
    </select>
  </div>

  <?php if ($user['role'] === 'admin'): ?>
    <div class="mb-3">
      <label class="form-label">Uprawnienia do edycji stron:</label>
      <div class="form-check">
        <?php foreach ($allPages as $page): ?>
          <div>
            <input
              class="form-check-input"
              type="checkbox"
              name="permissions[]"
              value="<?= $page['slug'] ?>"
              id="perm_<?= $page['slug'] ?>"
              <?= in_array($page['slug'], $currentPermissions) ? 'checked' : '' ?>
            >
            <label class="form-check-label" for="perm_<?= $page['slug'] ?>">
              <?= htmlspecialchars($page['title']) ?> (<?= $page['slug'] ?>)
            </label>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endif; ?>

  <button class="btn btn-primary">Zapisz zmiany</button>
  <a href="users.php" class="btn btn-secondary">Anuluj</a>
</form>

<?php require_once __DIR__ . '/../app/views/admin_layout_end.php'; ?>
