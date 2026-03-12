<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/layout.php';
requireAuth('admin');

$db = getDb();
$rows = $db->query('SELECT s.id, s.language, s.result, s.created_at, u.full_name, p.title FROM submissions s JOIN users u ON u.id=s.user_id JOIN problems p ON p.id=s.problem_id ORDER BY s.id DESC LIMIT 200')->fetchAll();
renderHeader('All Submissions');
?>
<div class="card"><div class="card-body">
    <h5>Recent Submissions</h5>
    <table class="table table-sm"><thead><tr><th>ID</th><th>Student</th><th>Problem</th><th>Language</th><th>Result</th><th>Time</th></tr></thead><tbody>
    <?php foreach ($rows as $r): ?>
        <tr><td><?= (int) $r['id'] ?></td><td><?= e($r['full_name']) ?></td><td><?= e($r['title']) ?></td><td><?= e($r['language']) ?></td><td><?= e($r['result']) ?></td><td><?= e($r['created_at']) ?></td></tr>
    <?php endforeach; ?>
    </tbody></table>
</div></div>
<?php renderFooter(); ?>
