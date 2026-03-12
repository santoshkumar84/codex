<?php

declare(strict_types=1);

require_once __DIR__ . '/db.php';

function currentUser(): ?array
{
    if (empty($_SESSION['user_id'])) {
        return null;
    }

    $stmt = getDb()->prepare('SELECT id, full_name, email, role FROM users WHERE id = :id');
    $stmt->execute(['id' => $_SESSION['user_id']]);
    $user = $stmt->fetch();

    return $user ?: null;
}

function loginUser(string $email, string $password, string $expectedRole = 'student'): bool
{
    $stmt = getDb()->prepare('SELECT id, password_hash, role FROM users WHERE email = :email LIMIT 1');
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if (!$user || $user['role'] !== $expectedRole) {
        return false;
    }

    if (!password_verify($password, $user['password_hash'])) {
        return false;
    }

    session_regenerate_id(true);
    $_SESSION['user_id'] = (int) $user['id'];
    return true;
}

function logoutUser(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'] ?? '', (bool) $params['secure'], (bool) $params['httponly']);
    }
    session_destroy();
}

function requireAuth(string $role): void
{
    $user = currentUser();
    if (!$user || $user['role'] !== $role) {
        header('Location: /login.php');
        exit;
    }
}
