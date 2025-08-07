<?php
require_once 'bootstrap.php';
require_once 'app/models/DatabaseDriver.php';

$db = new DatabaseDriver();
$pages = $db->query("SELECT * FROM pages");

echo '<pre>';
print_r($pages);

echo password_hash('haslo123', PASSWORD_DEFAULT);
?>
