<?php

declare(strict_types=1);

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function jsonResponse(array $payload, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($payload);
    exit;
}

function normalizeOutput(string $output): string
{
    return trim(str_replace(["\r\n", "\r"], "\n", $output));
}

function languageConfig(string $language): ?array
{
    return [
        'c' => ['ext' => 'c', 'compile' => 'gcc {src} -O2 -o {bin} 2>&1', 'run' => '{bin}'],
        'cpp' => ['ext' => 'cpp', 'compile' => 'g++ {src} -O2 -std=c++17 -o {bin} 2>&1', 'run' => '{bin}'],
        'java' => ['ext' => 'java', 'compile' => 'javac {src} 2>&1', 'run' => 'java -cp {dir} Main'],
        'python' => ['ext' => 'py', 'compile' => null, 'run' => 'python3 {src}'],
        'php' => ['ext' => 'php', 'compile' => null, 'run' => 'php {src}'],
    ][$language] ?? null;
}

function csrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCsrf(?string $token): bool
{
    return is_string($token) && isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function setFlash(string $key, string $message): void
{
    $_SESSION['flash'][$key] = $message;
}

function getFlash(string $key): ?string
{
    $value = $_SESSION['flash'][$key] ?? null;
    unset($_SESSION['flash'][$key]);
    return $value;
}
