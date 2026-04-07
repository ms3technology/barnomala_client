<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
        @hasSection('title')
            @yield('title') - {{ $instituteName }}
        @else
            {{ $instituteName }}
        @endif
    </title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Scripts -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        [x-cloak] { display: none !important; }
        :root {
            --accent-color: {{ $options['institute.branding.accent_color'] ?? '#4F46E5' }};
        }
        .bg-accent { background-color: var(--accent-color); }
        .text-accent { color: var(--accent-color); }
        .border-accent { border-color: var(--accent-color); }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-100 font-sans antialiased" x-data="{ sidebarOpen: false }">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside class="fixed inset-y-0 left-0 z-50 w-64 transform bg-slate-900 transition-transform duration-300 lg:static lg:translate-x-0"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
            <div class="flex h-full flex-col">
                <!-- Sidebar Header -->
                <div class="h-20 flex items-center justify-between px-6 py-4 bg-slate-950">
                    <a href="{{ route('admin.dashboard') }}" class="text-xl font-bold text-white flex items-center">
                        <i class="fas fa-shield-halved mr-2 text-accent"></i>
                        <span>Admin Panel</span>
                    </a>
                    <button @click="sidebarOpen = false" class="text-gray-400 lg:hidden focus:outline-none">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <!-- Sidebar Navigation -->
                <nav class="flex-1 overflow-y-auto px-4 py-4 space-y-1">
                    <a href="{{ route('admin.dashboard') }}" 
                       class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-slate-800 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fas fa-chart-line w-5 mr-3"></i>
                        Dashboard
                    </a>
                    
                    <div class="pt-4 pb-2 text-xs font-semibold text-slate-500 uppercase tracking-wider px-4">Content Management</div>
                    
                    <a href="{{ route('admin.notices.index') }}" 
                       class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.notices.*') ? 'bg-slate-800 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fas fa-bullhorn w-5 mr-3"></i>
                        Notices
                    </a>

                    <a href="{{ route('admin.news.index') }}" 
                       class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.news.*') ? 'bg-slate-800 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fas fa-newspaper w-5 mr-3"></i>
                        News
                    </a>

                    <a href="{{ route('admin.speeches.index') }}" 
                       class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.speeches.*') ? 'bg-slate-800 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fas fa-microphone-alt w-5 mr-3"></i>
                        Speeches
                    </a>

                    <a href="{{ route('admin.sliders.index') }}" 
                       class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.sliders.*') ? 'bg-slate-800 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fas fa-images w-5 mr-3"></i>
                        Home Slider
                    </a>

                    <div class="pt-4 pb-2 text-xs font-semibold text-slate-500 uppercase tracking-wider px-4">System Settings</div>

                    <a href="{{ route('admin.layout.index') }}" 
                       class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.layout.*') ? 'bg-slate-800 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fas fa-th-large w-5 mr-3"></i>
                        Homepage Layout
                    </a>

                    <a href="{{ route('admin.branding.index') }}" 
                       class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.branding.*') ? 'bg-slate-800 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fas fa-palette w-5 mr-3"></i>
                        Branding
                    </a>

                    <a href="{{ route('admin.settings.index') }}" 
                       class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.settings.*') ? 'bg-slate-800 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fas fa-university w-5 mr-3"></i>
                        Institution Settings
                    </a>

                    <a href="{{ route('admin.stats.index') }}" 
                       class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.stats.*') ? 'bg-slate-800 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fas fa-chart-pie w-5 mr-3"></i>
                        Stats & Demographics
                    </a>


                    <div class="pt-4 pb-2 text-xs font-semibold text-slate-500 uppercase tracking-wider px-4">Links</div>
                    
                    <a href="{{ route('home') }}" target="_blank" class="flex items-center px-4 py-2.5 text-sm font-medium text-slate-400 hover:bg-slate-800 hover:text-white rounded-lg transition-colors">
                        <i class="fas fa-globe w-5 mr-3"></i>
                        Back to Website
                    </a>
                </nav>

                <!-- Sidebar Footer -->
                <div class="p-4 bg-slate-950">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center px-4 py-2 text-sm font-medium text-red-500 hover:bg-red-500/10 rounded-lg transition-colors">
                            <i class="fas fa-sign-out-alt w-5 mr-3"></i>
                            Log out
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="h-20 bg-white flex items-center px-6">
                <button @click="sidebarOpen = true" class="text-gray-500 focus:outline-none lg:hidden mr-4">
                    <i class="fas fa-bars fa-lg"></i>
                </button>
                <div class="flex-1 flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <h2 class="text-lg font-semibold text-gray-800">@yield('title')</h2>
                    </div>

                    <div class="flex items-center">
                        @stack('header_actions')
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100">
                @include('partials.flash')
                <div class="p-6">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>
    @stack('scripts')
</body>
</html>
