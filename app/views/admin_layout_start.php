<?php
session_start();
require_once __DIR__ . '/../models/UserModel.php';

$userModel = new UserModel($db); // Upewnij się, że $pdo jest dostępne (np. z bootstrap.php)
$loggedInUser = $_SESSION['username'] ?? null;
$userData = $loggedInUser ? $userModel->find($loggedInUser) : null;
$avatar = $userData['avatar'] ?? 'public/avatars/default.png';
$username = $userData['username'] ?? 'Nieznany';

?>

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
            display: flex;
            flex-direction: column;
        }

        .topbar {
            background-color: #343a40;
            color: #fff;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-info img {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid #fff;
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

        .content-area {
            padding: 2rem;
            background-color: #fff;
            flex-grow: 1;
        }
    </style>
</head>
<body>
<div class="sidebar">
    <h4>🛠️ Admin</h4>
    <a href="../index.php" target="_blank">🌐 Strona główna</a>
    <a href="users.php">👤 Użytkownicy</a>
    <a href="list.php">📄 Strony</a>
    <a href="logout.php">🚪 Wyloguj</a>
</div>

<div class="main-content">
    <div class="topbar">
        <div>👋 Witaj, <?= htmlspecialchars($username) ?></div>
        <div class="user-info">
            <img src="/cms-test/<?= htmlspecialchars($avatar) ?>" alt="Avatar">
            <a href="logout.php" class="btn btn-sm btn-outline-light">Wyloguj</a>
        </div>
    </div>
    <div class="content-area">
