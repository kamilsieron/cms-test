<?php
require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();



$page = $_GET['page'] ?? 'home';
$content = json_decode(file_get_contents('content.json'), true);

// Domyślny tytuł strony
$title = $content[$page]['title'] ?? 'Nieznana strona';