<?php
require_once 'db.php';

if (!isset($_SESSION['user_id'])) send_json(['success' => false], 403);
$role = $_SESSION['role'];
$uid = $_SESSION['user_id'];
$action = $_GET['action'] ?? null;

// --- LIST QUIZZES ---
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'list') {
    if ($role === 'teacher') {
        // Teachers see their own quizzes
        $stmt = $pdo->prepare("SELECT q.*, s.name as subject_name, (SELECT COUNT(*) FROM questions WHERE quiz_id = q.id) as q_count FROM quizzes q JOIN subjects s ON q.subject_id = s.id WHERE q.created_by = ? ORDER BY q.created_at DESC");
        $stmt->execute([$uid]);
        $quizzes = $stmt->fetchAll();
        // Also fetch available subjects for dropdown
        $subjects = $pdo->query("SELECT * FROM subjects ORDER BY name")->fetchAll();
        send_json(['success' => true, 'quizzes' => $quizzes, 'subjects' => $subjects]);
    } elseif ($role === 'student') {
        // Students see quizzes matching their Dept & Sem
        $stmt = $pdo->prepare("SELECT q.*, s.name as subject_name, u.name as author FROM quizzes q JOIN subjects s ON q.subject_id = s.id JOIN users u ON q.created_by = u.id WHERE s.department = ? AND s.semester = ? AND q.is_published = 1 ORDER BY q.created_at DESC");
        $stmt->execute([$_SESSION['dept'], $_SESSION['sem']]);
        send_json(['success' => true, 'quizzes' => $stmt->fetchAll()]);
    }
}

// --- CREATE QUIZ (Teacher) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'create' && $role === 'teacher') {
    $data = json_decode(file_get_contents('php://input'), true);
    try {
        $pdo->prepare("INSERT INTO quizzes (title, description, subject_id, duration_minutes, created_by) VALUES (?, ?, ?, ?, ?)")
            ->execute([$data['title'], $data['description'], $data['subject_id'], $data['duration'], $uid]);
        send_json(['success' => true, 'message' => 'Quiz created!']);
    } catch (Exception $e) { send_json(['success' => false, 'message' => 'Error creating quiz.'], 500); }
}
?>