<?php
require_once __DIR__ . '/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Metodă nepermisă.');
}

verifyCsrf();

$title = trim($_POST['title'] ?? '');
$content = $_POST['content'] ?? '';
$id = $_POST['id'] ?? '';

if (!is_string($title) || !is_string($content) || !is_string($id)) {
    exit('Date invalide.');
}

if ($title === '' || mb_strlen($title) > 60) {
    exit('Titlul este obligatoriu și trebuie să aibă maximum 60 de caractere.');
}

if (mb_strlen($content) > 10000) {
    exit('Nota nu poate depăși 10.000 de caractere.');
}

if (trim($content) === '') {
    exit('Conținutul notei nu poate fi gol.');
}

if ($id !== '' && !validId($id)) {
    http_response_code(400);
    exit('Identificator invalid.');
}

$oldPath = null;

if ($id !== '') {
    $oldPath = getNotePath($id);

    if ($oldPath === null) {
        http_response_code(404);
        exit('Nota nu a fost găsită.');
    }
} else {
    $id = bin2hex(random_bytes(6));
}

$slug = createSlug($title);
$newPath = NOTES_DIR . $slug . '-' . $id . '.txt';

if ($oldPath !== null && $oldPath !== $newPath) {
    if (file_exists($newPath)) {
        http_response_code(409);
        exit('Există deja un fișier cu acest nume.');
    }

    if (!rename($oldPath, $newPath)) {
        http_response_code(500);
        exit('Nu s-a putut actualiza numele fișierului.');
    }
}

$result = file_put_contents($newPath, $content, LOCK_EX);

if ($result === false) {
    http_response_code(500);
    exit('Eroare la salvarea notei. Verifică permisiunile directorului note.');
}

redirectTo('index.php?id=' . urlencode($id) . '&message=saved');