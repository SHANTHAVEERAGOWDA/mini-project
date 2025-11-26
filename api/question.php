<?php
require_once 'db.php';

// 1. Prevent HTML errors from breaking JSON
ini_set('display_errors', 0);
error_reporting(E_ALL);
header('Content-Type: application/json');

if ($_SESSION['role'] !== 'teacher') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$action = $_GET['action'] ?? null;

try {
    // --- LIST QUESTIONS ---
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'list') {
        $stmt = $pdo->prepare("SELECT * FROM questions WHERE quiz_id = ?");
        $stmt->execute([$_GET['quiz_id']]);
        echo json_encode(['success' => true, 'questions' => $stmt->fetchAll()]);
        exit;
    }

    // --- ADD QUESTION ---
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'add') {
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);

        if (!$data) throw new Exception("Invalid JSON received");
        
        // 1. Get Type (Default to MCQ if missing)
        $type = $data['type'] ?? 'MCQ';
        $correct = $data['correct_option'] ?? '';

        // 2. Format Correct Option based on Type
        if ($type === 'MSQ') {
            // Sort and encode array to JSON
            if (is_array($correct)) {
                sort($correct); 
                $correct = json_encode($correct);
            } else {
                throw new Exception("MSQ requires at least one correct option selected.");
            }
        } elseif ($type === 'DESCRIPTIVE') {
            // Descriptive just needs text, ensure it's not empty
            if (empty(trim($correct))) throw new Exception("Descriptive answer cannot be empty.");
            // Clear distractors
            $data['option_a'] = $data['option_b'] = $data['option_c'] = $data['option_d'] = '';
        }

        // 3. Insert into DB
        $stmt = $pdo->prepare("INSERT INTO questions (quiz_id, question_text, option_a, option_b, option_c, option_d, correct_option, marks, type) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['quiz_id'], 
            $data['question_text'],
            $data['option_a'] ?? '', 
            $data['option_b'] ?? '', 
            $data['option_c'] ?? '', 
            $data['option_d'] ?? '', 
            $correct, 
            $data['marks'] ?? 1,
            $type
        ]);
        echo json_encode(['success' => true, 'message' => 'Question added!']);
        exit;
    }

    // --- DELETE QUESTION ---
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'delete') {
        $data = json_decode(file_get_contents('php://input'), true);
        $pdo->prepare("DELETE FROM questions WHERE id = ?")->execute([$data['question_id']]);
        echo json_encode(['success' => true]);
        exit;
    }

} catch (Exception $e) { 
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server Error: ' . $e->getMessage()]);
    exit;
}
?>