<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/layout.php';
requireAuth('student');
$db = getDb();
$user = currentUser();

$search = trim($_GET['search'] ?? '');
$difficulty = $_GET['difficulty'] ?? '';
$tag = trim($_GET['tag'] ?? '');

$sql = 'SELECT DISTINCT p.* FROM problems p LEFT JOIN problem_tags pt ON pt.problem_id=p.id LEFT JOIN tags t ON t.id=pt.tag_id WHERE 1=1';
$params = [];
if ($search !== '') {
    $sql .= ' AND (p.title LIKE :search OR p.description LIKE :search)';
    $params['search'] = "%$search%";
}
if ($difficulty !== '') {
    $sql .= ' AND p.difficulty = :difficulty';
    $params['difficulty'] = $difficulty;
}
if ($tag !== '') {
    $sql .= ' AND t.name = :tag';
    $params['tag'] = $tag;
}
$sql .= ' ORDER BY p.id DESC';
$stmt = $db->prepare($sql);
$stmt->execute($params);
$problems = $stmt->fetchAll();

$solvedStmt = $db->prepare("SELECT DISTINCT problem_id FROM submissions WHERE user_id=:uid AND result='Accepted'");
$solvedStmt->execute(['uid' => $user['id']]);
$solved = array_flip(array_map('intval', array_column($solvedStmt->fetchAll(), 'problem_id')));

$tags = $db->query('SELECT name FROM tags ORDER BY name')->fetchAll();

renderHeader('Problems');
?>
<form class="row g-2 mb-3">
    <div class="col-md-5"><input class="form-control" name="search" placeholder="Search" value="<?= e($search) ?>"></div>
    <div class="col-md-3"><select class="form-select" name="difficulty"><option value="">All Difficulty</option><?php foreach (['Easy','Medium','Hard'] as $d): ?><option value="<?= $d ?>" <?= $difficulty === $d ? "selected" : "" ?>><?= $d ?></option><?php endforeach; ?></select></div>
    <div class="col-md-3"><select class="form-select" name="tag"><option value="">All Tags</option><?php foreach ($tags as $t): ?><option <?= $tag === $t['name'] ? 'selected' : '' ?>><?= e($t['name']) ?></option><?php endforeach; ?></select></div>
    <div class="col-md-1"><button class="btn btn-primary w-100">Go</button></div>
</form>
<div class="row g-3">
<?php foreach ($problems as $p): ?>
    <div class="col-md-6">
        <div class="card problem-card shadow-sm"><div class="card-body">
            <div class="d-flex justify-content-between">
                <h5><?= e($p['title']) ?></h5>
                <span class="badge bg-secondary badge-difficulty"><?= e($p['difficulty']) ?></span>
            </div>
            <p class="text-muted">Problem #<?= (int) $p['id'] ?></p>
            <span class="badge <?= isset($solved[(int) $p['id']]) ? 'bg-success' : 'bg-warning text-dark' ?>"><?= isset($solved[(int) $p['id']]) ? 'Solved' : 'Unsolved' ?></span>
            <a href="/student/problem.php?id=<?= (int) $p['id'] ?>" class="btn btn-sm btn-primary float-end">Solve</a>
        </div></div>
    </div>
<?php endforeach; ?>
</div>
<?php renderFooter(); ?>
