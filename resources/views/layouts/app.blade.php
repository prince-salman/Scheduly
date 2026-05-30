<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Scheduly') — Scheduly</title>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Lucide icons (CDN) --}}
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js" defer></script>

    {{-- Chart.js (needed by analytics & admin dashboard) --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js" defer></script>

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --sidebar-w: 240px;
            --color-primary: #6351a7;
            --color-primary-light: #ede9ff;
            --color-surface: #f5f0ff;
            --color-bg: #ffffff;
            --color-text: #1c1b20;
            --color-muted: #797582;
            --color-border: #f0ecf8;
        }

        html, body {
            height: 100%;
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--color-surface);
            color: var(--color-text);
        }

        /* ── Shell layout ── */
        .app-shell {
            display: flex;
            min-height: 100vh;
        }

        /* ── Sidebar ── */
        .sidebar {
            width: var(--sidebar-w);
            background: #ffffff;
            border-right: 1px solid var(--color-border);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; left: 0;
            height: 100vh;
            z-index: 100;
            overflow-y: auto;
        }

        .sidebar-logo {
            padding: 24px 20px 18px;
            font-size: 20px;
            font-weight: 800;
            color: var(--color-primary);
            letter-spacing: -0.4px;
            display: flex;
            align-items: center;
            gap: 8px;
            border-bottom: 1px solid var(--color-border);
        }

        .sidebar-logo span { color: var(--color-text); }

        .sidebar-nav {
            padding: 16px 12px;
            flex: 1;
        }

        .nav-section-label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #b0aac0;
            padding: 10px 8px 6px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 12px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 600;
            color: var(--color-muted);
            text-decoration: none;
            transition: background 0.15s, color 0.15s;
            position: relative;
            margin-bottom: 2px;
        }

        .nav-link:hover { background: var(--color-primary-light); color: var(--color-primary); }

        .nav-link.active {
            background: var(--color-primary-light);
            color: var(--color-primary);
        }

        .nav-link.active::before {
            content: '';
            position: absolute;
            left: -12px;
            top: 50%;
            transform: translateY(-50%);
            width: 4px;
            height: 60%;
            background: var(--color-primary);
            border-radius: 0 4px 4px 0;
        }

        .nav-link svg {
            width: 16px;
            height: 16px;
            flex-shrink: 0;
            stroke: currentColor;
            fill: none;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        /* notification badge */
        .nav-badge {
            margin-left: auto;
            background: var(--color-primary);
            color: #fff;
            font-size: 10px;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 20px;
            min-width: 18px;
            text-align: center;
        }

        /* ── Sidebar user card ── */
        .sidebar-user {
            padding: 12px 16px;
            border-top: 1px solid var(--color-border);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar-user-avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: var(--color-primary-light);
            color: var(--color-primary);
            font-size: 12px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .sidebar-user-info { flex: 1; min-width: 0; }

        .sidebar-user-name {
            font-size: 13px;
            font-weight: 700;
            color: var(--color-text);
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .sidebar-user-role {
            font-size: 11px;
            color: var(--color-muted);
            text-transform: capitalize;
        }

        .sidebar-logout-btn {
            background: none;
            border: none;
            cursor: pointer;
            color: var(--color-muted);
            padding: 4px;
            border-radius: 8px;
            transition: color 0.15s, background 0.15s;
            display: flex;
        }

        .sidebar-logout-btn:hover { color: #ba1a1a; background: #ffedea; }

        .sidebar-logout-btn svg {
            width: 16px; height: 16px;
            stroke: currentColor; fill: none;
            stroke-width: 2; stroke-linecap: round; stroke-linejoin: round;
        }

        /* ── Main content ── */
        .main-content {
            margin-left: var(--sidebar-w);
            flex: 1;
            padding: 32px 36px;
            min-height: 100vh;
        }

        /* ── Flash message ── */
        .flash-message {
            padding: 12px 18px;
            border-radius: 14px;
            margin-bottom: 20px;
            font-size: 14px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .flash-success { background: #d6faf5; color: #006a61; }
        .flash-error   { background: #ffedea; color: #ba1a1a; }

        /* ── Icon helpers (used across views) ── */
        .icon-sm { width: 14px; height: 14px; display: inline-block; vertical-align: middle; }
        .icon-lg { width: 20px; height: 20px; display: inline-block; vertical-align: middle; }
        .text-primary { color: var(--color-primary); }
    </style>

    @stack('styles')
</head>
<body>
<div class="app-shell">

    {{-- ── Sidebar ── --}}
    <aside class="sidebar">
        <div class="sidebar-logo">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:22px;height:22px">
                <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/>
                <line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
            Sched<span>uly</span>
        </div>

        <nav class="sidebar-nav">

            @auth
                {{-- User area --}}
                <div class="nav-section-label">Menu</div>

                <a href="{{ route('dashboard') }}"
                   class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                    Dashboard
                </a>

                <a href="{{ route('tasks.board') }}"
                   class="nav-link {{ request()->routeIs('tasks.*') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
                    Task Board
                </a>

                <a href="{{ route('calendar.index') }}"
                   class="nav-link {{ request()->routeIs('calendar.*') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    Kalender
                </a>

                <a href="{{ route('analytics.index') }}"
                   class="nav-link {{ request()->routeIs('analytics.*') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                    Analytics
                </a>

                <a href="{{ route('notifications.index') }}"
                   class="nav-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                    Notifikasi
                    @php $unread = \App\Models\Notification::forUser(auth()->id())->unread()->count(); @endphp
                    @if($unread > 0)
                        <span class="nav-badge">{{ $unread }}</span>
                    @endif
                </a>

                {{-- Admin section --}}
                @if(auth()->user()->isAdmin())
                    <div class="nav-section-label" style="margin-top:8px">Admin</div>

                    <a href="{{ route('admin.dashboard') }}"
                       class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <svg viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                        Admin Overview
                    </a>

                    <a href="{{ route('admin.users.index') }}"
                       class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        Manajemen User
                    </a>
                @endif
            @endauth

        </nav>

        @auth
        <div class="sidebar-user">
            <div class="sidebar-user-avatar">{{ auth()->user()->initials }}</div>
            <div class="sidebar-user-info">
                <div class="sidebar-user-name">{{ auth()->user()->name }}</div>
                <div class="sidebar-user-role">{{ auth()->user()->role }}</div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="sidebar-logout-btn" title="Logout">
                    <svg viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                </button>
            </form>
        </div>
        @endauth
    </aside>

    {{-- ── Main content ── --}}
    <main class="main-content">

        {{-- Flash messages --}}
        @if(session('success'))
            <div class="flash-message flash-success">✓ {{ session('success') }}</div>
        @endif
        @if(session('error') || $errors->any())
            <div class="flash-message flash-error">
                ✕ {{ session('error') ?? $errors->first() }}
            </div>
        @endif

        @yield('content')
    </main>

</div>

<script>
    // Initialise Lucide icons after DOM is ready
    document.addEventListener('DOMContentLoaded', () => lucide.createIcons());
</script>

@stack('scripts')
</body>
</html>