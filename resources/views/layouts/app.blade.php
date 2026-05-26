<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') - Restaurant BYOB</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f1f5f9 0%, #e9eef5 100%);
        }

        /* ── Navbar ── */
        .navbar {
            background: linear-gradient(90deg, #0f172a 0%, #1e293b 60%, #1a1a2e 100%);
            box-shadow: 0 2px 20px rgba(0,0,0,0.35);
            border-bottom: 1px solid rgba(255,255,255,0.06);
            height: 64px;
        }
        .navbar-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 100%;
            padding: 0 24px;
        }

        /* Logo */
        .navbar-logo-wrap {
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 10px;
            padding: 5px 12px;
            display: flex;
            align-items: center;
            text-decoration: none;
            transition: background 0.2s;
        }
        .navbar-logo-wrap:hover { background: rgba(255,255,255,0.14); }
        .navbar-logo {
            height: 44px;
            width: auto;
            max-width: 160px;
            object-fit: contain;
            display: block;
            border-radius: 5px;
        }

        /* Right side actions */
        .navbar-actions { display: flex; align-items: center; gap: 8px; }

        /* Nav icon button */
        .nav-icon-btn {
            width: 38px; height: 38px;
            background: rgba(255,255,255,0.07);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            color: #cbd5e1; cursor: pointer; transition: all 0.18s;
            position: relative;
        }
        .nav-icon-btn:hover { background: rgba(255,255,255,0.14); color: #fff; border-color: rgba(255,255,255,0.2); }

        /* User pill */
        .nav-user-pill {
            display: flex; align-items: center; gap: 10px;
            background: rgba(255,255,255,0.07);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 50px;
            padding: 5px 14px 5px 6px;
            cursor: pointer; transition: all 0.18s;
            text-decoration: none;
        }
        .nav-user-pill:hover { background: rgba(255,255,255,0.13); border-color: rgba(255,255,255,0.2); }
        .nav-user-avatar {
            width: 32px; height: 32px; border-radius: 50%;
            background: linear-gradient(135deg, #dc2626, #991b1b);
            display: flex; align-items: center; justify-content: center;
            font-size: 13px; color: #fff; font-weight: 700; flex-shrink: 0;
        }
        .nav-user-name  { font-size: 13px; font-weight: 600; color: #f1f5f9; line-height: 1.2; }
        .nav-user-role  { font-size: 11px; color: #94a3b8; line-height: 1.2; }

        /* Red accent divider */
        .nav-divider {
            width: 1px; height: 28px;
            background: rgba(255,255,255,0.1);
            margin: 0 4px;
        }

        /* Dropdown */
        .nav-dropdown {
            display: none; position: absolute; right: 0; top: calc(100% + 10px);
            width: 200px; background: #1e293b;
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 14px; overflow: hidden;
            box-shadow: 0 16px 40px rgba(0,0,0,0.4);
            z-index: 100;
        }
        .nav-dropdown.open { display: block; }
        .nav-dropdown a, .nav-dropdown button {
            display: flex; align-items: center; gap: 10px;
            padding: 11px 16px; font-size: 13px; font-weight: 500;
            color: #cbd5e1; text-decoration: none;
            background: none; border: none; width: 100%; text-align: left;
            cursor: pointer; transition: background 0.15s;
        }
        .nav-dropdown a:hover, .nav-dropdown button:hover { background: rgba(255,255,255,0.07); color: #fff; }
        .nav-dropdown .dd-danger { color: #f87171; }
        .nav-dropdown .dd-danger:hover { background: rgba(239,68,68,0.1); color: #ef4444; }
        .nav-dropdown hr { border-color: rgba(255,255,255,0.08); margin: 4px 0; }

        /* Page header accent bar */
        .page-header-bar {
            height: 3px;
            background: linear-gradient(90deg, #dc2626, #f97316, #dc2626);
            background-size: 200% 100%;
            animation: shimmer 3s ease infinite;
        }
        @keyframes shimmer {
            0%   { background-position: 0% 0%; }
            50%  { background-position: 100% 0%; }
            100% { background-position: 0% 0%; }
        }

        /* Sidebar */
        .sidebar {
            background: #fff;
            box-shadow: 2px 0 12px rgba(0,0,0,0.06);
            border-right: 1px solid #f1f5f9;
        }
        .active-nav {
            background: linear-gradient(135deg, #fef2f2, #fee2e2);
            color: #991b1b;
            border-left: 3px solid #dc2626;
            font-weight: 700;
        }
        .main-content { margin-left: 256px; }
        @media (max-width: 1024px) { .main-content { margin-left: 0; } }
    </style>
    @yield('styles')
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar fixed top-0 left-0 right-0 z-50">
        <div class="page-header-bar"></div>
        <div class="navbar-inner">

            <!-- Logo -->
            <a href="{{ route('dashboard') }}" class="navbar-logo-wrap">
                <img src="{{ asset('images/jaan_logo.jpg') }}" alt="Logo" class="navbar-logo">
            </a>

            <!-- Right actions -->
            <div class="navbar-actions">

                <!-- Current page label -->
                <span class="hidden md:block" style="font-size:12px; color:#64748b; font-weight:500; margin-right:6px;">
                    {{ request()->routeIs('dashboard') ? 'Dashboard' : (request()->segment(1) ? ucfirst(str_replace('-', ' ', request()->segment(1))) : '') }}
                </span>

                <div class="nav-divider hidden sm:block"></div>

                <!-- User pill + dropdown -->
                <div style="position:relative;">
                    <button class="nav-user-pill" onclick="toggleDropdown()" type="button">
                        <div class="nav-user-avatar">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div class="hidden sm:block">
                            <div class="nav-user-name">{{ auth()->user()->name }}</div>
                            <div class="nav-user-role">{{ auth()->user()->role->name ?? 'User' }}</div>
                        </div>
                        <i class="fas fa-chevron-down hidden sm:block" style="font-size:10px; color:#64748b; margin-left:4px;"></i>
                    </button>

                    <div id="dropdown" class="nav-dropdown">
                        <div style="padding:12px 16px 8px; border-bottom:1px solid rgba(255,255,255,0.08);">
                            <div style="font-size:13px; font-weight:700; color:#f1f5f9;">{{ auth()->user()->name }}</div>
                            <div style="font-size:11px; color:#64748b; margin-top:2px;">{{ auth()->user()->email }}</div>
                        </div>
                        <a href="{{ route('dashboard') }}">
                            <i class="fas fa-gauge-high" style="width:16px;"></i> Dashboard
                        </a>
                        <a href="{{ route('settings.index') }}">
                            <i class="fas fa-gear" style="width:16px;"></i> Settings
                        </a>
                        <hr>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dd-danger">
                                <i class="fas fa-right-from-bracket" style="width:16px;"></i> Sign Out
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </nav>

    <div class="flex" style="padding-top:67px;">
        <!-- Sidebar -->
        <x-sidebar :modules="$modules ?? []" />

        <!-- Main Content -->
        <div class="flex-1 main-content px-6 py-8 w-full lg:w-auto">
            @yield('content')
        </div>
    </div>

    <script>
        function toggleDropdown() {
            document.getElementById('dropdown').classList.toggle('open');
        }
        document.addEventListener('click', function(e) {
            const dd = document.getElementById('dropdown');
            if (!e.target.closest('.nav-user-pill') && !e.target.closest('.nav-dropdown')) {
                dd.classList.remove('open');
            }
        });
    </script>
    @yield('scripts')
</body>
</html>
