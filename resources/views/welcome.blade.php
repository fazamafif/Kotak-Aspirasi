@extends('layouts.app', ['title' => 'Kotak Aspirasi — Suara Anda untuk Masa Depan Lebih Baik'])

@section('content')
<div class="space-y-16 py-8 sm:py-12">

    <!-- Hero Section -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="relative overflow-hidden rounded-3xl border border-border bg-gradient-to-b from-card to-card/60 p-8 sm:p-12 lg:p-16 shadow-xl">
            <!-- Background Glow Accent -->
            <div class="absolute -top-24 -right-24 w-96 h-96 bg-primary/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-accent/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10 max-w-3xl space-y-6">
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full border border-primary/30 bg-primary/10 text-primary text-xs font-semibold uppercase tracking-wider">
                    <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
                    Saluran Aspirasi Bebas & Terbuka
                </div>

                <h1 class="text-3xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight leading-tight">
                    Sampaikan Aspirasimu, <br>
                    <span class="bg-gradient-to-r from-primary via-blue-400 to-indigo-300 bg-clip-text text-transparent">
                        Wujudkan Perubahan Nyata.
                    </span>
                </h1>

                <p class="text-base sm:text-lg text-muted-foreground leading-relaxed max-w-2xl">
                    Kirimkan kritik membangun, ide segar, keluhan sarana, atau saran secara mudah dan aman. Anda memiliki kendali penuh untuk mengirimkannya secara <strong>Anonim</strong> atau Terbuka.
                </p>

                <div class="flex flex-wrap items-center gap-4 pt-2">
                    <a href="#form-aspirasi" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-primary text-primary-foreground font-semibold shadow-lg shadow-primary/25 hover:opacity-95 transition-all">
                        <i data-lucide="edit-3" class="w-4 h-4"></i>
                        <span>Tulis Aspirasi Sekarang</span>
                    </a>
                    <a href="{{ url('/aspirasi') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl border border-border bg-card hover:bg-secondary transition-colors font-medium">
                        <i data-lucide="layout-list" class="w-4 h-4"></i>
                        <span>Lihat Aspirasi Publik</span>
                    </a>
                </div>

                <!-- Stats Counters — Sumber: agregasi query real-time dari DB -->
                <div class="pt-8 border-t border-border/80 grid grid-cols-2 sm:grid-cols-4 gap-4">
                    {{-- Total aspirasi: SELECT COUNT(*) FROM aspirasis --}}
                    <div>
                        <div class="text-2xl sm:text-3xl font-bold text-foreground" id="statHeroTotal">
                            {{ number_format($stats['total']) }}
                        </div>
                        <div class="text-xs text-muted-foreground font-medium mt-0.5">Aspirasi Masuk</div>
                    </div>

                    {{-- Tindak lanjut: (diproses + selesai) / total × 100 --}}
                    <div>
                        <div class="text-2xl sm:text-3xl font-bold text-primary" id="statHeroTindakLanjut">
                            {{ $stats['persentase_tindak_lanjut'] }}%
                        </div>
                        <div class="text-xs text-muted-foreground font-medium mt-0.5">Tingkat Tindak Lanjut</div>
                    </div>

                    {{-- Selesai: SELECT COUNT(*) … WHERE status = 'selesai' --}}
                    <div>
                        <div class="text-2xl sm:text-3xl font-bold text-emerald-500" id="statHeroSelesai">
                            {{ number_format($stats['selesai']) }}
                        </div>
                        <div class="text-xs text-muted-foreground font-medium mt-0.5">Masalah Selesai</div>
                    </div>

                    {{-- Privasi anonim: selalu 100% — jaminan kebijakan platform --}}
                    <div>
                        <div class="text-2xl sm:text-3xl font-bold text-amber-500">100%</div>
                        <div class="text-xs text-muted-foreground font-medium mt-0.5">Privasi Anonim Dijamin</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Track Aspirasi by Ticket Section — AJAX Real-Time -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="rounded-2xl border border-border bg-card p-6 shadow-sm">
            <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="space-y-1">
                    <div class="flex items-center gap-2 text-primary font-semibold text-sm">
                        <i data-lucide="search" class="w-4 h-4"></i>
                        <span>Lacak Status Aspirasi</span>
                    </div>
                    <h3 class="text-lg font-bold text-foreground">Punya Kode Tiket Aspirasi?</h3>
                    <p class="text-xs sm:text-sm text-muted-foreground">Masukkan kode unik (contoh: ASP-20261028-F94A) untuk mengecek tanggapan terbaru admin.</p>
                </div>
                <div class="w-full md:w-auto flex-1 max-w-md flex items-center gap-2">
                    <div class="relative flex-1">
                        <i data-lucide="ticket" class="w-4 h-4 text-muted-foreground absolute left-3 top-1/2 -translate-y-1/2"></i>
                        <input type="text" id="lacakTiketInput" placeholder="Contoh: ASP-20261028-F94A"
                               class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-border bg-background text-sm focus:outline-none focus:ring-2 focus:ring-ring uppercase"
                               oninput="this.value = this.value.toUpperCase()">
                    </div>
                    <button type="button" onclick="lacakTiket()" id="lacakBtn"
                            class="px-4 py-2.5 rounded-xl bg-secondary text-secondary-foreground font-medium text-sm hover:opacity-90 transition-colors whitespace-nowrap flex items-center gap-1.5">
                        <i data-lucide="search" class="w-3.5 h-3.5"></i>
                        <span>Cari</span>
                    </button>
                </div>
            </div>

            <!-- Hasil Lacak Tiket -->
            <div id="lacakResult" class="hidden mt-4 pt-4 border-t border-border">
                <!-- Diisi oleh JS -->
            </div>
        </div>
    </section>

    <!-- Main Grid: Form Aspirasi + Info Card -->
    <section id="form-aspirasi" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 scroll-mt-24">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

            <!-- Form Card (8 Cols) -->
            <div class="lg:col-span-8 rounded-2xl border border-border bg-card p-6 sm:p-8 shadow-md">
                <div class="flex items-center justify-between border-b border-border pb-5 mb-6">
                    <div>
                        <div class="inline-flex items-center gap-2 text-xs font-semibold text-primary uppercase tracking-wider mb-1">
                            <i data-lucide="send" class="w-3.5 h-3.5"></i> Form Pengaduan
                        </div>
                        <h2 class="text-2xl font-bold tracking-tight text-foreground">Kirim Aspirasi Baru</h2>
                    </div>
                    <!-- Badge Info -->
                    <span class="text-xs px-3 py-1 rounded-full bg-secondary text-secondary-foreground font-medium flex items-center gap-1.5">
                        <i data-lucide="shield" class="w-3.5 h-3.5 text-primary"></i> Data Terenkripsi
                    </span>
                </div>

                @if(session('success'))
                <div class="mb-6 p-4 rounded-xl border border-emerald-500/30 bg-emerald-500/10 text-emerald-500 flex items-start gap-3">
                    <i data-lucide="check-circle-2" class="w-5 h-5 shrink-0 mt-0.5"></i>
                    <div>
                        <h4 class="font-bold text-sm">Aspirasi Berhasil Dikirim!</h4>
                        <p class="text-xs mt-0.5 text-foreground/80">{{ session('success') }}</p>
                    </div>
                </div>
                @endif

                <form action="{{ route('aspirasi.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6" id="aspirasiForm">
                    @csrf

                    <!-- Toggle Anonim Switch -->
                    <div class="p-4 rounded-xl border border-border bg-background/50 flex items-center justify-between transition-colors hover:border-primary/50">
                        <div class="space-y-0.5">
                            <label for="is_anonim" class="font-bold text-sm text-foreground flex items-center gap-2 cursor-pointer">
                                <i data-lucide="user-x" class="w-4 h-4 text-primary"></i>
                                Kirim Sebagai Anonim (Rahasia)
                            </label>
                            <p class="text-xs text-muted-foreground">Nama dan kontak Anda tidak akan ditampilkan ke publik.</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_anonim" id="is_anonim" value="1" class="sr-only peer" onchange="toggleAnonimFields(this)">
                            <div class="w-11 h-6 bg-border peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-border after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                        </label>
                    </div>

                    <!-- Identitas Fields (Bisa disembunyikan saat anonim) -->
                    <div id="identitasFields" class="grid grid-cols-1 sm:grid-cols-2 gap-4 transition-all duration-300">
                        <div class="space-y-1.5">
                            <label class="block text-xs font-semibold text-foreground">Nama Lengkap <span class="text-destructive">*</span></label>
                            <div class="relative">
                                <i data-lucide="user" class="w-4 h-4 text-muted-foreground absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                                <input type="text" name="nama" id="inputNama" required placeholder="Contoh: Muhammad Rizky" class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-border bg-background text-sm focus:outline-none focus:ring-2 focus:ring-ring">
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-xs font-semibold text-foreground">Kelas / Departemen / Instansi</label>
                            <div class="relative">
                                <i data-lucide="graduation-cap" class="w-4 h-4 text-muted-foreground absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                                <input type="text" name="kelas" placeholder="Contoh: XII IPA 2 / Mahasiswa" class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-border bg-background text-sm focus:outline-none focus:ring-2 focus:ring-ring">
                            </div>
                        </div>

                        <div class="sm:col-span-2 space-y-1.5">
                            <label class="block text-xs font-semibold text-foreground">Kontak (WhatsApp / Email) <span class="text-muted-foreground font-normal">(Opsional untuk update notifikasi)</span></label>
                            <div class="relative">
                                <i data-lucide="phone" class="w-4 h-4 text-muted-foreground absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                                <input type="text" name="kontak" placeholder="08xxxxxxxxxx atau email@anda.com" class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-border bg-background text-sm focus:outline-none focus:ring-2 focus:ring-ring">
                            </div>
                        </div>
                    </div>

                    <!-- Kategori Selector -->
                    <div class="space-y-2">
                        <label class="block text-xs font-semibold text-foreground">Kategori Aspirasi <span class="text-destructive">*</span></label>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                            <label class="cursor-pointer border border-border rounded-xl p-3 flex flex-col items-center gap-1.5 hover:border-primary text-center transition-all has-[:checked]:border-primary has-[:checked]:bg-primary/10">
                                <input type="radio" name="kategori" value="Fasilitas & Sarana" checked class="sr-only">
                                <i data-lucide="building" class="w-5 h-5 text-primary"></i>
                                <span class="text-xs font-medium">Fasilitas</span>
                            </label>
                            <label class="cursor-pointer border border-border rounded-xl p-3 flex flex-col items-center gap-1.5 hover:border-primary text-center transition-all has-[:checked]:border-primary has-[:checked]:bg-primary/10">
                                <input type="radio" name="kategori" value="Akademik & Kurikulum" class="sr-only">
                                <i data-lucide="book-open" class="w-5 h-5 text-primary"></i>
                                <span class="text-xs font-medium">Akademik</span>
                            </label>
                            <label class="cursor-pointer border border-border rounded-xl p-3 flex flex-col items-center gap-1.5 hover:border-primary text-center transition-all has-[:checked]:border-primary has-[:checked]:bg-primary/10">
                                <input type="radio" name="kategori" value="Kebersihan & Kantin" class="sr-only">
                                <i data-lucide="coffee" class="w-5 h-5 text-primary"></i>
                                <span class="text-xs font-medium">Kebersihan</span>
                            </label>
                            <label class="cursor-pointer border border-border rounded-xl p-3 flex flex-col items-center gap-1.5 hover:border-primary text-center transition-all has-[:checked]:border-primary has-[:checked]:bg-primary/10">
                                <input type="radio" name="kategori" value="Layanan & Organisasi" class="sr-only">
                                <i data-lucide="users" class="w-5 h-5 text-primary"></i>
                                <span class="text-xs font-medium">Layanan</span>
                            </label>
                        </div>
                    </div>

                    <!-- Judul Aspirasi -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-foreground">Judul Aspirasi / Topik Utama <span class="text-destructive">*</span></label>
                        <input type="text" name="judul" required placeholder="Tuliskan inti masalah atau ide dalam 1 kalimat ringkas" class="w-full px-4 py-2.5 rounded-xl border border-border bg-background text-sm focus:outline-none focus:ring-2 focus:ring-ring">
                    </div>

                    <!-- Pesan Lengkap -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-foreground">Isi Aspirasi, Keluhan, atau Usulan <span class="text-destructive">*</span></label>
                        <textarea name="pesan" rows="5" required placeholder="Jelaskan secara rinci situasi, lokasi kejadian, saran solusi yang Anda harapkan..." class="w-full px-4 py-3 rounded-xl border border-border bg-background text-sm focus:outline-none focus:ring-2 focus:ring-ring resize-y"></textarea>
                    </div>

                    <!-- Upload Lampiran Foto/Dokumen -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-foreground">Lampiran Bukti Foto / Dokumen <span class="text-muted-foreground font-normal">(Opsional, max 5MB)</span></label>
                        <div class="border-2 border-dashed border-border rounded-xl p-5 text-center hover:border-primary/60 transition-colors bg-background/30">
                            <i data-lucide="image-plus" class="w-8 h-8 text-muted-foreground mx-auto mb-2"></i>
                            <div class="text-xs text-foreground font-medium">
                                <label for="fileUpload" class="text-primary hover:underline cursor-pointer">Pilih file</label> atau tarik dan lepaskan ke sini
                            </div>
                            <p class="text-[11px] text-muted-foreground mt-1">PNG, JPG, PDF hingga 5MB</p>
                            <input id="fileUpload" type="file" name="lampiran" class="hidden">
                        </div>
                    </div>

                    <!-- Opsi Publikasi -->
                    <div class="flex items-center gap-3 pt-2">
                        <input type="checkbox" name="is_public" id="is_public" value="1" checked class="w-4 h-4 rounded border-border text-primary focus:ring-ring">
                        <label for="is_public" class="text-xs text-muted-foreground select-none cursor-pointer">
                            Izinkan aspirasi ini ditampilkan di halaman Aspirasi Publik (status transparansi).
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-3">
                        <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-3.5 rounded-xl bg-primary text-primary-foreground font-bold shadow-lg shadow-primary/25 hover:opacity-95 transition-all active:scale-[0.99]">
                            <i data-lucide="send" class="w-4 h-4"></i>
                            <span>Kirimkan Aspirasi Saya</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Side Cards (4 Cols) -->
            <div class="lg:col-span-4 space-y-6">
                <!-- Info Alur -->
                <div class="rounded-2xl border border-border bg-card p-6 shadow-sm space-y-4">
                    <h3 class="font-bold text-base text-foreground flex items-center gap-2">
                        <i data-lucide="git-merge" class="w-4 h-4 text-primary"></i>
                        Alur Tindak Lanjut
                    </h3>

                    <div class="space-y-4 text-xs">
                        <div class="flex gap-3">
                            <div class="w-6 h-6 rounded-full bg-secondary flex items-center justify-center text-primary font-bold shrink-0">1</div>
                            <div>
                                <span class="font-semibold text-foreground">Aspirasi Terkirim</span>
                                <p class="text-muted-foreground mt-0.5">Sistem menghasilkan nomor tiket unik untuk Anda pantau.</p>
                            </div>
                        </div>

                        <div class="flex gap-3">
                            <div class="w-6 h-6 rounded-full bg-secondary flex items-center justify-center text-primary font-bold shrink-0">2</div>
                            <div>
                                <span class="font-semibold text-foreground">Verifikasi & Peninjauan</span>
                                <p class="text-muted-foreground mt-0.5">Admin meninjau validitas aspirasi dalam waktu 1x24 jam.</p>
                            </div>
                        </div>

                        <div class="flex gap-3">
                            <div class="w-6 h-6 rounded-full bg-secondary flex items-center justify-center text-primary font-bold shrink-0">3</div>
                            <div>
                                <span class="font-semibold text-foreground">Diproses Pihak Terkait</span>
                                <p class="text-muted-foreground mt-0.5">Aspirasi diteruskan ke tim eksekutor lapangan atau pimpinan.</p>
                            </div>
                        </div>

                        <div class="flex gap-3">
                            <div class="w-6 h-6 rounded-full bg-emerald-500/20 text-emerald-500 flex items-center justify-center font-bold shrink-0">4</div>
                            <div>
                                <span class="font-semibold text-foreground">Selesai & Tanggapan Resmi</span>
                                <p class="text-muted-foreground mt-0.5">Admin mengunggah bukti penyelesaian dan tanggapan resmi.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Call to Action Aspirasi Terbuka -->
                <div class="rounded-2xl border border-border bg-gradient-to-br from-secondary/40 to-card p-6 space-y-3">
                    <div class="w-10 h-10 rounded-xl bg-primary/20 text-primary flex items-center justify-center">
                        <i data-lucide="eye" class="w-5 h-5"></i>
                    </div>
                    <h4 class="font-bold text-base text-foreground">Ingin Meninjau Suara Lain?</h4>
                    <p class="text-xs text-muted-foreground leading-relaxed">
                        Lihat aspirasi publik yang telah diverifikasi dan tanggapan resmi dari pihak pengelola.
                    </p>
                    <a href="{{ url('/aspirasi') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-primary hover:underline">
                        Buka Halaman Aspirasi Publik <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                    </a>
                </div>
            </div>

        </div>
    </section>

</div>

@push('scripts')
<script>
    function toggleAnonimFields(checkbox) {
        const identitasFields = document.getElementById('identitasFields');
        const inputNama = document.getElementById('inputNama');

        if (checkbox.checked) {
            identitasFields.classList.add('opacity-40', 'pointer-events-none');
            inputNama.removeAttribute('required');
        } else {
            identitasFields.classList.remove('opacity-40', 'pointer-events-none');
            inputNama.setAttribute('required', 'required');
        }
    }

    // ─── Lacak Tiket: AJAX Real-Time ──────────────────────────────
    async function lacakTiket() {
        const input  = document.getElementById('lacakTiketInput');
        const result = document.getElementById('lacakResult');
        const btn    = document.getElementById('lacakBtn');
        const tiket  = input.value.trim();

        if (!tiket || tiket.length < 5) {
            showLacakError('Masukkan nomor tiket yang valid (minimal 5 karakter).');
            return;
        }

        // Loading state
        btn.disabled = true;
        btn.innerHTML = '<svg class="animate-spin w-4 h-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg> Mencari…';

        try {
            const res  = await fetch(`{{ route('api.lacak') }}?tiket=${encodeURIComponent(tiket)}`);
            const json = await res.json();

            if (json.found) {
                showLacakSuccess(json);
            } else {
                showLacakError(json.message);
            }
        } catch (e) {
            showLacakError('Terjadi kesalahan koneksi. Silakan coba lagi.');
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg> Cari';
        }
    }

    function showLacakSuccess(data) {
        const result = document.getElementById('lacakResult');

        const statusColors = {
            'pending':  'bg-amber-500/10 text-amber-500 border-amber-500/20',
            'diproses': 'bg-primary/10 text-primary border-primary/20',
            'selesai':  'bg-emerald-500/10 text-emerald-500 border-emerald-500/20',
            'ditolak':  'bg-destructive/10 text-destructive border-destructive/20',
        };
        const statusLabels = {
            'pending':  'Menunggu Peninjauan',
            'diproses': 'Sedang Diproses',
            'selesai':  'Selesai Ditindaklanjuti',
            'ditolak':  'Ditolak',
        };

        const badgeClass = statusColors[data.status] || statusColors['pending'];
        const badgeLabel = statusLabels[data.status] || data.status;

        result.innerHTML = `
            <div class="space-y-3">
                <div class="flex flex-wrap items-center gap-3">
                    <span class="font-mono font-bold text-sm text-foreground px-2.5 py-1 bg-background rounded-lg border border-border">${data.kode_tiket}</span>
                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold border ${badgeClass}">${badgeLabel}</span>
                </div>
                <p class="font-semibold text-foreground text-sm">${data.judul}</p>
                <p class="text-xs text-muted-foreground">Dikirim pada ${data.tanggal}</p>
                ${data.tanggapan ? `
                <div class="p-3.5 rounded-xl border border-primary/30 bg-primary/5 space-y-1">
                    <div class="text-xs font-bold text-primary">Tanggapan Resmi (${data.tanggal_tanggapan ?? ''})</div>
                    <p class="text-xs text-foreground leading-relaxed">${data.tanggapan}</p>
                </div>` : `
                <div class="p-3.5 rounded-xl border border-border bg-background/50">
                    <p class="text-xs text-muted-foreground">Aspirasi Anda masih dalam antrean peninjauan. Belum ada tanggapan resmi dari admin.</p>
                </div>`}
            </div>`;

        result.classList.remove('hidden');
    }

    function showLacakError(message) {
        const result = document.getElementById('lacakResult');
        result.innerHTML = `
            <div class="flex items-center gap-2.5 text-destructive text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                <span>${message}</span>
            </div>`;
        result.classList.remove('hidden');
    }

    // Tekan Enter di input tiket
    document.addEventListener('DOMContentLoaded', () => {
        const input = document.getElementById('lacakTiketInput');
        if (input) {
            input.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') lacakTiket();
            });
        }
    });
</script>
@endpush
@endsection
