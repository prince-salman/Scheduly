<aside class="sidebar">
    <style>
        .sidebar {
            width: 260px;
            min-height: 100vh;
            background: #ffffff;
            border-right: 1px solid #cac4d3;
            display: flex;
            flex-direction: column;
            padding: 28px 20px;
            box-shadow: 4px 0 20px rgba(181, 162, 255, 0.15);
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
        }

        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 4px;
        }

        .sidebar-logo .logo-icon {
            font-size: 28px;
            line-height: 1;
        }

        .sidebar-logo .logo-text {
            font-size: 20px;
            font-weight: 800;
            color: #6351a7;
            letter-spacing: -0.3px;
        }

        .sidebar-subtitle {
            font-size: 11px;
            color: #797582;
            font-weight: 500;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-left: 38px;
            margin-bottom: 28px;
        }

        .new-task-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            background: #6351a7;
            color: #fff;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            padding: 12px 20px;
            border-radius: 14px;
            margin-bottom: 32px;
            transition: background 0.2s, transform 0.1s;
            box-shadow: 0 4px 16px rgba(99, 81, 167, 0.3);
        }

        .new-task-btn:hover {
            background: #5240a0;
        }

        .new-task-btn:active {
            transform: scale(0.98);
        }

        .nav-section-label {
            font-size: 10px;
            font-weight: 700;
            color: #797582;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
            padding-left: 8px;
        }

        .nav-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 2px;
            flex: 1;
        }

        .nav-item a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 14px;
            border-radius: 12px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            color: #797582;
            transition: background 0.15s, color 0.15s;
        }

        .nav-item a:hover {
            background: #f3eeff;
            color: #6351a7;
        }

        .nav-item a:hover svg {
            stroke: #6351a7;
        }

        /* active nav state */
        .nav-item a.active {
            background: #ede9ff;
            color: #6351a7;
            font-weight: 700;
        }

        .nav-item a.active svg {
            stroke: #6351a7;
        }

        .nav-item a svg {
            width: 18px;
            height: 18px;
            flex-shrink: 0;
            stroke: #797582;
            stroke-width: 2;
            fill: none;
            stroke-linecap: round;
            stroke-linejoin: round;
            transition: stroke 0.15s;
        }

        .sidebar-divider {
            border: none;
            border-top: 1px solid #cac4d3;
            margin: 20px 0;
        }

        .sidebar-bottom {
            margin-top: auto;
        }

        .sidebar-bottom a,
        .sidebar-bottom button {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 500;
            color: #797582;
            text-decoration: none;
            background: none;
            border: none;
            cursor: pointer;
            width: 100%;
            transition: background 0.15s, color 0.15s;
        }

        .sidebar-bottom a:hover {
            background: #f3eeff;
            color: #6351a7;
        }

        .sidebar-bottom .logout-btn:hover {
            background: #fff0f0;
            color: #ba1a1a;
        }

        .sidebar-bottom a svg,
        .sidebar-bottom button svg {
            width: 17px;
            height: 17px;
            stroke: currentColor;
            fill: none;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }
    </style>

    <!-- Logo & brand -->
    <div class="sidebar-logo">
        <span class="logo-icon"><i data-lucide="calendar" class="icon-sm"></i></span>
        <span class="logo-text">Scheduly</span>
    </div>
    <p class="sidebar-subtitle">Productivity Hub</p>

    <!-- New task CTA -->
    <a href="{{ route('tasks.board') }}" class="new-task-btn">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px">
            <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
        New Task
    </a>

    <!-- Main navigation -->
    <p class="nav-section-label">Menu</p>
    <ul class="nav-list">
        <li class="nav-item">
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <!-- home icon -->
                <svg viewBox="0 0 24 24">
                    <path d="M3 9.75L12 3l9 6.75V20a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V9.75z"/>
                    <path d="M9 21V12h6v9"/>
                </svg>
                Dashboard
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('tasks.board') }}" class="{{ request()->routeIs('tasks.*') ? 'active' : '' }}">
                <!-- kanban/board icon -->
                <svg viewBox="0 0 24 24">
                    <rect x="3" y="3" width="7" height="18" rx="1"/>
                    <rect x="14" y="3" width="7" height="11" rx="1"/>
                    <rect x="14" y="18" width="7" height="3" rx="1"/>
                </svg>
                Task Board
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('calendar') }}" class="{{ request()->routeIs('calendar*') ? 'active' : '' }}">
                <!-- calendar icon -->
                <svg viewBox="0 0 24 24">
                    <rect x="3" y="4" width="18" height="18" rx="2"/>
                    <line x1="16" y1="2" x2="16" y2="6"/>
                    <line x1="8" y1="2" x2="8" y2="6"/>
                    <line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
                Calendar
            </a>
        </li>

        {{-- HANYA ADMIN YANG BISA MELIHAT MENU INI --}}
        @if(auth()->check() && auth()->user()->role === 'admin')
        <li class="nav-item">
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.*') ? 'active' : '' }}">
                <!-- shield/admin icon -->
                <svg viewBox="0 0 24 24">
                    <path d="M12 2l7 4v5c0 5-3.5 9.74-7 11C8.5 20.74 5 16 5 11V6l7-4z"/>
                </svg>
                Admin Panel
            </a>
        </li>
        @endif

        <li class="nav-item">
            <a href="{{ route('notifications') }}" class="{{ request()->routeIs('notifications*') ? 'active' : '' }}">
                <!-- bell icon -->
                <svg viewBox="0 0 24 24">
                    <path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"/>
                    <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                </svg>
                Notifications
            </a>
        </li>
    </ul>

    <hr class="sidebar-divider">

    <!-- Bottom links -->
    <div class="sidebar-bottom">
        <a href="#">
            <svg viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/>
                <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/>
                <line x1="12" y1="17" x2="12.01" y2="17"/>
            </svg>
            Help Center
        </a>

        <!-- Logout via POST -->
        <form method="POST" action="{{ route('logout') }}" style="margin:0">
            @csrf
            <button type="submit" class="logout-btn">
                <svg viewBox="0 0 24 24">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                    <polyline points="16 17 21 12 16 7"/>
                    <line x1="21" y1="12" x2="9" y2="12"/>
                </svg>
                Logout
            </button>
        </form>
    </div>
</aside>
