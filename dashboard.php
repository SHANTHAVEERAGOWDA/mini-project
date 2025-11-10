<?php
require_once 'api/db.php';
if (!isset($_SESSION['user_id'])) { header("Location: index.html"); exit; }
$role = $_SESSION['role'];
$accents = ['student' => 'from-blue-600 to-cyan-500', 'teacher' => 'from-purple-600 to-pink-500', 'admin' => 'from-orange-500 to-red-500'];
?>
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- CHART.JS LIBRARY -->
    <script> tailwind.config = { darkMode: 'class' } </script>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="h-full bg-gray-50 dark:bg-gray-900 transition-colors duration-500">
    <div id="toast-container"></div>

    <!-- ANALYTICS/LEADERBOARD MODAL (Shared) -->
    <div id="analytics-modal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
        <div class="bg-white dark:bg-gray-800 w-full max-w-4xl rounded-2xl p-6 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between mb-6">
                <h3 id="analytics-title" class="text-2xl font-bold dark:text-white">Analytics</h3>
                <button onclick="closeAnalytics()" class="text-gray-500 hover:text-gray-700"><i class="fas fa-times fa-lg"></i></button>
            </div>
            <div id="analytics-content" class="space-y-6"></div>
        </div>
    </div>

    <!-- QUIZ TAKER OVERLAY -->
    <div id="quiz-taker" class="fixed inset-0 bg-white dark:bg-gray-900 z-50 hidden overflow-y-auto">
         <div class="max-w-3xl mx-auto py-8 px-4">
            <div class="flex justify-between items-center mb-8 sticky top-0 bg-white dark:bg-gray-900 py-4 border-b dark:border-gray-800">
                <h2 id="qt-title" class="text-2xl font-bold dark:text-white">Quiz Title</h2>
                <div class="text-xl font-mono font-bold text-red-600" id="qt-timer">00:00</div>
            </div>
            <form id="qt-form" onsubmit="submitQuiz(event)">
                <input type="hidden" name="quiz_id" id="qt-quiz-id">
                <div id="qt-questions" class="space-y-8"></div>
                <button type="submit" class="w-full py-4 mt-8 bg-green-600 hover:bg-green-700 text-white rounded-xl font-bold text-lg shadow-lg">Submit Quiz</button>
            </form>
        </div>
    </div>

    <!-- QUESTION MANAGER MODAL -->
    <div id="question-modal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
        <div class="bg-white dark:bg-gray-800 w-full max-w-2xl rounded-2xl p-6 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between mb-4">
                <h3 class="text-xl font-bold dark:text-white">Manage Questions</h3>
                <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700"><i class="fas fa-times fa-lg"></i></button>
            </div>
            <form onsubmit="handleAddQuestion(event)" class="bg-gray-50 dark:bg-gray-900 p-4 rounded-xl mb-6 space-y-3">
                <input type="hidden" name="quiz_id" id="modal-quiz-id">
                <textarea name="question_text" required placeholder="Question Text" class="w-full p-2 rounded border dark:bg-gray-800 dark:border-gray-700 dark:text-white"></textarea>
                <div class="grid grid-cols-2 gap-2">
                    <input type="text" name="option_a" required placeholder="Option A" class="p-2 rounded border dark:bg-gray-800 dark:border-gray-700 dark:text-white">
                    <input type="text" name="option_b" required placeholder="Option B" class="p-2 rounded border dark:bg-gray-800 dark:border-gray-700 dark:text-white">
                    <input type="text" name="option_c" required placeholder="Option C" class="p-2 rounded border dark:bg-gray-800 dark:border-gray-700 dark:text-white">
                    <input type="text" name="option_d" required placeholder="Option D" class="p-2 rounded border dark:bg-gray-800 dark:border-gray-700 dark:text-white">
                </div>
                <div class="flex gap-4">
                    <select name="correct_option" required class="p-2 rounded border dark:bg-gray-800 dark:border-gray-700 dark:text-white flex-1">
                        <option value="">Select Correct Option</option><option value="a">A</option><option value="b">B</option><option value="c">C</option><option value="d">D</option>
                    </select>
                    <input type="number" name="marks" value="1" min="1" class="w-20 p-2 rounded border dark:bg-gray-800 dark:border-gray-700 dark:text-white" placeholder="Marks">
                </div>
                <button class="w-full py-2 bg-green-600 text-white rounded-lg font-semibold">Add Question</button>
            </form>
            <div id="modal-questions-list" class="space-y-2"></div>
        </div>
    </div>

    <!-- MAIN LAYOUT -->
    <div id="main-dashboard">
        <header class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700 sticky top-0 z-30">
             <div class="max-w-7xl mx-auto px-4 h-16 flex items-center justify-between">
                <h1 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <span class="w-8 h-8 rounded-lg bg-gradient-to-tr <?php echo $accents[$role]; ?>"></span>
                    QuizPortal | <?php echo ucfirst($role); ?>
                    <?php if($role=='student') echo "<span class='text-sm ml-2 opacity-60'>(" . ($_SESSION['dept'] ?? 'N/A') . " Sem-" . ($_SESSION['sem'] ?? 'N/A') . ")</span>"; ?>
                     <?php if($role=='teacher') echo "<span class='text-sm ml-2 opacity-60'>(" . ($_SESSION['dept'] ?? 'N/A') . " Dept)</span>"; ?>
                </h1>
                <div class="flex items-center gap-4">
                    <button onclick="toggleDarkMode()" class="text-gray-500"><i class="fas fa-adjust"></i></button>
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-200"><?php echo htmlspecialchars($_SESSION['name']); ?></span>
                    <a href="api/auth.php?action=logout" class="text-red-500 ml-2"><i class="fas fa-sign-out-alt"></i></a>
                </div>
            </div>
        </header>

        <main class="max-w-7xl mx-auto px-4 py-8">
            <!-- === ADMIN VIEW === -->
            <?php if ($role === 'admin'): ?>
            <div class="grid lg:grid-cols-3 gap-8">
                 <!-- Assign Teacher ID -->
                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border dark:border-gray-700 h-fit">
                    <h3 class="font-bold dark:text-white mb-4">Assign Teacher ID</h3>
                    <form onsubmit="handlePost(event, 'api/admin.php?action=assign_id', loadAdminData)" class="space-y-4">
                        <input type="text" name="assigned_to" required placeholder="Teacher Name" class="w-full px-4 py-2 rounded-lg border dark:bg-gray-900 dark:border-gray-700 dark:text-white">
                        <select name="department" id="admin-dept-select" required class="w-full px-4 py-2 rounded-lg border dark:bg-gray-900 dark:border-gray-700 dark:text-white">
                            <option value="">Loading Depts...</option>
                        </select>
                        <input type="text" name="code" required placeholder="Unique Code (e.g. CSE-T1)" class="w-full px-4 py-2 rounded-lg border dark:bg-gray-900 dark:border-gray-700 dark:text-white">
                        <button class="w-full py-2 bg-orange-600 text-white rounded-lg">Assign ID</button>
                    </form>
                    <div class="mt-6">
                        <h4 class="font-semibold text-sm dark:text-gray-300 mb-2">Recent IDs</h4>
                         <ul id="admin-ids-list" class="text-sm space-y-2 max-h-40 overflow-y-auto"></ul>
                    </div>
                </div>
                <!-- Dept & Subject Managers -->
                 <div class="lg:col-span-2 space-y-8">
                    <div class="grid md:grid-cols-2 gap-8">
                         <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border dark:border-gray-700">
                            <h3 class="font-bold dark:text-white mb-4">Add Department</h3>
                            <form onsubmit="handlePost(event, 'api/admin.php?action=add_dept', loadAdminData)" class="flex gap-2">
                                <input type="text" name="name" required placeholder="Name (e.g. CSE)" class="flex-1 px-4 py-2 rounded-lg border dark:bg-gray-900 dark:border-gray-700 dark:text-white">
                                <button class="px-4 py-2 bg-blue-600 text-white rounded-lg">Add</button>
                            </form>
                        </div>
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border dark:border-gray-700">
                            <h3 class="font-bold dark:text-white mb-4">Add Subject</h3>
                             <form onsubmit="handlePost(event, 'api/admin.php?action=add_subject', loadAdminData)" class="space-y-3">
                                <div class="flex gap-2">
                                     <select name="department" id="subject-dept-select" required class="flex-1 px-2 py-2 rounded-lg border dark:bg-gray-900 dark:border-gray-700 dark:text-white"><option value="">Dept</option></select>
                                     <input type="number" name="semester" required placeholder="Sem" min="1" max="8" class="w-20 px-2 py-2 rounded-lg border dark:bg-gray-900 dark:border-gray-700 dark:text-white">
                                </div>
                                <input type="text" name="name" required placeholder="Subject Name" class="w-full px-4 py-2 rounded-lg border dark:bg-gray-900 dark:border-gray-700 dark:text-white">
                                <input type="text" name="code" required placeholder="Subject Code" class="w-full px-4 py-2 rounded-lg border dark:bg-gray-900 dark:border-gray-700 dark:text-white">
                                <button class="w-full py-2 bg-blue-600 text-white rounded-lg">Add Subject</button>
                            </form>
                        </div>
                    </div>
                     <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border dark:border-gray-700 overflow-y-auto max-h-[400px]">
                        <h3 class="font-bold dark:text-white mb-4">Subject List</h3>
                        <table class="w-full text-sm dark:text-gray-300"><thead><tr class="text-left border-b dark:border-gray-700"><th>Code</th><th>Name</th><th>Dept</th><th>Sem</th></tr></thead><tbody id="subject-list"></tbody></table>
                    </div>
                </div>
            </div>

            <!-- === TEACHER VIEW === -->
            <?php elseif ($role === 'teacher'): ?>
            <div class="grid lg:grid-cols-3 gap-8">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border dark:border-gray-700 h-fit sticky top-24">
                    <h3 class="font-bold dark:text-white mb-4">Create Quiz</h3>
                    <form onsubmit="handlePost(event, 'api/quiz.php?action=create', loadQuizzes)" class="space-y-4">
                        <select name="subject_id" id="quiz-subject-select" required class="w-full px-4 py-2 rounded-lg border dark:bg-gray-900 dark:border-gray-700 dark:text-white"><option value="">Loading Subjects...</option></select>
                        <input type="text" name="title" required placeholder="Quiz Title" class="w-full px-4 py-2 rounded-lg border dark:bg-gray-900 dark:border-gray-700 dark:text-white">
                        <textarea name="description" placeholder="Description" class="w-full px-4 py-2 rounded-lg border dark:bg-gray-900 dark:border-gray-700 dark:text-white"></textarea>
                        <input type="number" name="duration" value="30" required placeholder="Duration (min)" class="w-full px-4 py-2 rounded-lg border dark:bg-gray-900 dark:border-gray-700 dark:text-white">
                        <button class="w-full py-2 bg-purple-600 text-white rounded-lg">Create Quiz</button>
                    </form>
                </div>
                <div class="lg:col-span-2 space-y-6" id="quiz-list">Loading your quizzes...</div>
            </div>

            <!-- === STUDENT VIEW === -->
            <?php else: ?>
            <h2 class="text-2xl font-bold dark:text-white mb-6">Quizzes for <?php echo ($_SESSION['dept'] ?? 'N/A') . ' Sem-' . ($_SESSION['sem'] ?? 'N/A'); ?></h2>
            <div id="quiz-list" class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">Loading...</div>
            <?php endif; ?>
        </main>
    </div>

    <script src="assets/js/app.js"></script>
    <script>document.addEventListener('DOMContentLoaded', () => {
        const role = "<?php echo $role; ?>";
        if(role==='admin') loadAdminData();
        if(role==='teacher'||role==='student') loadQuizzes();
    });</script>
</body>
</html>