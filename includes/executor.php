<?php

declare(strict_types=1);

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/config.php';

function removeDirectory(string $dir): void
{
    if (!is_dir($dir)) {
        return;
    }

    $items = scandir($dir);
    if (!is_array($items)) {
        return;
    }

    foreach ($items as $item) {
        if ($item === '.' || $item === '..') {
            continue;
        }
        $path = $dir . DIRECTORY_SEPARATOR . $item;
        if (is_dir($path)) {
            removeDirectory($path);
        } else {
            @unlink($path);
        }
    }
    @rmdir($dir);
}

function runCommandWithInput(string $command, string $input, int $timeout): array
{
    $descriptors = [
        0 => ['pipe', 'r'],
        1 => ['pipe', 'w'],
        2 => ['pipe', 'w'],
    ];

    $process = proc_open($command, $descriptors, $pipes);
    if (!is_resource($process)) {
        return ['ok' => false, 'stdout' => '', 'stderr' => 'Failed to start process', 'timeout' => false];
    }

    fwrite($pipes[0], $input);
    fclose($pipes[0]);

    stream_set_blocking($pipes[1], false);
    stream_set_blocking($pipes[2], false);

    $stdout = '';
    $stderr = '';
    $start = microtime(true);
    $timedOut = false;

    while (true) {
        $status = proc_get_status($process);
        $stdout .= stream_get_contents($pipes[1]);
        $stderr .= stream_get_contents($pipes[2]);

        if (strlen($stdout) + strlen($stderr) > EXECUTION_MAX_OUTPUT_BYTES) {
            $timedOut = true;
            proc_terminate($process, 9);
            $stderr .= "\nOutput limit exceeded.";
            break;
        }

        if (!$status['running']) {
            break;
        }

        if ((microtime(true) - $start) > $timeout) {
            $timedOut = true;
            proc_terminate($process, 9);
            break;
        }

        usleep(10000);
    }

    $stdout .= stream_get_contents($pipes[1]);
    $stderr .= stream_get_contents($pipes[2]);

    fclose($pipes[1]);
    fclose($pipes[2]);

    $exitCode = proc_close($process);

    return [
        'ok' => !$timedOut && $exitCode === 0,
        'stdout' => $stdout,
        'stderr' => $stderr,
        'timeout' => $timedOut,
    ];
}

function judgeSubmission(string $language, string $code, array $testCases): array
{
    $config = languageConfig($language);
    if (!$config) {
        return ['status' => 'Runtime Error', 'details' => 'Unsupported language'];
    }

    $runDir = EXECUTION_ROOT . '/' . uniqid('sub_', true);
    mkdir($runDir, 0775, true);

    try {
        $sourceFile = $runDir . '/Main.' . $config['ext'];
        file_put_contents($sourceFile, $code);
        $binaryFile = $runDir . '/main_exec';

        if ($config['compile']) {
            $compileCmd = str_replace(['{src}', '{bin}', '{dir}'], [escapeshellarg($sourceFile), escapeshellarg($binaryFile), escapeshellarg($runDir)], $config['compile']);
            $compiled = runCommandWithInput($compileCmd, '', EXECUTION_TIMEOUT);
            if (!$compiled['ok']) {
                return ['status' => 'Runtime Error', 'details' => 'Compile error: ' . trim($compiled['stderr'] . $compiled['stdout'])];
            }
        }

        foreach ($testCases as $index => $case) {
            $runCmd = str_replace(['{src}', '{bin}', '{dir}'], [escapeshellarg($sourceFile), escapeshellarg($binaryFile), escapeshellarg($runDir)], $config['run']);
            $result = runCommandWithInput($runCmd, $case['input_data'], EXECUTION_TIMEOUT);

            if ($result['timeout']) {
                return ['status' => 'Time Limit Exceeded', 'details' => 'Failed on case ' . ($index + 1)];
            }

            if (!$result['ok']) {
                return ['status' => 'Runtime Error', 'details' => trim($result['stderr']) ?: 'Runtime failure'];
            }

            if (normalizeOutput($result['stdout']) !== normalizeOutput($case['expected_output'])) {
                return ['status' => 'Wrong Answer', 'details' => 'Failed on case ' . ($index + 1)];
            }
        }

        return ['status' => 'Accepted', 'details' => 'All test cases passed'];
    } finally {
        removeDirectory($runDir);
    }
}
