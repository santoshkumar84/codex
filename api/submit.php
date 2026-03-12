<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/executor.php';

requireAuth('student');
$db = getDb();
$user = currentUser();

if (!validateCsrf($_POST['csrf_token'] ?? null)) {
    setFlash('error', 'Invalid CSRF token.');
    header('Location: /student/problems.php');
    exit;
}

$problemId = (int) ($_POST['problem_id'] ?? 0);
$language = $_POST['language'] ?? 'cpp';
$code = trim($_POST['code'] ?? '');

if ($problemId < 1 || $code === '' || strlen($code) > 100000) {
    setFlash('error', 'Invalid submission payload.');
    header('Location: /student/problems.php');
    exit;
}

$tcStmt = $db->prepare('SELECT input_data, expected_output FROM test_cases WHERE problem_id=:id ORDER BY id ASC');
$tcStmt->execute(['id' => $problemId]);
$testCases = $tcStmt->fetchAll();
if (!$testCases) {
    setFlash('error', 'No test cases attached to this problem.');
    header('Location: /student/problems.php');
    exit;
}

$judgement = judgeSubmission($language, $code, $testCases);

$insert = $db->prepare('INSERT INTO submissions(user_id, problem_id, language, source_code, result, result_details) VALUES(:uid,:pid,:lang,:code,:result,:details)');
$insert->execute([
    'uid' => $user['id'],
    'pid' => $problemId,
    'lang' => $language,
    'code' => $code,
    'result' => $judgement['status'],
    'details' => $judgement['details'],
]);

setFlash($judgement['status'] === 'Accepted' ? 'success' : 'error', 'Result: ' . $judgement['status'] . ' - ' . $judgement['details']);
header('Location: /student/history.php');
exit;
