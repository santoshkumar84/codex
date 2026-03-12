<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/layout.php';
requireAuth('admin');
$db = getDb();
$stats = [
    'problems' => (int) $db->query('SELECT COUNT(*) FROM problems')->fetchColumn(),
    'students' => (int) $db->query("SELECT COUNT(*) FROM users WHERE role='student'")->fetchColumn(),
    'submissions' => (int) $db->query('SELECT COUNT(*) FROM submissions')->fetchColumn(),
    'accepted' => (int) $db->query("SELECT COUNT(*) FROM submissions WHERE result='Accepted'")->fetchColumn(),
];

renderHeader('Admin Dashboard');
?>
<div class="row g-3 mb-4">
    <?php foreach ($stats as $label => $value): ?>
    <div class="col-md-3"><div class="card shadow-sm"><div class="card-body"><h6><?= e(ucfirst($label)) ?></h6><h2><?= $value ?></h2></div></div></div>
    <?php endforeach; ?>
</div>
<div class="d-flex gap-2">
    <a href="/admin/problems.php" class="btn btn-primary">Manage Problems</a>
    <a href="/admin/users.php" class="btn btn-outline-primary">Manage Users</a>
    <a href="/admin/submissions.php" class="btn btn-outline-secondary">View Submissions</a>
</div>
<?php renderFooter(); ?>
