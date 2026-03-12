<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/layout.php';
require_once __DIR__ . '/includes/db.php';

$user = currentUser();
if ($user) {
    header('Location: /' . ($user['role'] === 'admin' ? 'admin/dashboard.php' : 'student/dashboard.php'));
    exit;
}

renderHeader('Welcome to CodeArena');
?>
<div class="p-5 mb-4 bg-white rounded-3 shadow-sm">
    <h1>CodeArena - Online Coding Platform</h1>
    <p class="lead">Teachers can create coding challenges and students can solve them with instant judging.</p>
    <a href="/register.php" class="btn btn-primary">Start as Student</a>
    <a href="/login.php" class="btn btn-outline-secondary">Login</a>
</div>
<?php renderFooter(); ?>
