<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard — Restaurant BYOB</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #f1f5f9 0%, #e9eef5 100%); }

        /* ── Navbar ── */
        .navbar {
            background: linear-gradient(90deg, #0f172a 0%, #1e293b 60%, #1a1a2e 100%);
            box-shadow: 0 2px 20px rgba(0,0,0,0.35);
            border-bottom: 1px solid rgba(255,255,255,0.06);
            height: 64px;
        }
        .navbar-inner {
            display: flex; align-items: center; justify-content: space-between;
            height: 100%; padding: 0 24px;
        }
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
        .navbar-logo-wrap {
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 10px; padding: 5px 12px;
            display: flex; align-items: center;
            text-decoration: none; transition: background 0.2s;
        }
        .navbar-logo-wrap:hover { background: rgba(255,255,255,0.14); }
        .navbar-logo {
            height: 44px; width: auto; max-width: 160px;
            object-fit: contain; display: block; border-radius: 5px;
        }
        .navbar-actions { display: flex; align-items: center; gap: 8px; }
        .nav-divider { width: 1px; height: 28px; background: rgba(255,255,255,0.1); margin: 0 4px; }
        .nav-user-pill {
            display: flex; align-items: center; gap: 10px;
            background: rgba(255,255,255,0.07);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 50px; padding: 5px 14px 5px 6px;
            cursor: pointer; transition: all 0.18s;
            font: inherit; outline: none;
        }
        .nav-user-pill:hover { background: rgba(255,255,255,0.13); border-color: rgba(255,255,255,0.2); }
        .nav-user-avatar {
            width: 32px; height: 32px; border-radius: 50%;
            background: linear-gradient(135deg, #dc2626, #991b1b);
            display: flex; align-items: center; justify-content: center;
            font-size: 13px; color: #fff; font-weight: 700; flex-shrink: 0;
        }
        .nav-user-name { font-size: 13px; font-weight: 600; color: #f1f5f9; line-height: 1.2; }
        .nav-user-role { font-size: 11px; color: #94a3b8; line-height: 1.2; }
        .nav-dropdown {
            display: none; position: absolute; right: 0; top: calc(100% + 10px);
            width: 210px; background: #1e293b;
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 14px; overflow: hidden;
            box-shadow: 0 16px 40px rgba(0,0,0,0.4); z-index: 100;
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

        /* ── Stats cards ── */
        .stat-card {
            background: #fff; border-radius: 16px;
            padding: 22px 24px;
            border: 1px solid #f1f5f9;
            box-shadow: 0 2px 12px rgba(0,0,0,0.05);
            transition: all 0.22s ease;
        }
        .stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,0.1); }

        /* ── Module cards ── */
        .module-card {
            border-radius: 16px; border: 1.5px solid transparent;
            transition: all 0.28s ease; position: relative; overflow: hidden;
            text-decoration: none; display: block;
        }
        .module-card::after {
            content: ''; position: absolute; inset: 0;
            background: rgba(0,0,0,0); transition: background 0.28s;
            border-radius: 16px;
        }
        .module-card:hover::after  { background: rgba(0,0,0,0.04); }
        .module-card:hover {
            box-shadow: 0 14px 36px rgba(0,0,0,0.13);
            transform: translateY(-5px);
            border-color: rgba(0,0,0,0.1);
        }
        .module-card:nth-child(1)  { background: linear-gradient(135deg, #fde8e8, #fecaca); }
        .module-card:nth-child(2)  { background: linear-gradient(135deg, #e8f4fd, #bfdbfe); }
        .module-card:nth-child(3)  { background: linear-gradient(135deg, #edfcf2, #bbf7d0); }
        .module-card:nth-child(4)  { background: linear-gradient(135deg, #fef9e8, #fde68a); }
        .module-card:nth-child(5)  { background: linear-gradient(135deg, #f3e8fd, #e9d5ff); }
        .module-card:nth-child(6)  { background: linear-gradient(135deg, #e8fdf9, #99f6e4); }
        .module-card:nth-child(7)  { background: linear-gradient(135deg, #fdf0e8, #fed7aa); }
        .module-card:nth-child(8)  { background: linear-gradient(135deg, #fde8f4, #fbcfe8); }
        .module-card:nth-child(9)  { background: linear-gradient(135deg, #e8eafd, #c7d2fe); }
        .module-card:nth-child(10) { background: linear-gradient(135deg, #f0fde8, #d9f99d); }
        .module-card:nth-child(11) { background: linear-gradient(135deg, #fde8e8, #fecaca); }
        .module-card:nth-child(12) { background: linear-gradient(135deg, #e8f4fd, #bfdbfe); }

        .module-icon {
            width: 50px; height: 50px;
            background: rgba(255,255,255,0.6);
            backdrop-filter: blur(6px);
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 22px; color: #374151;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .badge-role {
            background: linear-gradient(135deg, #1e293b, #0f172a);
            color: #fff; border: 1px solid rgba(255,255,255,0.1);
        }
    </style>
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
                <span class="hidden md:block" style="font-size:12px; color:#475569; font-weight:500; margin-right:4px;">Dashboard</span>
                <div class="nav-divider hidden sm:block"></div>

                <!-- User pill + dropdown -->
                <div style="position:relative;">
                    <button class="nav-user-pill" onclick="toggleDropdown()" type="button">
                        <div class="nav-user-avatar">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <div class="hidden sm:block">
                            <div class="nav-user-name">{{ $user->name }}</div>
                            <div class="nav-user-role">{{ $role->name }}</div>
                        </div>
                        <i class="fas fa-chevron-down hidden sm:block" style="font-size:10px; color:#64748b; margin-left:4px;"></i>
                    </button>

                    <div id="dropdown" class="nav-dropdown">
                        <div style="padding:12px 16px 8px; border-bottom:1px solid rgba(255,255,255,0.08);">
                            <div style="font-size:13px; font-weight:700; color:#f1f5f9;">{{ $user->name }}</div>
                            <div style="font-size:11px; color:#64748b; margin-top:2px;">{{ $user->email }}</div>
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

    <!-- Page content -->
    <div style="padding-top: 67px;">
        <div class="w-full px-6 py-8 max-w-screen-2xl mx-auto">

            <!-- Page header -->
            <div class="mb-8">
                <div class="flex items-center justify-between flex-wrap gap-4">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900" style="letter-spacing:-0.5px;">Dashboard</h1>
                        <p class="text-gray-500 mt-1 text-sm">Welcome back, <strong class="text-gray-700">{{ $user->name }}</strong>!</p>
                    </div>
                    <span class="badge-role px-4 py-2 rounded-full text-sm font-semibold">
                        <i class="fas fa-shield-halved mr-1"></i>{{ $role->name }} Access
                    </span>
                </div>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
                <div class="stat-card">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-xs font-semibold uppercase tracking-wide">Total Sales</p>
                            <p class="text-2xl font-bold text-gray-900 mt-1">LKR 0</p>
                        </div>
                        <div style="width:48px;height:48px;background:linear-gradient(135deg,#fde8e8,#fecaca);border-radius:14px;display:flex;align-items:center;justify-content:center;">
                            <i class="fas fa-rupee-sign" style="color:#dc2626;font-size:18px;"></i>
                        </div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-xs font-semibold uppercase tracking-wide">Active Orders</p>
                            <p class="text-2xl font-bold text-gray-900 mt-1">0</p>
                        </div>
                        <div style="width:48px;height:48px;background:linear-gradient(135deg,#e8f4fd,#bfdbfe);border-radius:14px;display:flex;align-items:center;justify-content:center;">
                            <i class="fas fa-shopping-cart" style="color:#2563eb;font-size:18px;"></i>
                        </div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-xs font-semibold uppercase tracking-wide">Inventory Items</p>
                            <p class="text-2xl font-bold text-gray-900 mt-1">0</p>
                        </div>
                        <div style="width:48px;height:48px;background:linear-gradient(135deg,#edfcf2,#bbf7d0);border-radius:14px;display:flex;align-items:center;justify-content:center;">
                            <i class="fas fa-boxes" style="color:#16a34a;font-size:18px;"></i>
                        </div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-xs font-semibold uppercase tracking-wide">Active Users</p>
                            <p class="text-2xl font-bold text-gray-900 mt-1">3</p>
                        </div>
                        <div style="width:48px;height:48px;background:linear-gradient(135deg,#f3e8fd,#e9d5ff);border-radius:14px;display:flex;align-items:center;justify-content:center;">
                            <i class="fas fa-users" style="color:#7c3aed;font-size:18px;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modules -->
            <div>
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-xl font-bold text-gray-900">Available Modules</h2>
                    <span class="text-xs text-gray-400 font-medium">{{ $modules->count() }} modules</span>
                </div>

                @if($modules->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                        @foreach($modules as $module)
                            <a href="{{ route($module->route) }}" class="module-card p-6">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="module-icon">
                                        <i class="fas fa-{{ $module->icon }}"></i>
                                    </div>
                                    <i class="fas fa-arrow-right" style="color:rgba(0,0,0,0.2); font-size:14px; margin-top:4px;"></i>
                                </div>
                                <h3 class="text-base font-bold text-gray-900 mb-1">{{ $module->name }}</h3>
                                <p class="text-gray-500 text-sm" style="line-height:1.5;">{{ $module->description ?? 'Manage ' . strtolower($module->name) }}</p>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="bg-white p-10 rounded-2xl text-center border border-gray-100">
                        <i class="fas fa-inbox text-gray-300 text-4xl mb-4"></i>
                        <p class="text-gray-500">No modules available for your role yet.</p>
                    </div>
                @endif
            </div>

            <!-- Mobile quick access -->
            <div class="lg:hidden mt-8 pb-8">
                <h2 class="text-base font-bold text-gray-900 mb-4">Quick Access</h2>
                <div class="grid grid-cols-3 gap-3">
                    @foreach($modules as $module)
                        <a href="{{ route($module->route) }}" class="bg-white p-3 rounded-xl text-center border border-gray-100 hover:border-red-400 transition-colors">
                            <i class="fas fa-{{ $module->icon }} text-red-500 text-xl mb-1 block"></i>
                            <p class="text-xs font-semibold text-gray-700">{{ substr($module->name, 0, 10) }}</p>
                        </a>
                    @endforeach
                </div>
            </div>

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
</body>
</html>
