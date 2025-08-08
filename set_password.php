<?php
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/app/models/PasswordResetModel.php';
require_once __DIR__ . '/app/models/UserModel.php';

$driver   = new DatabaseDriver(); // jeśli nie masz $db w bootstrap, użyj jak w projekcie
$prModel  = new PasswordResetModel($driver);
$userModel= new UserModel($driver);

$token = $_GET['token'] ?? '';
$valid = $token ? $prModel->findValid($token) : null;
$error = '';
$done  = false;

// CSRF (prosto)
session_start();
if (empty($_SESSION['csrf'])) { $_SESSION['csrf'] = bin2hex(random_bytes(16)); }
$csrf = $_SESSION['csrf'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
        $error = 'Nieprawidłowy token bezpieczeństwa.';
    } else {
        $token = $_POST['token'] ?? '';
        $valid = $token ? $prModel->findValid($token) : null;
        $pass1 = $_POST['password'] ?? '';
        $pass2 = $_POST['confirm'] ?? '';

        if (!$valid) {
            $error = 'Link wygasł lub został już użyty.';
        } elseif (strlen($pass1) < 8) {
            $error = 'Hasło musi mieć co najmniej 8 znaków.';
        } elseif ($pass1 !== $pass2) {
            $error = 'Hasła nie są identyczne.';
        } else {
            $hash = password_hash($pass1, PASSWORD_DEFAULT);
            $userModel->updatePassword($valid['username'], $hash);
            $prModel->markUsed((int)$valid['id']);
            $done = true;
        }
    }
}

?>
<!doctype html>
<html lang="pl">
<head>
  <meta charset="UTF-8">
  <title>Ustaw hasło | CMS</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card shadow-sm">
        <div class="card-body">
          <h1 class="h4 mb-3">Ustaw hasło</h1>

          <?php if ($done): ?>
            <div class="alert alert-success">Hasło zostało ustawione. Możesz się teraz zalogować.</div>
            <a class="btn btn-primary" href="admin/login.php">Przejdź do logowania</a>
          <?php elseif (!$valid && $_SERVER['REQUEST_METHOD'] !== 'POST'): ?>
            <div class="alert alert-danger">Link wygasł lub jest nieprawidłowy.</div>
          <?php else: ?>
            <?php if ($error): ?>
              <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="post" novalidate>
              <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
              <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>">

              <div class="mb-3">
                <label class="form-label">Nowe hasło</label>
                <input type="password" class="form-control" name="password" required minlength="8">
              </div>
              <div class="mb-3">
                <label class="form-label">Powtórz hasło</label>
                <input type="password" class="form-control" name="confirm" required minlength="8">
              </div>
              <button class="btn btn-success">Ustaw hasło</button>
            </form>
          <?php endif; ?>

        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>
