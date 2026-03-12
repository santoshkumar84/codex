<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';

$user = currentUser();
if (!$user) {
    jsonResponse(['error' => 'Unauthorized'], 401);
}

$db = getDb();
if ($user['role'] === 'admin') {
    $stmt = $db->query('SELECT id,user_id,problem_id,language,result,created_at FROM submissions ORDER BY id DESC LIMIT 200');
} else {
    $stmt = $db->prepare('SELECT id,problem_id,language,result,created_at FROM submissions WHERE user_id=:uid ORDER BY id DESC');
    $stmt->execute(['uid' => $user['id']]);
}

jsonResponse(['submissions' => $stmt->fetchAll()]);
