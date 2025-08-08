<?php
require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../app/models/UserModel.php';
require_once __DIR__ . '/../app/models/PasswordResetModel.php';
require_once __DIR__ . '/../app/services/Mailer.php';

$model = new UserModel($db);

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
   // $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm'] ?? '';
    $email  = $_POST['email'] ?? '';
	$role = 'user';

    if ($username === '' || $email === '') {
        $errors[] = 'Wszystkie pola są wymagane.';
    } else {
        //$hash = password_hash($password, PASSWORD_DEFAULT);
		
		// Zapis użytkownika do bazy
		$model->create($username, $email, $role);
		
$driver = $db; // Twój DatabaseDriver z bootstrap.php
$prModel = new PasswordResetModel($driver);

// 1) unieważnij stare tokeny dla tego użytkownika (opcjonalnie)
$prModel->clearAllForUser($username);

// 2) wygeneruj nowy, silny token
$rawToken = rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '='); // URL-safe
$expires  = (new DateTime('+24 hours'));

// 3) zapisz hash tokenu w DB
$prModel->create($username, $rawToken, $expires);

// 4) zbuduj URL
$resetUrl = rtrim($_ENV['APP_URL'], '/') . '/set_password.php?token=' . urlencode($rawToken);

$err = null;
if (!Mailer::sendSetPasswordMail($email, $username, $resetUrl, $err)) {
    echo "<pre style='white-space:pre-wrap;color:#b00;background:#fee;border:1px solid #f88;padding:8px'>".
         htmlspecialchars($err).
         "</pre>";
}

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
			<label>Email</label>
			<input type="email" name="email" class="form-control" required>
		</div>		
		

        <button type="submit" class="btn btn-primary">Dodaj użytkownika</button>
        <a href="users.php" class="btn btn-secondary">Anuluj</a>
      </form>
    </main>
  </div>
</div>

<?php require_once __DIR__ . '/../app/views/footer.php'; ?>
