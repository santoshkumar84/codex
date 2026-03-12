<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/layout.php';
requireAuth('admin');
$db = getDb();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCsrf($_POST['csrf_token'] ?? null)) {
        setFlash('error', 'Invalid CSRF token.');
    } else {
        $db->prepare("DELETE FROM users WHERE id=:id AND role='student'")->execute(['id' => (int) ($_POST['id'] ?? 0)]);
        setFlash('success', 'Student removed.');
    }
    header('Location: /admin/users.php');
    exit;
}

$users = $db->query("SELECT id, full_name, email, created_at FROM users WHERE role='student' ORDER BY id DESC")->fetchAll();
renderHeader('Manage Users');
?>
<div class="card"><div class="card-body">
    <h5>Students</h5>
    <table class="table"><thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Joined</th><th></th></tr></thead><tbody>
        <?php foreach ($users as $u): ?>
            <tr><td><?= (int) $u['id'] ?></td><td><?= e($u['full_name']) ?></td><td><?= e($u['email']) ?></td><td><?= e($u['created_at']) ?></td>
            <td>
                <form method="post" class="d-inline">
                    <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                    <input type="hidden" name="id" value="<?= (int) $u['id'] ?>">
                    <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete student?')">Delete</button>
                </form>
            </td></tr>
        <?php endforeach; ?>
    </tbody></table>
</div></div>
<?php renderFooter(); ?>
