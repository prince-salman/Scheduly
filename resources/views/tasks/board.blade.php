@extends('layouts.app')
@section('title', 'Task Board')

@push('styles')
<style>

    .btn-alloc-time {
    display:flex; align-items:center; gap:6px;
    padding:9px 16px; border-radius:12px;
    border:1.5px solid #cac4d3; background:#fff;
    font-size:13px; font-weight:600; color:#797582;
    text-decoration:none; transition:all .15s;
}
.btn-alloc-time:hover { border-color:#6351a7; color:#6351a7; background:#f3eeff; }
    /* ── layout ── */
    .board-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:24px; flex-wrap:wrap; gap:12px; }
    .board-title  { font-size:22px; font-weight:800; color:#1c1b20; }
    .board-header-right { display:flex; align-items:center; gap:10px; }

    .board-tabs { display:flex; align-items:center; gap:4px; background:#f3eeff; border-radius:12px; padding:4px; }
    .board-tab  { padding:8px 18px; border-radius:9px; font-size:13px; font-weight:600; color:#797582; cursor:pointer; border:none; background:none; transition:background .15s,color .15s; }
    .board-tab.active { background:#fff; color:#6351a7; box-shadow:0 2px 8px rgba(99,81,167,.15); }

    .sort-select { padding:8px 14px; border-radius:10px; border:1px solid #cac4d3; font-family:inherit; font-size:13px; color:#1c1b20; background:#fff; cursor:pointer; outline:none; }
    .sort-select:focus { border-color:#6351a7; }

    .fab-new-task { display:flex; align-items:center; gap:6px; background:#6351a7; color:#fff; border:none; border-radius:14px; padding:10px 18px; font-family:inherit; font-size:14px; font-weight:600; cursor:pointer; box-shadow:0 4px 16px rgba(99,81,167,.3); transition:background .2s; }
    .fab-new-task:hover { background:#5240a0; }

    /* ── kanban ── */
    .kanban-board { display:grid; grid-template-columns:repeat(3,1fr); gap:18px; align-items:start; }
    .kanban-col   { background:#f6f1ff; border-radius:20px; padding:16px; min-height:400px; display:flex; flex-direction:column; }
    .kanban-col.col-inprogress { background:#f0fbfa; }
    .kanban-col.col-done       { background:#f5f5f0; }
    .kanban-col.drag-over      { outline:2px dashed #6351a7; outline-offset:-4px; }

    .col-header { display:flex; align-items:center; gap:8px; margin-bottom:14px; padding-bottom:12px; border-bottom:1px solid rgba(0,0,0,.06); }
    .col-dot { width:10px; height:10px; border-radius:50%; flex-shrink:0; }
    .col-dot.todo       { background:#6351a7; }
    .col-dot.inprogress { background:#006a61; }
    .col-dot.done       { background:#797582; }
    .col-title  { font-size:14px; font-weight:700; color:#1c1b20; flex:1; }
    .col-count  { background:#fff; color:#797582; font-size:11px; font-weight:700; padding:2px 8px; border-radius:100px; border:1px solid #cac4d3; }
    .col-menu-btn { background:none; border:none; cursor:pointer; font-size:18px; color:#797582; padding:2px 4px; border-radius:6px; }
    .col-cards { display:flex; flex-direction:column; gap:12px; flex:1; min-height:60px; }

    /* ── task card ── */
    .task-card { background:#fff; border-radius:18px; padding:16px; box-shadow:0 4px 14px rgba(181,162,255,.12); border:1px solid rgba(202,196,211,.4); cursor:grab; transition:box-shadow .2s,transform .15s; position:relative; }
    .task-card:hover { box-shadow:0 8px 24px rgba(99,81,167,.18); transform:translateY(-2px); }
    .task-card:active { cursor:grabbing; transform:scale(.99); }

    .card-actions { position:absolute; top:10px; right:10px; display:flex; gap:4px; opacity:0; transition:opacity .15s; }
    .task-card:hover .card-actions { opacity:1; }
    .card-action-btn { width:26px; height:26px; border-radius:8px; border:1px solid #e0d9f0; background:#fff; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:background .15s,border-color .15s; }
    .card-action-btn svg { width:13px; height:13px; stroke:#797582; }
    .card-action-btn:hover { background:#f3eeff; border-color:#6351a7; }
    .card-action-btn:hover svg { stroke:#6351a7; }
    .card-action-btn.danger:hover { background:#ffedea; border-color:#ba1a1a; }
    .card-action-btn.danger:hover svg { stroke:#ba1a1a; }

    .card-chips { display:flex; gap:6px; flex-wrap:wrap; margin-bottom:10px; padding-right:80px; }
    .chip { display:inline-flex; align-items:center; padding:3px 10px; border-radius:100px; font-size:11px; font-weight:600; }
    .chip-design   { background:#ede9ff; color:#6351a7; }
    .chip-dev      { background:#d0f5f3; color:#006a61; }
    .chip-planning { background:#fff4cc; color:#6a5f00; }
    .chip-personal { background:#fde8e8; color:#ba1a1a; }
    .chip-meeting  { background:#e8f0fe; color:#1a6ef7; }
    .chip-high     { background:#fde8e8; color:#ba1a1a; }
    .chip-medium   { background:#fff4cc; color:#6a5f00; }
    .chip-low      { background:#d0f5f3; color:#006a61; }

    .card-title { font-size:14px; font-weight:700; color:#1c1b20; line-height:1.4; margin-bottom:10px; }
    .card-title.strikethrough { text-decoration:line-through; color:#797582; font-weight:500; }
    .card-meta  { display:flex; align-items:center; justify-content:space-between; gap:8px; flex-wrap:wrap; }
    .card-sub   { font-size:12px; color:#797582; }
    .card-sub.green { color:#006a61; font-weight:600; }

    .avatar { width:28px; height:28px; border-radius:50%; background:#ede9ff; color:#6351a7; font-size:11px; font-weight:700; display:flex; align-items:center; justify-content:center; border:2px solid #fff; margin-left:-4px; flex-shrink:0; }
    .avatar:first-child { margin-left:0; }
    .avatar.me { background:#6351a7; color:#fff; }

    .timer-badge { display:flex; align-items:center; gap:6px; background:#fff0f0; border:1px solid #fcc; border-radius:100px; padding:4px 10px; }
    .timer-badge span.timer-display { font-size:12px; font-weight:700; color:#ba1a1a; font-variant-numeric:tabular-nums; }
    .timer-stop-btn { width:18px; height:18px; border-radius:50%; background:#ba1a1a; border:none; cursor:pointer; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    .timer-stop-btn::after { content:''; width:6px; height:6px; background:#fff; border-radius:1px; display:block; }

    .btn-start-timer { display:flex; align-items:center; gap:5px; background:#ede9ff; color:#6351a7; border:none; border-radius:8px; padding:5px 12px; font-family:inherit; font-size:12px; font-weight:600; cursor:pointer; transition:background .15s; }
    .btn-start-timer:hover { background:#ddd6ff; }

    .card-progress-wrap  { background:#f0f0f0; border-radius:100px; height:6px; margin:8px 0; overflow:hidden; }
    .card-progress-fill  { height:100%; border-radius:100px; background:#006a61; transition:width .4s ease; }
    .card-progress-label { display:flex; justify-content:space-between; font-size:11px; color:#797582; margin-bottom:4px; }

    .add-task-btn { display:flex; align-items:center; justify-content:center; gap:6px; margin-top:12px; padding:10px; border:2px dashed #cac4d3; border-radius:14px; color:#797582; font-size:13px; font-weight:600; cursor:pointer; background:none; width:100%; font-family:inherit; transition:border-color .15s,color .15s,background .15s; }
    .add-task-btn:hover { border-color:#6351a7; color:#6351a7; background:#faf8ff; }

    /* ── modals ── */
    .modal-overlay { position:fixed; inset:0; background:rgba(28,27,32,.5); backdrop-filter:blur(4px); z-index:200; display:none; align-items:center; justify-content:center; padding:20px; }
    .modal-overlay.open { display:flex; }
    .modal-box { background:#fff; border-radius:24px; width:100%; max-width:540px; max-height:90vh; overflow-y:auto; box-shadow:0 24px 60px rgba(99,81,167,.25); animation:modalIn .2s ease; }
    @keyframes modalIn { from{opacity:0;transform:translateY(20px) scale(.97)} to{opacity:1;transform:translateY(0) scale(1)} }

    .modal-header { display:flex; align-items:center; justify-content:space-between; padding:22px 24px 16px; border-bottom:1px solid #f0eaf8; }
    .modal-title  { font-size:18px; font-weight:800; color:#1c1b20; }
    .modal-close-btn { background:none; border:none; cursor:pointer; font-size:22px; color:#797582; padding:2px 6px; border-radius:8px; line-height:1; }
    .modal-close-btn:hover { background:#f3eeff; color:#6351a7; }
    .modal-body { padding:20px 24px; display:flex; flex-direction:column; gap:16px; }

    .form-group { display:flex; flex-direction:column; gap:6px; }
    .form-label { font-size:13px; font-weight:600; color:#1c1b20; }
    .form-input, .form-textarea, .form-select { padding:10px 14px; border:1.5px solid #cac4d3; border-radius:12px; font-family:inherit; font-size:14px; color:#1c1b20; background:#fdf7ff; outline:none; transition:border-color .15s; width:100%; box-sizing:border-box; }
    .form-input:focus, .form-textarea:focus, .form-select:focus { border-color:#6351a7; background:#fff; }
    .form-textarea { resize:vertical; min-height:80px; }
    .form-row { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
    .form-error { font-size:12px; color:#ba1a1a; margin-top:2px; display:block; min-height:16px; }

    .subtask-list { display:flex; flex-direction:column; gap:8px; }
    .subtask-input-row { display:flex; gap:8px; align-items:center; }
    .subtask-input-row .form-input { flex:1; }
    .remove-subtask-btn { background:none; border:none; color:#ba1a1a; font-size:20px; cursor:pointer; padding:0 6px; border-radius:6px; line-height:1; flex-shrink:0; }
    .remove-subtask-btn:hover { background:#fde8e8; }
    .add-subtask-btn { background:none; border:1.5px dashed #cac4d3; border-radius:10px; padding:8px 14px; font-family:inherit; font-size:13px; font-weight:600; color:#797582; cursor:pointer; width:100%; }
    .add-subtask-btn:hover { border-color:#6351a7; color:#6351a7; }

    .modal-footer { display:flex; gap:10px; justify-content:flex-end; padding:16px 24px 22px; border-top:1px solid #f0eaf8; }
    .btn-cancel { padding:10px 20px; border-radius:12px; border:1.5px solid #cac4d3; background:none; font-family:inherit; font-size:14px; font-weight:600; color:#797582; cursor:pointer; }
    .btn-cancel:hover { background:#f3f0f7; }
    .btn-save { padding:10px 24px; border-radius:12px; border:none; background:#6351a7; font-family:inherit; font-size:14px; font-weight:700; color:#fff; cursor:pointer; box-shadow:0 4px 14px rgba(99,81,167,.3); transition:background .2s; }
    .btn-save:hover { background:#5240a0; }
    .btn-delete-confirm { padding:10px 24px; border-radius:12px; border:none; background:#ba1a1a; font-family:inherit; font-size:14px; font-weight:700; color:#fff; cursor:pointer; transition:background .2s; }
    .btn-delete-confirm:hover { background:#a01515; }

    /* ── progress modal ── */
    .progress-modal-box { max-width:460px; }
    .progress-hero { text-align:center; padding:20px 0 8px; }
    .progress-hero-pct { font-size:48px; font-weight:800; color:#6351a7; line-height:1; }
    .progress-hero-label { font-size:13px; color:#797582; margin-top:4px; }
    .progress-bar-big-wrap { background:#ede9ff; border-radius:100px; height:14px; overflow:hidden; margin:16px 0; }
    .progress-bar-big-fill { height:100%; border-radius:100px; background:#006a61; transition:width .5s ease; }

    .subtask-check-list { display:flex; flex-direction:column; gap:8px; margin-top:4px; }
    .subtask-check-item { display:flex; align-items:center; gap:12px; padding:10px 14px; border-radius:14px; background:#fdf7ff; border:1px solid #ede9ff; transition:background .15s; cursor:pointer; user-select:none; }
    .subtask-check-item:hover { background:#f3eeff; }
    .subtask-check-item.done-item { background:#f5fffe; border-color:#d0f5f3; }

    .subtask-check-box { width:22px; height:22px; border-radius:50%; border:2px solid #cac4d3; flex-shrink:0; display:flex; align-items:center; justify-content:center; transition:border-color .15s,background .15s; }
    .subtask-check-box.checked { background:#006a61; border-color:#006a61; }
    .subtask-check-box.checked::after { content:''; width:5px; height:9px; border:2px solid #fff; border-top:none; border-left:none; transform:rotate(45deg) translateY(-1px); display:block; }

    .subtask-check-label { font-size:14px; font-weight:600; color:#1c1b20; flex:1; transition:color .15s; }
    .subtask-check-label.done-label { text-decoration:line-through; color:#797582; font-weight:400; }

    .no-subtask-hint { text-align:center; padding:24px; font-size:14px; color:#797582; }

    /* loading spinner */
    .btn-loading { opacity:.6; pointer-events:none; }

    /* ── Responsive ── */
    @media (max-width: 1024px) {
        .kanban-board { display: flex; overflow-x: auto; padding-bottom: 24px; scroll-snap-type: x mandatory; }
        .kanban-col { flex: 0 0 320px; scroll-snap-align: start; }
    }
    @media (max-width: 640px) {
        .board-header { flex-direction: column; align-items: stretch; }
        .board-header-right { flex-direction: column; align-items: stretch; }
        .board-tabs { justify-content: space-between; overflow-x: auto; }
        .kanban-col { flex: 0 0 85vw; }
    }
</style>
@endpush

@section('content')

<div class="board-header">
    <h1 class="board-title">📋 Task Board</h1>
    <div class="board-header-right">
        <div class="board-tabs">
            <button class="board-tab active">My Tasks</button>
            <button class="board-tab">Team</button>
        </div>
        <select class="sort-select" id="sortSelect">
            <option value="priority">Sort: Priority</option>
            <option value="due_date">Sort: Deadline</option>
            <option value="title">Sort: A–Z</option>
        </select>

            <a href="{{ route('time-allocation.index') }}" class="btn-alloc-time">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
             stroke-linecap="round" stroke-linejoin="round" style="width:14px;height:14px">
            <line x1="18" y1="20" x2="18" y2="10"/>
            <line x1="12" y1="20" x2="12" y2="4"/>
            <line x1="6" y1="20" x2="6" y2="14"/>
        </svg>
        Alokasi Waktu
    </a>

        <button class="fab-new-task" id="openAddModalBtn">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:15px;height:15px"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            New Task
        </button>
    </div>
</div>

<div class="kanban-board">

    {{-- TO DO --}}
    <div class="kanban-col col-todo" data-column="todo"
         ondragover="handleDragOver(event)" ondrop="handleDrop(event,'todo')" ondragleave="handleDragLeave(event)">
        <div class="col-header">
            <span class="col-dot todo"></span>
            <span class="col-title">To Do</span>
            <span class="col-count">{{ $columns['todo']->count() }}</span>
            <button class="col-menu-btn">⋯</button>
        </div>
        <div class="col-cards">
            @foreach($columns['todo'] as $task)
                @include('tasks._card', ['task' => $task])
            @endforeach
        </div>
        <button class="add-task-btn" onclick="openAddModal('todo')">+ Add Task</button>
    </div>

    {{-- IN PROGRESS --}}
    <div class="kanban-col col-inprogress" data-column="in_progress"
         ondragover="handleDragOver(event)" ondrop="handleDrop(event,'in_progress')" ondragleave="handleDragLeave(event)">
        <div class="col-header">
            <span class="col-dot inprogress"></span>
            <span class="col-title">In Progress</span>
            <span class="col-count">{{ $columns['in_progress']->count() }}</span>
            <button class="col-menu-btn">⋯</button>
        </div>
        <div class="col-cards">
            @foreach($columns['in_progress'] as $task)
                @include('tasks._card', ['task' => $task])
            @endforeach
        </div>
        <button class="add-task-btn" onclick="openAddModal('in_progress')">+ Add Task</button>
    </div>

    {{-- DONE --}}
    <div class="kanban-col col-done" data-column="done"
         ondragover="handleDragOver(event)" ondrop="handleDrop(event,'done')" ondragleave="handleDragLeave(event)">
        <div class="col-header">
            <span class="col-dot done"></span>
            <span class="col-title">Done</span>
            <span class="col-count">{{ $columns['done']->count() }}</span>
            <button class="col-menu-btn">⋯</button>
        </div>
        <div class="col-cards">
            @foreach($columns['done'] as $task)
                @include('tasks._card', ['task' => $task])
            @endforeach
        </div>
        <button class="add-task-btn" onclick="openAddModal('done')">+ Add Task</button>
    </div>

</div>

{{-- ══════════════════════════════════════
     MODAL: Add Task
══════════════════════════════════════ --}}
<div class="modal-overlay" id="taskModal">
    <div class="modal-box">
        <div class="modal-header">
            <span class="modal-title">✨ Tambah Task Baru</span>
            <button class="modal-close-btn" type="button" onclick="closeModal('taskModal')">×</button>
        </div>
        <form id="taskForm" novalidate>
            @csrf
            <input type="hidden" name="status" id="modalStatus" value="todo">
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Judul Task *</label>
                    <input type="text" name="title" id="addTitle" class="form-input" placeholder="e.g. Review design system v2">
                    <span class="form-error" id="err-title"></span>
                </div>
                <div class="form-group">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="description" class="form-textarea" placeholder="Tambahkan deskripsi singkat..."></textarea>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Kategori</label>
                        <select name="category" class="form-select">
                            <option value="">Pilih kategori</option>
                            <option value="Design">Design</option>
                            <option value="Development">Development</option>
                            <option value="Planning">Planning</option>
                            <option value="Personal">Personal</option>
                            <option value="Meeting">Meeting</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Prioritas *</label>
                        <select name="priority" class="form-select">
                            <option value="">Pilih prioritas</option>
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                        </select>
                        <span class="form-error" id="err-priority"></span>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Deadline</label>
                        <input type="date" name="due_date" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Reminder</label>
                        <input type="datetime-local" name="reminder_at" class="form-input">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Subtasks</label>
                    <div class="subtask-list" id="addSubtaskList"></div>
                    <button type="button" class="add-subtask-btn" onclick="addSubtaskRow('addSubtaskList')" style="margin-top:8px">
                        + Tambah Subtask
                    </button>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeModal('taskModal')">Batal</button>
                <button type="submit" class="btn-save" id="addSaveBtn">💾 Simpan Task</button>
            </div>
        </form>
    </div>
</div>

{{-- ══════════════════════════════════════
     MODAL: Edit Task
══════════════════════════════════════ --}}
<div class="modal-overlay" id="editModal">
    <div class="modal-box">
        <div class="modal-header">
            <span class="modal-title">✏️ Edit Task</span>
            <button class="modal-close-btn" type="button" onclick="closeModal('editModal')">×</button>
        </div>
        <form id="editForm" novalidate>
            <input type="hidden" id="editTaskId">
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Judul Task *</label>
                    <input type="text" id="editTitle" class="form-input">
                    <span class="form-error" id="err-edit-title"></span>
                </div>
                <div class="form-group">
                    <label class="form-label">Deskripsi</label>
                    <textarea id="editDescription" class="form-textarea"></textarea>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Kategori</label>
                        <select id="editCategory" class="form-select">
                            <option value="">Pilih kategori</option>
                            <option value="Design">Design</option>
                            <option value="Development">Development</option>
                            <option value="Planning">Planning</option>
                            <option value="Personal">Personal</option>
                            <option value="Meeting">Meeting</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Prioritas</label>
                        <select id="editPriority" class="form-select">
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select id="editStatus" class="form-select">
                            <option value="todo">To Do</option>
                            <option value="in_progress">In Progress</option>
                            <option value="done">Done</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Deadline</label>
                        <input type="date" id="editDueDate" class="form-input">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Subtasks</label>
                    <div class="subtask-list" id="editSubtaskList"></div>
                    <button type="button" class="add-subtask-btn" onclick="addSubtaskRow('editSubtaskList')" style="margin-top:8px">
                        + Tambah Subtask
                    </button>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeModal('editModal')">Batal</button>
                <button type="submit" class="btn-save" id="editSaveBtn">💾 Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

{{-- ══════════════════════════════════════
     MODAL: Delete Task
══════════════════════════════════════ --}}
<div class="modal-overlay" id="deleteModal">
    <div class="modal-box" style="max-width:400px">
        <div class="modal-header">
            <span class="modal-title" style="color:#ba1a1a">🗑 Hapus Task</span>
            <button class="modal-close-btn" type="button" onclick="closeModal('deleteModal')">×</button>
        </div>
        <div class="modal-body" style="gap:12px">
            <p style="font-size:14px;color:#1c1b20">Yakin ingin menghapus task ini?</p>
            <p style="font-size:13px;color:#797582;font-weight:600" id="deleteTaskTitle"></p>
            <p style="font-size:12px;color:#ba1a1a;background:#ffedea;padding:10px 14px;border-radius:10px">
                ⚠ Task akan dipindahkan ke trash dan bisa dipulihkan.
            </p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-cancel" onclick="closeModal('deleteModal')">Batal</button>
            <button type="button" class="btn-delete-confirm" id="confirmDeleteBtn">🗑 Ya, Hapus</button>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════
     MODAL: Progress / Subtasks
══════════════════════════════════════ --}}
<div class="modal-overlay" id="progressModal">
    <div class="modal-box progress-modal-box">
        <div class="modal-header">
            <span class="modal-title" id="progressModalTitle">📊 Progres Task</span>
            <button class="modal-close-btn" type="button" onclick="closeModal('progressModal')">×</button>
        </div>
        <div class="modal-body">
            <div class="progress-hero">
                <div class="progress-hero-pct" id="progressHeroPct">0%</div>
                <div class="progress-hero-label" id="progressHeroLabel">0 dari 0 subtask selesai</div>
            </div>
            <div class="progress-bar-big-wrap">
                <div class="progress-bar-big-fill" id="progressBarBig" style="width:0%"></div>
            </div>
            <div class="subtask-check-list" id="subtaskCheckList"></div>
        </div>
    </div>
</div>


{{-- MODAL: Timer History --}}
<div class="modal-overlay" id="timerHistoryModal">
    <div class="modal-box" style="max-width:460px">
        <div class="modal-header">
            <span class="modal-title" id="timerHistoryTitle">⏱ Riwayat Timer</span>
            <button class="modal-close-btn" type="button" onclick="closeModal('timerHistoryModal')">×</button>
        </div>
        <div class="modal-body" style="gap:12px">
            <div style="text-align:center;padding:10px 0 4px">
                <div id="timerHistoryTotal"
                     style="font-size:38px;font-weight:800;color:#6351a7;line-height:1"></div>
                <div style="font-size:12px;color:#797582;margin-top:4px">total waktu fokus</div>
            </div>
            <div id="timerHistoryList"
                 style="display:flex;flex-direction:column;gap:8px;
                        max-height:320px;overflow-y:auto"></div>
        </div>
        <div class="modal-footer" style="justify-content:space-between">
            <a href="{{ route('time-allocation.index') }}"
               style="font-size:13px;font-weight:600;color:#6351a7;text-decoration:none;
                      display:flex;align-items:center;gap:5px">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                     stroke-linecap="round" stroke-linejoin="round" style="width:13px;height:13px">
                    <line x1="18" y1="20" x2="18" y2="10"/>
                    <line x1="12" y1="20" x2="12" y2="4"/>
                    <line x1="6" y1="20" x2="6" y2="14"/>
                </svg>
                Lihat Semua Alokasi Waktu →
            </a>
            <button type="button" class="btn-cancel"
                    onclick="closeModal('timerHistoryModal')">Tutup</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>

//constants
const CSRF         = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
const USER_INITIALS = '{{ auth()->user()->initials ?? "?" }}';

//modal helpers
function openModal(id) {
    document.getElementById(id).classList.add('open');
    document.body.style.overflow = 'hidden';
}

function closeModal(id) {
    document.getElementById(id).classList.remove('open');
    document.body.style.overflow = '';
}

// Close on backdrop click
['taskModal', 'editModal', 'deleteModal', 'progressModal'].forEach(id => {
    document.getElementById(id).addEventListener('click', function(e) {
        if (e.target === this) closeModal(id);
    });
});

// Close on Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        ['taskModal', 'editModal', 'deleteModal', 'progressModal', 'timerHistoryModal'].forEach(closeModal);
    }
});

//subtask row helper
let subtaskCounter = 0;

function addSubtaskRow(listId, title = '', done = false) {
    const list  = document.getElementById(listId);
    const idx   = subtaskCounter++;
    const row   = document.createElement('div');
    row.className = 'subtask-input-row';
    row.dataset.subtaskIdx = idx;
    row.innerHTML = `
        <input type="text" class="form-input subtask-title-input"
               placeholder="Nama subtask..." value="${escHtml(title)}">
        <input type="hidden" class="subtask-done-input" value="${done ? '1' : '0'}">
        <button type="button" class="remove-subtask-btn" onclick="removeSubtaskRow(this)">×</button>`;
    list.appendChild(row);
    if (!title) row.querySelector('input').focus();
}

function removeSubtaskRow(btn) {
    const list = btn.closest('.subtask-list');
    btn.closest('.subtask-input-row').remove();
}

/** Collect subtasks from a list element into an array [{title, done}] */
function collectSubtasks(listId) {
    const list = document.getElementById(listId);
    const result = [];
    list.querySelectorAll('.subtask-input-row').forEach(row => {
        const title = row.querySelector('.subtask-title-input')?.value.trim() || '';
        const done  = row.querySelector('.subtask-done-input')?.value === '1';
        if (title) result.push({ title, done });
    });
    return result;
}

//add task modal
function openAddModal(status) {
    // Reset form
    document.getElementById('taskForm').reset();
    document.getElementById('modalStatus').value = status || 'todo';
    document.getElementById('addSubtaskList').innerHTML = '';
    document.getElementById('err-title').textContent = '';
    document.getElementById('err-priority').textContent = '';
    // Start with one empty subtask row
    addSubtaskRow('addSubtaskList');
    openModal('taskModal');
}

document.getElementById('openAddModalBtn').addEventListener('click', function() {
    openAddModal('todo');
});

document.getElementById('taskForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const btn = document.getElementById('addSaveBtn');
    btn.classList.add('btn-loading');
    btn.textContent = 'Menyimpan...';
    document.getElementById('err-title').textContent    = '';
    document.getElementById('err-priority').textContent = '';

    // Build payload as JSON (more reliable than FormData for nested arrays)
    const payload = {
        _token:      CSRF,
        title:       this.querySelector('[name="title"]').value.trim(),
        description: this.querySelector('[name="description"]').value.trim(),
        category:    this.querySelector('[name="category"]').value,
        priority:    this.querySelector('[name="priority"]').value,
        status:      document.getElementById('modalStatus').value,
        due_date:    this.querySelector('[name="due_date"]').value     || null,
        reminder_at: this.querySelector('[name="reminder_at"]').value  || null,
        subtasks:    collectSubtasks('addSubtaskList'),
    };

    // Client-side validation
    if (!payload.title) {
        document.getElementById('err-title').textContent = 'Judul wajib diisi.';
        btn.classList.remove('btn-loading');
        btn.textContent = '💾 Simpan Task';
        return;
    }
    if (!payload.priority) {
        document.getElementById('err-priority').textContent = 'Prioritas wajib dipilih.';
        btn.classList.remove('btn-loading');
        btn.textContent = '💾 Simpan Task';
        return;
    }

    try {
        const res  = await fetch('{{ route("tasks.store") }}', {
            method:  'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'Accept':       'application/json',
            },
            body: JSON.stringify(payload),
        });

        const json = await res.json();

        if (!res.ok) {
            if (json.errors) {
                Object.entries(json.errors).forEach(([k, v]) => {
                    const el = document.getElementById('err-' + k);
                    if (el) el.textContent = v[0];
                });
            }
            return;
        }

        closeModal('taskModal');
        appendCardToBoard(json.task);

    } catch (err) {
        console.error('Store task error:', err);
        alert('Gagal menyimpan task. Coba lagi.');
    } finally {
        btn.classList.remove('btn-loading');
        btn.textContent = '💾 Simpan Task';
    }
});

//edit task modal
function openEditModal(btn) {
    const card = btn.closest('.task-card');
    const task = getTaskData(card);

    document.getElementById('editTaskId').value       = task.id;
    document.getElementById('editTitle').value        = task.title       || '';
    document.getElementById('editDescription').value  = task.description || '';
    document.getElementById('editCategory').value     = task.category    || '';
    document.getElementById('editPriority').value     = task.priority    || 'medium';
    document.getElementById('editStatus').value       = task.status      || 'todo';
    document.getElementById('editDueDate').value      = task.due_date    || '';
    document.getElementById('err-edit-title').textContent = '';

    // Rebuild subtask list
    const list = document.getElementById('editSubtaskList');
    list.innerHTML = '';
    const subtasks = Array.isArray(task.subtasks) ? task.subtasks : [];
    if (subtasks.length) {
        subtasks.forEach(s => addSubtaskRow('editSubtaskList', s.title || '', !!s.done));
    } else {
        addSubtaskRow('editSubtaskList');
    }

    openModal('editModal');
}

document.getElementById('editForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const taskId = document.getElementById('editTaskId').value;
    const btn    = document.getElementById('editSaveBtn');
    btn.classList.add('btn-loading');
    btn.textContent = 'Menyimpan...';
    document.getElementById('err-edit-title').textContent = '';

    const payload = {
        title:       document.getElementById('editTitle').value.trim(),
        description: document.getElementById('editDescription').value.trim(),
        category:    document.getElementById('editCategory').value,
        priority:    document.getElementById('editPriority').value,
        status:      document.getElementById('editStatus').value,
        due_date:    document.getElementById('editDueDate').value || null,
        subtasks:    collectSubtasks('editSubtaskList'),
    };

    if (!payload.title) {
        document.getElementById('err-edit-title').textContent = 'Judul wajib diisi.';
        btn.classList.remove('btn-loading');
        btn.textContent = '💾 Simpan Perubahan';
        return;
    }

    try {
        const res  = await fetch(`/tasks/${taskId}`, {
            method:  'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'Accept':       'application/json',
            },
            body: JSON.stringify(payload),
        });

        const json = await res.json();

        if (!res.ok) {
            if (json.errors) {
                Object.entries(json.errors).forEach(([k, v]) => {
                    const el = document.getElementById('err-edit-' + k);
                    if (el) el.textContent = v[0];
                });
            }
            return;
        }

        closeModal('editModal');
        // Sync card in-place without full reload
        syncCardAfterUpdate(json.task);

    } catch (err) {
        console.error('Update task error:', err);
        alert('Gagal menyimpan perubahan. Coba lagi.');
    } finally {
        btn.classList.remove('btn-loading');
        btn.textContent = '💾 Simpan Perubahan';
    }
});

//delete task
let pendingDeleteId = null;

function deleteTask(taskId) {
    pendingDeleteId = taskId;
    const card  = document.querySelector(`.task-card[data-task-id="${taskId}"]`);
    const title = card ? card.querySelector('.card-title')?.textContent.trim() : '';
    document.getElementById('deleteTaskTitle').textContent = `"${title}"`;
    openModal('deleteModal');
}

document.getElementById('confirmDeleteBtn').addEventListener('click', async function() {
    if (!pendingDeleteId) return;

    this.classList.add('btn-loading');
    this.textContent = 'Menghapus...';

    try {
        const res = await fetch(`/tasks/${pendingDeleteId}`, {
            method:  'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        });

        if (res.ok) {
            const card = document.querySelector(`.task-card[data-task-id="${pendingDeleteId}"]`);
            if (card) {
                card.style.transition = 'opacity .2s, transform .2s';
                card.style.opacity = '0';
                card.style.transform = 'scale(.95)';
                setTimeout(() => { card.remove(); updateColCounts(); }, 200);
            }
            closeModal('deleteModal');
        } else {
            alert('Gagal menghapus task.');
        }
    } catch (err) {
        console.error('Delete task error:', err);
        alert('Terjadi kesalahan.');
    } finally {
        this.classList.remove('btn-loading');
        this.textContent = '🗑 Ya, Hapus';
        pendingDeleteId = null;
    }
});

//progress modal
let progressTaskId = null;
let progressSubtasks = [];

function openProgressModal(btn) {
    const card = btn.closest('.task-card');
    const task = getTaskData(card);

    progressTaskId  = task.id;
    progressSubtasks = Array.isArray(task.subtasks) ? JSON.parse(JSON.stringify(task.subtasks)) : [];

    document.getElementById('progressModalTitle').textContent = task.title;
    renderProgressModal();
    openModal('progressModal');
}

function renderProgressModal() {
    const total = progressSubtasks.length;
    const done  = progressSubtasks.filter(s => s.done).length;
    const pct   = total ? Math.round((done / total) * 100) : 0;

    document.getElementById('progressHeroPct').textContent   = pct + '%';
    document.getElementById('progressHeroLabel').textContent = `${done} dari ${total} subtask selesai`;
    document.getElementById('progressBarBig').style.width    = pct + '%';

    const list = document.getElementById('subtaskCheckList');
    list.innerHTML = '';

    if (!total) {
        list.innerHTML = '<div class="no-subtask-hint">📝 Belum ada subtask. Edit task untuk menambahkan.</div>';
        return;
    }

    progressSubtasks.forEach((s, idx) => {
        const item = document.createElement('div');
        item.className = `subtask-check-item ${s.done ? 'done-item' : ''}`;
        item.innerHTML = `
            <div class="subtask-check-box ${s.done ? 'checked' : ''}"></div>
            <span class="subtask-check-label ${s.done ? 'done-label' : ''}">${escHtml(s.title || '')}</span>`;
        item.addEventListener('click', () => toggleSubtaskInModal(idx));
        list.appendChild(item);
    });
}

async function toggleSubtaskInModal(idx) {
    if (!progressTaskId) return;

    // Optimistic update in local state
    progressSubtasks[idx].done = !progressSubtasks[idx].done;
    renderProgressModal();

    // Update card's data-task attribute
    const card = document.querySelector(`.task-card[data-task-id="${progressTaskId}"]`);
    if (card) {
        const taskData = getTaskData(card);
        if (Array.isArray(taskData.subtasks) && taskData.subtasks[idx] !== undefined) {
            taskData.subtasks[idx].done = progressSubtasks[idx].done;
        }
        setTaskData(card, taskData);

        // Update progress bar on the card
        updateCardProgressBar(card, progressSubtasks);
    }

    // Persist to server
    try {
        const res = await fetch(`/tasks/${progressTaskId}/subtasks/${idx}`, {
            method:  'PATCH',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        });
        if (!res.ok) {
            // Rollback optimistic update
            progressSubtasks[idx].done = !progressSubtasks[idx].done;
            renderProgressModal();
            if (card) {
                const taskData = getTaskData(card);
                if (Array.isArray(taskData.subtasks) && taskData.subtasks[idx] !== undefined) {
                    taskData.subtasks[idx].done = progressSubtasks[idx].done;
                }
                setTaskData(card, taskData);
                updateCardProgressBar(card, progressSubtasks);
            }
        }
    } catch (err) {
        console.error('Toggle subtask error:', err);
    }
}

function updateCardProgressBar(card, subtasks) {
    const total = subtasks.length;
    const done  = subtasks.filter(s => s.done).length;
    const pct   = total ? Math.round((done / total) * 100) : 0;

    const fill  = card.querySelector('.card-progress-fill');
    const label = card.querySelector('.card-progress-pct');
    if (fill)  fill.style.width    = pct + '%';
    if (label) label.textContent   = pct + '%';
}

//drag and drop
let draggedCard = null;

function handleDragStart(e) {
    draggedCard = e.currentTarget;
    e.dataTransfer.effectAllowed = 'move';
    e.dataTransfer.setData('text/plain', draggedCard.dataset.taskId);
    setTimeout(() => { if (draggedCard) draggedCard.style.opacity = '0.4'; }, 0);
}

function handleDragOver(e) {
    e.preventDefault();
    e.dataTransfer.dropEffect = 'move';
    e.currentTarget.classList.add('drag-over');
}

function handleDragLeave(e) {
    // Only remove if leaving the column itself (not a child)
    if (!e.currentTarget.contains(e.relatedTarget)) {
        e.currentTarget.classList.remove('drag-over');
    }
}

async function handleDrop(e, newStatus) {
    e.preventDefault();
    const col = e.currentTarget;
    col.classList.remove('drag-over');
    if (!draggedCard) return;

    const taskId     = draggedCard.dataset.taskId;
    const oldStatus  = draggedCard.closest('.kanban-col')?.dataset.column;

    // Move card in DOM
    col.querySelector('.col-cards').appendChild(draggedCard);
    draggedCard.style.opacity = '1';

    // Update title style
    const titleEl = draggedCard.querySelector('.card-title');
    if (newStatus === 'done') titleEl.classList.add('strikethrough');
    else                      titleEl.classList.remove('strikethrough');

    // Update local data-task
    const taskData = getTaskData(draggedCard);
    taskData.status = newStatus;
    setTaskData(draggedCard, taskData);

    draggedCard = null;
    updateColCounts();

    // Persist
    try {
        await fetch(`/tasks/${taskId}/move`, {
            method:  'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
            },
            body: JSON.stringify({ status: newStatus }),
        });
    } catch (err) {
        console.error('Move task error:', err);
    }
}

// Init drag on existing cards
document.querySelectorAll('.task-card').forEach(card => {
    card.addEventListener('dragstart', handleDragStart);
    card.addEventListener('dragend', function() {
        this.style.opacity = '1';
        draggedCard = null;
        document.querySelectorAll('.kanban-col').forEach(c => c.classList.remove('drag-over'));
    });
});

//timer
// Start timer button (delegated, works for dynamically appended cards)
document.addEventListener('click', async function(e) {
    const startBtn = e.target.closest('.btn-start-timer');
    if (!startBtn) return;
    e.stopPropagation();

    const card   = startBtn.closest('.task-card');
    if (!card) return;
    const taskId = card.dataset.taskId;

    startBtn.disabled    = true;
    startBtn.textContent = 'Starting...';

    try {
        const res  = await fetch(`/tasks/${taskId}/timer/start`, {
            method:  'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        });

        if (res.ok) {
            const json = await res.json();
            // Replace the start-timer button with a live timer badge
            const timerStarted = json.timer_started_at;
            const focusBase    = parseInt(json.focus_minutes || 0);

            const badge = document.createElement('div');
            badge.className = 'timer-badge';
           const taskData   = getTaskData(card);
const baseSec    = parseInt(taskData.total_seconds || 0);
badge.innerHTML = `
    <button class="timer-stop-btn" title="Stop timer" data-task-id="${taskId}"></button>
    <span class="timer-display"
          data-timer-start="${timerStarted}"
          data-timer-base-seconds="${baseSec}">00:00:00</span>`;
            startBtn.replaceWith(badge);

            taskData.status          = 'in_progress';
            taskData.timer_started_at = timerStarted;
            setTaskData(card, taskData);

            // Move card to in_progress if not already
            const inProgressCol = document.querySelector('.kanban-col[data-column="in_progress"] .col-cards');
            if (inProgressCol && card.closest('.kanban-col')?.dataset.column !== 'in_progress') {
                inProgressCol.appendChild(card);
                updateColCounts();
            }

            // Start ticking
            startTickingTimer(badge.querySelector('[data-timer-start]'));
        }
    } catch (err) {
        console.error('Start timer error:', err);
        startBtn.disabled    = false;
        startBtn.textContent = '▶ Start timer';
    }
});

// Stop timer button (delegated)
document.addEventListener('click', async function(e) {
    const stopBtn = e.target.closest('.timer-stop-btn');
    if (!stopBtn) return;
    e.stopPropagation();

    const card   = stopBtn.closest('.task-card');
    if (!card) return;
    const taskId = card.dataset.taskId;

    stopBtn.style.opacity = '0.5';

    try {
        const res  = await fetch(`/tasks/${taskId}/timer/stop`, {
            method:  'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        });

        if (res.ok) {
            const json = await res.json();
            const badge = stopBtn.closest('.timer-badge');

            // Replace badge with start-timer button
            const newBtn = document.createElement('button');
            newBtn.className   = 'btn-start-timer';
            newBtn.textContent = '▶ Start timer';
            badge.replaceWith(newBtn);

            // Update card data
            const taskData = getTaskData(card);
            taskData.timer_started_at = null;
            taskData.focus_minutes    = json.focus_minutes;
            setTaskData(card, taskData);
        }
    } catch (err) {
        console.error('Stop timer error:', err);
        stopBtn.style.opacity = '1';
    }
});

/** Init ticking for a [data-timer-start] span */
function startTickingTimer(el) {
    if (!el) return;
    const started   = new Date(el.dataset.timerStart).getTime();
    // Ganti dari: const baseMin = parseInt(el.dataset.timerBase || '0');
    const baseSec   = parseInt(el.dataset.timerBaseSeconds || '0');

    function tick() {
        if (!document.contains(el)) return;
        // Ganti dari: const elapsed = baseMin * 60 + Math.floor(...)
        const elapsed = baseSec + Math.floor((Date.now() - started) / 1000);
        const h = String(Math.floor(elapsed / 3600)).padStart(2, '0');
        const m = String(Math.floor((elapsed % 3600) / 60)).padStart(2, '0');
        const s = String(elapsed % 60).padStart(2, '0');
        el.textContent = `${h}:${m}:${s}`;
        requestAnimationFrame(() => setTimeout(tick, 1000));
    }
    tick();
}

// Boot ticking timers for server-rendered cards
document.querySelectorAll('[data-timer-start]').forEach(startTickingTimer);

//card dom helpers 

/** Safely read task data from a card element */
function getTaskData(card) {
    try { return JSON.parse(card.dataset.task || '{}'); }
    catch { return {}; }
}

/** Safely write task data to a card element */
function setTaskData(card, data) {
    card.dataset.task = JSON.stringify(data);
}

/** Append a freshly-created task as a card on the board */
function appendCardToBoard(task) {
    const col = document.querySelector(`.kanban-col[data-column="${task.status}"] .col-cards`);
    if (!col) return;

    const card = document.createElement('div');
    card.className          = 'task-card';
    card.draggable          = true;
    card.dataset.taskId     = task.id;
    setTaskData(card, task);

    const catKey = (task.category || '').toLowerCase();
    const catMap = { design:'chip-design', development:'chip-dev', planning:'chip-planning', personal:'chip-personal', meeting:'chip-meeting' };
    const chips  = [];
    if (task.category) chips.push(`<span class="chip ${catMap[catKey] || 'chip-design'}">${escHtml(task.category)}</span>`);
    if (task.priority)  chips.push(`<span class="chip chip-${task.priority}">${ucfirst(task.priority)}</span>`);

    const subtasks = Array.isArray(task.subtasks) ? task.subtasks : [];
    const total    = subtasks.length;
    const done     = subtasks.filter(s => s.done).length;
    const pct      = total ? Math.round((done / total) * 100) : 0;
    const progress = total ? `
        <div class="card-progress-label">
            <span>Progress</span>
            <span class="card-progress-pct">${pct}%</span>
        </div>
        <div class="card-progress-wrap">
            <div class="card-progress-fill" style="width:${pct}%"></div>
        </div>` : '';

    const due = task.due_date
        ? `<span class="card-sub">📅 ${task.due_date}</span>`
        : `<span class="card-sub"></span>`;

    card.innerHTML = `
        <div class="card-actions">
            <button class="card-action-btn" title="Lihat Progres"
                    onclick="event.stopPropagation(); openProgressModal(this)">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                </svg>
            </button>
            <button class="card-action-btn" title="Edit Task"
                    onclick="event.stopPropagation(); openEditModal(this)">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                </svg>
            </button>
            <button class="card-action-btn danger" title="Hapus Task"
                    onclick="event.stopPropagation(); deleteTask(${task.id})">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="3 6 5 6 21 6"/>
                    <path d="M19 6l-1 14H6L5 6"/>
                    <path d="M10 11v6"/><path d="M14 11v6"/>
                    <path d="M9 6V4h6v2"/>
                </svg>
            </button>
        </div>
        <div class="card-chips">${chips.join('')}</div>
        <div class="card-title">${escHtml(task.title)}</div>
        ${progress}
        <div class="card-meta">
            ${due}
            <button class="btn-start-timer">▶ Start timer</button>
            <div class="avatar me">${USER_INITIALS}</div>
        </div>`;

    card.addEventListener('dragstart', handleDragStart);
    card.addEventListener('dragend', function() {
        this.style.opacity = '1';
        draggedCard = null;
        document.querySelectorAll('.kanban-col').forEach(c => c.classList.remove('drag-over'));
    });

    col.appendChild(card);
    updateColCounts();
}

/** Update an existing card in-place after edit */
function syncCardAfterUpdate(task) {
    const card = document.querySelector(`.task-card[data-task-id="${task.id}"]`);
    if (!card) { location.reload(); return; }

    // Update stored data
    setTaskData(card, task);

    // Title
    const titleEl = card.querySelector('.card-title');
    if (titleEl) {
        titleEl.textContent = task.title;
        titleEl.classList.toggle('strikethrough', task.status === 'done');
    }

    // Chips
    const catKey = (task.category || '').toLowerCase();
    const catMap = { design:'chip-design', development:'chip-dev', planning:'chip-planning', personal:'chip-personal', meeting:'chip-meeting' };
    const chips  = [];
    if (task.category) chips.push(`<span class="chip ${catMap[catKey] || 'chip-design'}">${escHtml(task.category)}</span>`);
    if (task.priority)  chips.push(`<span class="chip chip-${task.priority}">${ucfirst(task.priority)}</span>`);
    const chipsEl = card.querySelector('.card-chips');
    if (chipsEl) chipsEl.innerHTML = chips.join('');

    // Progress bar
    const subtasks = Array.isArray(task.subtasks) ? task.subtasks : [];
    updateCardProgressBar(card, subtasks);

    // Move to correct column if status changed
    const currentCol = card.closest('.kanban-col');
    if (currentCol?.dataset.column !== task.status) {
        const newCol = document.querySelector(`.kanban-col[data-column="${task.status}"] .col-cards`);
        if (newCol) newCol.appendChild(card);
        updateColCounts();
    }
}

function updateColCounts() {
    document.querySelectorAll('.kanban-col').forEach(col => {
        const countEl = col.querySelector('.col-count');
        if (countEl) countEl.textContent = col.querySelectorAll('.task-card').length;
    });
}

function escHtml(s) {
    return String(s)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

function ucfirst(s) {
    if (!s) return '';
    return s.charAt(0).toUpperCase() + s.slice(1);
}

//timer history modal
document.getElementById('timerHistoryModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal('timerHistoryModal');
});

async function openTimerHistoryModal(btn) {
    const card   = btn.closest('.task-card');
    const taskId = card.dataset.taskId;
    const task   = getTaskData(card);

    document.getElementById('timerHistoryTitle').textContent = `⏱ ${task.title}`;
    document.getElementById('timerHistoryTotal').textContent = '…';
    document.getElementById('timerHistoryList').innerHTML    =
        '<div style="text-align:center;padding:20px;color:#797582;font-size:13px">Memuat...</div>';

    openModal('timerHistoryModal');

    try {
        const res  = await fetch(`/tasks/${taskId}/timer/history`, {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
        });
        const json = await res.json();

        // Format total
    const totalSec = (json.sessions || []).reduce(
    (sum, s) => sum + (s.duration_seconds || s.duration_minutes * 60 || 0),
    0
);
const th = Math.floor(totalSec / 3600);
const tm = Math.floor((totalSec % 3600) / 60);
const ts = totalSec % 60;
const unitStyle = "font-size:18px;font-weight:600;color:#797582";
document.getElementById('timerHistoryTotal').innerHTML =
    th > 0
    ? `${th}<span style="${unitStyle}">j</span> ${tm}<span style="${unitStyle}">m</span> ${ts}<span style="${unitStyle}">d</span>`
    : tm > 0
    ? `${tm}<span style="${unitStyle}">m</span> ${ts}<span style="${unitStyle}">d</span>`
    : `${ts}<span style="${unitStyle}">d</span>`;


        if (!json.sessions || json.sessions.length === 0) {
            document.getElementById('timerHistoryList').innerHTML =
                '<div style="text-align:center;padding:24px;font-size:13px;color:#797582">' +
                '📭 Belum ada sesi timer tercatat untuk task ini.</div>';
            return;
        }

        // Group by date
        const byDate = {};
        json.sessions.forEach(s => {
            if (!byDate[s.date_label]) byDate[s.date_label] = [];
            byDate[s.date_label].push(s);
        });

        let html = '';
        Object.entries(byDate).forEach(([date, sessions]) => {
        const dayTotalSec = sessions.reduce((sum, s) => sum + (s.duration_seconds || s.duration_minutes * 60 || 0), 0);
        const dh = Math.floor(dayTotalSec / 3600);
        const dm = Math.floor((dayTotalSec % 3600) / 60);
        const ds = dayTotalSec % 60;
        const dayFmt = dh > 0
            ? `${dh}j ${dm}m ${ds}d`
            : dm > 0
            ? `${dm}m ${ds}d`
            : `${ds}d`;

            html += `
            <div style="margin-bottom:4px">
                <div style="display:flex;justify-content:space-between;align-items:center;
                            padding:6px 10px;background:#f3eeff;border-radius:10px;margin-bottom:6px">
                    <span style="font-size:12px;font-weight:700;color:#6351a7">${date}</span>
                    <span style="font-size:12px;font-weight:700;color:#6351a7">${dayFmt}</span>
                </div>`;

            sessions.forEach(s => {
                html += `
                <div style="display:flex;justify-content:space-between;align-items:center;
                            padding:8px 12px;background:#fdf7ff;border:1px solid #ede9ff;
                            border-radius:10px;margin-bottom:6px">
                    <span style="font-size:12px;color:#797582">${s.time_label}</span>
                    <span style="font-size:14px;font-weight:800;color:#1c1b20">${s.formatted}</span>
                </div>`;
            });

            html += '</div>';
        });

        document.getElementById('timerHistoryList').innerHTML = html;

    } catch (err) {
        console.error('Timer history error:', err);
        document.getElementById('timerHistoryList').innerHTML =
            '<div style="text-align:center;padding:20px;color:#ba1a1a;font-size:13px">' +
            'Gagal memuat riwayat.</div>';
    }
}
</script>
@endpush

