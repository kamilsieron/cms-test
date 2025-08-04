<?php
$page = $_GET['page'] ?? 'home';
$content = json_decode(file_get_contents('content.json'), true);

echo '<nav><ul>';

foreach ($content as $slug => $data) {
    if (!($data['visible_in_menu'] ?? false)) continue;

    $label = htmlspecialchars($data['menu_label'] ?? $slug);
    $active = $page === $slug ? 'style="font-weight:bold;"' : '';
    echo "<li><a href=\"?page=$slug\" $active>$label</a></li>";
}

echo '</ul></nav>';
?>
