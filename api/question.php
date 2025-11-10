<?php
require_once 'db.php';
if ($_SESSION['role'] !== 'teacher') send_json(['success' => false], 403);

$action = $_GET['action'] ?? null;

// --- LIST QUESTIONS for a specific quiz ---
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'list') {
    $stmt = $pdo->prepare("SELECT * FROM questions WHERE quiz_id = ?");
    $stmt->execute([$_GET['quiz_id']]);
    send_json(['success' => true, 'questions' => $stmt->fetchAll()]);
}

// --- ADD QUESTION ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'add') {
    $data = json_decode(file_get_contents('php://input'), true);
    try {
        $stmt = $pdo->prepare("INSERT INTO questions (quiz_id, question_text, option_a, option_b, option_c, option_d, correct_option, marks) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['quiz_id'], $data['question_text'],
            $data['option_a'], $data['option_b'], $data['option_c'], $data['option_d'],
            $data['correct_option'], $data['marks']
        ]);
        send_json(['success' => true, 'message' => 'Question added!']);
    } catch (Exception $e) { send_json(['success' => false, 'message' => 'Failed to add question.'], 500); }
}
?>