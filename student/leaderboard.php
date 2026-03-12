<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/layout.php';
requireAuth('student');
$db = getDb();

$sql = "SELECT u.full_name, COUNT(DISTINCT s.problem_id) AS solved_count
        FROM users u
        LEFT JOIN submissions s ON s.user_id = u.id AND s.result='Accepted'
        WHERE u.role='student'
        GROUP BY u.id
        ORDER BY solved_count DESC, u.full_name ASC";
$rows = $db->query($sql)->fetchAll();
renderHeader('Leaderboard');
?>
<div class="card"><div class="card-body">
    <h5>Leaderboard (Solved Problems)</h5>
    <table class="table"><thead><tr><th>Rank</th><th>Name</th><th>Solved</th></tr></thead><tbody>
        <?php foreach ($rows as $i => $r): ?>
            <tr><td><?= $i + 1 ?></td><td><?= e($r['full_name']) ?></td><td><?= (int) $r['solved_count'] ?></td></tr>
        <?php endforeach; ?>
    </tbody></table>
</div></div>
<?php renderFooter(); ?>
