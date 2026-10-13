<?php
    $note = [];
    $eroare = '';

    if (!is_dir('note') && !mkdir('note')) {
        $eroare = 'Directorul note nu a putut fi creat.';
    }

    $fisiere = is_dir('note') ? scandir('note') : [];

    foreach ($fisiere as $fisier) {
        if (pathinfo($fisier, PATHINFO_EXTENSION) !== 'txt') {
            continue;
        }

        $continut = file_get_contents('note/' . $fisier);
        if ($continut === false) {
            $eroare = 'Unele note nu au putut fi citite.';
            continue;
        }

        $note[] = [
            'titlu'    => pathinfo($fisier, PATHINFO_FILENAME),
            'continut' => $continut,
            'data'     => filemtime('note/' . $fisier),
        ];
    }

    // Aici primele note adaugate vor fi cele noi
    usort($note, function ($a, $b) {
        return $b['data'] - $a['data'];
    });
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notepad online</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body class="min-h-screen bg-slate-100 text-slate-800">

    <header class="border-b border-slate-200 bg-white">
        <div class="mx-auto flex max-w-5xl items-center justify-between px-4 py-5">
            <h1 class="text-2xl font-bold text-indigo-600">Notepad online</h1>
            <span class="text-sm text-slate-500"><?= count($note); ?> notițe</span>
        </div>
    </header>

    <main class="mx-auto max-w-5xl px-4 py-6">

        <?php if ($eroare !== ''): ?>
            <div class="mb-4 rounded-lg bg-red-100 px-4 py-3 text-sm font-medium text-red-800"><?= $eroare; ?></div>
        <?php endif; ?>

        
        <div class="mb-6 flex flex-col gap-3 sm:flex-row">
            <form class="flex-1" onsubmit="return false;">
                <input type="search" id="cautare" placeholder="Caută notă după titlu..."
                       class="w-full rounded-lg border border-slate-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </form>
            <button id="btn-nou" type="button"
                    class="rounded-lg bg-indigo-600 px-5 py-2 font-semibold text-white transition hover:bg-indigo-700">
                + Notă nouă
            </button>
        </div>

       
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <?php foreach ($note as $n): ?>
                <?php
                $titluSigur = htmlspecialchars($n['titlu'], ENT_QUOTES, 'UTF-8');
                $continutSigur = htmlspecialchars($n['continut'], ENT_QUOTES, 'UTF-8');
                $preview = htmlspecialchars(mb_substr($n['continut'], 0, 140), ENT_QUOTES, 'UTF-8');
                ?>
                <article class="note-card flex flex-col rounded-xl border border-slate-200 bg-white p-4 shadow-sm"
                         data-titlu="<?= strtolower($titluSigur); ?>">
                    <h2 class="truncate text-lg font-semibold"><?= $titluSigur; ?></h2>
                    <p class="mb-2 text-xs text-slate-400">
                        <?= date('d.m.Y H:i', $n['data']); ?> · <?= mb_strlen($n['continut']); ?> caractere
                    </p>
                    <p class="flex-1 whitespace-pre-line break-words text-sm text-slate-600"><?= $preview; ?></p>
                    <div class="mt-4 flex gap-2 text-sm">
                        <button type="button"
                                class="btn-editeaza rounded-md bg-amber-100 px-3 py-1 font-medium text-amber-800 hover:bg-amber-200"
                                data-titlu="<?= $titluSigur; ?>"
                                data-continut="<?= $continutSigur; ?>">Editează</button>
                        <a href="download.php?file=<?= rawurlencode($n['titlu'] . '.txt'); ?>"
                           class="rounded-md bg-sky-100 px-3 py-1 font-medium text-sky-800 hover:bg-sky-200">Descarcă</a>
                        <button type="button"
                                class="btn-sterge rounded-md bg-red-100 px-3 py-1 font-medium text-red-800 hover:bg-red-200"
                                data-titlu="<?= $titluSigur; ?>">Șterge</button>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <p id="gol" class="<?= empty($note) ? '' : 'hidden'; ?> py-12 text-center text-slate-500">
            Nu există nicio notă. Apasă „Notă nouă” pentru a crea una.
        </p>
    </main>

  
    <div id="modal-nota" class="modal fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4">
        <div class="w-full max-w-2xl rounded-xl bg-white p-6 shadow-xl">
            <h2 id="titlu-modal" class="mb-4 text-xl font-bold">Notă nouă</h2>
            <form action="save.php" method="post">
                <input type="hidden" name="original" id="original" value="">

                <label for="titlu" class="mb-1 block text-sm font-medium">Titlu</label>
                <input type="text" name="titlu" id="titlu" required maxlength="50"
                       pattern="[A-Za-z0-9_\-]+"
                       placeholder="litere, cifre, _ și -"
                       class="mb-4 w-full rounded-lg border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400">

                <label for="continut" class="mb-1 block text-sm font-medium">Conținut</label>
                <textarea name="continut" id="continut" rows="12" maxlength="10000"
                          placeholder="Scrie nota aici..."
                          class="editor w-full rounded-lg border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400"></textarea>
                <p class="mt-1 text-right text-xs text-slate-500"><span id="contor">0</span> / 10000 caractere</p>

                <div class="mt-4 flex justify-end gap-2">
                    <button type="button" class="btn-inchide rounded-lg bg-slate-200 px-4 py-2 font-medium hover:bg-slate-300">Anulează</button>
                    <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 font-semibold text-white hover:bg-indigo-700">Salvează</button>
                </div>
            </form>
        </div>
    </div>

    
    <div id="modal-stergere" class="modal fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4">
        <div class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl">
            <h2 class="mb-2 text-xl font-bold">Ștergi nota?</h2>
            <p class="mb-4 text-sm text-slate-600">
                Nota „<strong id="stergere-nume"></strong>” va fi ștearsă definitiv.
            </p>
            <form action="delete.php" method="post">
                <input type="hidden" name="file" id="stergere-fisier" value="">
                <div class="flex justify-end gap-2">
                    <button type="button" class="btn-inchide rounded-lg bg-slate-200 px-4 py-2 font-medium hover:bg-slate-300">Anulează</button>
                    <button type="submit" class="rounded-lg bg-red-600 px-4 py-2 font-semibold text-white hover:bg-red-700">Șterge</button>
                </div>
            </form>
        </div>
    </div>

    <script src="assets/script.js"></script>
</body>
</html>