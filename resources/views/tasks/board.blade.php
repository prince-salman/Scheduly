@extends('layouts.app')
@section('title', 'Task Board')

@push('styles')
<style>
    .board-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:24px; flex-wrap:wrap; gap:12px; }
    .board-title  { font-size:22px; font-weight:800; color:#1c1b20; }
    .board-header-right { display:flex; align-items:center; gap:10px; }

    .board-tabs { display:flex; align-items:center; gap:4px; background:#f3eeff; border-radius:12px; padding:4px; }
    .board-tab  { padding:8px 18px; border-radius:9px; font-size:13px; font-weight:600; color:#797582; cursor:pointer; border:none; background:none; transition:background .15s,color .15s; }
    .board-tab.active { background:#fff; color:#6351a7; box-shadow:0 2px 8px rgba(99,81,167,.15); }

    .sort-select { padding:8px 14px; border-radius:10px; border:1px solid #cac4d3; font-family:inherit; font-size:13px; font-weight:500; color:#1c1b20; background:#fff; cursor:pointer; outline:none; }
    .sort-select:focus { border-color:#6351a7; }

    .fab-new-task { display:flex; align-items:center; gap:6px; background:#6351a7; color:#fff; border:none; border-radius:14px; padding:10px 18px; font-family:inherit; font-size:14px; font-weight:600; cursor:pointer; box-shadow:0 4px 16px rgba(99,81,167,.3); transition:background .2s,transform .1s; }
    .fab-new-task:hover { background:#5240a0; }

    .kanban-board { display:grid; grid-template-columns:repeat(3,1fr); gap:18px; align-items:start; }
    .kanban-col   { background:#f6f1ff; border-radius:20px; padding:16px; min-height:400px; display:flex; flex-direction:column; }
    .kanban-col.col-inprogress { background:#f0fbfa; }
    .kanban-col.col-done       { background:#f5f5f0; }

    .col-header { display:flex; align-items:center; gap:8px; margin-bottom:14px; padding-bottom:12px; border-bottom:1px solid rgba(0,0,0,.06); }
    .col-dot    { width:10px; height:10px; border-radius:50%; flex-shrink:0; }
    .col-dot.todo       { background:#6351a7; }
    .col-dot.inprogress { background:#006a61; }
    .col-dot.done       { background:#797582; }
    .col-title  { font-size:14px; font-weight:700; color:#1c1b20; flex:1; }
    .col-count  { background:#fff; color:#797582; font-size:11px; font-weight:700; padding:2px 8px; border-radius:100px; border:1px solid #cac4d3; }
    .col-menu-btn { background:none; border:none; cursor:pointer; font-size:18px; color:#797582; padding:2px 4px; border-radius:6px; transition:background .15s; line-height:1; }
    .col-menu-btn:hover { background:rgba(0,0,0,.06); }

    .col-cards { display:flex; flex-direction:column; gap:12px; flex:1; min-height:60px; }

    .task-card { background:#fff; border-radius:18px; padding:16px; box-shadow:0 4px 14px rgba(181,162,255,.12); border:1px solid rgba(202,196,211,.4); cursor:grab; transition:box-shadow .2s,transform .15s; }
    .task-card:hover { box-shadow:0 8px 24px rgba(99,81,167,.18); transform:translateY(-2px); }
    .task-card:active { cursor:grabbing; transform:scale(.99); }

    .card-chips { display:flex; gap:6px; flex-wrap:wrap; margin-bottom:10px; }
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

    .card-meta { display:flex; align-items:center; justify-content:space-between; gap:8px; flex-wrap:wrap; }
    .card-sub  { font-size:12px; color:#797582; }
    .card-sub.green { color:#006a61; font-weight:600; }

    .avatar { width:28px; height:28px; border-radius:50%; background:#ede9ff; color:#6351a7; font-size:11px; font-weight:700; display:flex; align-items:center; justify-content:center; border:2px solid #fff; margin-left:-4px; flex-shrink:0; }
    .avatar:first-child { margin-left:0; }
    .avatar.me { background:#6351a7; color:#fff; }

    .timer-badge { display:flex; align-items:center; gap:6px; background:#fff0f0; border:1px solid #fcc; border-radius:100px; padding:4px 10px; }
    .timer-badge span { font-size:12px; font-weight:700; color:#ba1a1a; font-variant-numeric:tabular-nums; }
    .timer-stop-btn { width:18px; height:18px; border-radius:50%; background:#ba1a1a; border:none; cursor:pointer; display:flex; align-items:center; justify-content:center; }
    .timer-stop-btn::after { content:''; width:6px; height:6px; background:#fff; border-radius:1px; }

    .btn-start-timer { display:flex; align-items:center; gap:5px; background:#ede9ff; color:#6351a7; border:none; border-radius:8px; padding:5px 12px; font-family:inherit; font-size:12px; font-weight:600; cursor:pointer; transition:background .15s; }
    .btn-start-timer:hover { background:#ddd6ff; }

    .card-progress-wrap  { background:#f0f0f0; border-radius:100px; height:6px; margin:8px 0; overflow:hidden; }
    .card-progress-fill  { height:100%; border-radius:100px; background:#006a61; }
    .card-progress-label { display:flex; justify-content:space-between; font-size:11px; color:#797582; margin-bottom:10px; }

    .add-task-btn { display:flex; align-items:center; justify-content:center; gap:6px; margin-top:12px; padding:10px; border:2px dashed #cac4d3; border-radius:14px; color:#797582; font-size:13px; font-weight:600; cursor:pointer; background:none; width:100%; font-family:inherit; transition:border-color .15s,color .15s,background .15s; }
    .add-task-btn:hover { border-color:#6351a7; color:#6351a7; background:#faf8ff; }

    .modal-overlay { position:fixed; inset:0; background:rgba(28,27,32,.5); backdrop-filter:blur(4px); z-index:200; display:none; align-items:center; justify-content:center; padding:20px; }
    .modal-overlay.open { display:flex; }
    .modal-box { background:#fff; border-radius:24px; width:100%; max-width:540px; max-height:90vh; overflow-y:auto; box-shadow:0 24px 60px rgba(99,81,167,.25); animation:modalIn .2s ease; }
    @keyframes modalIn { from{opacity:0;transform:translateY(20px) scale(.97)} to{opacity:1;transform:translateY(0) scale(1)} }

    .modal-header { display:flex; align-items:center; justify-content:space-between; padding:22px 24px 16px; border-bottom:1px solid #f0eaf8; }
    .modal-title  { font-size:18px; font-weight:800; color:#1c1b20; }
    .modal-close-btn { background:none; border:none; cursor:pointer; font-size:22px; color:#797582; padding:2px 6px; border-radius:8px; line-height:1; transition:background .15s; }
    .modal-close-btn:hover { background:#f3eeff; color:#6351a7; }
    .modal-body { padding:20px 24px; display:flex; flex-direction:column; gap:16px; }

    .form-group { display:flex; flex-direction:column; gap:6px; }
    .form-label { font-size:13px; font-weight:600; color:#1c1b20; }
    .form-input, .form-textarea, .form-select { padding:10px 14px; border:1.5px solid #cac4d3; border-radius:12px; font-family:inherit; font-size:14px; color:#1c1b20; background:#fdf7ff; outline:none; transition:border-color .15s; width:100%; }
    .form-input:focus, .form-textarea:focus, .form-select:focus { border-color:#6351a7; background:#fff; }
    .form-textarea { resize:vertical; min-height:80px; }
    .form-row { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
    .form-error { font-size:12px; color:#ba1a1a; margin-top:2px; }

    .subtask-list { display:flex; flex-direction:column; gap:8px; }
    .subtask-input-row { display:flex; gap:8px; align-items:center; }
    .subtask-input-row .form-input { flex:1; }
    .remove-subtask-btn { background:none; border:none; color:#ba1a1a; font-size:18px; cursor:pointer; padding:2px 6px; border-radius:6px; transition:background .15s; flex-shrink:0; }
    .remove-subtask-btn:hover { background:#fde8e8; }
    .add-subtask-btn { background:none; border:1.5px dashed #cac4d3; border-radius:10px; padding:8px 14px; font-family:inherit; font-size:13px; font-weight:600; color:#797582; cursor:pointer; width:100%; transition:border-color .15s,color .15s; }
    .add-subtask-btn:hover { border-color:#6351a7; color:#6351a7; }

    .modal-footer { display:flex; gap:10px; justify-content:flex-end; padding:16px 24px 22px; border-top:1px solid #f0eaf8; }
    .btn-cancel { padding:10px 20px; border-radius:12px; border:1.5px solid #cac4d3; background:none; font-family:inherit; font-size:14px; font-weight:600; color:#797582; cursor:pointer; }
    .btn-cancel:hover { background:#f3f0f7; }
    .btn-save { padding:10px 24px; border-radius:12px; border:none; background:#6351a7; font-family:inherit; font-size:14px; font-weight:700; color:#fff; cursor:pointer; box-shadow:0 4px 14px rgba(99,81,167,.3); transition:background .2s,transform .1s; }
    .btn-save:hover { background:#5240a0; }

    .kanban-col.drag-over { outline:2px dashed #6351a7; outline-offset:-4px; }
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
        <button class="fab-new-task" id="openModalBtn">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:15px;height:15px"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            New Task
        </button>
    </div>
</div>

<div class="kanban-board">

    {{-- TO DO --}}
    <div class="kanban-col col-todo" data-column="todo"
         ondragover="handleDragOver(event)" ondrop="handleDrop(event,'todo')">
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
        <button class="add-task-btn" onclick="openModal('todo')">+ Add Task</button>
    </div>

    {{-- IN PROGRESS --}}
    <div class="kanban-col col-inprogress" data-column="in_progress"
         ondragover="handleDragOver(event)" ondrop="handleDrop(event,'in_progress')">
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
        <button class="add-task-btn" onclick="openModal('in_progress')">+ Add Task</button>
    </div>

    {{-- DONE --}}
    <div class="kanban-col col-done" data-column="done"
         ondragover="handleDragOver(event)" ondrop="handleDrop(event,'done')">
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
        <button class="add-task-btn" onclick="openModal('done')">+ Add Task</button>
    </div>

</div>

{{-- New Task Modal --}}
<div class="modal-overlay" id="taskModal">
    <div class="modal-box">
        <div class="modal-header">
            <span class="modal-title">✨ Tambah Task Baru</span>
            <button class="modal-close-btn" onclick="closeModal()">×</button>
        </div>
        <form id="taskForm">
            @csrf
            <input type="hidden" name="status" id="modalStatus" value="todo">
            <div class="modal-body">

                <div class="form-group">
                    <label class="form-label">Judul Task *</label>
                    <input type="text" name="title" class="form-input" placeholder="e.g. Review design system v2" required>
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
                        <label class="form-label">Prioritas</label>
                        <select name="priority" class="form-select" required>
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
                    <div class="subtask-list" id="subtaskList">
                        <div class="subtask-input-row">
                            <input type="text" name="subtasks[0][title]" class="form-input" placeholder="Subtask pertama...">
                            <button type="button" class="remove-subtask-btn" onclick="removeSubtask(this)">×</button>
                        </div>
                    </div>
                    <button type="button" class="add-subtask-btn" onclick="addSubtask()" style="margin-top:8px">
                        + Tambah Subtask
                    </button>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeModal()">Batal</button>
                <button type="submit" class="btn-save" id="saveBtn">💾 Simpan Task</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
const CSRF = '{{ csrf_token() }}';

// modal
const modal = document.getElementById('taskModal');

function openModal(status = 'todo') {
    document.getElementById('modalStatus').value = status;
    document.getElementById('taskForm').reset();
    document.getElementById('modalStatus').value = status;
    modal.classList.add('open');
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    modal.classList.remove('open');
    document.body.style.overflow = '';
}

document.getElementById('openModalBtn').addEventListener('click', () => openModal('todo'));
modal.addEventListener('click', e => { if (e.target === modal) closeModal(); });
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });

// subtasks
let subtaskIdx = 1;
function addSubtask() {
    const list = document.getElementById('subtaskList');
    const row  = document.createElement('div');
    row.className = 'subtask-input-row';
    row.innerHTML = `<input type="text" name="subtasks[${subtaskIdx++}][title]" class="form-input" placeholder="Nama subtask..."><button type="button" class="remove-subtask-btn" onclick="removeSubtask(this)">×</button>`;
    list.appendChild(row);
    row.querySelector('input').focus();
}

function removeSubtask(btn) {
    const list = document.getElementById('subtaskList');
    if (list.children.length > 1) btn.closest('.subtask-input-row').remove();
}

// submit new task via fetch
document.getElementById('taskForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const btn = document.getElementById('saveBtn');
    btn.disabled = true;
    btn.textContent = 'Menyimpan...';

    ['err-title','err-priority'].forEach(id => document.getElementById(id).textContent = '');

    const data = new FormData(this);

    try {
        const res  = await fetch('{{ route("tasks.store") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: data,
        });
        const json = await res.json();

        if (!res.ok) {
            if (json.errors) {
                Object.entries(json.errors).forEach(([k, v]) => {
                    const el = document.getElementById('err-' + k);
                    if (el) el.textContent = v[0];
                });
            }
        } else {
            closeModal();
            // insert card into correct column without full reload
            appendCard(json.task);
        }
    } catch (err) {
        console.error(err);
    } finally {
        btn.disabled = false;
        btn.textContent = '💾 Simpan Task';
    }
});

// build and insert card DOM
function appendCard(task) {
    const colMap = { todo: 'todo', in_progress: 'inprogress', done: 'done' };
    const col = document.querySelector(`.kanban-col[data-column="${task.status}"] .col-cards`);
    if (!col) return;

    const chips = [];
    if (task.category) chips.push(`<span class="chip chip-${task.category.toLowerCase()}">${task.category}</span>`);
    if (task.priority)  chips.push(`<span class="chip chip-${task.priority}">${task.priority.charAt(0).toUpperCase()+task.priority.slice(1)}</span>`);

    const due = task.due_date ? `<span class="card-sub">📅 ${task.due_date}</span>` : `<span class="card-sub">0 subtasks</span>`;

    const card = document.createElement('div');
    card.className = 'task-card';
    card.draggable  = true;
    card.dataset.taskId = task.id;
    card.ondragstart = handleDragStart;
    card.innerHTML = `
        <div class="card-chips">${chips.join('')}</div>
        <div class="card-title">${task.title}</div>
        <div class="card-meta">
            ${due}
            <div class="avatar me">{{ auth()->user()->initials }}</div>
        </div>`;
    col.appendChild(card);
    updateColCounts();
}

// drag & drop — persist via API on drop
let draggedCard = null;

function handleDragStart(e) {
    draggedCard = e.currentTarget;
    e.dataTransfer.effectAllowed = 'move';
    e.dataTransfer.setData('text/plain', draggedCard.dataset.taskId);
    setTimeout(() => draggedCard.style.opacity = '0.4', 0);
}

document.querySelectorAll('.task-card').forEach(c => { c.ondragstart = handleDragStart; });

function handleDragOver(e) {
    e.preventDefault();
    e.dataTransfer.dropEffect = 'move';
    e.currentTarget.classList.add('drag-over');
}

async function handleDrop(e, newStatus) {
    e.preventDefault();
    const col = e.currentTarget;
    col.classList.remove('drag-over');
    if (!draggedCard) return;

    const taskId = draggedCard.dataset.taskId;
    col.querySelector('.col-cards').appendChild(draggedCard);
    draggedCard.style.opacity = '1';

    // update strikethrough for done
    const titleEl = draggedCard.querySelector('.card-title');
    if (newStatus === 'done') titleEl.classList.add('strikethrough');
    else titleEl.classList.remove('strikethrough');

    draggedCard = null;
    updateColCounts();

    // persist
    await fetch(`/tasks/${taskId}/move`, {
        method: 'PATCH',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ status: newStatus }),
    });
}

document.querySelectorAll('.kanban-col').forEach(col => {
    col.addEventListener('dragleave', () => col.classList.remove('drag-over'));
    col.addEventListener('dragend',   () => {
        col.classList.remove('drag-over');
        if (draggedCard) draggedCard.style.opacity = '1';
    });
});

function updateColCounts() {
    document.querySelectorAll('.kanban-col').forEach(col => {
        col.querySelector('.col-count').textContent = col.querySelectorAll('.task-card').length;
    });
}

// start timer
document.querySelectorAll('.btn-start-timer').forEach(btn => {
    btn.addEventListener('click', async function() {
        const card   = this.closest('.task-card');
        const taskId = card.dataset.taskId;
        const res    = await fetch(`/tasks/${taskId}/timer/start`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF },
        });
        if (res.ok) location.reload();
    });
});

// stop timer
document.querySelectorAll('.timer-stop-btn').forEach(btn => {
    btn.addEventListener('click', async function() {
        const card   = this.closest('.task-card');
        const taskId = card.dataset.taskId;
        await fetch(`/tasks/${taskId}/timer/stop`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF },
        });
        location.reload();
    });
});

// live timer tick
(function() {
    document.querySelectorAll('[data-timer-start]').forEach(el => {
        const started = new Date(el.dataset.timerStart).getTime();
        const base    = parseInt(el.dataset.timerBase || '0') * 60;
        function tick() {
            const elapsed = base + Math.floor((Date.now() - started) / 1000);
            const h = String(Math.floor(elapsed / 3600)).padStart(2,'0');
            const m = String(Math.floor((elapsed % 3600) / 60)).padStart(2,'0');
            const s = String(elapsed % 60).padStart(2,'0');
            el.textContent = `${h}:${m}:${s}`;
        }
        tick();
        setInterval(tick, 1000);
    });
})();
</script>
@endpush