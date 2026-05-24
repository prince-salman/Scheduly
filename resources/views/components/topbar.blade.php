<header class="topbar">
    <style>
        .topbar {
            background: #ffffff;
            border-bottom: 1px solid #cac4d3;
            padding: 16px 32px;
            display: flex;
            align-items: center;
            gap: 20px;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 12px rgba(181, 162, 255, 0.1);
        }

        .topbar-title {
            font-size: 20px;
            font-weight: 700;
            color: #1c1b20;
            flex-shrink: 0;
            letter-spacing: -0.3px;
        }

        .topbar-search {
            flex: 1;
            max-width: 400px;
            position: relative;
            margin-left: 16px;
        }

        .topbar-search input {
            width: 100%;
            padding: 9px 16px 9px 40px;
            border: 1.5px solid #cac4d3;
            border-radius: 12px;
            background: #fdf7ff;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 14px;
            color: #1c1b20;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .topbar-search input::placeholder {
            color: #797582;
        }

        .topbar-search input:focus {
            border-color: #6351a7;
            box-shadow: 0 0 0 3px rgba(99, 81, 167, 0.12);
        }

        .topbar-search .search-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #797582;
            display: flex;
            pointer-events: none;
        }

        .topbar-search .search-icon svg {
            width: 16px;
            height: 16px;
            stroke: #797582;
            fill: none;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .topbar-actions {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-left: auto;
        }

        .topbar-icon-btn {
            position: relative;
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: #fdf7ff;
            border: 1.5px solid #cac4d3;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background 0.15s, border-color 0.15s;
            text-decoration: none;
            color: #1c1b20;
        }

        .topbar-icon-btn:hover {
            background: #ede9ff;
            border-color: #6351a7;
        }

        .topbar-icon-btn svg {
            width: 18px;
            height: 18px;
            stroke: #797582;
            fill: none;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .topbar-icon-btn:hover svg {
            stroke: #6351a7;
        }

        /* notification badge */
        .notif-badge {
            position: absolute;
            top: -4px;
            right: -4px;
            background: #ba1a1a;
            color: #fff;
            font-size: 10px;
            font-weight: 700;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #fff;
        }

        /* user avatar */
        .topbar-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #6351a7, #b5a2ff);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 700;
            color: #fff;
            cursor: pointer;
            flex-shrink: 0;
            border: 2px solid #cac4d3;
            transition: border-color 0.15s;
        }

        .topbar-avatar:hover {
            border-color: #6351a7;
        }
    </style>

    <!-- Page title comes from the child view's @section('title') -->
    <h1 class="topbar-title">@yield('title', 'Dashboard')</h1>

    <!-- Search -->
    <div class="topbar-search">
        <span class="search-icon">
            <svg viewBox="0 0 24 24">
                <circle cx="11" cy="11" r="8"/>
                <line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
        </span>
        <input type="search" placeholder="Search tasks, projects...">
    </div>

    <!-- Right-side actions -->
    <div class="topbar-actions">

        <!-- Notifications with badge -->
        <a href="{{ route('notifications') }}" class="topbar-icon-btn" title="Notifications">
            <svg viewBox="0 0 24 24">
                <path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"/>
                <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
            </svg>
            <span class="notif-badge">2</span>
        </a>

        <!-- Settings / gear -->
        <a href="#" class="topbar-icon-btn" title="Settings">
            <svg viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="3"/>
                <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/>
            </svg>
        </a>

        <!-- User avatar with initials -->
        <div class="topbar-avatar" title="{{ auth()->user()->name ?? 'User' }}">
            {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}
        </div>

    </div>
</header>
