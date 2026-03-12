<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/layout.php';
requireAuth('student');
$db = getDb();
$user = currentUser();

$stmt = $db->prepare('SELECT s.*, p.title FROM submissions s JOIN problems p ON p.id=s.problem_id WHERE s.user_id=:uid ORDER BY s.id DESC');
$stmt->execute(['uid' => $user['id']]);
$rows = $stmt->fetchAll();
renderHeader('Submission History');
?>
<div class="card"><div class="card-body">
    <h5>Your Submissions</h5>
    <table class="table table-sm"><thead><tr><th>ID</th><th>Problem</th><th>Language</th><th>Result</th><th>Time</th><th>Code</th></tr></thead><tbody>
    <?php foreach ($rows as $r): ?>
        <tr><td><?= (int) $r['id'] ?></td><td><?= e($r['title']) ?></td><td><?= e($r['language']) ?></td><td><?= e($r['result']) ?></td><td><?= e($r['created_at']) ?></td><td><details><summary>View</summary><pre><?= e($r['source_code']) ?></pre></details></td></tr>
    <?php endforeach; ?>
    </tbody></table>
</div></div>
<?php renderFooter(); ?>
