<?php
declare(strict_types=1);

// ---------- Settings ----------
const SITE_NAME      = 'មេរៀនសមីការអិចស្ប៉ូណង់ស្យែល';
const DB_PATH        = __DIR__ . '/data/lesson.sqlite';
const ALLOW_REGISTER = true;   // set to false to stop new sign-ups
const CLASS_CODE     = '';     // e.g. 'MATH12A': if set, students must type it to register

session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
session_start();

// ---------- Database (SQLite file, created automatically) ----------
if (!is_dir(dirname(DB_PATH))) {
    mkdir(dirname(DB_PATH), 0750, true);
    file_put_contents(
        dirname(DB_PATH) . '/.htaccess',
        "<IfModule mod_authz_core.c>\nRequire all denied\n</IfModule>\n<IfModule !mod_authz_core.c>\nDeny from all\n</IfModule>\n"
    );
}
$pdo = new PDO('sqlite:' . DB_PATH, null, null, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);
$pdo->exec("CREATE TABLE IF NOT EXISTS users (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name TEXT NOT NULL,
  email TEXT NOT NULL UNIQUE,
  password_hash TEXT NOT NULL,
  created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
)");

// ---------- Helpers ----------
function e(?string $s): string { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

function redirect(string $to): never { header('Location: ' . $to); exit; }

function csrf_field(): string
{
    $_SESSION['csrf'] ??= bin2hex(random_bytes(32));
    return '<input type="hidden" name="csrf" value="' . e($_SESSION['csrf']) . '">';
}

function csrf_verify(): void
{
    $sent = $_POST['csrf'] ?? '';
    if (!is_string($sent) || !hash_equals($_SESSION['csrf'] ?? '', $sent)) {
        http_response_code(419);
        exit('សម័យកាលផុតកំណត់ ។ សូមត្រឡប់ក្រោយ ផ្ទុកទំព័រឡើងវិញ ហើយសាកល្បងម្តងទៀត ។');
    }
}

function current_user(): ?array
{
    global $pdo;
    static $user = false;
    if ($user === false) {
        $user = null;
        if (!empty($_SESSION['uid'])) {
            $stmt = $pdo->prepare('SELECT id, name, email FROM users WHERE id = ?');
            $stmt->execute([$_SESSION['uid']]);
            $user = $stmt->fetch() ?: null;
        }
    }
    return $user;
}

// Anyone who is not logged in is sent to the login page; no lesson content is sent to them.
function require_login(): array
{
    $user = current_user();
    if (!$user) {
        redirect('login.php');
    }
    return $user;
}

function log_in(int $id): void
{
    session_regenerate_id(true);
    $_SESSION['uid'] = $id;
}

// Shared layout for login and register pages.
function auth_head(string $title): void
{
    ?>
<!DOCTYPE html>
<html lang="km">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($title) ?> | <?= e(SITE_NAME) ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Moul&family=Noto+Sans+Khmer:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body class="auth-page">
<main class="auth">
  <p class="auth-brand"><?= e(SITE_NAME) ?></p>
<?php
}

function auth_foot(): void
{
    echo "</main>\n</body>\n</html>\n";
}
