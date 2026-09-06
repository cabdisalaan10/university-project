<?php

// Shared configuration, database connection, sessions, and helper functions.
// Keep sessions inside the project so PHP does not depend on XAMPP's protected temp folder.
$sessionDirectory = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR . 'sessions';
if (!is_dir($sessionDirectory)) {
    mkdir($sessionDirectory, 0775, true);
}
session_save_path($sessionDirectory);
session_start();
// Database connection settings used by every application page.
define('DB_HOST', 'localhost');
define('DB_NAME', 'hotel_management');
define('DB_USER', 'root');
define('DB_PASS', '');
try {
    // Create one shared PDO connection for database queries.
    $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
} catch (PDOException $e) {
    die('Database connection failed. Import database/hotel_management.sql and update includes/config.php if your MySQL password is different.');
}
function e($value)
{
    // Escape dynamic values before displaying them in HTML.
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
function redirect($path)
{
    header("Location: $path");
    exit;
}
function flash($message = null)
{
    if ($message !== null) {
        $_SESSION['flash'] = $message;
        return;
    } $m = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $m;
}
function logged_in()
{
    // Check whether the current session contains an authenticated user.
    return isset($_SESSION['user']);
}
function require_login()
{
    if (!logged_in()) {
        redirect('login.php');
    }
}
function is_admin()
{
    return ($_SESSION['user']['role'] ?? '') === 'admin';
}
function require_admin()
{
    require_login();
    if (!is_admin()) {
        flash('Only administrators can access that page.');
        redirect('dashboard.php');
    }
}
function hotel_settings()
{
    global $pdo;
    static $settings = null;
    if ($settings === null) {
        // Cache hotel branding so it is queried only once per request.
        $row = $pdo->query('SELECT * FROM hotel_settings WHERE id=1')->fetch(PDO::FETCH_ASSOC);
        $settings = $row ?: ['hotel_name' => 'HotelMS', 'logo_path' => null, 'hotel_email' => null, 'hotel_phone' => null, 'hotel_address' => null];
    } return $settings;
}

/** Returns an active class for the sidebar link matching the current page. */
function nav_active($page)
{
    return basename($_SERVER['PHP_SELF']) === $page ? 'active' : '';
}
