// API Base URL - 本番環境では適切なURLに変更してください
const API_BASE_URL = 'http://localhost:8000/api';

// 現在のユーザー情報
let currentUser = null;

// ページロード時の初期化
document.addEventListener('DOMContentLoaded', () => {
    // ログインフォーム
    document.getElementById('loginForm').addEventListener('submit', handleLogin);
    
    // ログアウトボタン
    document.getElementById('adultLogout').addEventListener('click', handleLogout);
    document.getElementById('childLogout').addEventListener('click', handleLogout);
    
    // タブ切り替え
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const tab = e.target.dataset.tab;
            switchTab(e.target.closest('.container'), tab);
        });
    });
    
    // 大人用フォーム
    document.getElementById('addTaskForm').addEventListener('submit', handleAddTask);
    
    // 子ども用フォーム
    document.getElementById('usePointsForm').addEventListener('submit', handleUsePoints);
    
    // ローカルストレージからユーザー情報を復元
    const savedUser = localStorage.getItem('currentUser');
    if (savedUser) {
        currentUser = JSON.parse(savedUser);
        showScreen(currentUser.role === 'adult' ? 'adultScreen' : 'childScreen');
        if (currentUser.role === 'adult') {
            loadAdultData();
        } else {
            loadChildData();
        }
    }
});

// ログイン処理
async function handleLogin(e) {
    e.preventDefault();
    const formData = new FormData(e.target);
    const data = {
        username: formData.get('username'),
        password: formData.get('password')
    };
    
    try {
        const response = await fetch(`${API_BASE_URL}/auth/login`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            currentUser = result.user;
            localStorage.setItem('currentUser', JSON.stringify(currentUser));
            localStorage.setItem('authToken', result.token);
            
            showScreen(currentUser.role === 'adult' ? 'adultScreen' : 'childScreen');
            
            if (currentUser.role === 'adult') {
                loadAdultData();
            } else {
                loadChildData();
            }
            
            document.getElementById('loginForm').reset();
            document.getElementById('loginError').textContent = '';
        } else {
            document.getElementById('loginError').textContent = result.message;
        }
    } catch (error) {
        console.error('Login error:', error);
        document.getElementById('loginError').textContent = 'ログインに失敗しました';
    }
}

// ログアウト処理
function handleLogout() {
    currentUser = null;
    localStorage.removeItem('currentUser');
    localStorage.removeItem('authToken');
    showScreen('loginScreen');
}

// 画面切り替え
function showScreen(screenId) {
    document.querySelectorAll('.screen').forEach(screen => {
        screen.classList.remove('active');
    });
    document.getElementById(screenId).classList.add('active');
    
    if (screenId === 'adultScreen') {
        document.getElementById('adultUserName').textContent = currentUser.name;
    } else if (screenId === 'childScreen') {
        document.getElementById('childUserName').textContent = currentUser.name;
    }
}

// タブ切り替え
function switchTab(container, tabName) {
    container.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    container.querySelectorAll('.tab-content').forEach(content => {
        content.classList.remove('active');
    });
    
    container.querySelector(`[data-tab="${tabName}"]`).classList.add('active');
    
    const tabMap = {
        'tasks': 'tasksTab',
        'approvals': 'approvalsTab',
        'children': 'childrenTab',
        'available': 'availableTab',
        'history': 'historyTab',
        'points': 'pointsTab'
    };
    
    container.querySelector(`#${tabMap[tabName]}`).classList.add('active');
    
    // タブが切り替わった時にデータをリロード
    if (currentUser.role === 'adult') {
        if (tabName === 'approvals') {
            loadPendingCompletions();
        } else if (tabName === 'children') {
            loadChildrenPoints();
        }
    } else {
        if (tabName === 'history') {
            loadCompletionHistory();
        } else if (tabName === 'points') {
            loadPointsData();
        }
    }
}

// 大人用データロード
async function loadAdultData() {
    await loadTasks();
    await loadPendingCompletions();
}

// タスク一覧取得
async function loadTasks() {
    try {
        const response = await fetch(`${API_BASE_URL}/tasks`);
        const tasks = await response.json();
        
        const tasksList = document.getElementById('tasksList');
        
        if (tasks.length === 0) {
            tasksList.innerHTML = '<div class="empty-message">タスクがありません</div>';
            return;
        }
        
        tasksList.innerHTML = tasks.map(task => `
            <div class="task-item">
                <div class="task-info">
                    <div class="task-title">${escapeHtml(task.title)}</div>
                    <div class="task-description">${escapeHtml(task.description || '')}</div>
                    <span class="task-points">${task.points}ポイント</span>
                </div>
            </div>
        `).join('');
    } catch (error) {
        console.error('Failed to load tasks:', error);
    }
}

// タスク追加
async function handleAddTask(e) {
    e.preventDefault();
    const formData = new FormData(e.target);
    const data = {
        title: formData.get('title'),
        description: formData.get('description'),
        points: parseInt(formData.get('points')),
        created_by: currentUser.id
    };
    
    try {
        const response = await fetch(`${API_BASE_URL}/tasks`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            e.target.reset();
            await loadTasks();
        }
    } catch (error) {
        console.error('Failed to add task:', error);
    }
}

// 承認待ち一覧取得
async function loadPendingCompletions() {
    try {
        const response = await fetch(`${API_BASE_URL}/completions`);
        const completions = await response.json();
        
        const pending = completions.filter(c => c.status === 'pending');
        const pendingCompletions = document.getElementById('pendingCompletions');
        
        if (pending.length === 0) {
            pendingCompletions.innerHTML = '<div class="empty-message">承認待ちのお手伝いはありません</div>';
            return;
        }
        
        pendingCompletions.innerHTML = pending.map(completion => `
            <div class="completion-item">
                <div class="completion-info">
                    <div class="task-title">${escapeHtml(completion.title)}</div>
                    <div>子ども: ${escapeHtml(completion.child_name)}</div>
                    <div>完了日時: ${new Date(completion.completed_at).toLocaleString('ja-JP')}</div>
                    <span class="task-points">${completion.points}ポイント</span>
                </div>
                <button class="btn btn-success" onclick="approveCompletion(${completion.id})">承認</button>
            </div>
        `).join('');
    } catch (error) {
        console.error('Failed to load pending completions:', error);
    }
}

// 完了承認
async function approveCompletion(completionId) {
    try {
        const response = await fetch(`${API_BASE_URL}/completions/${completionId}/approve`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ approved_by: currentUser.id })
        });
        
        const result = await response.json();
        
        if (result.success) {
            await loadPendingCompletions();
        }
    } catch (error) {
        console.error('Failed to approve completion:', error);
    }
}

// 子どもポイント一覧取得
async function loadChildrenPoints() {
    try {
        // Get all users and filter children
        // For simplicity, we'll just show child1 (id:2) for now
        // In a real app, you'd fetch all children from an API endpoint
        const childId = 2; // child1のID
        const response = await fetch(`${API_BASE_URL}/points/${childId}`);
        const points = await response.json();
        
        const childrenPoints = document.getElementById('childrenPoints');
        
        // Fetch child name from a user endpoint (simplified version)
        childrenPoints.innerHTML = `
            <div class="child-item">
                <div class="child-info">
                    <div class="task-title">太郎 (child1)</div>
                    <div>獲得ポイント: ${points.earned}</div>
                    <div>使用ポイント: ${points.used}</div>
                    <div style="font-weight: bold; color: #667eea;">残高: ${points.balance}ポイント</div>
                </div>
            </div>
        `;
    } catch (error) {
        console.error('Failed to load children points:', error);
    }
}

// 子ども用データロード
async function loadChildData() {
    await loadAvailableTasks();
}

// 利用可能なタスク一覧
async function loadAvailableTasks() {
    try {
        const response = await fetch(`${API_BASE_URL}/tasks`);
        const tasks = await response.json();
        
        const availableTasks = document.getElementById('availableTasks');
        
        if (tasks.length === 0) {
            availableTasks.innerHTML = '<div class="empty-message">お手伝いがありません</div>';
            return;
        }
        
        availableTasks.innerHTML = tasks.map(task => `
            <div class="task-item">
                <div class="task-info">
                    <div class="task-title">${escapeHtml(task.title)}</div>
                    <div class="task-description">${escapeHtml(task.description || '')}</div>
                    <span class="task-points">${task.points}ポイント</span>
                </div>
                <button class="btn btn-success" onclick="completeTask(${task.id})">完了報告</button>
            </div>
        `).join('');
    } catch (error) {
        console.error('Failed to load available tasks:', error);
    }
}

// タスク完了報告
async function completeTask(taskId) {
    try {
        const response = await fetch(`${API_BASE_URL}/tasks/${taskId}/complete`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ child_id: currentUser.id })
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('お手伝い完了を報告しました！大人の承認をお待ちください。');
        }
    } catch (error) {
        console.error('Failed to complete task:', error);
    }
}

// 完了履歴取得
async function loadCompletionHistory() {
    try {
        const response = await fetch(`${API_BASE_URL}/completions`);
        const completions = await response.json();
        
        const myCompletions = completions.filter(c => c.child_id === currentUser.id);
        const completionHistory = document.getElementById('completionHistory');
        
        if (myCompletions.length === 0) {
            completionHistory.innerHTML = '<div class="empty-message">履歴がありません</div>';
            return;
        }
        
        completionHistory.innerHTML = myCompletions.map(completion => `
            <div class="completion-item">
                <div class="completion-info">
                    <div class="task-title">${escapeHtml(completion.title)}</div>
                    <div>完了日時: ${new Date(completion.completed_at).toLocaleString('ja-JP')}</div>
                    <span class="task-points">${completion.points}ポイント</span>
                    <span class="status-badge status-${completion.status}">
                        ${completion.status === 'pending' ? '承認待ち' : completion.status === 'approved' ? '承認済み' : '却下'}
                    </span>
                </div>
            </div>
        `).join('');
    } catch (error) {
        console.error('Failed to load completion history:', error);
    }
}

// ポイントデータ取得
async function loadPointsData() {
    await loadPointsBalance();
    await loadPointsHistory();
}

// ポイント残高取得
async function loadPointsBalance() {
    try {
        const response = await fetch(`${API_BASE_URL}/points/${currentUser.id}`);
        const points = await response.json();
        
        const pointsBalance = document.getElementById('pointsBalance');
        pointsBalance.innerHTML = `
            <div class="balance">${points.balance}</div>
            <div class="details">
                獲得: ${points.earned} | 使用: ${points.used}
            </div>
        `;
    } catch (error) {
        console.error('Failed to load points balance:', error);
    }
}

// ポイント使用
async function handleUsePoints(e) {
    e.preventDefault();
    const formData = new FormData(e.target);
    const data = {
        child_id: currentUser.id,
        points: parseInt(formData.get('points')),
        purpose: formData.get('purpose')
    };
    
    try {
        const response = await fetch(`${API_BASE_URL}/points/use`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            e.target.reset();
            await loadPointsData();
            alert('ポイントを使用しました！');
        } else {
            alert(result.message || 'ポイントの使用に失敗しました');
        }
    } catch (error) {
        console.error('Failed to use points:', error);
    }
}

// ポイント使用履歴取得
async function loadPointsHistory() {
    try {
        const response = await fetch(`${API_BASE_URL}/points/history/${currentUser.id}`);
        const history = await response.json();
        
        const pointsHistory = document.getElementById('pointsHistory');
        
        if (history.length === 0) {
            pointsHistory.innerHTML = '<div class="empty-message">使用履歴がありません</div>';
            return;
        }
        
        pointsHistory.innerHTML = history.map(usage => `
            <div class="usage-item">
                <div class="usage-info">
                    <div class="task-title">${escapeHtml(usage.purpose)}</div>
                    <div>使用日時: ${new Date(usage.used_at).toLocaleString('ja-JP')}</div>
                    <span class="task-points">-${usage.points}ポイント</span>
                </div>
            </div>
        `).join('');
    } catch (error) {
        console.error('Failed to load points history:', error);
    }
}

// HTMLエスケープ
function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, m => map[m]);
}
