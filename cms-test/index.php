<?php
require 'bootstrap.php';

include 'app/views/header.php';
include 'app/views/menu.php';


require_once 'app/models/ContentModel.php';

$model = new ContentModel();
$content = $model->load();

if (isset($content[$page])) {
	?>
<main class="container">
    <h2><?= htmlspecialchars($content[$page]['title']) ?></h2>
    <p><?= nl2br(htmlspecialchars($content[$page]['body'])) ?></p>
</main>
<?php
} else {
    echo "<main><h2>404</h2><p>Nie znaleziono strony: $page</p></main>";
}

include 'app/views/footer.php';
?>
