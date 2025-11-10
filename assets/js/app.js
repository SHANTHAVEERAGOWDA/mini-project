// --- DARK MODE & TOASTS ---
function toggleDarkMode() {
    document.documentElement.classList.toggle('dark');
    localStorage.setItem('theme', document.documentElement.classList.contains('dark') ? 'dark' : 'light');
}
if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
    document.documentElement.classList.add('dark');
}

function showToast(message, type = 'info') {
    const container = document.getElementById('toast-container');
    if (!container) return;
    const toast = document.createElement('div');
    // Tailwind classes for toast
    const typeClasses = {
        success: 'bg-green-100 border-green-500 text-green-800 dark:bg-green-900/50 dark:text-green-200 dark:border-green-500',
        error: 'bg-red-100 border-red-500 text-red-800 dark:bg-red-900/50 dark:text-red-200 dark:border-red-500',
        info: 'bg-blue-100 border-blue-500 text-blue-800 dark:bg-blue-900/50 dark:text-blue-200 dark:border-blue-500'
    };
    toast.className = `p-4 rounded-lg shadow-lg text-sm font-medium animate-entrance border-l-4 mb-3 ${typeClasses[type] || typeClasses.info} backdrop-blur-md`;
    toast.innerHTML = message; // Use innerHTML to allow bolding if needed
    container.appendChild(toast);
    // Auto-remove after 3 seconds
    setTimeout(() => {
        toast.classList.add('opacity-0', 'translate-x-full', 'transition-all', 'duration-300');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// --- GENERIC POST SUBMITTER ---
async function handlePost(e, url, onSuccessCallback) {
    e.preventDefault();
    const form = e.target;
    const btn = form.querySelector('button[type="submit"]');
    const originalText = btn ? btn.innerHTML : '';

    // Loading state
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';
    }

    try {
        const formData = new FormData(form);
        const jsonData = JSON.stringify(Object.fromEntries(formData));
        
        const res = await fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: jsonData
        });

        const data = await res.json();

        if (data.success) {
            showToast(data.message || 'Operation successful!', 'success');
            form.reset();
            // If a callback function was provided (like loadAdminData), run it now
            if (typeof onSuccessCallback === 'function') {
                onSuccessCallback(data);
            }
        } else {
            showToast(data.message || 'An error occurred.', 'error');
        }
    } catch (err) {
        console.error("Submission Error:", err);
        showToast('Network error. Please check your connection.', 'error');
    } finally {
        // Restore button state
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    }
}

// --- AUTHENTICATION HANDLER ---
async function handleAuth(e, action) {
    await handlePost(e, `api/auth.php?action=${action}`, (data) => {
        if (action === 'login' && data.redirect) {
            window.location.href = data.redirect;
        } else if (action === 'register') {
            // If on index.html, switch back to login view
            if (typeof toggleView === 'function') {
                toggleView('login');
            }
        }
    });
}

// =========================================
// === DASHBOARD DATA LOADERS (THE FIX) ===
// =========================================

// 1. ADMIN DATA LOADER
async function loadAdminData() {
    try {
        const res = await fetch('api/admin.php?action=dashboard_data');
        const data = await res.json();

        if (!data.success) throw new Error(data.message || 'Failed to load data');

        // A. Update Department Badges List
        const deptListEl = document.getElementById('dept-list');
        if (deptListEl) {
            deptListEl.innerHTML = data.departments.length ? 
                data.departments.map(d => `<span class="inline-block px-3 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded-full text-sm font-medium mr-2 mb-2">${d.name}</span>`).join('') : 
                '<span class="text-gray-500 dark:text-gray-400 text-sm italic">No departments added yet.</span>';
        }

        // B. Update ALL Department Dropdowns (Assign ID form & Add Subject form)
        const deptOptions = data.departments.length ? 
            '<option value="">Select Department</option>' + data.departments.map(d => `<option value="${d.name}">${d.name}</option>`).join('') : 
            '<option value="" disabled selected>⚠ Add a Department first!</option>';
        
        document.querySelectorAll('select[name="department"]').forEach(select => {
            select.innerHTML = deptOptions;
        });

        // C. Update Subjects Table
        const subjectListEl = document.getElementById('subject-list');
        if (subjectListEl) {
            subjectListEl.innerHTML = data.subjects.length ? 
                data.subjects.map(s => `
                    <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                        <td class="p-3 font-mono font-bold text-blue-600 dark:text-blue-400">${s.code}</td>
                        <td class="p-3 font-medium dark:text-gray-200">${s.name}</td>
                        <td class="p-3"><span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded text-xs">${s.department}</span></td>
                        <td class="p-3 text-gray-600 dark:text-gray-400">Sem-${s.semester}</td>
                    </tr>
                `).join('') : 
                '<tr><td colspan="4" class="p-6 text-center text-gray-500 dark:text-gray-400 italic">No subjects added yet.</td></tr>';
        }

        // D. Update Recent IDs List
        const adminIdsListEl = document.getElementById('admin-ids-list');
        if (adminIdsListEl) {
            adminIdsListEl.innerHTML = data.teacher_ids.length ? 
                data.teacher_ids.map(id => `
                    <li class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg border dark:border-gray-700">
                        <div>
                            <span class="font-mono font-bold text-gray-900 dark:text-white">${id.code}</span>
                            <span class="text-xs text-gray-500 dark:text-gray-400 ml-2">(${id.department})</span>
                            <div class="text-xs text-gray-600 dark:text-gray-300">${id.assigned_to_name || id.assigned_to}</div>
                        </div>
                        <span class="px-2 py-1 text-xs font-bold rounded ${id.is_used == 1 ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' : 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400'}">
                            ${id.is_used == 1 ? 'USED' : 'OPEN'}
                        </span>
                    </li>
                `).join('') : 
                '<li class="text-center text-gray-500 dark:text-gray-400 py-4 italic">No Teacher IDs assigned yet.</li>';
        }

    } catch (error) {
        console.error("Admin Data Error:", error);
        // Only show toast if it's a real error, not just an empty state on first load
        if (document.getElementById('dept-list')) {
             showToast('Failed to load latest data. Please refresh.', 'error');
        }
    }
}

// 2. QUIZ DATA LOADER (Student/Teacher)
async function loadQuizzes() {
    try {
        const res = await fetch('api/quiz.php?action=list');
        const data = await res.json();
        
        // Teacher: Populate Subject Dropdown for "Create Quiz" form
        const subjSelect = document.getElementById('quiz-subject-select');
        if (data.subjects && subjSelect) {
            subjSelect.innerHTML = '<option value="">Select Subject</option>' + 
                data.subjects.map(s => `<option value="${s.id}">${s.name} (${s.department} Sem-${s.semester})</option>`).join('');
        }

        // Both: Populate Quiz List
        const list = document.getElementById('quiz-list');
        if (!list) return;

        if (!data.quizzes || data.quizzes.length === 0) {
            list.innerHTML = '<div class="col-span-full flex flex-col items-center justify-center p-10 opacity-60"><i class="fas fa-inbox fa-3x mb-4 text-gray-300 dark:text-gray-600"></i><p class="text-gray-500 dark:text-gray-400">No quizzes found.</p></div>';
            return;
        }

        list.innerHTML = data.quizzes.map(q => `
            <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition-all group">
                <div class="flex justify-between items-start mb-4">
                    <span class="text-xs font-bold text-blue-600 bg-blue-50 dark:bg-blue-900/30 dark:text-blue-300 px-2 py-1 rounded-md uppercase tracking-wider">${q.subject_name}</span>
                    <!-- Show Trophy for Students only -->
                    ${q.author ? `<button onclick="showLeaderboard(${q.id}, '${q.title.replace(/'/g, "\\'")}')" class="text-yellow-400 hover:text-yellow-500 transition-colors" title="View Leaderboard"><i class="fas fa-trophy fa-lg"></i></button>` : ''}
                </div>
                
                <h3 class="font-bold text-lg text-gray-900 dark:text-white mb-2 line-clamp-1 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">${q.title}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-2 mb-6 h-10">${q.description || 'No description provided.'}</p>
                
                <div class="flex items-center justify-between pt-4 border-t border-gray-100 dark:border-gray-700">
                    <div class="flex items-center text-gray-500 dark:text-gray-400 text-sm font-medium">
                        <i class="far fa-clock mr-2"></i> ${q.duration_minutes} mins
                    </div>
                    
                    ${q.author ? 
                        // STUDENT ACTION BUTTON
                        `<button onclick="startQuiz(${q.id})" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold text-sm transition-all hover:scale-105 active:scale-95 shadow-sm">
                            Start Quiz
                        </button>` 
                        : 
                        // TEACHER ACTION BUTTONS
                        `<div class="flex gap-2">
                            <button onclick="openQuestionModal(${q.id})" class="px-3 py-1.5 bg-purple-50 text-purple-600 dark:bg-purple-900/30 dark:text-purple-300 rounded-lg text-sm font-medium hover:bg-purple-100 dark:hover:bg-purple-900/50 transition-colors">
                                <i class="fas fa-list-ul mr-1"></i> Q's (${q.q_count})
                            </button>
                            <button onclick="showTeacherStats(${q.id}, '${q.title.replace(/'/g, "\\'")}')" class="px-3 py-1.5 bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-300 rounded-lg text-sm font-medium hover:bg-blue-100 dark:hover:bg-blue-900/50 transition-colors" title="Analytics">
                                <i class="fas fa-chart-pie"></i>
                            </button>
                        </div>`
                    }
                </div>
            </div>
        `).join('');

    } catch (error) {
        console.error("Quiz Load Error:", error);
        // showToast('Failed to refresh quizzes.', 'error');
    }
}

// =========================================
// === QUIZ TAKER (STUDENT) ===
// =========================================
let quizTimerInterval = null;

async function startQuiz(qid) {
    try {
        // 1. Fetch Data
        const res = await fetch(`api/attempt.php?action=start&quiz_id=${qid}`);
        
        // 2. Check for non-JSON response (The most common cause of your error)
        const contentType = res.headers.get("content-type");
        if (!contentType || !contentType.includes("application/json")) {
            const text = await res.text();
            console.error("Server Error Raw Output:", text);
            throw new Error("Server Error: " + text.substring(0, 150) + "..."); 
        }

        const data = await res.json();

        // 3. Handle API-reported errors
        if (!data.success) {
            showToast(data.message, 'error');
            return;
        }

        // 4. Switch Views
        document.getElementById('main-dashboard').classList.add('hidden');
        document.getElementById('quiz-taker').classList.remove('hidden');

        // 5. Set Header Info
        document.getElementById('qt-title').textContent = data.quiz.title;
        document.getElementById('qt-quiz-id').value = qid;

        // 6. Render Questions
        document.getElementById('qt-questions').innerHTML = data.questions.map((q, index) => `
            <div class="bg-gray-50 dark:bg-gray-800 p-6 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                <div class="flex justify-between items-start mb-4">
                    <h3 class="text-lg font-medium dark:text-white">
                        <span class="text-gray-400 dark:text-gray-500 font-bold mr-2">Q${index + 1}.</span>
                        ${q.question_text}
                    </h3>
                    <span class="text-xs font-semibold px-2.5 py-1 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-md ml-4 whitespace-nowrap">
                        ${q.marks} Mark${q.marks > 1 ? 's' : ''}
                    </span>
                </div>
                <div class="space-y-3 ml-8">
                    ${['a', 'b', 'c', 'd'].map(opt => `
                        <label class="flex items-center p-3 bg-white dark:bg-gray-900 border-2 border-transparent dark:border-gray-700 rounded-lg cursor-pointer hover:border-blue-500 dark:hover:border-blue-500 transition-all group">
                            <input type="radio" name="answers[${q.id}]" value="${opt}" class="w-4 h-4 text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-gray-600 dark:bg-gray-800">
                            <span class="ml-3 text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-white">
                                ${q['option_' + opt]}
                            </span>
                        </label>
                    `).join('')}
                </div>
            </div>
        `).join('');

        // 7. Start Timer
        startTimer(data.quiz.duration_minutes);

    } catch (e) {
        console.error("Start Quiz Error:", e);
        // ALERT the actual error so you can see it
        alert("Failed to start quiz:\n" + e.message);
    }
}
function startTimer(minutes) {
    let timeLeft = parseInt(minutes) * 60;
    const timerEl = document.getElementById('qt-timer');
    
    if (quizTimerInterval) clearInterval(quizTimerInterval);
    
    updateTimerDisplay(timeLeft, timerEl);
    
    quizTimerInterval = setInterval(() => {
        timeLeft--;
        updateTimerDisplay(timeLeft, timerEl);
        
        if (timeLeft <= 0) {
            clearInterval(quizTimerInterval);
            alert("Time's up! Your quiz is being submitted.");
            submitQuiz();
        }
    }, 1000);
}

function updateTimerDisplay(seconds, element) {
    const m = Math.floor(seconds / 60).toString().padStart(2, '0');
    const s = (seconds % 60).toString().padStart(2, '0');
    element.textContent = `${m}:${s}`;
    
    if (seconds < 60) {
        element.classList.add('text-red-600', 'animate-pulse');
        element.classList.remove('text-gray-900', 'dark:text-white');
    } else {
         element.classList.remove('text-red-600', 'animate-pulse');
         // Optional: Add base color back if needed, but usually parent handles it
    }
}

async function submitQuiz(e) {
    if (e) e.preventDefault();
    if (quizTimerInterval) clearInterval(quizTimerInterval);

    // Show loading state on submit button if it exists (auto-submit might not have event)
    const submitBtn = document.querySelector('#qt-form button[type="submit"]');
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Submitting...';
    }

    const form = document.getElementById('qt-form');
    const formData = { 
        quiz_id: document.getElementById('qt-quiz-id').value, 
        answers: {} 
    };
    
    // Extract answers from form data
    new FormData(form).forEach((value, key) => {
        const match = key.match(/answers\[(\d+)\]/);
        if (match) formData.answers[match[1]] = value;
    });

    try {
        const res = await fetch('api/attempt.php?action=submit', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(formData)
        });
        const result = await res.json();
        
        if (result.success) {
            // Use a standard confirm dialog to show score, then reload on click OK
            alert(`🎉 Quiz Submitted!\n\nYour Score: ${result.score} / ${result.total}`);
            window.location.reload();
        } else {
            showToast(result.message || 'Submission failed.', 'error');
            if (submitBtn) {
                 submitBtn.disabled = false;
                 submitBtn.innerText = 'Submit Quiz';
            }
        }
    } catch (err) {
        console.error("Submit Error:", err);
        alert('Network error during submission. Please check your connection and try again.');
        if (submitBtn) {
             submitBtn.disabled = false;
             submitBtn.innerText = 'Submit Quiz';
        }
    }
}

// =========================================
// === TEACHER MODALS & ANALYTICS ===
// =========================================
let currentQuizId = null;

// --- QUESTION MANAGER ---
function openQuestionModal(qid) {
    currentQuizId = qid;
    document.getElementById('modal-quiz-id').value = qid;
    const modal = document.getElementById('question-modal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    loadQuestions(qid);
}

function closeModal() {
    const modal = document.getElementById('question-modal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    loadQuizzes(); // Refresh quiz list to update question count
}

async function loadQuestions(qid) {
    const listEl = document.getElementById('modal-questions-list');
    listEl.innerHTML = '<p class="text-center text-gray-500 animate-pulse">Loading questions...</p>';
    
    try {
        const res = await fetch(`api/question.php?action=list&quiz_id=${qid}`);
        const data = await res.json();
        
        if (!data.questions || data.questions.length === 0) {
            listEl.innerHTML = '<p class="text-center text-gray-500 py-4">No questions added yet.</p>';
            return;
        }

        listEl.innerHTML = data.questions.map((q, i) => `
            <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-lg border border-gray-200 dark:border-gray-700 flex justify-between items-center group hover:border-blue-300 dark:hover:border-blue-700 transition-colors">
                <div>
                    <p class="font-medium dark:text-white">
                        <span class="text-gray-500 dark:text-gray-400 mr-2">Q${i+1}.</span> 
                        ${q.question_text}
                    </p>
                    <p class="text-xs font-semibold text-green-600 dark:text-green-400 mt-1">
                        Correct: Option ${q.correct_option.toUpperCase()} • ${q.marks} Marks
                    </p>
                </div>
                 <button onclick="deleteQuestion(${q.id})" class="text-gray-400 hover:text-red-500 p-2 transition-colors rounded-full hover:bg-red-50 dark:hover:bg-red-900/20" title="Delete Question">
                    <i class="fas fa-trash-alt"></i>
                 </button>
            </div>
        `).join('');
    } catch (e) {
        listEl.innerHTML = '<p class="text-red-500">Error loading questions.</p>';
    }
}

async function handleAddQuestion(e) {
    await handlePost(e, 'api/question.php?action=add', () => loadQuestions(currentQuizId));
}

async function deleteQuestion(qid) {
    if (!confirm('Are you sure you want to delete this question?')) return;
    try {
         const res = await fetch('api/question.php?action=delete', {
             method: 'POST',
             headers: {'Content-Type': 'application/json'},
             body: JSON.stringify({question_id: qid})
         });
         const data = await res.json();
         if (data.success) {
             showToast('Question deleted.', 'success');
             loadQuestions(currentQuizId);
         } else {
             showToast(data.message || 'Delete failed.', 'error');
         }
    } catch(e) { showToast('Network error during delete.', 'error'); }
}

// --- ANALYTICS VIEWER ---
function closeAnalytics() {
    const modal = document.getElementById('analytics-modal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

// 1. LEADERBOARD (Shared)
async function showLeaderboard(qid, title) {
    document.getElementById('analytics-title').textContent = `Leaderboard: ${title}`;
    const contentEl = document.getElementById('analytics-content');
    contentEl.innerHTML = '<div class="flex justify-center p-10"><i class="fas fa-spinner fa-spin fa-2x text-blue-500"></i></div>';
    
    const modal = document.getElementById('analytics-modal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');

    try {
        const res = await fetch(`api/analytics.php?action=leaderboard&quiz_id=${qid}`);
        const data = await res.json();
        
        contentEl.innerHTML = `
            <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700">
                <table class="w-full text-left dark:text-white text-sm">
                    <thead class="bg-gray-100 dark:bg-gray-700/50 text-gray-600 dark:text-gray-300 uppercase tracking-wider text-xs">
                        <tr>
                            <th class="p-4 font-semibold">Rank</th>
                            <th class="p-4 font-semibold">Student</th>
                            <th class="p-4 font-semibold">Score</th>
                            <th class="p-4 font-semibold">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        ${data.leaderboard.length ? data.leaderboard.map((r, i) => `
                            <tr class="bg-white dark:bg-gray-800">
                                <td class="p-4">
                                    ${i < 3 ? `<span class="text-lg">${['🥇','🥈','🥉'][i]}</span>` : `<span class="font-bold text-gray-500 ml-2">#${i+1}</span>`}
                                </td>
                                <td class="p-4 font-medium">${r.name}</td>
                                <td class="p-4">
                                    <span class="px-2.5 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 rounded-md font-bold font-mono">
                                        ${r.score}/${r.total_marks}
                                    </span>
                                </td>
                                <td class="p-4 text-gray-500 dark:text-gray-400">${new Date(r.completed_at).toLocaleDateString()}</td>
                            </tr>
                        `).join('') : '<tr><td colspan="4" class="p-8 text-center text-gray-500 dark:text-gray-400 italic">No attempts yet. Be the first!</td></tr>'}
                    </tbody>
                </table>
            </div>
        `;
    } catch (e) {
        contentEl.innerHTML = '<p class="text-red-500 text-center p-4">Failed to load leaderboard.</p>';
    }
}

// 2. TEACHER STATS
let statsChart = null; // Global var to destroy old charts
async function showTeacherStats(qid, title) {
    document.getElementById('analytics-title').textContent = `Analytics: ${title}`;
    const contentEl = document.getElementById('analytics-content');
    contentEl.innerHTML = '<div class="flex justify-center p-10"><i class="fas fa-spinner fa-spin fa-2x text-purple-500"></i></div>';
    
    const modal = document.getElementById('analytics-modal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');

    try {
        const res = await fetch(`api/analytics.php?action=quiz_stats&quiz_id=${qid}`);
        const data = await res.json();
        
        if (!data.success) throw new Error(data.message);

        // Calculate Pass/Fail (assuming 50% is pass)
        const passCount = data.scores.filter(s => (s.score / s.total_marks) >= 0.5).length;
        const failCount = data.scores.length - passCount;

        contentEl.innerHTML = `
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                <div class="bg-blue-50 dark:bg-blue-900/20 p-5 rounded-2xl text-center">
                    <h4 class="text-blue-600 dark:text-blue-400 text-sm font-semibold uppercase tracking-wider mb-1">Attempts</h4>
                    <p class="text-3xl font-bold dark:text-white">${data.stats.attempts}</p>
                </div>
                <div class="bg-purple-50 dark:bg-purple-900/20 p-5 rounded-2xl text-center">
                    <h4 class="text-purple-600 dark:text-purple-400 text-sm font-semibold uppercase tracking-wider mb-1">Avg Score</h4>
                    <p class="text-3xl font-bold dark:text-white">${parseFloat(data.stats.avg_score || 0).toFixed(1)}</p>
                </div>
                <div class="bg-green-50 dark:bg-green-900/20 p-5 rounded-2xl text-center">
                    <h4 class="text-green-600 dark:text-green-400 text-sm font-semibold uppercase tracking-wider mb-1">High</h4>
                    <p class="text-3xl font-bold dark:text-white">${data.stats.max_score || 0}</p>
                </div>
                <div class="bg-red-50 dark:bg-red-900/20 p-5 rounded-2xl text-center">
                    <h4 class="text-red-600 dark:text-red-400 text-sm font-semibold uppercase tracking-wider mb-1">Low</h4>
                    <p class="text-3xl font-bold dark:text-white">${data.stats.min_score || 0}</p>
                </div>
            </div>

            <div class="bg-gray-50 dark:bg-gray-800/50 p-6 rounded-2xl mb-8 border dark:border-gray-700">
                <h4 class="text-center font-semibold text-gray-700 dark:text-gray-300 mb-6">Performance Overview (Pass vs Fail)</h4>
                <div class="h-64 flex justify-center">
                    <canvas id="statsChart"></canvas>
                </div>
            </div>

            <div>
                <h4 class="font-bold text-xl dark:text-white mb-4 flex items-center">
                    <i class="fas fa-list-alt mr-2 text-gray-400"></i> Detailed Student Results
                </h4>
                <div id="student-results-table" class="max-h-80 overflow-y-auto rounded-xl border border-gray-200 dark:border-gray-700">
                    <div class="p-8 text-center text-gray-500 dark:text-gray-400 animate-pulse">Loading details...</div>
                </div>
            </div>
        `;

        // Render Chart
        if (statsChart) statsChart.destroy(); // Prevent canvas reuse errors
        const ctx = document.getElementById('statsChart').getContext('2d');
        statsChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Passed (>50%)', 'Failed (<50%)'],
                datasets: [{
                    data: [passCount, failCount],
                    backgroundColor: ['#10b981', '#ef4444'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { color: document.documentElement.classList.contains('dark') ? '#fff' : '#333' } }
                }
            }
        });

        // Load details
        loadStudentResults(qid);

    } catch (e) {
        contentEl.innerHTML = `<p class="text-red-500 text-center p-4">Error loading stats: ${e.message}</p>`;
    }
}

async function loadStudentResults(qid) {
    const res = await fetch(`api/analytics.php?action=student_results&quiz_id=${qid}`);
    const data = await res.json();
    
    const tableHtml = `
        <table class="w-full text-sm text-left dark:text-white">
            <thead class="bg-gray-100 dark:bg-gray-700/80 sticky top-0 backdrop-blur-md">
                <tr>
                    <th class="p-3 font-semibold text-gray-600 dark:text-gray-300">Student Name</th>
                    <th class="p-3 font-semibold text-gray-600 dark:text-gray-300">Score</th>
                    <th class="p-3 font-semibold text-gray-600 dark:text-gray-300">Submitted</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                ${data.results.map(r => `
                    <tr class="bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="p-3">
                            <div class="font-medium">${r.name}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">${r.email}</div>
                        </td>
                        <td class="p-3">
                            <span class="px-2 py-1 rounded-md font-mono font-bold ${ (r.score/r.total_marks) >= 0.5 ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400'}">
                                ${r.score} / ${r.total_marks}
                            </span>
                        </td>
                        <td class="p-3 text-gray-500 dark:text-gray-400">${new Date(r.completed_at).toLocaleString()}</td>
                    </tr>
                `).join('')}
            </tbody>
        </table>
    `;
    document.getElementById('student-results-table').innerHTML = data.results.length ? tableHtml : '<p class="p-6 text-center text-gray-500 dark:text-gray-400 italic">No attempts recorded yet.</p>';
}