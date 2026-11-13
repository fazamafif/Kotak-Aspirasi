@extends('layouts.app', ['title' => 'Login Petugas Admin — Kotak Aspirasi'])

@section('content')
<div class="min-h-[calc(100vh-4rem)] flex items-center justify-center px-4 py-12 sm:px-6 lg:px-8">
    <div class="w-full max-w-md space-y-8">
        
        <!-- Header Card -->
        <div class="text-center space-y-2">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-primary/10 border border-primary/20 text-primary mb-2 shadow-lg shadow-primary/5">
                <i data-lucide="shield-check" class="w-7 h-7"></i>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-foreground">
                Masuk Panel Admin
            </h1>
            <p class="text-xs sm:text-sm text-muted-foreground">
                Silakan masukkan email dan kata sandi petugas untuk mengelola aspirasi.
            </p>
        </div>

        <!-- Form Card -->
        <div class="rounded-2xl border border-border bg-card p-6 sm:p-8 shadow-xl">
            @if(session('success'))
                <div class="mb-5 p-3.5 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 text-xs font-semibold flex items-center gap-2">
                    <i data-lucide="check-circle" class="w-4 h-4 shrink-0"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-5 p-3.5 rounded-xl bg-destructive/10 border border-destructive/20 text-destructive text-xs font-semibold space-y-1">
                    <div class="flex items-center gap-2">
                        <i data-lucide="alert-circle" class="w-4 h-4 shrink-0"></i>
                        <span>Terjadi kesalahan saat masuk:</span>
                    </div>
                    <ul class="list-disc list-inside text-[11px] pl-2 font-normal">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}" class="space-y-5">
                @csrf

                <!-- Input Email -->
                <div class="space-y-1.5">
                    <label for="email" class="block text-xs font-bold text-foreground">
                        Alamat Email Petugas <span class="text-destructive">*</span>
                    </label>
                    <div class="relative">
                        <i data-lucide="mail" class="w-4 h-4 text-muted-foreground absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               value="{{ old('email') }}" 
                               required 
                               autofocus 
                               placeholder="admin@kotakaspirasi.com" 
                               class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-border bg-background text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-ring transition-colors">
                    </div>
                </div>

                <!-- Input Password -->
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between">
                        <label for="password" class="block text-xs font-bold text-foreground">
                            Kata Sandi <span class="text-destructive">*</span>
                        </label>
                    </div>
                    <div class="relative">
                        <i data-lucide="lock" class="w-4 h-4 text-muted-foreground absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                        <input type="password" 
                               id="password" 
                               name="password" 
                               required 
                               placeholder="••••••••" 
                               class="w-full pl-10 pr-10 py-2.5 rounded-xl border border-border bg-background text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-ring transition-colors">
                        <button type="button" 
                                onclick="togglePasswordVisibility()" 
                                class="absolute right-3.5 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground">
                            <i data-lucide="eye" id="togglePasswordIcon" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-primary text-primary-foreground text-xs sm:text-sm font-bold shadow-lg shadow-primary/20 hover:opacity-95 transition-all active:scale-[0.98] flex items-center justify-center gap-2">
                    <i data-lucide="log-in" class="w-4 h-4"></i>
                    <span>Masuk ke Dashboard</span>
                </button>
            </form>
        </div>

        <!-- Back to Home -->
        <div class="text-center">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-muted-foreground hover:text-foreground transition-colors">
                <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                <span>Kembali ke Beranda Publik</span>
            </a>
        </div>

    </div>
</div>

@push('scripts')
<script>
    function togglePasswordVisibility() {
        const input = document.getElementById('password');
        const icon  = document.getElementById('togglePasswordIcon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.setAttribute('data-lucide', 'eye-off');
        } else {
            input.type = 'password';
            icon.setAttribute('data-lucide', 'eye');
        }
        lucide.createIcons();
    }
</script>
@endpush
@endsection
