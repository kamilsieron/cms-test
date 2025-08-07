<?php
$page = $_GET['page'] ?? 'home';
$content = json_decode(file_get_contents('content.json'), true);
?>
<nav class="bg-white border-bottom mb-4">
    <div class="container">
        <ul class="nav nav-pills py-2">
            <?php
            $content = json_decode(file_get_contents('content.json'), true);
            foreach ($content as $slug => $data):
                if (!($data['visible_in_menu'] ?? false)) continue;
                $label = htmlspecialchars($data['menu_label'] ?? $slug);
                $active = $page === $slug ? 'active' : '';
            ?>
                <li class="nav-item">
                    <a href="?page=<?= $slug ?>" class="nav-link <?= $active ?>">
                        <?= $label ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</nav>

