@extends('layouts.app', ['title' => 'Aspirasi Berhasil Dikirim — ' . $kodeTiket])

@section('content')
<div class="min-h-[70vh] flex items-center justify-center py-16 px-4">
    <div class="w-full max-w-lg space-y-6">

        {{-- Animasi Centang Berhasil --}}
        <div class="flex justify-center">
            <div class="relative w-20 h-20">
                <div class="absolute inset-0 rounded-full bg-emerald-500/20 animate-ping"></div>
                <div class="relative w-20 h-20 rounded-full bg-emerald-500/10 border-2 border-emerald-500 flex items-center justify-center">
                    <i data-lucide="check" class="w-9 h-9 text-emerald-500"></i>
                </div>
            </div>
        </div>

        {{-- Heading --}}
        <div class="text-center space-y-2">
            <h1 class="text-2xl sm:text-3xl font-extrabold text-foreground tracking-tight">
                Aspirasi Berhasil Terkirim!
            </h1>
            <p class="text-sm text-muted-foreground">
                Terima kasih telah menyuarakan aspirasimu. Tim pengelola akan segera meninjaunya.
            </p>
        </div>

        {{-- Tiket Card — Inti tampilan #3 --}}
        <div class="rounded-2xl border-2 border-primary/40 bg-card shadow-xl shadow-primary/5 overflow-hidden">
            {{-- Header tiket --}}
            <div class="bg-gradient-to-r from-primary/20 via-primary/10 to-transparent px-6 py-4 border-b border-border flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-primary/20 flex items-center justify-center text-primary">
                    <i data-lucide="ticket" class="w-5 h-5"></i>
                </div>
                <div>
                    <div class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">Nomor Tiket Aspirasi Anda</div>
                    <div class="text-xs text-muted-foreground">Simpan kode ini untuk memantau tindak lanjut</div>
                </div>
            </div>

            {{-- Kode Tiket Besar --}}
            <div class="px-6 py-8 text-center space-y-3">
                <div class="inline-block">
                    <span id="tiketCode"
                          class="font-mono text-3xl sm:text-4xl font-black tracking-[0.12em] text-foreground select-all cursor-text">
                        {{ $kodeTiket }}
                    </span>
                </div>

                {{-- Tombol Salin --}}
                <div>
                    <button type="button" onclick="salinTiket()"
                            id="salinBtn"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-border bg-secondary text-secondary-foreground font-semibold text-xs hover:opacity-90 transition-all">
                        <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                        <span id="salinBtnText">Salin Kode Tiket</span>
                    </button>
                </div>
            </div>

            {{-- Info tambahan dari aspirasi --}}
            @if($aspirasi)
            <div class="px-6 pb-6 space-y-3 border-t border-border pt-4 text-xs">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <div class="text-muted-foreground font-medium mb-0.5">Status Saat Ini</div>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full font-semibold bg-amber-500/10 text-amber-500 border border-amber-500/20">
                            Menunggu Peninjauan
                        </span>
                    </div>
                    <div>
                        <div class="text-muted-foreground font-medium mb-0.5">Tanggal Kirim</div>
                        <div class="font-semibold text-foreground">{{ $aspirasi->created_at->format('d M Y, H:i') }}</div>
                    </div>
                    <div class="col-span-2">
                        <div class="text-muted-foreground font-medium mb-0.5">Judul Aspirasi</div>
                        <div class="font-semibold text-foreground truncate">{{ $aspirasi->judul }}</div>
                    </div>
                    @if(!$aspirasi->is_anonim && $aspirasi->nama)
                    <div>
                        <div class="text-muted-foreground font-medium mb-0.5">Nama Pengirim</div>
                        <div class="font-semibold text-foreground">{{ $aspirasi->nama }}</div>
                    </div>
                    @else
                    <div>
                        <div class="text-muted-foreground font-medium mb-0.5">Identitas</div>
                        <div class="font-semibold text-foreground flex items-center gap-1">
                            <i data-lucide="eye-off" class="w-3 h-3 text-muted-foreground"></i> Anonim
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        {{-- Alur Selanjutnya --}}
        <div class="rounded-2xl border border-border bg-card p-5 space-y-3">
            <h3 class="text-sm font-bold text-foreground flex items-center gap-2">
                <i data-lucide="git-merge" class="w-4 h-4 text-primary"></i>
                Apa yang Terjadi Selanjutnya?
            </h3>
            <div class="space-y-2.5 text-xs text-muted-foreground">
                <div class="flex items-start gap-2.5">
                    <span class="w-5 h-5 rounded-full bg-secondary text-foreground font-bold flex items-center justify-center shrink-0 text-[10px]">1</span>
                    <span>Admin akan <strong class="text-foreground">meninjau aspirasi</strong> Anda dalam waktu 1×24 jam kerja.</span>
                </div>
                <div class="flex items-start gap-2.5">
                    <span class="w-5 h-5 rounded-full bg-secondary text-foreground font-bold flex items-center justify-center shrink-0 text-[10px]">2</span>
                    <span>Status aspirasi akan berubah dari <em class="text-amber-500">Pending → Diproses → Selesai</em>.</span>
                </div>
                <div class="flex items-start gap-2.5">
                    <span class="w-5 h-5 rounded-full bg-secondary text-foreground font-bold flex items-center justify-center shrink-0 text-[10px]">3</span>
                    <span>Gunakan <strong class="text-primary font-mono">{{ $kodeTiket }}</strong> untuk memantau status kapanpun.</span>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="flex flex-col sm:flex-row items-center gap-3 pt-2">
            <a href="{{ route('home') }}"
               class="w-full sm:flex-1 inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl border border-border bg-card font-semibold text-sm hover:bg-secondary transition-colors">
                <i data-lucide="home" class="w-4 h-4"></i>
                Kembali ke Beranda
            </a>
            <a href="{{ route('aspirasi.public') }}"
               class="w-full sm:flex-1 inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-primary text-primary-foreground font-semibold text-sm shadow hover:opacity-95 transition-all">
                <i data-lucide="layout-list" class="w-4 h-4"></i>
                Pantau Daftar Aspirasi
            </a>
        </div>

        {{-- Lacak tiket langsung --}}
        <p class="text-center text-xs text-muted-foreground">
            Atau lacak status tiket ini sekarang →
            <a href="{{ route('aspirasi.public', ['search' => $kodeTiket]) }}"
               class="text-primary font-semibold hover:underline">
                Cari <span class="font-mono">{{ $kodeTiket }}</span>
            </a>
        </p>

    </div>
</div>

@push('scripts')
<script>
    async function salinTiket() {
        const kode = document.getElementById('tiketCode').innerText.trim();
        const btn  = document.getElementById('salinBtn');
        const txt  = document.getElementById('salinBtnText');

        try {
            await navigator.clipboard.writeText(kode);
            txt.textContent = 'Tersalin!';
            btn.classList.add('border-emerald-500', 'text-emerald-500');
            btn.classList.remove('border-border');
            setTimeout(() => {
                txt.textContent = 'Salin Kode Tiket';
                btn.classList.remove('border-emerald-500', 'text-emerald-500');
                btn.classList.add('border-border');
            }, 2500);
        } catch {
            // Fallback untuk browser yang tidak support clipboard API
            const range = document.createRange();
            range.selectNode(document.getElementById('tiketCode'));
            window.getSelection().removeAllRanges();
            window.getSelection().addRange(range);
            document.execCommand('copy');
            txt.textContent = 'Teks dipilih & disalin';
        }
    }
</script>
@endpush
@endsection
