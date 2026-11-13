@extends('layouts.app', ['title' => 'Daftar Aspirasi Publik — Kotak Aspirasi'])

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12 space-y-8">

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 border-b border-border pb-6">
        <div class="space-y-1">
            <div class="inline-flex items-center gap-2 text-xs font-semibold text-primary uppercase tracking-wider">
                <i data-lucide="layout-list" class="w-3.5 h-3.5"></i> Transparansi Publik
            </div>
            <h1 class="text-3xl font-extrabold tracking-tight text-foreground">Daftar Aspirasi & Tindak Lanjut</h1>
            <p class="text-sm text-muted-foreground">Pantau seluruh aspirasi yang disampaikan secara terbuka beserta tanggapan resmi tim pengelola.</p>
        </div>

        <a href="{{ url('/#form-aspirasi') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-primary text-primary-foreground font-semibold text-sm shadow hover:opacity-95 transition-all self-start md:self-auto">
            <i data-lucide="plus-circle" class="w-4 h-4"></i>
            <span>Tulis Aspirasi Baru</span>
        </a>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="rounded-2xl border border-border bg-card p-4 sm:p-5 shadow-sm space-y-4">
        <div class="flex flex-col sm:flex-row gap-3 items-center justify-between">
            <!-- Search Bar -->
            <div class="relative w-full sm:max-w-md">
                <i data-lucide="search" class="w-4 h-4 text-muted-foreground absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                <input type="text" id="searchInput" placeholder="Cari judul, pesan, nomor tiket, atau kategori..." class="w-full pl-10 pr-4 py-2 rounded-xl border border-border bg-background text-sm focus:outline-none focus:ring-2 focus:ring-ring">
            </div>

            <!-- View Mode Switcher (Card vs Table) -->
            <div class="flex items-center gap-1 bg-background border border-border p-1 rounded-xl self-end sm:self-auto">
                <button type="button" id="btnViewCards" onclick="switchView('cards')" class="p-2 rounded-lg text-xs font-semibold transition-colors bg-secondary text-secondary-foreground flex items-center gap-1.5" title="Tampilan Kartu">
                    <i data-lucide="layout-grid" class="w-4 h-4"></i>
                    <span class="hidden sm:inline">Kartu</span>
                </button>
                <button type="button" id="btnViewTable" onclick="switchView('table')" class="p-2 rounded-lg text-xs font-semibold transition-colors text-muted-foreground hover:text-foreground flex items-center gap-1.5" title="Tampilan Tabel">
                    <i data-lucide="table" class="w-4 h-4"></i>
                    <span class="hidden sm:inline">Tabel</span>
                </button>
            </div>
        </div>

        <!-- Filter Status & Category Pills -->
        <div class="flex flex-wrap items-center gap-2 pt-2 border-t border-border/60">
            <span class="text-xs font-semibold text-muted-foreground mr-1">Status:</span>
            <button type="button" onclick="filterStatus('all')" class="status-btn active px-3 py-1 rounded-lg text-xs font-medium border border-border bg-secondary text-secondary-foreground transition-all">Semua</button>
            <button type="button" onclick="filterStatus('pending')" class="status-btn px-3 py-1 rounded-lg text-xs font-medium border border-border bg-background hover:bg-secondary transition-all flex items-center gap-1">
                <span class="w-2 h-2 rounded-full bg-amber-500"></span> Pending
            </button>
            <button type="button" onclick="filterStatus('diproses')" class="status-btn px-3 py-1 rounded-lg text-xs font-medium border border-border bg-background hover:bg-secondary transition-all flex items-center gap-1">
                <span class="w-2 h-2 rounded-full bg-primary"></span> Diproses
            </button>
            <button type="button" onclick="filterStatus('selesai')" class="status-btn px-3 py-1 rounded-lg text-xs font-medium border border-border bg-background hover:bg-secondary transition-all flex items-center gap-1">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Selesai
            </button>
        </div>
    </div>

    <!-- Cards Grid View (Default) -->
    <div id="cardsContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($data as $item)
        <div class="aspirasi-card rounded-2xl border border-border bg-card p-6 flex flex-col justify-between hover:border-primary/50 transition-all shadow-sm group"
             data-status="{{ strtolower($item->status ?? 'pending') }}"
             data-text="{{ strtolower(($item->judul ?? '') . ' ' . ($item->pesan ?? '') . ' ' . ($item->kode_tiket ?? '') . ' ' . ($item->kategori ?? 'Umum')) }}">
            
            <div class="space-y-4">
                <!-- Top Meta: Ticket & Status Badge -->
                <div class="flex items-center justify-between gap-2">
                    <span class="font-mono text-[11px] px-2.5 py-1 rounded-md bg-background border border-border text-muted-foreground font-semibold">
                        {{ $item->kode_tiket ?? ('ASP-' . str_pad($item->id, 5, '0', STR_PAD_LEFT)) }}
                    </span>

                    @php
                        $st = strtolower($item->status ?? 'pending');
                    @endphp
                    @if($st === 'selesai')
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-500 border border-emerald-500/20">
                            <i data-lucide="check-circle" class="w-3.5 h-3.5"></i> Selesai
                        </span>
                    @elseif($st === 'diproses')
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-primary/10 text-primary border border-primary/20">
                            <i data-lucide="loader-2" class="w-3.5 h-3.5 animate-spin"></i> Diproses
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-500/10 text-amber-500 border border-amber-500/20">
                            <i data-lucide="clock" class="w-3.5 h-3.5"></i> Pending
                        </span>
                    @endif
                </div>

                <!-- Title & Body -->
                <div>
                    <h3 class="font-bold text-base text-foreground group-hover:text-primary transition-colors line-clamp-2">
                        {{ $item->judul ?? 'Aspirasi Warga / Siswa' }}
                    </h3>
                    <p class="text-xs text-muted-foreground mt-2 line-clamp-3 leading-relaxed">
                        {{ $item->pesan }}
                    </p>
                </div>
            </div>

            <!-- Footer Meta -->
            <div class="pt-5 mt-5 border-t border-border flex items-center justify-between text-xs text-muted-foreground">
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 rounded-full bg-secondary flex items-center justify-center text-[10px] font-bold text-foreground">
                        {{ substr($item->is_anonim ? 'A' : ($item->nama ?? 'U'), 0, 1) }}
                    </div>
                    <span class="font-medium text-foreground">
                        {{ $item->is_anonim ? 'Anonim' : ($item->nama ?? 'Pengirim') }}
                    </span>
                    @if($item->kelas)
                        <span class="text-muted-foreground/60">•</span>
                        <span>{{ $item->kelas }}</span>
                    @endif
                </div>

                <button type="button" 
                        onclick="openDetailModal({{ json_encode($item) }})"
                        class="text-primary hover:underline font-semibold flex items-center gap-1">
                    Detail <i data-lucide="arrow-right" class="w-3 h-3"></i>
                </button>
            </div>
        </div>
        @empty
        <div class="col-span-full py-16 text-center rounded-2xl border border-dashed border-border bg-card/40">
            <i data-lucide="inbox" class="w-12 h-12 text-muted-foreground mx-auto mb-3"></i>
            <h3 class="font-bold text-base text-foreground">Belum ada aspirasi publik</h3>
            <p class="text-xs text-muted-foreground mt-1">Jadilah orang pertama yang mengirimkan aspirasi untuk perubahan lebih baik!</p>
        </div>
        @endforelse
    </div>

    <!-- Table View (Hidden by default) -->
    <div id="tableContainer" class="hidden rounded-2xl border border-border bg-card overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-border bg-background/50 text-muted-foreground font-semibold uppercase tracking-wider">
                        <th class="p-4">Tiket</th>
                        <th class="p-4">Pengirim</th>
                        <th class="p-4">Aspirasi</th>
                        <th class="p-4">Status</th>
                        <th class="p-4">Tanggal</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($data as $item)
                    <tr class="aspirasi-row hover:bg-secondary/40 transition-colors"
                        data-status="{{ strtolower($item->status ?? 'pending') }}"
                        data-text="{{ strtolower(($item->judul ?? '') . ' ' . ($item->pesan ?? '') . ' ' . ($item->kode_tiket ?? '')) }}">
                        <td class="p-4 font-mono font-semibold text-foreground">
                            {{ $item->kode_tiket ?? ('ASP-' . str_pad($item->id, 5, '0', STR_PAD_LEFT)) }}
                        </td>
                        <td class="p-4 font-medium text-foreground">
                            {{ $item->is_anonim ? 'Anonim' : ($item->nama ?? 'Pengirim') }}
                            @if($item->kelas)
                                <div class="text-[11px] text-muted-foreground">{{ $item->kelas }}</div>
                            @endif
                        </td>
                        <td class="p-4 max-w-xs sm:max-w-md">
                            <div class="font-semibold text-foreground truncate">{{ $item->judul ?? 'Aspirasi' }}</div>
                            <div class="text-muted-foreground truncate text-[11px]">{{ $item->pesan }}</div>
                        </td>
                        <td class="p-4">
                            @php $st = strtolower($item->status ?? 'pending'); @endphp
                            @if($st === 'selesai')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-500/10 text-emerald-500">
                                    Selesai
                                </span>
                            @elseif($st === 'diproses')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-primary/10 text-primary">
                                    Diproses
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-amber-500/10 text-amber-500">
                                    Pending
                                </span>
                            @endif
                        </td>
                        <td class="p-4 text-muted-foreground whitespace-nowrap">
                            {{ $item->created_at ? $item->created_at->format('d M Y') : date('d M Y') }}
                        </td>
                        <td class="p-4 text-right">
                            <button type="button" onclick="openDetailModal({{ json_encode($item) }})" class="px-2.5 py-1 rounded-lg border border-border bg-background hover:bg-secondary text-primary font-medium text-[11px] transition-colors">
                                Rincian
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-muted-foreground">Tidak ada data aspirasi ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Modal Detail Aspirasi & Tanggapan Resmi -->
<div id="detailModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm hidden">
    <div class="relative w-full max-w-2xl rounded-2xl border border-border bg-card p-6 sm:p-8 shadow-2xl max-h-[90vh] overflow-y-auto">
        <button type="button" onclick="closeDetailModal()" class="absolute top-5 right-5 p-2 rounded-lg text-muted-foreground hover:text-foreground hover:bg-secondary">
            <i data-lucide="x" class="w-5 h-5"></i>
        </button>

        <div class="space-y-6">
            <div class="space-y-1">
                <div class="flex items-center gap-2" id="modalStatusBadge"></div>
                <h3 id="modalJudul" class="text-xl font-bold text-foreground"></h3>
                <div class="flex items-center gap-3 text-xs text-muted-foreground">
                    <span id="modalTiket" class="font-mono font-semibold"></span>
                    <span>•</span>
                    <span id="modalPengirim"></span>
                </div>
            </div>

            <!-- Isi Pesan -->
            <div class="p-4 rounded-xl border border-border bg-background/50 space-y-2">
                <span class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">Isi Aspirasi</span>
                <p id="modalPesan" class="text-sm text-foreground leading-relaxed whitespace-pre-wrap"></p>
            </div>

            <!-- Tanggapan Resmi Admin Section -->
            <div class="space-y-3 pt-2">
                <h4 class="text-sm font-bold text-foreground flex items-center gap-2">
                    <i data-lucide="shield-check" class="w-4 h-4 text-primary"></i>
                    Tanggapan Resmi Pengelola
                </h4>
                <div id="modalTanggapanContainer" class="p-4 rounded-xl border border-primary/30 bg-primary/5 space-y-2">
                    <p id="modalTanggapanText" class="text-xs sm:text-sm text-foreground leading-relaxed">
                        Belum ada tanggapan resmi dari pihak admin atau pengelola. Aspirasi Anda masih dalam antrean peninjauan.
                    </p>
                </div>
            </div>

            <div class="pt-4 border-t border-border flex justify-end">
                <button type="button" onclick="closeDetailModal()" class="px-5 py-2 rounded-xl bg-secondary text-secondary-foreground font-semibold text-xs hover:opacity-90 transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let activeFilter = 'all';

    function switchView(type) {
        const cards = document.getElementById('cardsContainer');
        const table = document.getElementById('tableContainer');
        const btnCards = document.getElementById('btnViewCards');
        const btnTable = document.getElementById('btnViewTable');

        if (type === 'cards') {
            cards.classList.remove('hidden');
            table.classList.add('hidden');
            btnCards.classList.add('bg-secondary', 'text-secondary-foreground');
            btnCards.classList.remove('text-muted-foreground');
            btnTable.classList.remove('bg-secondary', 'text-secondary-foreground');
            btnTable.classList.add('text-muted-foreground');
        } else {
            cards.classList.add('hidden');
            table.classList.remove('hidden');
            btnTable.classList.add('bg-secondary', 'text-secondary-foreground');
            btnTable.classList.remove('text-muted-foreground');
            btnCards.classList.remove('bg-secondary', 'text-secondary-foreground');
            btnCards.classList.add('text-muted-foreground');
        }
    }

    function filterStatus(status) {
        activeFilter = status;
        document.querySelectorAll('.status-btn').forEach(btn => {
            btn.classList.remove('active', 'bg-secondary', 'text-secondary-foreground');
            btn.classList.add('bg-background');
        });
        event.currentTarget.classList.add('active', 'bg-secondary', 'text-secondary-foreground');
        event.currentTarget.classList.remove('bg-background');
        applyFilters();
    }

    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', applyFilters);
    }

    function applyFilters() {
        const query = (searchInput?.value || '').toLowerCase();
        const cards = document.querySelectorAll('.aspirasi-card');
        const rows = document.querySelectorAll('.aspirasi-row');

        const filterElement = (el) => {
            const status = el.getAttribute('data-status');
            const text = el.getAttribute('data-text');
            const matchStatus = activeFilter === 'all' || status === activeFilter;
            const matchQuery = !query || text.includes(query);
            el.style.display = (matchStatus && matchQuery) ? '' : 'none';
        };

        cards.forEach(filterElement);
        rows.forEach(filterElement);
    }

    function openDetailModal(item) {
        document.getElementById('modalJudul').textContent = item.judul || 'Aspirasi Masuk';
        document.getElementById('modalTiket').textContent = item.kode_tiket || ('ASP-' + String(item.id).padStart(5, '0'));
        document.getElementById('modalPengirim').textContent = item.is_anonim ? 'Anonim' : (item.nama + (item.kelas ? ' (' + item.kelas + ')' : ''));
        document.getElementById('modalPesan').textContent = item.pesan;

        const badge = document.getElementById('modalStatusBadge');
        const st = (item.status || 'pending').toLowerCase();
        if (st === 'selesai') {
            badge.innerHTML = '<span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-500">Selesai Ditindaklanjuti</span>';
        } else if (st === 'diproses') {
            badge.innerHTML = '<span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-primary/10 text-primary">Sedang Diproses</span>';
        } else {
            badge.innerHTML = '<span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-500/10 text-amber-500">Menunggu Peninjauan</span>';
        }

        const tanggapanText = document.getElementById('modalTanggapanText');
        if (item.tanggapans && item.tanggapans.length > 0) {
            tanggapanText.textContent = item.tanggapans[0].isi_tanggapan;
        } else if (item.tanggapan_terakhir) {
            tanggapanText.textContent = item.tanggapan_terakhir;
        } else {
            tanggapanText.textContent = 'Belum ada tanggapan resmi dari pihak admin atau pengelola. Aspirasi Anda masih dalam antrean peninjauan.';
        }

        document.getElementById('detailModal').classList.remove('hidden');
        lucide.createIcons();
    }

    function closeDetailModal() {
        document.getElementById('detailModal').classList.add('hidden');
    }
</script>
@endpush
@endsection
