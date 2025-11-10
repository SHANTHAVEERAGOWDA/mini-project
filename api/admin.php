<?php
require_once 'db.php';

// Security check: Ensure only admin can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    send_json(['success' => false, 'message' => 'Unauthorized access.'], 403);
}

$action = $_GET['action'] ?? null;

// --- 1. GET DASHBOARD DATA (Loads Departments, Subjects, Logs) ---
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'dashboard_data') {
    try {
        // Fetch logs
        $logs = $pdo->query("SELECT u.name, u.role, l.login_time FROM login_logs l JOIN users u ON l.user_id = u.id ORDER BY l.login_time DESC LIMIT 5")->fetchAll();
        // Fetch teacher IDs
        $ids = $pdo->query("SELECT * FROM teacher_ids ORDER BY id DESC LIMIT 10")->fetchAll();
        // Fetch subjects
        $subjects = $pdo->query("SELECT * FROM subjects ORDER BY department, semester")->fetchAll();
        // Fetch departments
        $depts = $pdo->query("SELECT * FROM departments ORDER BY name ASC")->fetchAll();
        
        send_json([
            'success' => true,
            'logs' => $logs,
            'teacher_ids' => $ids,
            'subjects' => $subjects,
            'departments' => $depts
        ]);
    } catch (PDOException $e) {
        // If this fails, it usually means a table is missing
        send_json(['success' => false, 'message' => 'Database error: ' . $e->getMessage()], 500);
    }
}

// --- 2. ADD DEPARTMENT ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'add_dept') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (empty($data['name'])) send_json(['success' => false, 'message' => 'Name required.'], 400);
    
    try {
        $stmt = $pdo->prepare("INSERT INTO departments (name) VALUES (?)");
        $stmt->execute([strtoupper(trim($data['name']))]);
        send_json(['success' => true, 'message' => 'Department added successfully.']);
    } catch (PDOException $e) {
        send_json(['success' => false, 'message' => 'Department already exists.'], 409);
    }
}

// --- 3. ADD SUBJECT ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'add_subject') {
    $data = json_decode(file_get_contents('php://input'), true);
    // Basic validation
    if (empty($data['name']) || empty($data['code']) || empty($data['department']) || empty($data['semester'])) {
        send_json(['success' => false, 'message' => 'All subject fields are required.'], 400);
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO subjects (name, code, department, semester) VALUES (?, ?, ?, ?)");
        $stmt->execute([trim($data['name']), strtoupper(trim($data['code'])), $data['department'], $data['semester']]);
        send_json(['success' => true, 'message' => 'Subject added successfully.']);
    } catch (PDOException $e) {
         send_json(['success' => false, 'message' => 'Subject code might already exist.'], 409);
    }
}

// --- 4. ASSIGN TEACHER ID ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'assign_id') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (empty($data['code']) || empty($data['assigned_to']) || empty($data['department'])) {
        send_json(['success' => false, 'message' => 'All fields, including Department, are required.'], 400);
    }

    try {
        // Ensure the 'department' column exists in your 'teacher_ids' table!
        $stmt = $pdo->prepare("INSERT INTO teacher_ids (code, assigned_to, department) VALUES (?, ?, ?)");
        $stmt->execute([trim($data['code']), trim($data['assigned_to']), $data['department']]);
        send_json(['success' => true, 'message' => 'Teacher ID assigned for ' . $data['department']]);
    } catch (PDOException $e) {
        send_json(['success' => false, 'message' => 'This Teacher Code already exists.'], 409);
    }
}
?>