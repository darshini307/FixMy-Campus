<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
$page_title = $page_title ?? 'FixMy Campus';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($page_title) ?> | FixMy Campus</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="app-shell">
    <?php include __DIR__ . '/sidebar.php'; ?>
    <main class="main-content">
        <header class="topbar">
            <div>
                <p class="eyebrow">Campus maintenance portal</p>
                <h1><?= e($page_title) ?></h1>
            </div>
            <?php if (!empty($_SESSION['user_id'])): ?>
                <div class="user-chip">
                    <span class="avatar"><?= e(strtoupper(substr($_SESSION['name'], 0, 1))) ?></span>
                    <span><?= e($_SESSION['name']) ?></span>
                </div>
            <?php endif; ?>
        </header>
