<?php
require_once 'db.php';

$action = $_GET['action'] ?? null;

// --- LOGIN ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'login') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (empty($data['email']) || empty($data['password'])) {
        send_json(['success' => false, 'message' => 'Email and password required.'], 400);
    }

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$data['email']]);
    $user = $stmt->fetch();

    if ($user && password_verify($data['password'], $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['name'] = $user['name'];
        // Save academic details for students
        $_SESSION['dept'] = $user['department'];
        $_SESSION['sem'] = $user['semester'];
        
        // Log login
        $pdo->prepare("INSERT INTO login_logs (user_id) VALUES (?)")->execute([$user['id']]);
        send_json(['success' => true, 'role' => $user['role'], 'redirect' => 'dashboard.php']);
    } else {
        send_json(['success' => false, 'message' => 'Invalid credentials.'], 401);
    }
}

// --- REGISTER ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'register') {
    $data = json_decode(file_get_contents('php://input'), true);

     // Validate Teacher Code & Get Linked Department
    $linkedDept = null;
    if ($data['role'] === 'teacher') {
        if (empty($data['teacher_code'])) send_json(['success' => false, 'message' => 'Teacher Code required.'], 400);
        $stmt = $pdo->prepare("SELECT id, department FROM teacher_ids WHERE code = ? AND is_used = 0");
        $stmt->execute([$data['teacher_code']]);
        $teacherInfo = $stmt->fetch();
        if (!$teacherInfo) send_json(['success' => false, 'message' => 'Invalid or used Teacher Code.'], 400);
        $tid = $teacherInfo['id'];
        $linkedDept = $teacherInfo['department'];
    }
    // Basic validation
    if (empty($data['name']) || empty($data['email']) || empty($data['password'])) {
        send_json(['success' => false, 'message' => 'Required fields missing.'], 400);
    }

    try {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, department, semester) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['name'],
            $data['email'],
            password_hash($data['password'], PASSWORD_DEFAULT),
            $data['role'],
            !empty($data['department']) ? $data['department'] : null,
            !empty($data['semester']) ? $data['semester'] : null
        ]);

        // Mark teacher code as used
        if (isset($tid)) {
            $pdo->prepare("UPDATE teacher_ids SET is_used = 1 WHERE id = ?")->execute([$tid]);
        }

        $pdo->commit();
        send_json(['success' => true, 'message' => 'Registered successfully! Please login.']);
    } catch (PDOException $e) {
        $pdo->rollBack();
        if ($e->getCode() == 23000) {
             send_json(['success' => false, 'message' => 'Email already registered.'], 409);
        }
        send_json(['success' => false, 'message' => 'Registration failed.'], 500);
    }
}

// --- LOGOUT ---
if ($action === 'logout') {
    session_destroy();
    header("Location: ../index.html");
    exit;
}
?>