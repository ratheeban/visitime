<?php
$pageTitle = $pageTitle ?? SITE_NAME;
$currentPage = basename($_SERVER['PHP_SELF'] ?? '');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?> | <?= e(SITE_NAME) ?></title>
    <link rel="icon" type="image/svg+xml" href="<?= BASE_URL ?>/assets/visitime-logo.png">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/style.css">
</head>
<body>
<header class="site-header">
    <div class="container nav-wrap">
        <a class="brand" href="<?= BASE_URL ?>/index.php" aria-label="Visitime home">
            <img class="brand-logo" src="<?= BASE_URL ?>/assets/visitime-logo.png" alt="" width="42" height="42"><span class="brand-name">Visitime</span>
        </a>
        <button class="menu-toggle" type="button" aria-label="Toggle navigation" onclick="document.querySelector('.main-nav').classList.toggle('open')">☰</button>
        <nav class="main-nav">
            <a class="<?= $currentPage === 'index.php' ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php">Home</a>
            <a class="<?= $currentPage === 'explore.php' ? 'active' : '' ?>" href="<?= BASE_URL ?>/explore.php">Explore Places</a>
            <a class="<?= $currentPage === 'planner.php' ? 'active' : '' ?>" href="<?= BASE_URL ?>/planner.php">Day Planner</a>
            <a class="<?= $currentPage === 'contact.php' ? 'active' : '' ?>" href="<?= BASE_URL ?>/contact.php">Contact</a>
            <?php if (is_admin()): ?>
                <a href="<?= BASE_URL ?>/admin.php">Admin</a>
                <a href="<?= BASE_URL ?>/logout.php">Logout</a>
            <?php else: ?>
                <a href="<?= BASE_URL ?>/login.php">Admin Login</a>
            <?php endif; ?>
        </nav>
        <div class="header-datetime" id="headerDateTime" aria-live="off"></div>
    </div>
</header>
<main>
