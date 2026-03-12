<?php

declare(strict_types=1);

$https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => $https,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

const DB_HOST = '127.0.0.1';
const DB_NAME = 'coding_portal';
const DB_USER = 'root';
const DB_PASS = '';

const EXECUTION_ROOT = __DIR__ . '/../submissions/runtime';
const EXECUTION_TIMEOUT = 2;
const EXECUTION_MAX_OUTPUT_BYTES = 200000;

if (!is_dir(EXECUTION_ROOT)) {
    mkdir(EXECUTION_ROOT, 0775, true);
}
