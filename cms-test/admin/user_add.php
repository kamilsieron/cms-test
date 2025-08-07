<?php
require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../app/models/UserModel.php';

$model = new UserModel($db);

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm'] ?? '';

    if ($username === '' || $password === '' || $confirm === '') {
        $errors[] = 'Wszystkie pola są wymagane.';
    } elseif ($password !== $confirm) {
        $errors[] = 'Hasła nie są identyczne.';
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $model->create($username, $hash);
        $success = true;
    }
}

require_once __DIR__ . '/../app/views/admin_layout_start.php';
?>

<div class="container-fluid">
  <div class="row">

    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 mt-4">
      <h2>Dodaj użytkownika</h2>

      <?php if ($success): ?>
        <div class="alert alert-success">Użytkownik został dodany.</div>
      <?php endif; ?>

      <?php if ($errors): ?>
        <div class="alert alert-danger">
          <ul class="mb-0">
            <?php foreach ($errors as $e): ?>
              <li><?= htmlspecialchars($e) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <form method="post" class="mt-3">
        <div class="mb-3">
          <label for="username" class="form-label">Nazwa użytkownika</label>
          <input type="text" class="form-control" name="username" id="username" required>
        </div>

        <div class="mb-3">
          <label for="password" class="form-label">Hasło</label>
          <input type="password" class="form-control" name="password" id="password" required>
        </div>

        <div class="mb-3">
          <label for="confirm" class="form-label">Powtórz hasło</label>
          <input type="password" class="form-control" name="confirm" id="confirm" required>
        </div>

        <button type="submit" class="btn btn-primary">Dodaj użytkownika</button>
        <a href="users.php" class="btn btn-secondary">Anuluj</a>
      </form>
    </main>
  </div>
</div>

<?php require_once __DIR__ . '/../app/views/footer.php'; ?>
