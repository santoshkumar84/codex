<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/layout.php';
requireAuth('student');
$db = getDb();

$id = (int) ($_GET['id'] ?? 0);
$stmt = $db->prepare('SELECT * FROM problems WHERE id=:id');
$stmt->execute(['id' => $id]);
$problem = $stmt->fetch();
if (!$problem) {
    http_response_code(404);
    exit('Problem not found');
}

$tagsStmt = $db->prepare('SELECT t.name FROM tags t JOIN problem_tags pt ON pt.tag_id=t.id WHERE pt.problem_id=:id');
$tagsStmt->execute(['id' => $id]);
$tags = array_column($tagsStmt->fetchAll(), 'name');

renderHeader('Solve Problem');
?>
<div class="row g-4">
    <div class="col-md-5">
        <div class="card shadow-sm"><div class="card-body">
            <h4><?= e($problem['title']) ?></h4>
            <p><span class="badge bg-secondary"><?= e($problem['difficulty']) ?></span></p>
            <p><?= nl2br(e($problem['description'])) ?></p>
            <h6>Input Format</h6><pre><?= e($problem['input_format']) ?></pre>
            <h6>Output Format</h6><pre><?= e($problem['output_format']) ?></pre>
            <h6>Constraints</h6><pre><?= e($problem['constraints_text']) ?></pre>
            <h6>Sample Input</h6><pre><?= e($problem['sample_input']) ?></pre>
            <h6>Sample Output</h6><pre><?= e($problem['sample_output']) ?></pre>
            <div><?php foreach ($tags as $tag): ?><span class="badge bg-info text-dark"><?= e($tag) ?></span> <?php endforeach; ?></div>
        </div></div>
    </div>
    <div class="col-md-7">
        <div class="card shadow-sm"><div class="card-body">
            <form id="submission-form" method="post" action="/api/submit.php">
                <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                <input type="hidden" name="problem_id" value="<?= (int) $problem['id'] ?>">
                <div class="d-flex gap-2 mb-2">
                    <select id="language" name="language" class="form-select w-25">
                        <option value="c">C</option><option value="cpp" selected>C++</option><option value="java">Java</option><option value="python">Python</option><option value="php">PHP</option>
                    </select>
                    <button class="btn btn-primary">Submit</button>
                </div>
                <textarea id="code-editor" name="code">#include <bits/stdc++.h>
using namespace std;
int main(){
    return 0;
}</textarea>
            </form>
        </div></div>
    </div>
</div>
<?php renderFooter(); ?>
