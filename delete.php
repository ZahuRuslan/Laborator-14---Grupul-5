<?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['file'])) {
        $file = basename($_POST['file']);
        $cale = 'note/' . $file;

        if (pathinfo($file, PATHINFO_EXTENSION) === 'txt' && is_file($cale)) {
            if (!unlink($cale)) {
                http_response_code(500);
                exit('Nota nu a putut fi ștearsă.');
            }
        }
    }

    header('Location: index.php');
    exit;
?>