<?php
require_once 'db.php';

// Prevent HTML errors
ini_set('display_errors', 0);
error_reporting(E_ALL);
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$role = $_SESSION['role'];
$uid = $_SESSION['user_id'];
$action = $_GET['action'] ?? null;

try {
    // --- LIST QUIZZES ---
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'list') {
        if ($role === 'teacher') {
            $stmt = $pdo->prepare("SELECT q.*, s.name as subject_name, (SELECT COUNT(*) FROM questions WHERE quiz_id = q.id) as q_count FROM quizzes q JOIN subjects s ON q.subject_id = s.id WHERE q.created_by = ? ORDER BY q.created_at DESC");
            $stmt->execute([$uid]);
            $quizzes = $stmt->fetchAll();
            $subjects = $pdo->query("SELECT * FROM subjects ORDER BY name")->fetchAll();
            echo json_encode(['success' => true, 'quizzes' => $quizzes, 'subjects' => $subjects]);
        } elseif ($role === 'student') {
            $stmt = $pdo->prepare("SELECT q.*, s.name as subject_name, u.name as author FROM quizzes q JOIN subjects s ON q.subject_id = s.id JOIN users u ON q.created_by = u.id WHERE s.department = ? AND s.semester = ? AND q.is_published = 1 ORDER BY q.created_at DESC");
            $stmt->execute([$_SESSION['dept'], $_SESSION['sem']]);
            echo json_encode(['success' => true, 'quizzes' => $stmt->fetchAll()]);
        }
        exit;
    }

    // --- CREATE QUIZ (Teacher) ---
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'create' && $role === 'teacher') {
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Handle end_time: Remove 'T' if present (e.g. 2023-10-25T14:30 -> 2023-10-25 14:30)
        $endTime = !empty($data['end_time']) ? str_replace('T', ' ', $data['end_time']) : null;

        $stmt = $pdo->prepare("INSERT INTO quizzes (title, description, subject_id, duration_minutes, end_time, created_by) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['title'], 
            $data['description'], 
            $data['subject_id'], 
            $data['duration'], 
            $endTime,
            $uid
        ]);
        
        echo json_encode(['success' => true, 'message' => 'Quiz created successfully!']);
        exit;
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server Error: ' . $e->getMessage()]);
}
?>