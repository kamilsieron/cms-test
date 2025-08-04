<?php
session_start();

$page = $_GET['page'] ?? 'home';
$content = json_decode(file_get_contents('content.json'), true);

// Domyślny tytuł strony
$title = $content[$page]['title'] ?? 'Nieznana strona';