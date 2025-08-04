<?php
require 'bootstrap.php';
$title = 'Panel administracyjny';
include 'header.php';
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Ścieżka do pliku z treścią
$contentFile = 'content.json';

// Wczytaj aktualną zawartość
$content = json_decode(file_get_contents($contentFile), true);

// Zapis zmian, jeśli formularz został wysłany
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
		die('Błąd: nieprawidłowy token CSRF');
	}	
	
    $page = $_POST['page'];
    $title = $_POST['title'];
    $body = $_POST['body'];

	$menu_label = $_POST['menu_label'] ?? $title;
	$visible_in_menu = isset($_POST['visible_in_menu']) ? true : false;

	$content[$page] = [
		'title' => $title,
		'body' => $body,
		'menu_label' => $menu_label,
		'visible_in_menu' => $visible_in_menu
	];

    // Zapis do pliku
    file_put_contents($contentFile, json_encode($content, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    echo "<p style='color:green;'>Zapisano zmiany dla strony: <strong>$page</strong></p>";
}

// Formularz edycji
?>
<h1>Panel edycji treści</h1>

<form method="post">
    <label>Wybierz stronę:
        <select name="page">
            <?php foreach ($content as $key => $data): ?>
                <option value="<?= htmlspecialchars($key) ?>"><?= htmlspecialchars($key) ?></option>
            <?php endforeach; ?>
        </select>
    </label>
    <br><br>
    <label>Tytuł: <br>
        <input type="text" name="title" value="" id="title" style="width:400px;">
    </label>
    <br><br>
    <label>Treść: <br>
        <textarea name="body" id="body" rows="10" cols="80"></textarea>
    </label>
	<label>Nazwa w menu:<br>
		<input type="text" name="menu_label" id="menu_label" style="width:400px;">
	</label>
	<br><br>
	<label>
		<input type="checkbox" name="visible_in_menu" id="visible_in_menu" value="1">
		Pokaż w menu
	</label>
	<br><br>	
    <br><br>
	<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
    <button type="submit">Zapisz</button>
</form>

<script>
// Proste wypełnianie formularza danymi po zmianie strony
const content = <?= json_encode($content, JSON_UNESCAPED_UNICODE) ?>;
const select = document.querySelector('select[name="page"]');
const titleInput = document.getElementById('title');
const bodyInput = document.getElementById('body');
const menuLabelInput = document.getElementById('menu_label');
const visibleCheckbox = document.getElementById('visible_in_menu');

function fillForm() {
    const page = select.value;
    titleInput.value = content[page].title;
    bodyInput.value = content[page].body;
    menuLabelInput.value = content[page].menu_label || '';
    visibleCheckbox.checked = !!content[page].visible_in_menu;
}


select.addEventListener('change', fillForm);
window.addEventListener('load', fillForm);
</script>
<p><a href="logout.php">Wyloguj</a></p>
<?php include 'footer.php'; ?>