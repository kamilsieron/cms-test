<?php
require_once 'bootstrap.php';
require_once 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = $_POST['login'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($login === ADMIN_LOGIN && $password === ADMIN_PASSWORD) {
        $_SESSION['logged_in'] = true;
        header('Location: admin.php');
        exit;
    } else {
        $error = 'Nieprawidłowy login lub hasło';
    }
}
?>

<h2>Logowanie do panelu</h2>

<?php if ($error): ?>
    <p style="color:red;"><?= $error ?></p>
<?php endif; ?>

<form method="post">
    <label>Login: <input type="text" name="login"></label><br><br>
    <label>Hasło: <input type="password" name="password"></label><br><br>
    <button type="submit">Zaloguj</button>
</form>
