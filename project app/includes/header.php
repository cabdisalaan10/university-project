<?php
// Shared page header: authentication, hotel branding, sidebar, and top navigation.
require_once __DIR__ . '/config.php';

require_login();

$page_title = $page_title ?? 'Hotel Management';
$hotel = hotel_settings();
?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?= e($page_title) ?> | <?= e($hotel['hotel_name']) ?></title>
        <link rel="stylesheet" href="assets/css/style.css">
    </head>
    <body>
<!-- Collapsible application sidebar -->
<aside class="sidebar" id="sidebar">
            <div class="brand-wrap">
                <button class="sidebar-toggle" id="menuToggle" aria-label="Toggle sidebar">☰</button>
                <?php if (!empty($hotel['logo_path'])): ?>
                <img class="hotel-logo" src="<?= e($hotel['logo_path']) ?>" alt="Hotel logo">
            <?php else: ?>
            <div class="logo-placeholder">H</div>
        <?php endif; ?>
        <div class="brand-text">
            <?= e($hotel['hotel_name']) ?>
            <small>Management System</small>
        </div>
    </div>

    <!-- Display the role of the currently signed-in user. -->
    <p class="role"><?= e($_SESSION['user']['role']) ?></p>
    <!-- Primary navigation links available to all authenticated users. -->
    <nav>
        <a class="<?= nav_active('dashboard.php') ?>" href="dashboard.php"><i>⌂</i><span>Dashboard</span></a>
        <a class="<?= nav_active('rooms.php') ?>" href="rooms.php"><i>▣</i><span>Rooms</span></a>
        <a class="<?= nav_active('bookings.php') ?>" href="bookings.php"><i>▤</i><span>Bookings</span></a>
        <a class="<?= nav_active('availability.php') ?>" href="availability.php"><i>⌕</i><span>Availability</span></a>
        <a class="<?= nav_active('payments.php') ?>" href="payments.php"><i>◈</i><span>Payments &amp; Invoices</span></a>

        <!-- Additional links visible only to administrators. -->
        <?php if (is_admin()): ?>
        <div class="nav-label">ADMINISTRATION</div>
        <a class="<?= nav_active('staff.php') ?>" href="staff.php"><i>♙</i><span>Staff</span></a>
        <a class="<?= nav_active('reports.php') ?>" href="reports.php"><i>▥</i><span>Reports</span></a>
        <a class="<?= nav_active('users.php') ?>" href="users.php"><i>♚</i><span>Users</span></a>
        <a class="<?= nav_active('hotel_settings.php') ?>" href="hotel_settings.php"><i>⚙</i><span>Hotel Settings</span></a>
    <?php endif; ?>
</nav>

<!-- Account actions remain at the bottom of the sidebar. -->
<div class="sidebar-bottom">
    <a class="<?= nav_active('profile.php') ?>" href="profile.php"><i>◉</i><span>My Profile</span></a>
    <a class="logout" href="logout.php"><i>↪</i><span>Sign out</span></a>
</div>
</aside>

<!-- Main page content begins here -->
<main>
    <!-- Top bar with page navigation, title, and signed-in user details. -->
    <header class="topbar">
        <div class="top-left">
            <button class="back-button " type="button" onclick="history.back()" aria-label="Go back">←</button>
            <div>
                <h1><?= e($page_title) ?></h1>
                <small><?= e($hotel['hotel_name']) ?></small>
            </div>
        </div>

        <a class="profile-chip" href="profile.php">
            <div class="avatar"><?= e(strtoupper(substr($_SESSION['user']['full_name'], 0, 1))) ?></div>
            <div>
                <strong><?= e($_SESSION['user']['full_name']) ?></strong>
                <small><?= e($_SESSION['user']['role']) ?></small>
            </div>
        </a>
    </header>

    <!-- Show a one-time notification when one has been set. -->
    <?php if ($message = flash()): ?>
    <div class="alert"><?= e($message) ?></div>
<?php endif; ?>
