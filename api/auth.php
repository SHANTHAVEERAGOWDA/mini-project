<?php
require_once 'db.php';

// Prevent HTML errors from breaking JSON
ini_set('display_errors', 0);
error_reporting(E_ALL);
header('Content-Type: application/json');

$action = $_GET['action'] ?? null;

try {
    // --- LOGIN ---
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'login') {
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
        
        if (empty($data['email']) || empty($data['password'])) {
            throw new Exception('Email and password required.');
        }

        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$data['email']]);
        $user = $stmt->fetch();

        if ($user && password_verify($data['password'], $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['dept'] = $user['department'];
            $_SESSION['sem'] = $user['semester'];
            
            $pdo->prepare("INSERT INTO login_logs (user_id) VALUES (?)")->execute([$user['id']]);
            echo json_encode(['success' => true, 'role' => $user['role'], 'redirect' => 'dashboard.php']);
        } else {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Invalid credentials.']);
        }
        exit;
    }

    // --- REGISTER (Updated with Teacher Dept Fix) ---
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'register') {
        $data = json_decode(file_get_contents('php://input'), true);

        $dept = null;
        $sem = null;
        $tid = null;
        
        // 1. HANDLE TEACHER
        if ($data['role'] === 'teacher') {
            if (empty($data['teacher_code'])) throw new Exception('Teacher Code required.');
            
            $stmt = $pdo->prepare("SELECT id, department FROM teacher_ids WHERE code = ? AND is_used = 0");
            $stmt->execute([$data['teacher_code']]);
            $teacherInfo = $stmt->fetch();
            
            if (!$teacherInfo) throw new Exception('Invalid or used Teacher Code.');
            
            $tid = $teacherInfo['id'];
            $dept = $teacherInfo['department']; // <--- THIS WAS THE FIX
        } 
        // 2. HANDLE STUDENT
        else {
            $dept = !empty($data['department']) ? $data['department'] : null;
            $sem = !empty($data['semester']) ? $data['semester'] : null;
        }

        if (empty($data['name']) || empty($data['email']) || empty($data['password'])) {
            throw new Exception('Required fields missing.');
        }

        try {
            $pdo->beginTransaction();
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, department, semester) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $data['name'],
                $data['email'],
                password_hash($data['password'], PASSWORD_DEFAULT),
                $data['role'],
                $dept,
                $sem
            ]);

            if ($tid) {
                $pdo->prepare("UPDATE teacher_ids SET is_used = 1 WHERE id = ?")->execute([$tid]);
            }

            $pdo->commit();
            echo json_encode(['success' => true, 'message' => 'Registered successfully! Please login.']);
        } catch (PDOException $e) {
            $pdo->rollBack();
            if ($e->getCode() == 23000) {
                http_response_code(409);
                echo json_encode(['success' => false, 'message' => 'Email already registered.']);
            } else {
                throw $e;
            }
        }
        exit;
    }

    // --- FORGOT PASSWORD: REQUEST CODE ---
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'forgot_request') {
        $data = json_decode(file_get_contents('php://input'), true);
        $email = $data['email'] ?? '';

        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            $code = rand(100000, 999999);
            $pdo->prepare("UPDATE users SET reset_token = ? WHERE id = ?")->execute([$code, $user['id']]);
            echo json_encode(['success' => true, 'message' => 'Code sent!', 'debug_code' => $code]);
        } else {
            echo json_encode(['success' => false, 'message' => 'If email exists, code sent.']);
        }
        exit;
    }

    // --- FORGOT PASSWORD: RESET NOW ---
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'forgot_reset') {
        $data = json_decode(file_get_contents('php://input'), true);
        $email = $data['email'];
        $code = $data['code'];
        $new_pass = $data['new_password'];

        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND reset_token = ?");
        $stmt->execute([$email, $code]);
        $user = $stmt->fetch();

        if ($user) {
            $new_hash = password_hash($new_pass, PASSWORD_DEFAULT);
            $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL WHERE id = ?")->execute([$new_hash, $user['id']]);
            echo json_encode(['success' => true, 'message' => 'Password Reset Successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid Email or Code.']);
        }
        exit;
    }

    // --- LOGOUT ---
    if ($action === 'logout') {
        session_destroy();
        header("Location: ../index.html");
        exit;
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>