<?php
require_once __DIR__ . '/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Metodă nepermisă.');
}

verifyCsrf();

$id = $_POST['id'] ?? '';

if (!is_string($id) || !validId($id)) {
    http_response_code(400);
    exit('Identificator invalid.');
}

$path = getNotePath($id);

if ($path === null) {
    http_response_code(404);
    exit('Nota nu a fost găsită.');
}

if (!unlink($path)) {
    http_response_code(500);
    exit('Nota nu a putut fi ștearsă.');
}

redirectTo('index.php?message=deleted');