<?php
require_once __DIR__ . '/functions.php';

$id = $_GET['id'] ?? '';

if (!is_string($id) || !validId($id)) {
    http_response_code(400);
    exit('Identificator invalid.');
}

$path = getNotePath($id);

if ($path === null || !is_readable($path)) {
    http_response_code(404);
    exit('Fișierul nu a fost găsit sau nu poate fi citit.');
}

$title = getNoteTitle($path);
$filename = createSlug($title) . '.txt';

header('Content-Type: text/plain; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Content-Length: ' . filesize($path));
header('X-Content-Type-Options: nosniff');

if (readfile($path) === false) {
    error_log('Eroare la citirea notei pentru descărcare.');
    exit;
}

exit;