<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title><?= $pageTitle ?? 'Panel admina' ?> | CMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: row;
        }

        .sidebar {
            width: 220px;
            background-color: #f8f9fa;
            padding: 1rem;
            border-right: 1px solid #ddd;
        }

        .main-content {
            flex-grow: 1;
            padding: 2rem;
        }

        .sidebar h4 {
            font-size: 1.2rem;
            margin-bottom: 1rem;
        }

        .sidebar a {
            display: block;
            margin-bottom: 0.5rem;
            text-decoration: none;
            color: #333;
        }

        .sidebar a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="sidebar">
    <h4>🛠️ Admin</h4>
    <a href="../index.php" target="_blank">🌐 Strona główna</a>
	<a href="users.php">👤 Użytkownicy</a>
	<a href="list.php">📄 Strony</a>
    <a href="../logout.php">🚪 Wyloguj</a>
</div>
<div class="main-content">
