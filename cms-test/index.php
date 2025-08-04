<?php
require 'bootstrap.php';

include 'header.php';
include 'menu.php';

$content = json_decode(file_get_contents('content.json'), true);

if (isset($content[$page])) {
    echo "<main>";
    echo "<h2>" . htmlspecialchars($content[$page]['title']) . "</h2>";
    echo "<p>" . nl2br(htmlspecialchars($content[$page]['body'])) . "</p>";
    echo "</main>";
} else {
    echo "<main><h2>404</h2><p>Nie znaleziono strony: $page</p></main>";
}

include 'footer.php';
?>
