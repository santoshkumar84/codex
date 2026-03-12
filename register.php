<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/layout.php';

$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCsrf($_POST['csrf_token'] ?? null)) {
        $error = 'Invalid CSRF token.';
    } else {
        $name = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($name === '' || $email === '' || strlen($password) < 6) {
            $error = 'Please fill all fields. Password must be at least 6 chars.';
        } else {
            try {
                $stmt = getDb()->prepare('INSERT INTO users(full_name, email, password_hash, role) VALUES(:name,:email,:hash, "student")');
                $stmt->execute([
                    'name' => $name,
                    'email' => $email,
                    'hash' => password_hash($password, PASSWORD_BCRYPT),
                ]);
                $success = 'Registration complete. You can login now.';
            } catch (PDOException $e) {
                $error = 'Email already exists.';
            }
        }
    }
}

renderHeader('Register');
?>
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-body">
                <h4>Create Student Account</h4>
                <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
                <?php if ($success): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>
                <form method="post">
                    <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                    <div class="mb-3"><label class="form-label">Full Name</label><input name="full_name" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Password</label><input type="password" name="password" class="form-control" required></div>
                    <button class="btn btn-primary">Register</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php renderFooter(); ?>
