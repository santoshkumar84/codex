<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/layout.php';
requireAuth('student');

renderHeader('Student Dashboard');
?>
<div class="d-flex gap-2 mb-3">
    <a class="btn btn-primary" href="/student/problems.php">Problem List</a>
    <a class="btn btn-outline-secondary" href="/student/history.php">Submission History</a>
    <a class="btn btn-outline-success" href="/student/leaderboard.php">Leaderboard</a>
</div>
<div class="alert alert-info">Solve problems, submit code, and climb the leaderboard.</div>
<?php renderFooter(); ?>
