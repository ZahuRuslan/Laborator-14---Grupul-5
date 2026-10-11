<?php
session_start();

define('NOTES_DIR', __DIR__ . '/note/');

if (!is_dir(NOTES_DIR)) {
    mkdir(NOTES_DIR, 0755, true);
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function escapeHtml(string $value): string
{
    return htmlspecialchars(
        $value,
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );
}

function verifyCsrf(): void
{
    $token = $_POST['csrf_token'] ?? '';

    if (
        !is_string($token) ||
        !hash_equals($_SESSION['csrf_token'], $token)
    ) {
        http_response_code(403);
        exit('Cerere invalidă. Reîncarcă pagina și încearcă din nou.');
    }
}

function validId(string $id): bool
{
    return preg_match('/^[a-f0-9]{12}$/', $id) === 1;
}

function getNotePath(string $id): ?string
{
    if (!validId($id)) {
        return null;
    }

    $files = glob(NOTES_DIR . '*-' . $id . '.txt');

    return !empty($files) ? $files[0] : null;
}

function getNoteTitle(string $path): string
{
    $name = pathinfo($path, PATHINFO_FILENAME);

    return preg_replace('/-[a-f0-9]{12}$/', '', $name) ?: 'Nota';
}

function createSlug(string $title): string
{
    $title = trim($title);

    $slug = preg_replace('/[^a-zA-Z0-9_-]+/', '-', $title);
    $slug = trim($slug ?? '', '-_');

    return substr($slug ?: 'nota', 0, 60);
}

function getNotes(): array
{
    $files = glob(NOTES_DIR . '*.txt') ?: [];
    $notes = [];

    foreach ($files as $file) {
        $filename = basename($file);

        if (!preg_match('/-([a-f0-9]{12})\.txt$/', $filename, $matches)) {
            continue;
        }

        $notes[] = [
            'id' => $matches[1],
            'title' => getNoteTitle($file),
            'updated' => filemtime($file) ?: 0,
        ];
    }

    usort($notes, fn($a, $b) => $b['updated'] <=> $a['updated']);

    return $notes;
}

function redirectTo(string $url): never
{
    header('Location: ' . $url);
    exit;
}