@extends('layouts.app')
@section('title', 'Task Board')

@push('styles')
<style>
    /* --- Page header --- */
    .board-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 24px;
        flex-wrap: wrap;
        gap: 12px;
    }

    .board-title {
        font-size: 22px;
        font-weight: 800;
        color: #1c1b20;
    }

    .board-header-right {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* --- Tabs --- */
    .board-tabs {
        display: flex;
        align-items: center;
        gap: 4px;
        background: #f3eeff;
        border-radius: 12px;
        padding: 4px;
    }

    .board-tab {
        padding: 8px 18px;
        border-radius: 9px;
        font-size: 13px;
        font-weight: 600;
        color: #797582;
        cursor: pointer;
        border: none;
        background: none;
        transition: background 0.15s, color 0.15s;
    }

    .board-tab.active {
        background: #ffffff;
        color: #6351a7;
        box-shadow: 0 2px 8px rgba(99, 81, 167, 0.15);
    }

    /* sort dropdown */
    .sort-select {
        padding: 8px 14px;
        border-radius: 10px;
        border: 1px solid #cac4d3;
        font-family: inherit;
        font-size: 13px;
        font-weight: 500;
        color: #1c1b20;
        background: #ffffff;
        cursor: pointer;
        outline: none;
        transition: border-color 0.15s;
    }

    .sort-select:focus { border-color: #6351a7; }

    /* new task FAB */
    .fab-new-task {
        display: flex;
        align-items: center;
        gap: 6px;
        background: #6351a7;
        color: #fff;
        border: none;
        border-radius: 14px;
        padding: 10px 18px;
        font-family: inherit;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        box-shadow: 0 4px 16px rgba(99, 81, 167, 0.3);
        transition: background 0.2s, transform 0.1s;
    }

    .fab-new-task:hover { background: #5240a0; }
    .fab-new-task:active { transform: scale(0.98); }

    /* --- Kanban board --- */
    .kanban-board {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 18px;
        align-items: start;
    }

    .kanban-col {
        background: #f6f1ff;
        border-radius: 20px;
        padding: 16px;
        min-height: 400px;
        display: flex;
        flex-direction: column;
        gap: 0;
    }

    .kanban-col.col-inprogress { background: #f0fbfa; }
    .kanban-col.col-done       { background: #f5f5f0; }

    /* column header */
    .col-header {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 14px;
        padding-bottom: 12px;
        border-bottom: 1px solid rgba(0,0,0,0.06);
    }

    .col-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .col-dot.todo       { background: #6351a7; }
    .col-dot.inprogress { background: #006a61; }
    .col-dot.done       { background: #797582; }

    .col-title {
        font-size: 14px;
        font-weight: 700;
        color: #1c1b20;
        flex: 1;
    }

    .col-count {
        background: #ffffff;
        color: #797582;
        font-size: 11px;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 100px;
        border: 1px solid #cac4d3;
    }

    .col-menu-btn {
        background: none;
        border: none;
        cursor: pointer;
        font-size: 18px;
        color: #797582;
        padding: 2px 4px;
        border-radius: 6px;
        transition: background 0.15s;
        line-height: 1;
    }

    .col-menu-btn:hover { background: rgba(0,0,0,0.06); }

    /* cards container */
    .col-cards {
        display: flex;
        flex-direction: column;
        gap: 12px;
        flex: 1;
        min-height: 60px;
    }

    /* task card */
    .task-card {
        background: #ffffff;
        border-radius: 18px;
        padding: 16px;
        box-shadow: 0 4px 14px rgba(181, 162, 255, 0.12);
        border: 1px solid rgba(202, 196, 211, 0.4);
        cursor: grab;
        transition: box-shadow 0.2s, transform 0.15s;
    }

    .task-card:hover {
        box-shadow: 0 8px 24px rgba(99, 81, 167, 0.18);
        transform: translateY(-2px);
    }

    .task-card:active { cursor: grabbing; transform: scale(0.99); }

    .card-chips {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
        margin-bottom: 10px;
    }

    .chip {
        display: inline-flex;
        align-items: center;
        padding: 3px 10px;
        border-radius: 100px;
        font-size: 11px;
        font-weight: 600;
    }

    .chip-design    { background: #ede9ff; color: #6351a7; }
    .chip-dev       { background: #d0f5f3; color: #006a61; }
    .chip-planning  { background: #fff4cc; color: #6a5f00; }
    .chip-personal  { background: #fde8e8; color: #ba1a1a; }
    .chip-meeting   { background: #e8f0fe; color: #1a6ef7; }
    .chip-high      { background: #fde8e8; color: #ba1a1a; }
    .chip-medium    { background: #fff4cc; color: #6a5f00; }
    .chip-low       { background: #d0f5f3; color: #006a61; }

    .card-title {
        font-size: 14px;
        font-weight: 700;
        color: #1c1b20;
        line-height: 1.4;
        margin-bottom: 10px;
    }

    .card-title.strikethrough {
        text-decoration: line-through;
        color: #797582;
        font-weight: 500;
    }

    .card-meta {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        flex-wrap: wrap;
    }

    .card-sub {
        font-size: 12px;
        color: #797582;
    }

    .card-sub.green { color: #006a61; font-weight: 600; }

    .card-avatars {
        display: flex;
        gap: -4px;
    }

    .avatar {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: #ede9ff;
        color: #6351a7;
        font-size: 11px;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid #fff;
        margin-left: -4px;
        flex-shrink: 0;
    }

    .avatar:first-child { margin-left: 0; }
    .avatar.teal { background: #d0f5f3; color: #006a61; }
    .avatar.me { background: #6351a7; color: #fff; }

    /* timer badge */
    .timer-badge {
        display: flex;
        align-items: center;
        gap: 6px;
        background: #fff0f0;
        border: 1px solid #fcc;
        border-radius: 100px;
        padding: 4px 10px;
    }

    .timer-badge span {
        font-size: 12px;
        font-weight: 700;
        color: #ba1a1a;
        font-variant-numeric: tabular-nums;
    }

    .timer-stop-btn {
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: #ba1a1a;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .timer-stop-btn::after {
        content: '';
        width: 6px;
        height: 6px;
        background: #fff;
        border-radius: 1px;
    }

    /* start timer button */
    .btn-start-timer {
        display: flex;
        align-items: center;
        gap: 5px;
        background: #ede9ff;
        color: #6351a7;
        border: none;
        border-radius: 8px;
        padding: 5px 12px;
        font-family: inherit;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.15s;
    }

    .btn-start-timer:hover { background: #ddd6ff; }

    /* progress bar inside card */
    .card-progress-wrap {
        background: #f0f0f0;
        border-radius: 100px;
        height: 6px;
        margin: 8px 0;
        overflow: hidden;
    }

    .card-progress-fill {
        height: 100%;
        border-radius: 100px;
        background: #006a61;
    }

    .card-progress-label {
        display: flex;
        justify-content: space-between;
        font-size: 11px;
        color: #797582;
        margin-bottom: 10px;
    }

    /* add task dashed button */
    .add-task-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        margin-top: 12px;
        padding: 10px;
        border: 2px dashed #cac4d3;
        border-radius: 14px;
        color: #797582;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        background: none;
        width: 100%;
        font-family: inherit;
        transition: border-color 0.15s, color 0.15s, background 0.15s;
    }

    .add-task-btn:hover {
        border-color: #6351a7;
        color: #6351a7;
        background: #faf8ff;
    }

    /* --- Modal overlay --- */
    .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(28, 27, 32, 0.5);
        backdrop-filter: blur(4px);
        z-index: 200;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .modal-overlay.open { display: flex; }

    .modal-box {
        background: #ffffff;
        border-radius: 24px;
        width: 100%;
        max-width: 540px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 24px 60px rgba(99, 81, 167, 0.25);
        animation: modalIn 0.2s ease;
    }

    @keyframes modalIn {
        from { opacity: 0; transform: translateY(20px) scale(0.97); }
        to   { opacity: 1; transform: translateY(0) scale(1); }
    }

    .modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 22px 24px 16px;
        border-bottom: 1px solid #f0eaf8;
    }

    .modal-title {
        font-size: 18px;
        font-weight: 800;
        color: #1c1b20;
    }

    .modal-close-btn {
        background: none;
        border: none;
        cursor: pointer;
        font-size: 22px;
        color: #797582;
        padding: 2px 6px;
        border-radius: 8px;
        line-height: 1;
        transition: background 0.15s;
    }

    .modal-close-btn:hover { background: #f3eeff; color: #6351a7; }

    .modal-body {
        padding: 20px 24px;
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .form-group { display: flex; flex-direction: column; gap: 6px; }

    .form-label {
        font-size: 13px;
        font-weight: 600;
        color: #1c1b20;
    }

    .form-input,
    .form-textarea,
    .form-select {
        padding: 10px 14px;
        border: 1.5px solid #cac4d3;
        border-radius: 12px;
        font-family: inherit;
        font-size: 14px;
        color: #1c1b20;
        background: #fdf7ff;
        outline: none;
        transition: border-color 0.15s;
        width: 100%;
    }

    .form-input:focus,
    .form-textarea:focus,
    .form-select:focus {
        border-color: #6351a7;
        background: #fff;
    }

    .form-textarea { resize: vertical; min-height: 80px; }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }

    /* subtasks inside modal */
    .subtask-list { display: flex; flex-direction: column; gap: 8px; }

    .subtask-input-row {
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .subtask-input-row .form-input { flex: 1; }

    .remove-subtask-btn {
        background: none;
        border: none;
        color: #ba1a1a;
        font-size: 18px;
        cursor: pointer;
        padding: 2px 6px;
        border-radius: 6px;
        transition: background 0.15s;
        flex-shrink: 0;
    }

    .remove-subtask-btn:hover { background: #fde8e8; }

    .add-subtask-btn {
        background: none;
        border: 1.5px dashed #cac4d3;
        border-radius: 10px;
        padding: 8px 14px;
        font-family: inherit;
        font-size: 13px;
        font-weight: 600;
        color: #797582;
        cursor: pointer;
        width: 100%;
        transition: border-color 0.15s, color 0.15s;
    }

    .add-subtask-btn:hover { border-color: #6351a7; color: #6351a7; }

    .modal-footer {
        display: flex;
        gap: 10px;
        justify-content: flex-end;
        padding: 16px 24px 22px;
        border-top: 1px solid #f0eaf8;
    }

    .btn-cancel {
        padding: 10px 20px;
        border-radius: 12px;
        border: 1.5px solid #cac4d3;
        background: none;
        font-family: inherit;
        font-size: 14px;
        font-weight: 600;
        color: #797582;
        cursor: pointer;
        transition: background 0.15s;
    }

    .btn-cancel:hover { background: #f3f0f7; }

    .btn-save {
        padding: 10px 24px;
        border-radius: 12px;
        border: none;
        background: #6351a7;
        font-family: inherit;
        font-size: 14px;
        font-weight: 700;
        color: #fff;
        cursor: pointer;
        box-shadow: 0 4px 14px rgba(99, 81, 167, 0.3);
        transition: background 0.2s, transform 0.1s;
    }

    .btn-save:hover { background: #5240a0; }
    .btn-save:active { transform: scale(0.98); }

    /* drag-over visual */
    .kanban-col.drag-over {
        outline: 2px dashed #6351a7;
        outline-offset: -4px;
    }
</style>
@endpush

@section('content')

{{-- Board page header --}}
<div class="board-header">
    <h1 class="board-title">📋 Task Board</h1>
    <div class="board-header-right">
        {{-- Tabs --}}
        <div class="board-tabs">
            <button class="board-tab active">My Tasks</button>
            <button class="board-tab">Team</button>
        </div>
        {{-- Sort dropdown --}}
        <select class="sort-select">
            <option>Sort: Priority</option>
            <option>Sort: Deadline</option>
            <option>Sort: A–Z</option>
        </select>
        {{-- New task button --}}
        <button class="fab-new-task" id="openModalBtn">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:15px;height:15px">
                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            New Task
        </button>
    </div>
</div>

{{-- Kanban board --}}
<div class="kanban-board">

    {{-- === TO DO column === --}}
    <div class="kanban-col col-todo" data-column="todo" ondragover="handleDragOver(event)" ondrop="handleDrop(event)">
        <div class="col-header">
            <span class="col-dot todo"></span>
            <span class="col-title">To Do</span>
            <span class="col-count">3</span>
            <button class="col-menu-btn" title="Opsi">⋯</button>
        </div>

        <div class="col-cards">

            {{-- Task card 1 --}}
            <div class="task-card"
                 draggable="true"
                 data-task-id="task-001"
                 ondragstart="handleDragStart(event)">
                <div class="card-chips">
                    <span class="chip chip-design">Design</span>
                    <span class="chip chip-high">High</span>
                </div>
                <div class="card-title">Create soft UI component library</div>
                <div class="card-meta">
                    <span class="card-sub">0 / 4 subtasks</span>
                    <div style="display:flex;align-items:center;gap:8px">
                        <button class="btn-start-timer">
                            ▶ Start timer
                        </button>
                        <div class="avatar">SL</div>
                    </div>
                </div>
            </div>

            {{-- Task card 2 --}}
            <div class="task-card"
                 draggable="true"
                 data-task-id="task-002"
                 ondragstart="handleDragStart(event)">
                <div class="card-chips">
                    <span class="chip chip-personal">Personal</span>
                </div>
                <div class="card-title">Review weekly schedule and goals</div>
                <div class="card-meta">
                    <span class="card-sub"><i data-lucide="clock" class="icon-sm"></i> 15m est.</span>
                    <div class="avatar me">ME</div>
                </div>
            </div>

            {{-- Task card 3 --}}
            <div class="task-card"
                 draggable="true"
                 data-task-id="task-003"
                 ondragstart="handleDragStart(event)">
                <div class="card-chips">
                    <span class="chip chip-meeting">Meeting</span>
                    <span class="chip chip-medium">Medium</span>
                </div>
                <div class="card-title">Meeting dengan Client — Q2 Review</div>
                <div class="card-meta">
                    <span class="card-sub"><i data-lucide="calendar" class="icon-sm"></i> Hari ini, 14:00</span>
                    <div class="avatar me">ME</div>
                </div>
            </div>

        </div>

        <button class="add-task-btn" onclick="openModal()">+ Add Task</button>
    </div>

    {{-- === IN PROGRESS column === --}}
    <div class="kanban-col col-inprogress" data-column="inprogress" ondragover="handleDragOver(event)" ondrop="handleDrop(event)">
        <div class="col-header">
            <span class="col-dot inprogress"></span>
            <span class="col-title">In Progress</span>
            <span class="col-count">1</span>
            <button class="col-menu-btn">⋯</button>
        </div>

        <div class="col-cards">

            {{-- Task card in progress --}}
            <div class="task-card"
                 draggable="true"
                 data-task-id="task-004"
                 ondragstart="handleDragStart(event)">
                <div class="card-chips">
                    <span class="chip chip-dev">Development</span>
                </div>
                <div class="card-title">Implement navigation shell logic</div>

                {{-- progress bar --}}
                <div class="card-progress-label">
                    <span>Progress</span>
                    <span>65%</span>
                </div>
                <div class="card-progress-wrap">
                    <div class="card-progress-fill" style="width: 65%"></div>
                </div>

                <div class="card-meta">
                    {{-- running timer --}}
                    <div class="timer-badge">
                        <button class="timer-stop-btn" title="Stop timer"></button>
                        <span id="liveTimer">01:24:05</span>
                    </div>
                    <div style="display:flex">
                        <div class="avatar">SL</div>
                        <div class="avatar teal">AK</div>
                    </div>
                </div>
            </div>

        </div>

        <button class="add-task-btn" onclick="openModal()">+ Add Task</button>
    </div>

    {{-- === DONE column === --}}
    <div class="kanban-col col-done" data-column="done" ondragover="handleDragOver(event)" ondrop="handleDrop(event)">
        <div class="col-header">
            <span class="col-dot done"></span>
            <span class="col-title">Done</span>
            <span class="col-count">2</span>
            <button class="col-menu-btn">⋯</button>
        </div>

        <div class="col-cards">

            {{-- Done card 1 --}}
            <div class="task-card"
                 draggable="true"
                 data-task-id="task-005"
                 ondragstart="handleDragStart(event)">
                <div class="card-chips">
                    <span class="chip chip-planning">Planning</span>
                </div>
                <div class="card-title strikethrough">Define brand anchors &amp; JSON structure</div>
                <div class="card-meta">
                    <span class="card-sub green"><i data-lucide="check-circle" class="icon-sm"></i> 3 / 3 subtasks</span>
                    <span class="card-sub">2h 15m logged</span>
                </div>
            </div>

            {{-- Done card 2 --}}
            <div class="task-card"
                 draggable="true"
                 data-task-id="task-006"
                 ondragstart="handleDragStart(event)">
                <div class="card-chips">
                    <span class="chip chip-design">Design</span>
                </div>
                <div class="card-title strikethrough">Riset referensi UI pattern library</div>
                <div class="card-meta">
                    <span class="card-sub green"><i data-lucide="check-circle" class="icon-sm"></i> Selesai</span>
                    <span class="card-sub">45m logged</span>
                </div>
            </div>

        </div>

        <button class="add-task-btn" onclick="openModal()">+ Add Task</button>
    </div>

</div>{{-- /.kanban-board --}}


{{-- === New Task Modal === --}}
<div class="modal-overlay" id="taskModal" onclick="handleOverlayClick(event)">
    <div class="modal-box">
        <div class="modal-header">
            <span class="modal-title">✨ Tambah Task Baru</span>
            <button class="modal-close-btn" onclick="closeModal()">×</button>
        </div>

        <div class="modal-body">

            <div class="form-group">
                <label class="form-label">Judul Task *</label>
                <input type="text" class="form-input" placeholder="e.g. Review design system v2">
            </div>

            <div class="form-group">
                <label class="form-label">Deskripsi</label>
                <textarea class="form-textarea" placeholder="Tambahkan deskripsi singkat..."></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Kategori</label>
                    <select class="form-select">
                        <option value="">Pilih kategori</option>
                        <option>Design</option>
                        <option>Development</option>
                        <option>Planning</option>
                        <option>Personal</option>
                        <option>Meeting</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Prioritas</label>
                    <select class="form-select">
                        <option value="">Pilih prioritas</option>
                        <option>Low</option>
                        <option>Medium</option>
                        <option>High</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Deadline</label>
                    <input type="date" class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Reminder</label>
                    <input type="time" class="form-input">
                </div>
            </div>

            {{-- Subtasks --}}
            <div class="form-group">
                <label class="form-label">Subtasks</label>
                <div class="subtask-list" id="subtaskList">
                    <div class="subtask-input-row">
                        <input type="text" class="form-input" placeholder="Subtask pertama...">
                        <button class="remove-subtask-btn" onclick="removeSubtask(this)" title="Hapus">×</button>
                    </div>
                </div>
                <button type="button" class="add-subtask-btn" onclick="addSubtask()" style="margin-top:8px">
                    + Tambah Subtask
                </button>
            </div>

        </div>

        <div class="modal-footer">
            <button class="btn-cancel" onclick="closeModal()">Batal</button>
            <button class="btn-save">💾 Simpan Task</button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // --- Modal open/close ---
    const modal = document.getElementById('taskModal');

    function openModal() {
        modal.classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        modal.classList.remove('open');
        document.body.style.overflow = '';
    }

    function handleOverlayClick(e) {
        if (e.target === modal) closeModal();
    }

    document.getElementById('openModalBtn').addEventListener('click', openModal);

    // keyboard shortcut: Escape
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') closeModal();
    });

    // --- Subtask management ---
    function addSubtask() {
        const list = document.getElementById('subtaskList');
        const row = document.createElement('div');
        row.className = 'subtask-input-row';
        row.innerHTML = `
            <input type="text" class="form-input" placeholder="Nama subtask...">
            <button class="remove-subtask-btn" onclick="removeSubtask(this)" title="Hapus">×</button>
        `;
        list.appendChild(row);
        row.querySelector('input').focus();
    }

    function removeSubtask(btn) {
        const list = document.getElementById('subtaskList');
        if (list.children.length > 1) {
            btn.closest('.subtask-input-row').remove();
        }
    }

    // --- Drag and drop ---
    let draggedCard = null;

    function handleDragStart(e) {
        draggedCard = e.currentTarget;
        e.dataTransfer.effectAllowed = 'move';
        e.dataTransfer.setData('text/plain', draggedCard.dataset.taskId);
        setTimeout(() => draggedCard.style.opacity = '0.4', 0);
    }

    function handleDragOver(e) {
        e.preventDefault();
        e.dataTransfer.dropEffect = 'move';
        e.currentTarget.classList.add('drag-over');
    }

    function handleDrop(e) {
        e.preventDefault();
        const col = e.currentTarget;
        col.classList.remove('drag-over');
        if (draggedCard) {
            const cards = col.querySelector('.col-cards');
            cards.appendChild(draggedCard);
            draggedCard.style.opacity = '1';
            draggedCard = null;
            updateColCounts();
        }
    }

    // remove drag-over highlight when leaving column
    document.querySelectorAll('.kanban-col').forEach(col => {
        col.addEventListener('dragleave', () => col.classList.remove('drag-over'));
        col.addEventListener('dragend', () => {
            col.classList.remove('drag-over');
            if (draggedCard) draggedCard.style.opacity = '1';
        });
    });

    function updateColCounts() {
        document.querySelectorAll('.kanban-col').forEach(col => {
            const count = col.querySelectorAll('.task-card').length;
            col.querySelector('.col-count').textContent = count;
        });
    }

    // --- Live timer (fake, just ticks up) ---
    let timerSeconds = 5045; // 01:24:05
    const timerEl = document.getElementById('liveTimer');

    if (timerEl) {
        setInterval(() => {
            timerSeconds++;
            const h = String(Math.floor(timerSeconds / 3600)).padStart(2, '0');
            const m = String(Math.floor((timerSeconds % 3600) / 60)).padStart(2, '0');
            const s = String(timerSeconds % 60).padStart(2, '0');
            timerEl.textContent = `${h}:${m}:${s}`;
        }, 1000);
    }
</script>
@endpush
