<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

define('SITE_NAME', 'Visitime');
define('BASE_URL', '/visitime'); // Change this if your folder name is different, e.g. '' for domain root.
//define('DB_HOST', '127.0.0.1');
define('DB_HOST', 'localhost');
define('DB_NAME', 'visitime');
define('DB_USER', 'root');
define('DB_PASS', '');

try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    exit('Database connection failed. Check config.php and confirm the visitime database exists.');
}

function e(?string $value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function is_admin(): bool {
    return isset($_SESSION['admin_id']) && (int)$_SESSION['admin_id'] > 0;
}

function require_admin(): void {
    if (!is_admin()) {
        header('Location: ' . BASE_URL . '/login.php');
        exit;
    }
}

function redirect(string $path): never {
    header('Location: ' . BASE_URL . '/' . ltrim($path, '/'));
    exit;
}
