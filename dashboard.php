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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/dark.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script> tailwind.config = { darkMode: 'class', theme: { extend: { animation: { blob: "blob 7s infinite" }, keyframes: { blob: { "0%": { transform: "translate(0px, 0px) scale(1)" }, "33%": { transform: "translate(30px, -50px) scale(1.1)" }, "66%": { transform: "translate(-20px, 20px) scale(0.9)" }, "100%": { transform: "translate(0px, 0px) scale(1)" } } } } } } </script>
    <link rel="stylesheet" href="assets/css/style.css?v=2">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Star Rating CSS */
        .star-rating { direction: rtl; display: inline-flex; }
        .star-rating input { display: none; }
        .star-rating label { color: #ddd; font-size: 2rem; cursor: pointer; transition: color 0.2s; }
        .star-rating input:checked ~ label, .star-rating label:hover, .star-rating label:hover ~ label { color: #fbbf24; }
    </style>
</head>
<body class="h-full bg-gray-50 dark:bg-gray-900 transition-colors duration-500 relative">
    <div id="toast-container"></div>

    <div class="fixed top-0 left-0 w-full h-full overflow-hidden -z-10 pointer-events-none">
        <div class="absolute top-0 left-0 w-96 h-96 bg-purple-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob dark:bg-purple-900 dark:opacity-20"></div>
        <div class="absolute top-0 right-0 w-96 h-96 bg-yellow-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-2000 dark:bg-yellow-900 dark:opacity-20"></div>
        <div class="absolute -bottom-32 left-20 w-96 h-96 bg-pink-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-4000 dark:bg-pink-900 dark:opacity-20"></div>
    </div>

    <div id="analytics-modal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4 backdrop-blur-sm">
        <div class="bg-white dark:bg-gray-800 w-full max-w-4xl rounded-2xl p-6 max-h-[90vh] overflow-y-auto shadow-2xl">
            <div class="flex justify-between mb-6 border-b dark:border-gray-700 pb-4">
                <h3 id="analytics-title" class="text-2xl font-bold dark:text-white">Analytics</h3>
                <button onclick="closeAnalytics()" class="text-gray-500 hover:text-red-500"><i class="fas fa-times fa-lg"></i></button>
            </div>
            <div id="analytics-content" class="space-y-6"></div>
        </div>
    </div>

    <div id="feedback-modal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-[60] p-4 backdrop-blur-sm">
        <div class="bg-white dark:bg-gray-800 w-full max-w-md rounded-2xl p-8 shadow-2xl text-center animate-entrance">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-2">Great Job! 🎉</h2>
            <p class="text-gray-500 dark:text-gray-400 mb-6">How would you rate this quiz?</p>
            
            <form onsubmit="submitFeedback(event)">
                <input type="hidden" id="fb-quiz-id">
                
                <div class="star-rating mb-6 justify-center">
                    <input type="radio" id="star5" name="rating" value="5"><label for="star5"><i class="fas fa-star"></i></label>
                    <input type="radio" id="star4" name="rating" value="4"><label for="star4"><i class="fas fa-star"></i></label>
                    <input type="radio" id="star3" name="rating" value="3"><label for="star3"><i class="fas fa-star"></i></label>
                    <input type="radio" id="star2" name="rating" value="2"><label for="star2"><i class="fas fa-star"></i></label>
                    <input type="radio" id="star1" name="rating" value="1"><label for="star1"><i class="fas fa-star"></i></label>
                </div>

                <textarea name="message" placeholder="Any comments? (Optional)" class="w-full p-3 rounded-lg border dark:bg-gray-900 dark:border-gray-700 dark:text-white mb-6 focus:ring-2 focus:ring-blue-500 outline-none" rows="3"></textarea>
                
                <div class="flex gap-3">
                    <button type="button" onclick="window.location.reload()" class="flex-1 py-2 text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">Skip</button>
                    <button type="submit" class="flex-1 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-bold">Submit</button>
                </div>
            </form>
        </div>
    </div>

    <div id="quiz-taker" class="fixed inset-0 bg-white dark:bg-gray-900 z-50 hidden overflow-y-auto">
         <div class="max-w-3xl mx-auto py-8 px-4">
            <div class="flex justify-between items-center mb-8 sticky top-0 bg-white dark:bg-gray-900 py-4 border-b dark:border-gray-800 z-10">
                <h2 id="qt-title" class="text-2xl font-bold dark:text-white">Quiz Title</h2>
                <div class="text-xl font-mono font-bold text-red-600 bg-red-50 dark:bg-red-900/20 px-3 py-1 rounded-lg" id="qt-timer">00:00</div>
            </div>
            <form id="qt-form" onsubmit="submitQuiz(event)">
                <input type="hidden" name="quiz_id" id="qt-quiz-id">
                <div id="qt-questions" class="space-y-8"></div>
                <button type="submit" class="w-full py-4 mt-8 bg-green-600 hover:bg-green-700 text-white rounded-xl font-bold text-lg shadow-lg transform hover:scale-[1.01] transition-all">Submit Quiz</button>
            </form>
        </div>
    </div>

    <div id="question-modal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4 backdrop-blur-sm">
        <div class="bg-white dark:bg-gray-800 w-full max-w-2xl rounded-2xl p-6 max-h-[90vh] overflow-y-auto shadow-2xl">
            <div class="flex justify-between mb-4 border-b dark:border-gray-700 pb-2">
                <h3 class="text-xl font-bold dark:text-white">Manage Questions</h3>
                <button onclick="closeModal()" class="text-gray-500 hover:text-red-500"><i class="fas fa-times fa-lg"></i></button>
            </div>
            <form id="add-question-form" onsubmit="handleAddQuestion(event)" class="bg-gray-50 dark:bg-gray-900 p-4 rounded-xl mb-6 space-y-3 border dark:border-gray-700">
                <input type="hidden" name="quiz_id" id="modal-quiz-id">
                <select name="type" id="q-type" onchange="toggleQuestionType()" class="w-full p-2 rounded border font-bold text-blue-800 dark:bg-gray-800 dark:border-gray-700 dark:text-blue-400 outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="MCQ">Multiple Choice (MCQ)</option>
                    <option value="MSQ">Multiple Select (MSQ)</option>
                    <option value="DESCRIPTIVE">Descriptive (Keyword Match)</option>
                </select>
                <textarea name="question_text" required placeholder="Question Text" class="w-full p-2 rounded border dark:bg-gray-800 dark:border-gray-700 dark:text-white outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                <div id="options-container" class="grid grid-cols-2 gap-2">
                    <input type="text" name="option_a" placeholder="Option A" class="p-2 rounded border dark:bg-gray-800 dark:border-gray-700 dark:text-white">
                    <input type="text" name="option_b" placeholder="Option B" class="p-2 rounded border dark:bg-gray-800 dark:border-gray-700 dark:text-white">
                    <input type="text" name="option_c" placeholder="Option C" class="p-2 rounded border dark:bg-gray-800 dark:border-gray-700 dark:text-white">
                    <input type="text" name="option_d" placeholder="Option D" class="p-2 rounded border dark:bg-gray-800 dark:border-gray-700 dark:text-white">
                </div>
                <div class="flex gap-4 items-center">
                    <div class="flex-1" id="correct-answer-container"></div>
                    <input type="number" name="marks" value="1" min="1" class="w-20 p-2 rounded border dark:bg-gray-800 dark:border-gray-700 dark:text-white" placeholder="Marks">
                </div>
                <button class="w-full py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-semibold transition-colors">Add Question</button>
            </form>
            <div id="modal-questions-list" class="space-y-2"></div>
        </div>
    </div>

    <div id="announcement-modal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4 backdrop-blur-sm">
        <div class="bg-white dark:bg-gray-800 w-full max-w-lg rounded-2xl p-6 shadow-2xl">
            <div class="flex justify-between mb-4 border-b dark:border-gray-700 pb-2">
                <h3 id="announcement-modal-title" class="text-xl font-bold dark:text-white">Post Announcement</h3>
                <button onclick="document.getElementById('announcement-modal').classList.add('hidden')" class="text-gray-500 hover:text-red-500"><i class="fas fa-times fa-lg"></i></button>
            </div>
            <form id="announcement-form" onsubmit="handlePostAnnouncement(event)" class="space-y-4">
                <input type="hidden" name="id" id="ann-id">
                <input type="text" name="title" id="ann-title" required placeholder="Title" class="w-full p-3 rounded-lg border dark:bg-gray-900 dark:border-gray-700 dark:text-white focus:ring-2 focus:ring-orange-500 outline-none">
                <textarea name="message" id="ann-message" required placeholder="Message..." rows="4" class="w-full p-3 rounded-lg border dark:bg-gray-900 dark:border-gray-700 dark:text-white focus:ring-2 focus:ring-orange-500 outline-none"></textarea>
                <div class="grid grid-cols-2 gap-2">
                    <select name="target_dept" id="ann-dept" required class="p-3 rounded-lg border dark:bg-gray-900 dark:border-gray-700 dark:text-white">
                        <option value="">Select Dept</option><option value="CSE">CSE</option><option value="AIML">AIML</option><option value="ECE">ECE</option><option value="MECH">MECH</option><option value="CIVIL">CIVIL</option>
                    </select>
                    <select name="target_sem" id="ann-sem" required class="p-3 rounded-lg border dark:bg-gray-900 dark:border-gray-700 dark:text-white">
                        <option value="">Select Sem</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option>
                    </select>
                </div>
                <div class="relative">
                    <label class="text-xs text-gray-500 dark:text-gray-400 ml-1">Expiration (Optional)</label>
                    <input type="text" id="ann-expiry" name="expires_at" class="w-full p-3 rounded-lg border dark:bg-gray-900 dark:border-gray-700 dark:text-white text-sm bg-white dark:bg-gray-900" placeholder="Select Date & Time">
                </div>
                <button id="ann-btn" class="w-full py-3 bg-orange-600 hover:bg-orange-700 text-white font-bold rounded-lg">Post Now</button>
            </form>
        </div>
    </div>

    <div id="main-dashboard">
        <header class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-md shadow-sm border-b border-gray-200 dark:border-gray-700 sticky top-0 z-30">
             <div class="max-w-7xl mx-auto px-4 h-16 flex items-center justify-between">
                <h1 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <span class="w-8 h-8 rounded-lg bg-gradient-to-tr <?php echo $accents[$role]; ?> shadow-lg"></span>
                    QuizPortal | <?php echo ucfirst($role); ?>
                </h1>
                <div class="flex items-center gap-4">
                    <button onclick="toggleDarkMode()" class="text-gray-500 hover:text-gray-900 dark:hover:text-white transition-colors"><i class="fas fa-adjust"></i></button>
                    <a href="change_password.php" class="flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                        <i class="fas fa-user-circle fa-lg"></i> <?php echo htmlspecialchars($_SESSION['name']); ?>
                    </a>
                    <a href="api/auth.php?action=logout" class="text-red-500 hover:text-red-700 ml-2"><i class="fas fa-sign-out-alt"></i></a>
                </div>
            </div>
        </header>

        <main class="max-w-7xl mx-auto px-4 py-8">
            
            <div class="mb-8" id="announcement-section">
                <h2 class="text-xl font-bold dark:text-white mb-4 flex items-center">
                    <i class="fas fa-bullhorn text-orange-500 mr-2"></i> Notice Board
                    <?php if($role === 'teacher') echo '<button onclick="openAnnouncementModal()" class="ml-auto text-sm bg-orange-100 text-orange-700 px-3 py-1 rounded-lg hover:bg-orange-200 transition shadow-sm">+ Post New</button>'; ?>
                </h2>
                <div id="announcement-list" class="grid md:grid-cols-2 gap-4"></div>
            </div>

            <?php if ($role === 'admin'): ?>
            <div class="grid lg:grid-cols-3 gap-8">
                <div class="bg-white/90 dark:bg-gray-800/90 backdrop-blur p-6 rounded-xl shadow-sm border dark:border-gray-700 h-fit">
                    <h3 class="font-bold dark:text-white mb-4">Assign Teacher ID</h3>
                    <form onsubmit="handlePost(event, 'api/admin.php?action=assign_id', loadAdminData)" class="space-y-4">
                        <input type="text" name="assigned_to" required placeholder="Teacher Name" class="w-full px-4 py-2 rounded-lg border dark:bg-gray-900 dark:border-gray-700 dark:text-white">
                        <select name="department" id="admin-dept-select" required class="w-full px-4 py-2 rounded-lg border dark:bg-gray-900 dark:border-gray-700 dark:text-white"><option value="">Loading Depts...</option></select>
                        <input type="text" name="code" required placeholder="Unique Code" class="w-full px-4 py-2 rounded-lg border dark:bg-gray-900 dark:border-gray-700 dark:text-white">
                        <button class="w-full py-2 bg-orange-600 text-white rounded-lg">Assign ID</button>
                    </form>
                    <div class="mt-6"><h4 class="font-semibold text-sm dark:text-gray-300 mb-2">Recent IDs</h4><ul id="admin-ids-list" class="text-sm space-y-2 max-h-40 overflow-y-auto"></ul></div>
                </div>
                <div class="lg:col-span-2 space-y-8">
                     <div class="grid md:grid-cols-2 gap-8">
                         <div class="bg-white/90 dark:bg-gray-800/90 backdrop-blur p-6 rounded-xl shadow-sm border dark:border-gray-700">
                            <h3 class="font-bold dark:text-white mb-4">Add Department</h3>
                            <form onsubmit="handlePost(event, 'api/admin.php?action=add_dept', loadAdminData)" class="flex gap-2">
                                <input type="text" name="name" required placeholder="Name" class="flex-1 px-4 py-2 rounded-lg border dark:bg-gray-900 dark:border-gray-700 dark:text-white">
                                <button class="px-4 py-2 bg-blue-600 text-white rounded-lg">Add</button>
                            </form>
                        </div>
                        <div class="bg-white/90 dark:bg-gray-800/90 backdrop-blur p-6 rounded-xl shadow-sm border dark:border-gray-700">
                             <h3 class="font-bold dark:text-white mb-4">Add Subject</h3>
                             <form onsubmit="handlePost(event, 'api/admin.php?action=add_subject', loadAdminData)" class="space-y-3">
                                <div class="flex gap-2">
                                     <select name="department" id="subject-dept-select" required class="flex-1 px-2 py-2 rounded-lg border dark:bg-gray-900 dark:border-gray-700 dark:text-white"><option value="">Dept</option></select>
                                     <input type="number" name="semester" required placeholder="Sem" class="w-20 px-2 py-2 rounded-lg border dark:bg-gray-900 dark:border-gray-700 dark:text-white">
                                </div>
                                <input type="text" name="name" required placeholder="Subject Name" class="w-full px-4 py-2 rounded-lg border dark:bg-gray-900 dark:border-gray-700 dark:text-white">
                                <input type="text" name="code" required placeholder="Subject Code" class="w-full px-4 py-2 rounded-lg border dark:bg-gray-900 dark:border-gray-700 dark:text-white">
                                <button class="w-full py-2 bg-blue-600 text-white rounded-lg">Add Subject</button>
                            </form>
                        </div>
                    </div>
                     <div class="bg-white/90 dark:bg-gray-800/90 backdrop-blur p-6 rounded-xl shadow-sm border dark:border-gray-700 overflow-y-auto max-h-[400px]">
                        <h3 class="font-bold dark:text-white mb-4">Subject List</h3>
                        <table class="w-full text-sm dark:text-gray-300"><thead><tr class="text-left border-b dark:border-gray-700"><th>Code</th><th>Name</th><th>Dept</th><th>Sem</th></tr></thead><tbody id="subject-list"></tbody></table>
                    </div>
                </div>
            </div>

            <?php elseif ($role === 'teacher'): ?>
            <div class="grid lg:grid-cols-3 gap-8">
                <div class="bg-white/90 dark:bg-gray-800/90 backdrop-blur p-6 rounded-xl shadow-sm border dark:border-gray-700 h-fit sticky top-24">
                    <h3 class="font-bold dark:text-white mb-4">Create Quiz</h3>
                    <form onsubmit="handlePost(event, 'api/quiz.php?action=create', loadQuizzes)" class="space-y-4">
                        <select name="subject_id" id="quiz-subject-select" required class="w-full px-4 py-2 rounded-lg border dark:bg-gray-900 dark:border-gray-700 dark:text-white"><option value="">Loading...</option></select>
                        <input type="text" name="title" required placeholder="Quiz Title" class="w-full px-4 py-2 rounded-lg border dark:bg-gray-900 dark:border-gray-700 dark:text-white">
                        <textarea name="description" placeholder="Description" class="w-full px-4 py-2 rounded-lg border dark:bg-gray-900 dark:border-gray-700 dark:text-white"></textarea>
                        
                        <div class="space-y-2">
                             <div class="flex gap-2">
                                 <div class="flex-1">
                                     <label class="text-xs text-gray-500 font-bold dark:text-gray-400 ml-1">Duration</label>
                                     <input type="number" name="duration" value="30" required placeholder="Mins" class="w-full px-4 py-2 rounded-lg border dark:bg-gray-900 dark:border-gray-700 dark:text-white">
                                 </div>
                             </div>
                             <div>
                                 <label class="text-xs text-gray-500 font-bold dark:text-gray-400 ml-1">Deadline (Date & Time)</label>
                                 <input type="text" id="deadline-picker" name="end_time" class="w-full px-4 py-2 rounded-lg border dark:bg-gray-900 dark:border-gray-700 dark:text-white text-sm bg-white dark:bg-gray-900" placeholder="Select Deadline...">
                             </div>
                        </div>

                        <button class="w-full py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">Create Quiz</button>
                    </form>
                </div>
                <div class="lg:col-span-2 space-y-6" id="quiz-list">Loading...</div>
            </div>

            <?php else: ?>
            <h2 class="text-2xl font-bold dark:text-white mb-6">Quizzes for <?php echo ($_SESSION['dept'] ?? 'N/A') . ' Sem-' . ($_SESSION['sem'] ?? 'N/A'); ?></h2>
            <div id="quiz-list" class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">Loading...</div>
            <?php endif; ?>
        </main>
    </div>
    
    <script src="assets/js/app.js?v=12"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const role = "<?php echo $role; ?>";
            if(role==='admin') loadAdminData();
            if(role==='teacher'||role==='student') {
                loadQuizzes();
                loadAnnouncements();
            }

            const pickerEl = document.getElementById('deadline-picker');
            if (pickerEl) flatpickr(pickerEl, { enableTime: true, dateFormat: "Y-m-d H:i:S", time_24hr: false, minDate: "today", disableMobile: "true" });
            
            const annPicker = document.getElementById('ann-expiry');
            if (annPicker) flatpickr(annPicker, { enableTime: true, dateFormat: "Y-m-d H:i:S", time_24hr: false, minDate: "today", disableMobile: "true" });
        });
    </script>
</body>
</html>