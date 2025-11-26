<?php
require_once 'db.php';

ini_set('display_errors', 0);
error_reporting(E_ALL);
header('Content-Type: application/json');

try {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (!isset($_SESSION['user_id'])) throw new Exception('Session expired.');

    $user_id = $_SESSION['user_id'];
    $action = $_GET['action'] ?? null;

    // --- START QUIZ ---
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'start') {
        $qid = $_GET['quiz_id'] ?? null;
        if (!$qid) throw new Exception('Missing Quiz ID.');

        $qStmt = $pdo->prepare("SELECT * FROM quizzes WHERE id = ? AND is_published = 1");
        $qStmt->execute([$qid]);
        $quiz = $qStmt->fetch(PDO::FETCH_ASSOC);

        if (!$quiz) throw new Exception('Quiz not found.');

        // 1. CHECK DEADLINE
        if (!empty($quiz['end_time'])) {
            $deadline = strtotime($quiz['end_time']);
            $now = time();
            if ($now > $deadline) {
                throw new Exception('⛔ This quiz expired on ' . date('d M Y, h:i A', $deadline));
            }
        }

        // Fetch Questions
        $qtStmt = $pdo->prepare("SELECT id, question_text, option_a, option_b, option_c, option_d, marks, type FROM questions WHERE quiz_id = ?");
        $qtStmt->execute([$qid]);
        $questions = $qtStmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($questions)) throw new Exception('No questions in this quiz.');

        echo json_encode(['success' => true, 'quiz' => $quiz, 'questions' => $questions]);
        exit;
    }

    // --- SUBMIT QUIZ ---
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'submit') {
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
        $qid = $data['quiz_id'] ?? null;
        $answers = $data['answers'] ?? [];

        // Fetch Correct Answers
        $stmt = $pdo->prepare("SELECT id, correct_option, marks, type FROM questions WHERE quiz_id = ?");
        $stmt->execute([$qid]);
        $questions_db = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $total_score = 0;
        $max_marks = 0;

        foreach ($questions_db as $q) {
            $max_marks += $q['marks'];
            $user_ans = $answers[$q['id']] ?? null;
            $correct_ans = $q['correct_option'];

            // 1. MCQ
            if ($q['type'] === 'MCQ') {
                if ($user_ans === $correct_ans) $total_score += $q['marks'];
            }
            // 2. MSQ
            elseif ($q['type'] === 'MSQ') {
                $correct_arr = json_decode($correct_ans, true) ?? [];
                $user_arr = is_array($user_ans) ? $user_ans : [];
                sort($correct_arr); sort($user_arr);
                if ($correct_arr === $user_arr) $total_score += $q['marks'];
            }
            // 3. Descriptive (Smart Match)
            elseif ($q['type'] === 'DESCRIPTIVE') {
                if (!empty($user_ans) && stripos($user_ans, $correct_ans) !== false) {
                    $total_score += $q['marks'];
                }
            }
        }

        $saveStmt = $pdo->prepare("INSERT INTO attempts (quiz_id, user_id, score, total_marks, answers_json) VALUES (?, ?, ?, ?, ?)");
        $saveStmt->execute([$qid, $user_id, $total_score, $max_marks, json_encode($answers)]);

        echo json_encode(['success' => true, 'score' => $total_score, 'total' => $max_marks]);
        exit;
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>