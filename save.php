<?php
    function afiseazaEroare(string $mesaj)
    {
        http_response_code(400);
        $mesajSigur = htmlspecialchars($mesaj, ENT_QUOTES, 'UTF-8');
        ?>
        <!DOCTYPE html>
        <html lang="ro">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Eroare</title>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css">
        </head>
        <body class="flex min-h-screen items-center justify-center bg-gray-100 px-4 py-6 text-gray-900">
            <main class="w-full max-w-md rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                <h1 class="text-xl font-bold">Nota nu a putut fi salvată</h1>
                <p class="mt-2 text-sm text-gray-600"><?= $mesajSigur; ?></p>
                <a href="index.php" class="mt-4 inline-block rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                    Înapoi la note
                </a>
            </main>
        </body>
        </html>
        <?php
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: index.php');
        exit;
    }

    $titlu = isset($_POST['titlu']) ? trim($_POST['titlu']) : '';
    $original = isset($_POST['original']) ? trim($_POST['original']) : '';
    $continut = isset($_POST['continut']) ? $_POST['continut'] : '';
    $continut = str_replace("\r\n", "\n", $continut);

    // Aici este asta ca daca titlul devine numele fisierului :doar cu litere ,cifre _ si -
    if (!preg_match('/^[A-Za-z0-9_-]{1,50}$/', $titlu)) {
        afiseazaEroare("Titlul poate avea 1-50 de caractere: litere, cifre, _ sau -.");
    }
    if ($original !== '' && !preg_match('/^[A-Za-z0-9_-]{1,50}$/', $original)) {
        afiseazaEroare("Nota care se editează este invalidă.");
    }

    
    if (mb_strlen($continut) > 10000) {
        afiseazaEroare("Nota depășește limita de 10.000 de caractere.");
    }

    // Aici am pus linie de cod pentru prevenire injectiei de cod
    if ($continut !== strip_tags($continut)) {
        afiseazaEroare("Nota nu poate conține tag-uri HTML sau cod PHP.");
    }

    if (!is_dir('note') && !mkdir('note')) {
        afiseazaEroare("Directorul note nu a putut fi creat.");
    }

    $cale = 'note/' . $titlu . '.txt';
    $caleVeche = 'note/' . $original . '.txt';

    // Aici am pus sa nu suprascriem un alt titlu cu cel original
    if (file_exists($cale) && $titlu !== $original) {
        afiseazaEroare("Există deja o notă cu acest titlu.");
    }

    if (file_put_contents($cale, $continut) === false) {
        afiseazaEroare("Eroare la scrierea fișierului. Încercați din nou.");
    }

    // Aici am pus daca la editare se schimba titlul o sa stearga fisierul vechi
    if ($original !== '' && $original !== $titlu && is_file($caleVeche)) {
        unlink($caleVeche);
    }

    header('Location: index.php');
    exit;
?>