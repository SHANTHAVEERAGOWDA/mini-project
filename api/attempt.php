<?php
require_once 'db.php';

// Prevent PHP warnings from breaking the JSON response
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Ensure we always return JSON
header('Content-Type: application/json');

try {
    // 1. Security Check
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('Session expired. Please login again.');
    }

    $user_id = $_SESSION['user_id'];
    $action = $_GET['action'] ?? null;
    // =========================================
    // === ACTION: START QUIZ (GET) ===
    // =========================================
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'start') {
        $qid = $_GET['quiz_id'] ?? null;
        if (!$qid) throw new Exception('Missing Quiz ID.');

        // Fetch Quiz Details
        $qStmt = $pdo->prepare("SELECT * FROM quizzes WHERE id = ? AND is_published = 1");
        $qStmt->execute([$qid]);
        $quiz = $qStmt->fetch(PDO::FETCH_ASSOC);

        if (!$quiz) {
            throw new Exception('Quiz not found or is not currently active.');
        }

        // Fetch Questions (Security: DO NOT select 'correct_option' here)
        $qtStmt = $pdo->prepare("SELECT id, question_text, option_a, option_b, option_c, option_d, marks FROM questions WHERE quiz_id = ?");
        $qtStmt->execute([$qid]);
        $questions = $qtStmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($questions)) {
            throw new Exception('This quiz has no questions added yet. Please ask your teacher to add some.');
        }

        echo json_encode(['success' => true, 'quiz' => $quiz, 'questions' => $questions]);
        exit;
    }
    // =========================================
    // === ACTION: SUBMIT QUIZ (POST) ===
    // =========================================
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'submit') {
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);

        if (!$data) throw new Exception('Invalid data received.');

        $qid = $data['quiz_id'] ?? null;
        $answers = $data['answers'] ?? [];

        if (!$qid) throw new Exception('Cannot submit: Missing Quiz ID.');

        // Fetch correct answers to grade against
        $stmt = $pdo->prepare("SELECT id, correct_option, marks FROM questions WHERE quiz_id = ?");
        $stmt->execute([$qid]);
        $questions_db = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $total_score = 0;
        $max_marks = 0;

        // Grading Loop
        foreach ($questions_db as $q) {
            $max_marks += $q['marks'];
            // Check if student answer exists and matches correct option
            if (isset($answers[$q['id']]) && $answers[$q['id']] === $q['correct_option']) {
                $total_score += $q['marks'];
            }
        }

        // Save Attempt
        $saveStmt = $pdo->prepare("INSERT INTO attempts (quiz_id, user_id, score, total_marks, answers_json) VALUES (?, ?, ?, ?, ?)");
        $saveStmt->execute([
            $qid, 
            $user_id, 
            $total_score, 
            $max_marks, 
            json_encode($answers)
        ]);

        echo json_encode([
            'success' => true, 
            'score' => $total_score, 
            'total' => $max_marks, 
            'message' => "Quiz Submitted! You scored $total_score / $max_marks."
        ]);
        exit;
    }

    throw new Exception('Invalid action requested.');

} catch (Exception $e) {
    // Catch ANY error and return it as JSON so the frontend can show it
    http_response_code(400); // Bad Request
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    exit;
}
?>