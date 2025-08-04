<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($title) ?> | Prosty CMS</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<header>
    <h1>Moja strona CMS</h1>

    <div style="text-align:right; font-size:0.9em;">
        <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
            Zalogowano jako <strong>admin</strong> |
            <a href="admin.php">Panel</a> |
            <a href="logout.php">Wyloguj</a>
        <?php else: ?>
            <a href="login.php">Zaloguj się</a>
        <?php endif; ?>
    </div>
</header>
