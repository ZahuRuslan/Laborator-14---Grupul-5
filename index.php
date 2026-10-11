<?php
require_once __DIR__ . '/functions.php';

$notes = getNotes();
$id = $_GET['id'] ?? '';
$selectedNote = null;
$content = '';
$title = '';

if (is_string($id) && $id !== '') {
    $path = getNotePath($id);

    if ($path !== null && is_file($path)) {
        $selectedNote = [
            'id' => $id,
            'title' => getNoteTitle($path),
        ];

        $title = $selectedNote['title'];
        $content = file_get_contents($path);

        if ($content === false) {
            $content = '';
            $error = 'Nota nu a putut fi citită.';
        }
    }
}

$error = $error ?? '';
$message = $_GET['message'] ?? '';
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notepad Online</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-slate-100 text-slate-800 min-h-screen">

<div class="min-h-screen md:flex">

    <aside class="bg-slate-900 text-white p-6 md:w-80 md:min-h-screen">
        <div class="flex items-center gap-3 mb-8">
            <div class="bg-indigo-500 rounded-xl p-3 text-xl">✎</div>
            <div>
                <h1 class="text-xl font-bold">Notepad Online</h1>
                <p class="text-slate-400 text-sm">Notele tale, într-un singur loc</p>
            </div>
        </div>

        <a href="index.php"
           class="block w-full text-center bg-indigo-500 hover:bg-indigo-600 rounded-xl py-3 px-4 font-semibold mb-6">
            + Notă nouă
        </a>

        <label for="search" class="block text-sm text-slate-300 mb-2">
            Caută după titlu
        </label>

        <input
            type="search"
            id="search"
            placeholder="Caută o notă..."
            class="w-full bg-slate-800 border border-slate-700 rounded-xl p-3 mb-6 text-white outline-none focus:ring-2 focus:ring-indigo-500"
        >

        <div class="flex justify-between items-center mb-3">
            <h2 class="font-semibold">Notele mele</h2>
            <span class="text-xs bg-slate-700 rounded-full px-3 py-1">
                <?= count($notes) ?>
            </span>
        </div>

        <div id="notes-list" class="space-y-2">
            <?php foreach ($notes as $note): ?>
                <a
                    href="index.php?id=<?= urlencode($note['id']) ?>"
                    data-title="<?= escapeHtml(mb_strtolower($note['title'])) ?>"
                    class="note-item block p-3 rounded-xl hover:bg-slate-800 transition
                    <?= ($selectedNote && $selectedNote['id'] === $note['id'])
                        ? 'bg-slate-800 border border-indigo-400'
                        : '' ?>"
                >
                    <div class="font-medium truncate">
                        <?= escapeHtml($note['title']) ?>
                    </div>
                    <div class="text-xs text-slate-400 mt-1">
                        <?= date('d.m.Y H:i', $note['updated']) ?>
                    </div>
                </a>
            <?php endforeach; ?>

            <?php if (empty($notes)): ?>
                <p class="text-sm text-slate-400" id="empty-notes">
                    Nu ai încă nicio notă. Creează prima notă!
                </p>
            <?php endif; ?>

            <p id="no-results" class="hidden text-sm text-slate-400">
                Nu s-au găsit note.
            </p>
        </div>
    </aside>

    <main class="flex-1 p-4 md:p-10">
        <div class="max-w-5xl mx-auto">

            <header class="flex flex-wrap justify-between items-center gap-4 mb-8">
                <div>
                    <h2 class="text-2xl md:text-3xl font-bold">
                        <?= $selectedNote ? 'Editează nota' : 'Creează o notă' ?>
                    </h2>
                    <p class="text-slate-500 mt-1">
                        Scrie ideile tale și salvează-le ca fișiere text.
                    </p>
                </div>

                <span class="text-sm bg-white rounded-full px-4 py-2 shadow-sm">
                    Fișiere .txt
                </span>
            </header>

            <?php if ($error !== ''): ?>
                <div class="bg-red-100 text-red-700 rounded-xl p-4 mb-5">
                    <?= escapeHtml($error) ?>
                </div>
            <?php endif; ?>

            <?php if ($message === 'saved'): ?>
                <div class="bg-green-100 text-green-800 rounded-xl p-4 mb-5">
                    Nota a fost salvată cu succes!
                </div>
            <?php elseif ($message === 'deleted'): ?>
                <div class="bg-green-100 text-green-800 rounded-xl p-4 mb-5">
                    Nota a fost ștearsă.
                </div>
            <?php endif; ?>

            <section class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <form action="save.php" method="POST" id="note-form">
                    <input type="hidden" name="csrf_token"
                           value="<?= escapeHtml($_SESSION['csrf_token']) ?>">

                    <input type="hidden" name="id"
                           value="<?= escapeHtml($selectedNote['id'] ?? '') ?>">

                    <div class="p-5 border-b border-slate-200">
                        <label for="title" class="block font-semibold mb-2">
                            Titlul notei
                        </label>
                        <input
                            type="text"
                            name="title"
                            id="title"
                            maxlength="60"
                            required
                            value="<?= escapeHtml($title) ?>"
                            placeholder="Exemplu: Ideile mele"
                            class="w-full border border-slate-300 rounded-xl p-3 focus:ring-2 focus:ring-indigo-500 outline-none"
                        >
                    </div>

                    <div class="p-5">
                        <div class="flex justify-between items-center mb-3 gap-3">
                            <label for="content" class="font-semibold">
                                Conținut
                            </label>
                            <span class="text-sm text-slate-500">
                                <span id="char-count"><?= mb_strlen($content) ?></span>/10.000
                            </span>
                        </div>

                        <textarea
                            name="content"
                            id="content"
                            rows="16"
                            maxlength="10000"
                            required
                            placeholder="Începe să scrii aici..."
                            class="w-full resize-y border border-slate-300 rounded-xl p-4 outline-none focus:ring-2 focus:ring-indigo-500 editor"
                        ><?= escapeHtml($content) ?></textarea>

                        <p id="limit-warning" class="hidden text-sm text-red-600 mt-2">
                            Ai atins limita de 10.000 de caractere.
                        </p>
                    </div>

                    <div class="bg-slate-50 border-t border-slate-200 p-5 flex flex-wrap gap-3">
                        <button
                            type="submit"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl px-6 py-3 font-semibold"
                        >
                            Salvează nota
                        </button>

                        <a href="index.php"
                           class="bg-white border border-slate-300 hover:bg-slate-100 rounded-xl px-6 py-3 font-semibold">
                            Notă nouă
                        </a>
                    </div>
                </form>
            </section>

            <?php if ($selectedNote): ?>
                <section class="mt-5 flex flex-wrap gap-3">
                    <a href="download.php?id=<?= urlencode($selectedNote['id']) ?>"
                       class="bg-white border border-slate-300 hover:bg-slate-50 rounded-xl px-5 py-3 font-medium">
                        ↓ Descarcă .txt
                    </a>

                    <button type="button" id="delete-button"
                            class="bg-red-50 text-red-700 hover:bg-red-100 rounded-xl px-5 py-3 font-medium">
                        Șterge nota
                    </button>

                    <form action="delete.php" method="POST" id="delete-form" class="hidden">
                        <input type="hidden" name="csrf_token"
                               value="<?= escapeHtml($_SESSION['csrf_token']) ?>">
                        <input type="hidden" name="id"
                               value="<?= escapeHtml($selectedNote['id']) ?>">
                    </form>
                </section>
            <?php endif; ?>

            <p class="text-center text-sm text-slate-400 mt-8">
                Notepad Online · Grupul 5
            </p>
        </div>
    </main>
</div>

<script src="script.js"></script>
</body>
</html>