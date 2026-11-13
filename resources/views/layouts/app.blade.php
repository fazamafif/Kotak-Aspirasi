<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Kotak Aspirasi — Suara Anda Membangun Perubahan' }}</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        // Init Dark Mode from localStorage
        if (localStorage.getItem('theme') === 'light') {
            document.documentElement.classList.remove('dark');
        } else {
            document.documentElement.classList.add('dark');
        }
    </script>
</head>
<body class="bg-background text-foreground min-h-screen flex flex-col transition-colors duration-200">

    <!-- Header / Navbar Modern -->
    <header class="sticky top-0 z-40 w-full border-b border-border bg-background/80 backdrop-blur-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <!-- Brand -->
            <a href="{{ url('/') }}" class="flex items-center gap-3 group">
                <div class="w-10 h-10 rounded-xl bg-primary flex items-center justify-center text-primary-foreground shadow-lg shadow-primary/20 transition-transform group-hover:scale-105">
                    <i data-lucide="message-square-plus" class="w-5 h-5"></i>
                </div>
                <div>
                    <span class="font-bold text-lg tracking-tight flex items-center gap-1.5">
                        Kotak<span class="text-primary">Aspirasi</span>
                    </span>
                    <span class="hidden sm:block text-[11px] text-muted-foreground -mt-1 font-medium">Platform Aspirasi & Pengaduan</span>
                </div>
            </a>

            <!-- Nav Links Desktop -->
            <nav class="hidden md:flex items-center gap-1">
                <a href="{{ url('/') }}" class="px-3.5 py-2 rounded-lg text-sm font-medium transition-colors hover:bg-secondary hover:text-secondary-foreground {{ request()->is('/') ? 'text-primary font-semibold' : 'text-muted-foreground' }}">
                    Beranda
                </a>
                <a href="{{ url('/aspirasi') }}" class="px-3.5 py-2 rounded-lg text-sm font-medium transition-colors hover:bg-secondary hover:text-secondary-foreground {{ request()->is('aspirasi*') ? 'text-primary font-semibold' : 'text-muted-foreground' }}">
                    Daftar Aspirasi
                </a>
                <a href="{{ url('/#form-aspirasi') }}" class="px-3.5 py-2 rounded-lg text-sm font-medium transition-colors hover:bg-secondary hover:text-secondary-foreground text-muted-foreground">
                    Kirim Aspirasi
                </a>
            </nav>

            <!-- Actions & Theme Toggle -->
            <div class="flex items-center gap-2 sm:gap-3">
                <!-- Theme Toggle Button -->
                <button id="themeToggleBtn" type="button" aria-label="Toggle Theme" class="p-2.5 rounded-lg border border-border bg-card hover:bg-secondary transition-colors text-muted-foreground hover:text-foreground">
                    <i data-lucide="sun" class="w-4 h-4 hidden dark:block"></i>
                    <i data-lucide="moon" class="w-4 h-4 block dark:hidden"></i>
                </button>

                @auth
                    <!-- Logged in Admin indicator & Logout -->
                    <div class="hidden sm:flex items-center gap-2 pl-2 border-l border-border">
                        <a href="{{ route('admin.dashboard') }}" title="Masuk ke Panel Admin" class="text-xs font-semibold text-foreground px-2.5 py-1.5 rounded-lg border border-border bg-card hover:bg-secondary transition-colors flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span>{{ auth()->user()->name }}</span>
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" title="Keluar / Logout" class="p-2 rounded-lg border border-border bg-card hover:bg-destructive/10 hover:text-destructive hover:border-destructive/30 transition-colors text-muted-foreground">
                                <i data-lucide="log-out" class="w-4 h-4"></i>
                            </button>
                        </form>
                    </div>
                @endauth

                <!-- CTA Button -->
                <a href="{{ url('/#form-aspirasi') }}" class="hidden sm:inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-primary text-primary-foreground font-semibold text-sm shadow hover:opacity-95 transition-all active:scale-95">
                    <i data-lucide="send" class="w-4 h-4"></i>
                    <span>Tulis Aspirasi</span>
                </a>

                <!-- Mobile Menu Button -->
                <button id="mobileMenuBtn" type="button" class="md:hidden p-2.5 rounded-lg border border-border bg-card hover:bg-secondary text-muted-foreground">
                    <i data-lucide="menu" class="w-5 h-5"></i>
                </button>
            </div>
        </div>

        <!-- Mobile Nav Menu -->
        <div id="mobileMenu" class="hidden md:hidden border-b border-border bg-card px-4 pt-3 pb-4 space-y-2">
            <a href="{{ url('/') }}" class="block px-3 py-2 rounded-md text-sm font-medium text-foreground hover:bg-secondary">Beranda</a>
            <a href="{{ url('/aspirasi') }}" class="block px-3 py-2 rounded-md text-sm font-medium text-foreground hover:bg-secondary">Daftar Aspirasi</a>
            <a href="{{ url('/#form-aspirasi') }}" class="block px-3 py-2 rounded-md text-sm font-medium text-foreground hover:bg-secondary">Kirim Aspirasi</a>
            @auth
            <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded-md text-sm font-medium text-primary hover:bg-secondary flex items-center gap-2">
                <i data-lucide="shield-check" class="w-4 h-4"></i> Panel Admin ({{ auth()->user()->name }})
            </a>
            <form method="POST" action="{{ route('logout') }}" class="pt-2 border-t border-border">
                @csrf
                <button type="submit" class="w-full text-left px-3 py-2 rounded-md text-sm font-medium text-destructive hover:bg-destructive/10 flex items-center gap-2">
                    <i data-lucide="log-out" class="w-4 h-4"></i> Logout
                </button>
            </form>
            @endauth
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1">
        @yield('content')
    </main>

    <!-- Footer Modern -->
    <footer class="border-t border-border bg-card py-10 mt-16 transition-colors">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="md:col-span-2 space-y-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-primary flex items-center justify-center text-primary-foreground">
                            <i data-lucide="message-square-plus" class="w-4 h-4"></i>
                        </div>
                        <span class="font-bold text-base tracking-tight">Kotak<span class="text-primary">Aspirasi</span></span>
                    </div>
                    <p class="text-sm text-muted-foreground max-w-sm">
                        Wadah penyampaian aspirasi, kritik, dan saran yang transparan, aman, dan dapat dipantau secara real-time. Identitas Anda dapat dijaga sepenuhnya anonim.
                    </p>
                </div>
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-muted-foreground mb-3">Navigasi</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ url('/') }}" class="text-muted-foreground hover:text-primary transition-colors">Beranda</a></li>
                        <li><a href="{{ url('/aspirasi') }}" class="text-muted-foreground hover:text-primary transition-colors">Aspirasi Publik</a></li>
                        <li><a href="{{ url('/#form-aspirasi') }}" class="text-muted-foreground hover:text-primary transition-colors">Kirim Aspirasi</a></li>
                        <li><a href="{{ url('/admin') }}" class="text-muted-foreground hover:text-primary transition-colors">Area Admin</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-muted-foreground mb-3">Privasi & Keamanan</h4>
                    <p class="text-xs text-muted-foreground leading-relaxed">
                        Fitur kirim anonim memastikan identitas pengirim tidak dibagikan ke publik maupun petugas jika diinginkan.
                    </p>
                    <div class="mt-3 flex items-center gap-2 text-xs text-emerald-500 font-medium">
                        <i data-lucide="check-circle-2" class="w-4 h-4"></i>
                        <span>Enkripsi Aman & Terpercaya</span>
                    </div>
                </div>
            </div>
            <div class="border-t border-border/60 mt-8 pt-6 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-muted-foreground">
                <p>&copy; {{ date('Y') }} KotakAspirasi. All rights reserved.</p>
                <div class="flex items-center gap-4">
                    <span>Modern Dark UI with Tailwind CSS</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- Script Init Lucide & Dark Mode -->
    <script>
        lucide.createIcons();

        // Dark Mode Toggle
        const themeToggleBtn = document.getElementById('themeToggleBtn');
        if (themeToggleBtn) {
            themeToggleBtn.addEventListener('click', () => {
                const isDark = document.documentElement.classList.toggle('dark');
                localStorage.setItem('theme', isDark ? 'dark' : 'light');
                lucide.createIcons();
            });
        }

        // Mobile Menu Toggle
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const mobileMenu = document.getElementById('mobileMenu');
        if (mobileMenuBtn && mobileMenu) {
            mobileMenuBtn.addEventListener('click', () => {
                mobileMenu.classList.toggle('hidden');
            });
        }
    </script>
    @stack('scripts')
</body>
</html>
