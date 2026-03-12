<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/layout.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCsrf($_POST['csrf_token'] ?? null)) {
        $error = 'Invalid CSRF token.';
    } else {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'student';

        if (loginUser($email, $password, $role)) {
            header('Location: /' . ($role === 'admin' ? 'admin/dashboard.php' : 'student/dashboard.php'));
            exit;
        }
        $error = 'Invalid credentials or role.';
    }
}

renderHeader('Login');
?>
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-body">
                <h4>Login</h4>
                <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
                <form method="post">
                    <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                    <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Password</label><input type="password" name="password" class="form-control" required></div>
                    <div class="mb-3">
                        <label class="form-label">Login As</label>
                        <select name="role" class="form-select"><option value="student">Student</option><option value="admin">Admin</option></select>
                    </div>
                    <button class="btn btn-primary">Login</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php renderFooter(); ?>
