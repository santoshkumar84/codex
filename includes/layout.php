<?php

declare(strict_types=1);

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/helpers.php';

function renderHeader(string $title): void
{
    $user = currentUser();
    ?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?= e(csrfToken()) ?>">
    <title><?= e($title) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/codemirror.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="/index.php">CodeArena</a>
        <div class="ms-auto d-flex gap-2">
            <?php if ($user): ?>
                <span class="text-light">Hi, <?= e($user['full_name']) ?> (<?= e($user['role']) ?>)</span>
                <a class="btn btn-sm btn-outline-light" href="/api/logout.php">Logout</a>
            <?php else: ?>
                <a class="btn btn-sm btn-outline-light" href="/login.php">Login</a>
                <a class="btn btn-sm btn-warning" href="/register.php">Register</a>
            <?php endif; ?>
        </div>
    </div>
</nav>
<div class="container">
    <?php if ($msg = getFlash('success')): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>
    <?php if ($msg = getFlash('error')): ?><div class="alert alert-danger"><?= e($msg) ?></div><?php endif; ?>
    <?php
}

function renderFooter(): void
{
    ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/codemirror.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/clike/clike.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/python/python.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/php/php.min.js"></script>
<script src="/assets/js/app.js"></script>
</body>
</html>
    <?php
}
