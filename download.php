<?php
    if (isset($_GET['file'])) {
        $file = basename($_GET['file']);
        $cale = 'note/' . $file;

        if (pathinfo($file, PATHINFO_EXTENSION) === 'txt' && is_file($cale)) {
            header('Content-Type: text/plain; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $file . '"');
            header('Content-Length: ' . filesize($cale));
            if (readfile($cale) === false) {
            http_response_code(500);
            exit('Eroare la citirea notei.');
}
            exit;
        }
    }

    http_response_code(404);
    echo 'Nota nu a fost găsită.';
?>