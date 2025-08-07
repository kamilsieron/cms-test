<?php
session_start();
require_once __DIR__ . '/../bootstrap.php';

if (!($_SESSION['logged_in'] ?? false)) {
    header("Location: login.php");
    exit;
}

header("Location: list.php");
exit;
