<?php
require_once __DIR__ . '/vendor/autoload.php';
define('BASE_URL', '/cms-test');
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

require_once __DIR__ . '/app/models/DatabaseDriver.php';

$db = new DatabaseDriver(); // ← instancja sterownika

$page = $_GET['page'] ?? 'home';
$contentPath = __DIR__ . '/content.json';
$content = json_decode(file_get_contents($contentPath), true);

// Domyślny tytuł strony
$title = $content[$page]['title'] ?? 'Nieznana strona';