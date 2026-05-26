<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Restaurant BYOB - Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
        }
        .navbar {
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.15);
        }
        .navbar-logo {
            height: 52px;
            width: auto;
            max-width: 200px;
            object-fit: contain;
            border-radius: 8px;
            mix-blend-mode: multiply;
            background: transparent;
        }
        .sidebar {
            background: white;
            box-shadow: 2px 0 8px rgba(0, 0, 0, 0.08);
        }
        /* Base module card */
        .module-card {
            border: 1.5px solid transparent;
            transition: all 0.28s ease;
            position: relative;
            overflow: hidden;
        }
        .module-card::before {
            content: '';
            position: absolute;
            inset: 0;
            opacity: 0;
            transition: opacity 0.28s;
            background: rgba(0,0,0,0.04);
        }
        .module-card:hover::before { opacity: 1; }
        .module-card:hover {
            box-shadow: 0 12px 32px rgba(0,0,0,0.12);
            transform: translateY(-5px);
            border-color: rgba(0,0,0,0.12);
        }
        /* Pastel colour palette — one per card slot (cycles via nth-child) */
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

        /* Module icon inherits tint from card */
        .module-icon {
            width: 48px;
            height: 48px;
            background: rgba(255,255,255,0.55);
            backdrop-filter: blur(4px);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            color: #374151;
            box-shadow: 0 2px 6px rgba(0,0,0,0.08);
        }
        .badge-role {
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
        }
        .active-nav {
            background: linear-gradient(135deg, #fecaca 0%, #fca5a5 100%);
            color: #991b1b;
            border-left: 4px solid #dc2626;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar fixed top-0 left-0 right-0 z-50">
        <div class="flex items-center justify-between px-6 py-4">
            <div class="flex items-center">
                <img src="{{ asset('images/jaan_logo.jpg') }}" alt="Piznek Logo" class="navbar-logo">
            </div>

            <div class="flex items-center space-x-4">
                <div class="hidden sm:flex items-center space-x-3 text-white">
                    <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-red-600"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold">{{ $user->name }}</p>
                        <p class="text-xs text-red-100">{{ $role->name }}</p>
                    </div>
                </div>

                <div class="dropdown relative">
                    <button class="text-white hover:text-red-100 transition-colors" onclick="toggleDropdown()">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <div id="dropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg">
                        <a href="#" class="block px-4 py-2 text-gray-700 hover:bg-red-50">
                            <i class="fas fa-user-circle mr-2"></i> Profile
                        </a>
                        <a href="#" class="block px-4 py-2 text-gray-700 hover:bg-red-50">
                            <i class="fas fa-cog mr-2"></i> Settings
                        </a>
                        <hr class="my-2">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-red-600 hover:bg-red-50">
                                <i class="fas fa-sign-out-alt mr-2"></i> Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="flex pt-16">
        <!-- Main Content -->
        <div class="flex-1 w-full px-6 py-8">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center justify-between flex-wrap gap-4">
                    <div>
                        <h1 class="text-4xl font-bold text-gray-900">Dashboard</h1>
                        <p class="text-gray-600 mt-2">Welcome back, <strong>{{ $user->name }}</strong>!</p>
                    </div>
                    <div class="badge-role text-white px-4 py-2 rounded-full text-sm font-semibold">
                        {{ $role->name }} Access
                    </div>
                </div>
            </div>

            <!-- Quick Stats (Optional) -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm font-medium">Total Sales</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">LKR 0</p>
                        </div>
                        <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-rupee-sign text-red-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm font-medium">Active Orders</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">0</p>
                        </div>
                        <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-shopping-cart text-red-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm font-medium">Inventory Items</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">0</p>
                        </div>
                        <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-boxes text-red-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm font-medium">Active Users</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">3</p>
                        </div>
                        <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-users text-red-600 text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modules Section -->
            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Available Modules</h2>

                @if($modules->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($modules as $module)
                            <a href="{{ route($module->route) }}" class="module-card p-6 rounded-lg cursor-pointer">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="module-icon">
                                        <i class="fas fa-{{ $module->icon }}"></i>
                                    </div>
                                    <i class="fas fa-arrow-right text-gray-300 text-lg"></i>
                                </div>
                                <h3 class="text-lg font-bold text-gray-900 mb-2">{{ $module->name }}</h3>
                                <p class="text-gray-600 text-sm">{{ $module->description ?? 'Manage ' . strtolower($module->name) }}</p>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="bg-white p-8 rounded-lg text-center border border-gray-100">
                        <i class="fas fa-inbox text-gray-300 text-4xl mb-4"></i>
                        <p class="text-gray-600">No modules available for your role yet.</p>
                    </div>
                @endif
            </div>

            <!-- Mobile Navigation (visible on small screens) -->
            <div class="lg:hidden mt-8 pb-8">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Quick Access</h2>
                <div class="grid grid-cols-2 gap-4">
                    @foreach($modules as $module)
                        <a href="{{ route($module->route) }}" class="bg-white p-4 rounded-lg text-center border border-gray-100 hover:border-red-600 transition-colors">
                            <i class="fas fa-{{ $module->icon }} text-red-600 text-2xl mb-2"></i>
                            <p class="text-xs font-semibold text-gray-900">{{ substr($module->name, 0, 12) }}</p>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleDropdown() {
            const dropdown = document.getElementById('dropdown');
            dropdown.classList.toggle('hidden');
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('dropdown');
            const button = event.target.closest('button');
            if (!button && !dropdown.classList.contains('hidden')) {
                dropdown.classList.add('hidden');
            }
        });
    </script>
</body>
</html>
