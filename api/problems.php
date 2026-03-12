<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';

$user = currentUser();
if (!$user) {
    jsonResponse(['error' => 'Unauthorized'], 401);
}

$stmt = getDb()->query('SELECT id,title,difficulty FROM problems ORDER BY id DESC');
jsonResponse(['problems' => $stmt->fetchAll()]);
