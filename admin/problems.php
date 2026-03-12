<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/layout.php';
requireAuth('admin');
$db = getDb();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCsrf($_POST['csrf_token'] ?? null)) {
        setFlash('error', 'Invalid CSRF token.');
        header('Location: /admin/problems.php');
        exit;
    }

    $action = $_POST['action'] ?? 'save';
    if ($action === 'delete') {
        $stmt = $db->prepare('DELETE FROM problems WHERE id = :id');
        $stmt->execute(['id' => (int) ($_POST['id'] ?? 0)]);
        setFlash('success', 'Problem deleted.');
        header('Location: /admin/problems.php');
        exit;
    }

    $payload = [
        'title' => trim($_POST['title'] ?? ''),
        'description' => trim($_POST['description'] ?? ''),
        'input_format' => trim($_POST['input_format'] ?? ''),
        'output_format' => trim($_POST['output_format'] ?? ''),
        'constraints' => trim($_POST['constraints'] ?? ''),
        'sample_input' => trim($_POST['sample_input'] ?? ''),
        'sample_output' => trim($_POST['sample_output'] ?? ''),
        'difficulty' => in_array($_POST['difficulty'] ?? '', ['Easy', 'Medium', 'Hard'], true) ? $_POST['difficulty'] : 'Easy',
    ];

    if (array_filter($payload, fn($v) => $v === '')) {
        setFlash('error', 'All problem fields are required.');
        header('Location: /admin/problems.php');
        exit;
    }

    if (!empty($_POST['id'])) {
        $payload['id'] = (int) $_POST['id'];
        $stmt = $db->prepare('UPDATE problems SET title=:title,description=:description,input_format=:input_format,output_format=:output_format,constraints_text=:constraints,sample_input=:sample_input,sample_output=:sample_output,difficulty=:difficulty WHERE id=:id');
        $stmt->execute($payload);
        setFlash('success', 'Problem updated successfully.');
    } else {
        $stmt = $db->prepare('INSERT INTO problems(title,description,input_format,output_format,constraints_text,sample_input,sample_output,difficulty,created_by) VALUES(:title,:description,:input_format,:output_format,:constraints,:sample_input,:sample_output,:difficulty,:created_by)');
        $stmt->execute($payload + ['created_by' => currentUser()['id']]);
        $problemId = (int) $db->lastInsertId();

        $testInputs = $_POST['test_input'] ?? [];
        $testOutputs = $_POST['test_output'] ?? [];
        foreach ($testInputs as $i => $input) {
            if (trim($input) === '') {
                continue;
            }
            $tc = $db->prepare('INSERT INTO test_cases(problem_id,input_data,expected_output,is_sample) VALUES(:pid,:in,:out,0)');
            $tc->execute(['pid' => $problemId, 'in' => $input, 'out' => $testOutputs[$i] ?? '']);
        }

        $tags = array_filter(array_map('trim', explode(',', $_POST['tags'] ?? '')));
        foreach ($tags as $tagName) {
            $db->prepare('INSERT IGNORE INTO tags(name) VALUES(:name)')->execute(['name' => $tagName]);
            $tagStmt = $db->prepare('SELECT id FROM tags WHERE name=:name');
            $tagStmt->execute(['name' => $tagName]);
            $tagId = (int) $tagStmt->fetchColumn();
            if ($tagId > 0) {
                $db->prepare('INSERT IGNORE INTO problem_tags(problem_id, tag_id) VALUES(:pid,:tid)')->execute(['pid' => $problemId, 'tid' => $tagId]);
            }
        }
        setFlash('success', 'Problem created successfully.');
    }

    header('Location: /admin/problems.php');
    exit;
}

$editing = null;
if (isset($_GET['edit'])) {
    $stmt = $db->prepare('SELECT * FROM problems WHERE id = :id');
    $stmt->execute(['id' => (int) $_GET['edit']]);
    $editing = $stmt->fetch();
}

$problems = $db->query('SELECT p.*, u.full_name AS creator FROM problems p LEFT JOIN users u ON u.id=p.created_by ORDER BY p.id DESC')->fetchAll();
renderHeader('Manage Problems');
?>
<div class="row g-4">
    <div class="col-md-5">
        <div class="card"><div class="card-body">
            <h5><?= $editing ? 'Edit Problem' : 'Add Problem' ?></h5>
            <form method="post">
                <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                <input type="hidden" name="id" value="<?= (int) ($editing['id'] ?? 0) ?>">
                <input class="form-control mb-2" name="title" placeholder="Title" value="<?= e($editing['title'] ?? '') ?>" required>
                <textarea class="form-control mb-2" name="description" placeholder="Description" required><?= e($editing['description'] ?? '') ?></textarea>
                <textarea class="form-control mb-2" name="input_format" placeholder="Input Format" required><?= e($editing['input_format'] ?? '') ?></textarea>
                <textarea class="form-control mb-2" name="output_format" placeholder="Output Format" required><?= e($editing['output_format'] ?? '') ?></textarea>
                <textarea class="form-control mb-2" name="constraints" placeholder="Constraints" required><?= e($editing['constraints_text'] ?? '') ?></textarea>
                <textarea class="form-control mb-2" name="sample_input" placeholder="Sample Input" required><?= e($editing['sample_input'] ?? '') ?></textarea>
                <textarea class="form-control mb-2" name="sample_output" placeholder="Sample Output" required><?= e($editing['sample_output'] ?? '') ?></textarea>
                <select class="form-select mb-2" name="difficulty">
                    <?php foreach (['Easy', 'Medium', 'Hard'] as $d): ?>
                        <option value="<?= $d ?>" <?= (($editing['difficulty'] ?? 'Easy') === $d) ? 'selected' : '' ?>><?= $d ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if (!$editing): ?>
                <input class="form-control mb-2" name="tags" placeholder="Tags comma-separated (Array,Stack)">
                <div class="row g-2 mb-2">
                    <div class="col"><textarea class="form-control" name="test_input[]" placeholder="Test Input 1"></textarea></div>
                    <div class="col"><textarea class="form-control" name="test_output[]" placeholder="Expected Output 1"></textarea></div>
                </div>
                <div class="row g-2 mb-2">
                    <div class="col"><textarea class="form-control" name="test_input[]" placeholder="Test Input 2"></textarea></div>
                    <div class="col"><textarea class="form-control" name="test_output[]" placeholder="Expected Output 2"></textarea></div>
                </div>
                <?php endif; ?>
                <button class="btn btn-primary"><?= $editing ? 'Update' : 'Create' ?></button>
            </form>
        </div></div>
    </div>
    <div class="col-md-7">
        <div class="card"><div class="card-body">
            <h5>Problem List</h5>
            <table class="table table-sm"><thead><tr><th>ID</th><th>Title</th><th>Difficulty</th><th>Actions</th></tr></thead><tbody>
            <?php foreach ($problems as $p): ?>
                <tr>
                    <td><?= (int) $p['id'] ?></td><td><?= e($p['title']) ?></td><td><?= e($p['difficulty']) ?></td>
                    <td>
                        <a class="btn btn-sm btn-outline-primary" href="?edit=<?= (int) $p['id'] ?>">Edit</a>
                        <form method="post" class="d-inline">
                            <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
                            <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete problem?')">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody></table>
        </div></div>
    </div>
</div>
<?php renderFooter(); ?>
