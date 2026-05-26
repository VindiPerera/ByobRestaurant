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
            background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
        }
        .navbar {
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.15);
        }
        .sidebar {
            background: white;
            box-shadow: 2px 0 8px rgba(0, 0, 0, 0.08);
        }
        .active-nav {
            background: linear-gradient(135deg, #fecaca 0%, #fca5a5 100%);
            color: #991b1b;
            border-left: 4px solid #dc2626;
        }
        .main-content {
            margin-left: 256px;
        }
        @media (max-width: 1024px) {
            .main-content {
                margin-left: 0;
            }
        }
    </style>
    @yield('styles')
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar fixed top-0 left-0 right-0 z-50">
        <div class="flex items-center justify-between px-6 py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('dashboard') }}" class="text-2xl font-bold text-white hover:text-red-100 transition-colors">
                    Piznek
                </a>
                <span class="hidden sm:block text-red-100 text-sm">Restaurant Management System</span>
            </div>

            <div class="flex items-center space-x-4">
                <div class="hidden sm:flex items-center space-x-3 text-white">
                    <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-red-600"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-red-100">{{ auth()->user()->role->name ?? 'User' }}</p>
                    </div>
                </div>

                <div class="dropdown relative">
                    <button class="text-white hover:text-red-100 transition-colors" onclick="toggleDropdown()">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <div id="dropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg z-10">
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
        <!-- Sidebar -->
        <x-sidebar :modules="$modules ?? []" />

        <!-- Main Content -->
        <div class="flex-1 main-content px-6 py-8 w-full lg:w-auto">
            @yield('content')
        </div>
    </div>

    <script>
        function toggleDropdown() {
            const dropdown = document.getElementById('dropdown');
            dropdown.classList.toggle('hidden');
        }

        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('dropdown');
            const button = event.target.closest('button');
            if (!button && !dropdown.classList.contains('hidden')) {
                dropdown.classList.add('hidden');
            }
        });
    </script>
    @yield('scripts')
</body>
</html>
