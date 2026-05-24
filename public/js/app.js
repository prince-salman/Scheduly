/**
 * Scheduly — app.js
 * Vanilla JS, no framework. Keeps things simple.
 */

document.addEventListener('DOMContentLoaded', () => {
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
    initSidebar();
    initModals();
    initTaskTimers();
    initKanban();
    initCalendarToggle();
    initNotificationDropdown();
    initCharts();
    initToastSystem();
    initMobileMenu();
});

/* ============================================
   SIDEBAR — active link from current URL
   ============================================ */
function initSidebar() {
    const links = document.querySelectorAll('.sidebar__link');
    const currentPath = window.location.pathname;

    links.forEach(link => {
        const href = link.getAttribute('href');
        if (!href) return;

        // exact match or starts-with for nested routes
        if (currentPath === href || (href !== '/' && currentPath.startsWith(href))) {
            link.classList.add('active');
        } else {
            link.classList.remove('active');
        }
    });
}

/* ============================================
   MOBILE MENU TOGGLE
   ============================================ */
function initMobileMenu() {
    const menuBtn = document.getElementById('sidebar-toggle');
    const sidebar  = document.querySelector('.sidebar');
    const overlay  = document.querySelector('.sidebar-overlay');

    if (!menuBtn || !sidebar) return;

    menuBtn.addEventListener('click', () => {
        sidebar.classList.toggle('mobile-open');
        if (overlay) overlay.classList.toggle('active');
    });

    overlay?.addEventListener('click', () => {
        sidebar.classList.remove('mobile-open');
        overlay.classList.remove('active');
    });
}

/* ============================================
   MODALS — data-modal-open / data-modal-close
   ============================================ */
function initModals() {
    // open
    document.querySelectorAll('[data-modal-open]').forEach(trigger => {
        trigger.addEventListener('click', e => {
            e.preventDefault();
            const targetId = trigger.dataset.modalOpen;
            openModal(targetId);
        });
    });

    // close buttons inside modals
    document.querySelectorAll('[data-modal-close]').forEach(btn => {
        btn.addEventListener('click', () => {
            const overlay = btn.closest('.modal-overlay');
            if (overlay) closeModal(overlay.id);
        });
    });

    // click outside to close
    document.querySelectorAll('.modal-overlay').forEach(overlay => {
        overlay.addEventListener('click', e => {
            if (e.target === overlay) closeModal(overlay.id);
        });
    });

    // esc key
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal-overlay.open').forEach(m => {
                closeModal(m.id);
            });
        }
    });
}

function openModal(id) {
    const overlay = document.getElementById(id);
    if (!overlay) return;
    overlay.classList.add('open');
    document.body.style.overflow = 'hidden';

    // focus first input if any
    setTimeout(() => {
        const firstInput = overlay.querySelector('input, textarea, select');
        firstInput?.focus();
    }, 250);
}

function closeModal(id) {
    const overlay = document.getElementById(id);
    if (!overlay) return;
    overlay.classList.remove('open');

    // restore scroll only if no other modals open
    if (!document.querySelector('.modal-overlay.open')) {
        document.body.style.overflow = '';
    }
}

// expose globally so Blade can call openModal('some-id')
window.openModal  = openModal;
window.closeModal = closeModal;

/* ============================================
   TASK TIMERS — per task, stored in localStorage
   ============================================ */

// structure: { taskId: { elapsed: 0, running: false } }
let timerData = JSON.parse(localStorage.getItem('scheduly_timers') || '{}');
let timerIntervals = {}; // taskId -> intervalId

function initTaskTimers() {
    document.querySelectorAll('[data-task-id]').forEach(taskEl => {
        const taskId = taskEl.dataset.taskId;

        // restore display
        const display = taskEl.querySelector('.task-card__timer');
        if (display) {
            const saved = timerData[taskId];
            if (saved) {
                display.textContent = formatTime(saved.elapsed);
            }
        }

        const startBtn = taskEl.querySelector('[data-timer-start]');
        const stopBtn  = taskEl.querySelector('[data-timer-stop]');

        startBtn?.addEventListener('click', () => startTimer(taskId, taskEl));
        stopBtn?.addEventListener('click',  () => stopTimer(taskId, taskEl));

        // if it was running before page reload, restart it
        if (timerData[taskId]?.running) {
            startTimer(taskId, taskEl);
        }
    });
}

function startTimer(taskId, taskEl) {
    if (timerIntervals[taskId]) return; // already running

    if (!timerData[taskId]) {
        timerData[taskId] = { elapsed: 0, running: false };
    }

    timerData[taskId].running = true;
    saveTimers();

    const display = taskEl?.querySelector('.task-card__timer');

    timerIntervals[taskId] = setInterval(() => {
        timerData[taskId].elapsed += 1;
        saveTimers();

        if (display) {
            display.textContent = formatTime(timerData[taskId].elapsed);
            display.classList.add('timer-display--running');
        }
    }, 1000);

    // visual cue
    taskEl?.classList.add('timer-running');
}

function stopTimer(taskId, taskEl) {
    clearInterval(timerIntervals[taskId]);
    delete timerIntervals[taskId];

    if (timerData[taskId]) {
        timerData[taskId].running = false;
        saveTimers();
    }

    const display = taskEl?.querySelector('.task-card__timer');
    display?.classList.remove('timer-display--running');
    taskEl?.classList.remove('timer-running');

    showToast(`Timer stopped — ${formatTime(timerData[taskId]?.elapsed || 0)} logged`, 'success');
}

function resetTimer(taskId) {
    stopTimer(taskId);
    if (timerData[taskId]) {
        timerData[taskId].elapsed = 0;
        saveTimers();
    }
}
window.resetTimer = resetTimer;

function saveTimers() {
    localStorage.setItem('scheduly_timers', JSON.stringify(timerData));
}

function formatTime(totalSeconds) {
    const m = Math.floor(totalSeconds / 60).toString().padStart(2, '0');
    const s = (totalSeconds % 60).toString().padStart(2, '0');
    return `${m}:${s}`;
}

/* ============================================
   KANBAN DRAG & DROP (HTML5 native)
   ============================================ */
let draggedCard = null;

function initKanban() {
    const board = document.querySelector('.kanban-board');
    if (!board) return;

    setupDraggableCards();

    // columns as drop targets
    board.querySelectorAll('.kanban-column').forEach(col => {
        col.addEventListener('dragover', e => {
            e.preventDefault();
            col.classList.add('drag-over');
        });

        col.addEventListener('dragleave', e => {
            // only remove if leaving the column itself, not a child
            if (!col.contains(e.relatedTarget)) {
                col.classList.remove('drag-over');
            }
        });

        col.addEventListener('drop', e => {
            e.preventDefault();
            col.classList.remove('drag-over');

            if (!draggedCard) return;

            // append to end of column (before the add-task btn if present)
            const addBtn = col.querySelector('.kanban-add-btn');
            if (addBtn) {
                col.insertBefore(draggedCard, addBtn);
            } else {
                col.appendChild(draggedCard);
            }

            draggedCard.classList.remove('dragging');
            draggedCard = null;

            updateColumnCounts(board);
        });
    });
}

function setupDraggableCards() {
    document.querySelectorAll('.kanban-card').forEach(card => {
        card.setAttribute('draggable', 'true');

        card.addEventListener('dragstart', e => {
            draggedCard = card;
            card.classList.add('dragging');
            // needed for firefox
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/plain', '');
        });

        card.addEventListener('dragend', () => {
            card.classList.remove('dragging');
            draggedCard = null;
        });
    });
}

function updateColumnCounts(board) {
    board.querySelectorAll('.kanban-column').forEach(col => {
        const count = col.querySelectorAll('.kanban-card').length;
        const badge = col.querySelector('.kanban-column-header__count');
        if (badge) badge.textContent = count;
    });
}

// call this after dynamically adding a new kanban card
window.refreshKanban = function() {
    setupDraggableCards();
    const board = document.querySelector('.kanban-board');
    if (board) updateColumnCounts(board);
};

/* ============================================
   CALENDAR VIEW SWITCHER
   ============================================ */
let currentCalendarView = 'weekly';

function initCalendarToggle() {
    const toggleGroup = document.querySelector('.calendar-view-toggle');
    if (!toggleGroup) return;

    toggleGroup.querySelectorAll('.toggle-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const view = btn.dataset.view;
            if (!view || view === currentCalendarView) return;

            toggleGroup.querySelectorAll('.toggle-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            currentCalendarView = view;
            switchCalendarView(view);
        });
    });
}

function switchCalendarView(view) {
    const views = document.querySelectorAll('[data-calendar-view]');
    views.forEach(v => {
        v.classList.toggle('hidden', v.dataset.calendarView !== view);
    });

    // dispatch a custom event so individual pages can hook in
    document.dispatchEvent(new CustomEvent('calendarViewChanged', { detail: { view } }));
}

/* ============================================
   NOTIFICATION DROPDOWN
   ============================================ */
function initNotificationDropdown() {
    const triggers = document.querySelectorAll('[data-notif-toggle]');

    triggers.forEach(trigger => {
        const panelId = trigger.dataset.notifToggle;
        const panel   = document.getElementById(panelId);
        if (!panel) return;

        trigger.addEventListener('click', e => {
            e.stopPropagation();
            const isOpen = panel.classList.contains('open');
            closeAllDropdowns();
            if (!isOpen) panel.classList.add('open');
        });
    });

    // close when clicking outside
    document.addEventListener('click', e => {
        if (!e.target.closest('.notif-dropdown')) {
            closeAllDropdowns();
        }
    });

    // mark as read on click
    document.querySelectorAll('.notif-item.unread').forEach(item => {
        item.addEventListener('click', () => {
            item.classList.remove('unread');
            item.classList.add('read');
            item.querySelector('.notif-item__dot')?.classList.remove('unread');
            updateNotifBadge();
        });
    });
}

function closeAllDropdowns() {
    document.querySelectorAll('.notif-panel.open').forEach(p => p.classList.remove('open'));
}

function updateNotifBadge() {
    const unreadCount = document.querySelectorAll('.notif-item.unread').length;
    const badge = document.querySelector('.notif-badge');
    if (!badge) return;

    if (unreadCount === 0) {
        badge.style.display = 'none';
    } else {
        badge.style.display = 'flex';
        badge.textContent = unreadCount > 9 ? '9+' : unreadCount;
    }
}

/* ============================================
   CHART.JS — admin dashboard
   ============================================ */
function initCharts() {
    if (typeof Chart === 'undefined') return;

    // bar chart — task completion per week
    const barCtx = document.getElementById('chart-tasks-bar');
    if (barCtx) {
        new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                datasets: [
                    {
                        label: 'Completed',
                        data: [8, 12, 6, 14, 10, 4, 2],
                        backgroundColor: 'rgba(0, 106, 97, 0.75)',
                        borderRadius: 8,
                        borderSkipped: false,
                    },
                    {
                        label: 'Created',
                        data: [10, 15, 9, 16, 13, 5, 3],
                        backgroundColor: 'rgba(99, 81, 167, 0.35)',
                        borderRadius: 8,
                        borderSkipped: false,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            font: { family: "'Plus Jakarta Sans', sans-serif", size: 12 },
                            color: '#4a4458',
                            boxWidth: 12,
                            borderRadius: 4,
                        }
                    },
                    tooltip: {
                        backgroundColor: '#1c1b20',
                        titleFont: { family: "'Plus Jakarta Sans', sans-serif" },
                        bodyFont:  { family: "'Plus Jakarta Sans', sans-serif" },
                        cornerRadius: 10,
                        padding: 10,
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: {
                            font: { family: "'Plus Jakarta Sans', sans-serif", size: 11 },
                            color: '#797582',
                        }
                    },
                    y: {
                        grid: { color: 'rgba(202, 196, 211, 0.4)' },
                        ticks: {
                            font: { family: "'Plus Jakarta Sans', sans-serif", size: 11 },
                            color: '#797582',
                            stepSize: 5,
                        },
                        beginAtZero: true,
                    }
                }
            }
        });
    }

    // donut chart — task status breakdown
    const donutCtx = document.getElementById('chart-tasks-donut');
    if (donutCtx) {
        new Chart(donutCtx, {
            type: 'doughnut',
            data: {
                labels: ['Completed', 'In Progress', 'Pending', 'Overdue'],
                datasets: [{
                    data: [42, 28, 18, 12],
                    backgroundColor: [
                        'rgba(0, 106, 97, 0.85)',
                        'rgba(99, 81, 167, 0.8)',
                        'rgba(193, 177, 55, 0.8)',
                        'rgba(186, 26, 26, 0.75)',
                    ],
                    borderWidth: 0,
                    hoverOffset: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                cutout: '68%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: { family: "'Plus Jakarta Sans', sans-serif", size: 12 },
                            color: '#4a4458',
                            padding: 14,
                            boxWidth: 12,
                            borderRadius: 4,
                        }
                    },
                    tooltip: {
                        backgroundColor: '#1c1b20',
                        titleFont: { family: "'Plus Jakarta Sans', sans-serif" },
                        bodyFont:  { family: "'Plus Jakarta Sans', sans-serif" },
                        cornerRadius: 10,
                        padding: 10,
                        callbacks: {
                            label: ctx => ` ${ctx.label}: ${ctx.parsed}%`
                        }
                    }
                }
            }
        });
    }
}

/* ============================================
   TOAST NOTIFICATIONS
   ============================================ */
function initToastSystem() {
    // make sure the container exists
    if (!document.querySelector('.toast-container')) {
        const container = document.createElement('div');
        container.className = 'toast-container';
        container.id = 'toast-container';
        document.body.appendChild(container);
    }
}

/**
 * Show a toast.
 * @param {string} message
 * @param {'default'|'success'|'error'|'warning'} type
 * @param {number} duration  ms before auto-dismiss
 */
function showToast(message, type = 'default', duration = 3500) {
    const container = document.getElementById('toast-container') || document.querySelector('.toast-container');
    if (!container) return;

    const toast = document.createElement('div');
    toast.className = `toast${type !== 'default' ? ` toast--${type}` : ''}`;

    const icons = {
        success: '✓',
        error:   '✕',
        warning: '⚠',
        default: 'ℹ',
    };

    toast.innerHTML = `<span>${icons[type] || icons.default}</span><span>${message}</span>`;
    container.appendChild(toast);

    // auto remove
    const timer = setTimeout(() => dismissToast(toast), duration);

    toast.addEventListener('click', () => {
        clearTimeout(timer);
        dismissToast(toast);
    });
}

function dismissToast(toast) {
    toast.classList.add('exiting');
    toast.addEventListener('animationend', () => toast.remove(), { once: true });
}

// expose so Blade/other scripts can call it
window.showToast = showToast;

/* ============================================
   MISC HELPERS
   ============================================ */

// generic confirm dialog wrapper
window.confirmAction = function(message, onConfirm) {
    // could swap this for a real modal confirm later
    if (window.confirm(message)) {
        onConfirm();
    }
};

// auto-expand textarea as user types
document.querySelectorAll('textarea[data-autoresize]').forEach(ta => {
    ta.addEventListener('input', () => {
        ta.style.height = 'auto';
        ta.style.height = ta.scrollHeight + 'px';
    });
});

// smooth scroll to anchor (for single-page sections)
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', e => {
        const target = document.querySelector(anchor.getAttribute('href'));
        if (!target) return;
        e.preventDefault();
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
});
