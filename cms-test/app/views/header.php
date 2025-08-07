<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($title) ?> | Prosty CMS</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
	

</head>
<body>
<header class="bg-light py-3 mb-4 border-bottom">
    <div class="container d-flex justify-content-between align-items-center">
        <h1 class="h4 mb-0">Moja strona CMS</h1>
        <div>
            <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
                <span class="me-2 text-muted">Zalogowano jako <strong>admin</strong></span>
                <a href="admin.php" class="btn btn-outline-primary btn-sm">Panel</a>
                <a href="logout.php" class="btn btn-outline-secondary btn-sm">Wyloguj</a>
            <?php else: ?>
                <a href="admin/login.php" class="btn btn-primary btn-sm">Zaloguj się</a>
            <?php endif; ?>
        </div>
    </div>
</header>

