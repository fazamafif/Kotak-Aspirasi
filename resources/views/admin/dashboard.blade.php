@extends('layouts.app', ['title' => 'Dashboard Manajemen Aspirasi — Admin Panel'])

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10 space-y-8">

    <!-- Top Admin Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-border pb-6">
        <div>
            <div class="inline-flex items-center gap-2 text-xs font-semibold text-primary uppercase tracking-wider mb-1">
                <i data-lucide="shield-check" class="w-4 h-4"></i> Admin Control Panel
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-foreground">Manajemen Kotak Aspirasi</h1>
            <p class="text-xs sm:text-sm text-muted-foreground">Kelola, verifikasi, perbarui status aspirasi masuk, dan berikan tanggapan resmi.</p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-border bg-card text-xs font-medium text-foreground">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>{{ auth()->user()->name ?? 'Petugas Aktif' }}</span>
                <span class="text-[10px] uppercase font-bold text-primary bg-primary/10 px-1.5 py-0.5 rounded ml-1">{{ auth()->user()->role ?? 'Admin' }}</span>
            </span>
            <a href="{{ url('/') }}" target="_blank" class="px-3.5 py-1.5 rounded-xl border border-border bg-secondary text-secondary-foreground text-xs font-semibold hover:opacity-90 transition-colors flex items-center gap-1.5">
                <i data-lucide="external-link" class="w-3.5 h-3.5"></i> Lihat Web
            </a>
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit" class="px-3.5 py-1.5 rounded-xl border border-destructive/30 bg-destructive/10 text-destructive text-xs font-semibold hover:bg-destructive/20 transition-colors flex items-center gap-1.5" title="Keluar dari Panel Admin">
                    <i data-lucide="log-out" class="w-3.5 h-3.5"></i> Keluar
                </button>
            </form>
        </div>
    </div>

    <!-- Overview Stats Cards — Sumber: agregasi real-time dari DB -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total -->
        <div class="rounded-2xl border border-border bg-card p-5 shadow-sm space-y-2">
            <div class="flex items-center justify-between text-muted-foreground text-xs font-medium">
                <span>Total Aspirasi</span>
                <i data-lucide="inbox" class="w-4 h-4 text-foreground"></i>
            </div>
            <div class="text-2xl sm:text-3xl font-extrabold text-foreground">{{ $stats['total'] }}</div>
            <p class="text-[11px] text-muted-foreground">Seluruh data yang telah masuk</p>
        </div>

        <!-- Pending -->
        <div class="rounded-2xl border border-amber-500/20 bg-card p-5 shadow-sm space-y-2">
            <div class="flex items-center justify-between text-amber-500 text-xs font-medium">
                <span>Menunggu Respon (Pending)</span>
                <i data-lucide="clock" class="w-4 h-4"></i>
            </div>
            <div class="text-2xl sm:text-3xl font-extrabold text-amber-500">
                {{ $data->where('status', 'pending')->count() }}
            </div>
            <p class="text-[11px] text-muted-foreground">Perlu verifikasi & tanggapan</p>
        </div>

        <!-- Diproses -->
        <div class="rounded-2xl border border-primary/20 bg-card p-5 shadow-sm space-y-2">
            <div class="flex items-center justify-between text-primary text-xs font-medium">
                <span>Sedang Ditindaklanjuti</span>
                <i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i>
            </div>
            <div class="text-2xl sm:text-3xl font-extrabold text-primary">
                {{ $data->where('status', 'diproses')->count() }}
            </div>
            <p class="text-[11px] text-muted-foreground">Dalam penanganan pihak terkait</p>
        </div>

        <!-- Selesai — dari $stats['selesai'] -->
        <div class="rounded-2xl border border-emerald-500/20 bg-card p-5 shadow-sm space-y-2">
            <div class="flex items-center justify-between text-emerald-500 text-xs font-medium">
                <span>Terselesaikan</span>
                <i data-lucide="check-circle-2" class="w-4 h-4"></i>
            </div>
            <div class="text-2xl sm:text-3xl font-extrabold text-emerald-500">{{ $stats['selesai'] }}</div>
            <p class="text-[11px] text-muted-foreground">
                Tindak lanjut: <strong class="text-foreground">{{ $stats['persentase_tindak_lanjut'] }}%</strong>
            </p>
        </div>
    </div>

    <!-- Management Table Section -->
    <div class="rounded-2xl border border-border bg-card shadow-sm overflow-hidden space-y-4 p-5">
        
        <!-- Table Filters & Search -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="relative flex-1 max-w-md">
                <i data-lucide="search" class="w-4 h-4 text-muted-foreground absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                <input type="text" id="adminSearchInput" placeholder="Cari nama, tiket, pengirim, atau isi aspirasi..." class="w-full pl-10 pr-4 py-2 rounded-xl border border-border bg-background text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-ring">
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <select id="adminStatusFilter" onchange="filterAdminTable()" class="px-3 py-2 rounded-xl border border-border bg-background text-xs font-medium focus:outline-none focus:ring-2 focus:ring-ring">
                    <option value="all">Semua Status</option>
                    <option value="pending">Hanya Pending</option>
                    <option value="diproses">Hanya Diproses</option>
                    <option value="selesai">Hanya Selesai</option>
                </select>

                <button type="button" onclick="window.print()" class="px-3 py-2 rounded-xl border border-border bg-secondary text-secondary-foreground text-xs font-semibold hover:opacity-90 transition-colors flex items-center gap-1.5">
                    <i data-lucide="printer" class="w-3.5 h-3.5"></i> Cetak Laporan
                </button>
            </div>
        </div>

        <!-- Table Container -->
        <div class="overflow-x-auto rounded-xl border border-border">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-border bg-background/60 text-muted-foreground font-semibold uppercase tracking-wider">
                        <th class="p-3.5">Tiket</th>
                        <th class="p-3.5">Pengirim</th>
                        <th class="p-3.5">Judul & Pesan Aspirasi</th>
                        <th class="p-3.5">Tanggal</th>
                        <th class="p-3.5">Status Saat Ini</th>
                        <th class="p-3.5 text-right">Aksi Kelola</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border" id="adminTableBody">
                    @forelse($data as $item)
                    @php 
                        $currentStatus = strtolower($item->status ?? 'pending');
                    @endphp
                    <tr class="admin-row hover:bg-secondary/40 transition-colors"
                        id="row-{{ $item->id }}"
                        data-status="{{ $currentStatus }}"
                        data-text="{{ strtolower(($item->judul ?? '') . ' ' . ($item->pesan ?? '') . ' ' . ($item->kode_tiket ?? '') . ' ' . ($item->nama ?? '')) }}">
                        
                        <!-- Tiket -->
                        <td class="p-3.5 font-mono font-semibold text-foreground whitespace-nowrap">
                            {{ $item->kode_tiket ?? ('ASP-' . str_pad($item->id, 5, '0', STR_PAD_LEFT)) }}
                        </td>

                        <!-- Pengirim -->
                        <td class="p-3.5 whitespace-nowrap">
                            @if($item->is_anonim)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-secondary text-secondary-foreground font-semibold text-[11px]">
                                    <i data-lucide="eye-off" class="w-3 h-3 text-muted-foreground"></i> Anonim
                                </span>
                            @else
                                <div class="font-bold text-foreground">{{ $item->nama }}</div>
                                <div class="text-[11px] text-muted-foreground">{{ $item->kelas ?? '-' }}</div>
                            @endif
                        </td>

                        <!-- Pesan -->
                        <td class="p-3.5 max-w-sm">
                            <div class="font-semibold text-foreground truncate">{{ $item->judul ?? 'Aspirasi Masuk' }}</div>
                            <div class="text-muted-foreground line-clamp-2 text-[11px] mt-0.5">{{ $item->pesan }}</div>
                            @if($item->lampiran)
                                <div class="mt-1.5">
                                    <a href="{{ asset('storage/' . $item->lampiran) }}" target="_blank" class="inline-flex items-center gap-1 text-[11px] text-primary hover:underline font-semibold bg-primary/10 px-2 py-0.5 rounded">
                                        <i data-lucide="paperclip" class="w-3 h-3"></i> Lihat Lampiran
                                    </a>
                                </div>
                            @endif
                        </td>

                        <!-- Tanggal -->
                        <td class="p-3.5 text-muted-foreground whitespace-nowrap">
                            {{ $item->created_at ? $item->created_at->format('d M Y, H:i') : date('d M Y') }}
                        </td>

                        <!-- Status Badge -->
                        <td class="p-3.5 whitespace-nowrap" id="status-cell-{{ $item->id }}">
                            @if($currentStatus === 'selesai')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-500 border border-emerald-500/20">
                                    <i data-lucide="check-circle" class="w-3 h-3"></i> Selesai
                                </span>
                            @elseif($currentStatus === 'diproses')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-primary/10 text-primary border border-primary/20">
                                    <i data-lucide="loader" class="w-3 h-3"></i> Diproses
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-500/10 text-amber-500 border border-amber-500/20">
                                    <i data-lucide="clock" class="w-3 h-3"></i> Pending
                                </span>
                            @endif
                        </td>

                        <!-- Aksi -->
                        <td class="p-3.5 text-right whitespace-nowrap">
                            <div class="inline-flex items-center gap-1.5">
                                <button type="button" 
                                        onclick="openTanggapanModal({{ json_encode($item) }})"
                                        class="px-3 py-1.5 rounded-lg bg-primary text-primary-foreground font-semibold text-xs hover:opacity-90 transition-opacity flex items-center gap-1 shadow-sm"
                                        title="Beri Tanggapan & Perbarui Status">
                                    <i data-lucide="message-square" class="w-3.5 h-3.5"></i>
                                    <span>Tanggapi</span>
                                </button>
                                <button type="button" 
                                        onclick="confirmDeleteAspirasi({{ $item->id }}, '{{ $item->kode_tiket ?? ('ASP-' . $item->id) }}')"
                                        class="p-1.5 rounded-lg border border-border bg-card hover:bg-destructive/10 hover:text-destructive hover:border-destructive/30 transition-colors text-muted-foreground"
                                        title="Hapus Aspirasi">
                                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-muted-foreground">Belum ada aspirasi tersimpan di basis data.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Modal Tanggapan & Ubah Status Admin -->
<div id="tanggapanModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm hidden">
    <div class="relative w-full max-w-xl rounded-2xl border border-border bg-card p-6 sm:p-8 shadow-2xl max-h-[90vh] overflow-y-auto">
        <button type="button" onclick="closeTanggapanModal()" class="absolute top-5 right-5 p-2 rounded-lg text-muted-foreground hover:text-foreground hover:bg-secondary">
            <i data-lucide="x" class="w-5 h-5"></i>
        </button>

        <form id="formTanggapanAdmin" onsubmit="handleSimpanTanggapan(event)" class="space-y-6">
            <input type="hidden" id="adminAspirasiId" name="aspirasi_id">

            <div class="space-y-1">
                <div class="inline-flex items-center gap-1.5 text-xs font-semibold text-primary uppercase tracking-wider">
                    <i data-lucide="send" class="w-3.5 h-3.5"></i> Respon Resmi
                </div>
                <h3 class="text-xl font-bold text-foreground">Beri Tanggapan Aspirasi</h3>
                <p id="adminModalTiket" class="text-xs text-muted-foreground font-mono"></p>
            </div>

            <!-- Ringkasan Aspirasi Pengirim -->
            <div class="p-3.5 rounded-xl border border-border bg-background/50 space-y-1 text-xs">
                <div class="font-bold text-foreground" id="adminModalJudul"></div>
                <div class="text-muted-foreground line-clamp-3" id="adminModalPesan"></div>
            </div>

            <!-- Lampiran Jika Ada -->
            <div id="adminModalLampiran" class="hidden p-3 rounded-xl border border-border bg-background/50 text-xs flex items-center justify-between">
                <span class="text-muted-foreground flex items-center gap-1.5">
                    <i data-lucide="file" class="w-4 h-4 text-primary"></i> Berkas Lampiran:
                </span>
                <a id="adminModalLampiranLink" href="#" target="_blank" class="text-primary font-bold hover:underline flex items-center gap-1">
                    <i data-lucide="external-link" class="w-3.5 h-3.5"></i> Buka Lampiran
                </a>
            </div>

            <!-- Pilihan Ubah Status -->
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-foreground">Perbarui Status Aspirasi <span class="text-destructive">*</span></label>
                <div class="grid grid-cols-3 gap-2">
                    <label class="cursor-pointer border border-border rounded-xl p-2.5 flex items-center justify-center gap-2 hover:border-amber-500 transition-all has-[:checked]:border-amber-500 has-[:checked]:bg-amber-500/10">
                        <input type="radio" name="status" value="pending" id="statusRadioPending" class="sr-only">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        <span class="text-xs font-semibold">Pending</span>
                    </label>

                    <label class="cursor-pointer border border-border rounded-xl p-2.5 flex items-center justify-center gap-2 hover:border-primary transition-all has-[:checked]:border-primary has-[:checked]:bg-primary/10">
                        <input type="radio" name="status" value="diproses" id="statusRadioDiproses" class="sr-only">
                        <span class="w-2 h-2 rounded-full bg-primary"></span>
                        <span class="text-xs font-semibold">Diproses</span>
                    </label>

                    <label class="cursor-pointer border border-border rounded-xl p-2.5 flex items-center justify-center gap-2 hover:border-emerald-500 transition-all has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-500/10">
                        <input type="radio" name="status" value="selesai" id="statusRadioSelesai" class="sr-only">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span class="text-xs font-semibold">Selesai</span>
                    </label>
                </div>
            </div>

            <!-- Teks Tanggapan Resmi -->
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-foreground">Isi Tanggapan Resmi / Solusi <span class="text-destructive">*</span></label>
                <textarea id="adminIsiTanggapan" name="isi_tanggapan" rows="4" required placeholder="Tuliskan respon resmi, klarifikasi langkah penanganan, atau bukti penyelesaian..." class="w-full px-3.5 py-2.5 rounded-xl border border-border bg-background text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-ring resize-y"></textarea>
            </div>

            <!-- Modal Action Buttons -->
            <div class="pt-2 border-t border-border flex items-center justify-end gap-2.5">
                <button type="button" onclick="closeTanggapanModal()" class="px-4 py-2.5 rounded-xl border border-border bg-card text-xs font-medium hover:bg-secondary transition-colors">
                    Batal
                </button>
                <button type="submit" id="btnSimpanTanggapan" class="px-5 py-2.5 rounded-xl bg-primary text-primary-foreground text-xs font-bold shadow hover:opacity-95 transition-opacity flex items-center gap-1.5">
                    <i data-lucide="check" class="w-4 h-4"></i>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Peta route updateStatus per id aspirasi — digenerate dari Blade
    const updateRoutes = {
        @foreach($data as $item)
        {{ $item->id }}: "{{ route('admin.update-status', $item->id) }}",
        @endforeach
    };

    let activeAspirasiId = null;

    function openTanggapanModal(item) {
        activeAspirasiId = item.id;

        document.getElementById('adminAspirasiId').value   = item.id;
        document.getElementById('adminModalTiket').textContent  = 'Nomor Tiket: ' + (item.kode_tiket || ('ASP-' + String(item.id).padStart(5, '0')));
        document.getElementById('adminModalJudul').textContent  = item.judul || 'Aspirasi Masuk';
        document.getElementById('adminModalPesan').textContent  = item.pesan;

        // Set radio status
        const currentStatus = (item.status || 'pending').toLowerCase();
        const radios = { selesai: 'statusRadioSelesai', diproses: 'statusRadioDiproses', pending: 'statusRadioPending' };
        document.getElementById(radios[currentStatus] || 'statusRadioPending').checked = true;

        // Isi tanggapan sebelumnya jika ada
        const tanggapanInput = document.getElementById('adminIsiTanggapan');
        if (item.tanggapans && item.tanggapans.length > 0) {
            tanggapanInput.value = item.tanggapans[0].isi_tanggapan;
        } else {
            tanggapanInput.value = '';
        }

        // Tampilkan info lampiran jika tersedia
        const lampiranContainer = document.getElementById('adminModalLampiran');
        const lampiranLink = document.getElementById('adminModalLampiranLink');
        if (item.lampiran) {
            lampiranLink.href = "{{ asset('storage') }}/" + item.lampiran;
            lampiranContainer.classList.remove('hidden');
        } else {
            lampiranContainer.classList.add('hidden');
        }

        document.getElementById('tanggapanModal').classList.remove('hidden');
        lucide.createIcons();
    }

    function closeTanggapanModal() {
        document.getElementById('tanggapanModal').classList.add('hidden');
        activeAspirasiId = null;
    }

    // ── Hapus Aspirasi ──────────────────────────────────────────
    async function confirmDeleteAspirasi(id, kodeTiket) {
        if (!confirm(`Apakah Anda yakin ingin menghapus aspirasi tiket #${kodeTiket} ini secara permanen?`)) {
            return;
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        const deleteUrl = "{{ url('/admin/aspirasi') }}/" + id;

        try {
            const res = await fetch(deleteUrl, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                }
            });

            const json = await res.json();
            if (json.success) {
                const row = document.getElementById('row-' + id);
                if (row) {
                    row.style.transition = 'all 0.3s ease';
                    row.style.opacity = '0';
                    row.style.transform = 'scale(0.95)';
                    setTimeout(() => row.remove(), 300);
                }
                showToast(`Aspirasi #${kodeTiket} berhasil dihapus.`);
            } else {
                alert('Gagal menghapus: ' + (json.message || 'Terjadi kesalahan.'));
            }
        } catch (err) {
            alert('Kesalahan koneksi saat menghapus data.');
            console.error(err);
        }
    }

    // ── PERBAIKAN: Simpan tanggapan + update status via AJAX ke Laravel ──
    async function handleSimpanTanggapan(e) {
        e.preventDefault();

        const id             = document.getElementById('adminAspirasiId').value;
        const selectedStatus = document.querySelector('input[name="status"]:checked')?.value;
        const tanggapanText  = document.getElementById('adminIsiTanggapan').value.trim();
        const submitBtn      = document.getElementById('btnSimpanTanggapan');

        if (!selectedStatus) {
            alert('Pilih status aspirasi terlebih dahulu.');
            return;
        }
        if (!tanggapanText) {
            alert('Isi tanggapan resmi tidak boleh kosong.');
            return;
        }

        // Loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<svg class="animate-spin w-4 h-4 mr-1 inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg> Menyimpan…';

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        const url       = updateRoutes[id];

        if (!url) {
            alert('Route tidak ditemukan untuk aspirasi ID ' + id);
            submitBtn.disabled = false;
            return;
        }

        try {
            const res  = await fetch(url, {
                method:  'POST',
                headers: {
                    'Content-Type':  'application/json',
                    'Accept':        'application/json',
                    'X-CSRF-TOKEN':  csrfToken,
                },
                body: JSON.stringify({
                    status:        selectedStatus,
                    isi_tanggapan: tanggapanText,
                }),
            });

            const json = await res.json();

            if (json.success) {
                // Update UI baris tabel tanpa reload halaman
                updateRowStatus(id, selectedStatus);
                closeTanggapanModal();
                showToast(`Status berhasil diperbarui menjadi: ${selectedStatus.toUpperCase()}`);
            } else {
                alert('Gagal menyimpan: ' + (json.message || 'Terjadi kesalahan.'));
            }
        } catch (err) {
            alert('Kesalahan koneksi ke server. Coba lagi.');
            console.error(err);
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i data-lucide="check" class="w-4 h-4 mr-1"></i> Simpan Perubahan';
            lucide.createIcons();
        }
    }

    // Update baris tabel secara visual setelah simpan berhasil
    function updateRowStatus(id, newStatus) {
        const row = document.getElementById('row-' + id);
        if (row) {
            row.setAttribute('data-status', newStatus);
        }

        const statusCell = document.getElementById('status-cell-' + id);
        if (!statusCell) return;

        const map = {
            selesai:  '<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-500 border border-emerald-500/20"><i data-lucide="check-circle" class="w-3 h-3"></i> Selesai</span>',
            diproses: '<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-primary/10 text-primary border border-primary/20"><i data-lucide="loader" class="w-3 h-3"></i> Diproses</span>',
            pending:  '<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-500/10 text-amber-500 border border-amber-500/20"><i data-lucide="clock" class="w-3 h-3"></i> Pending</span>',
            ditolak:  '<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-destructive/10 text-destructive border border-destructive/20"><i data-lucide="x-circle" class="w-3 h-3"></i> Ditolak</span>',
        };

        statusCell.innerHTML = map[newStatus] || map.pending;
        lucide.createIcons();
    }

    // Toast notifikasi ringan
    function showToast(message) {
        const toast = document.createElement('div');
        toast.className = 'fixed bottom-6 right-6 z-[100] px-5 py-3 rounded-xl bg-card border border-emerald-500/40 text-sm font-semibold text-foreground shadow-xl transition-all translate-y-0 flex items-center gap-2';
        toast.innerHTML = message;
        document.body.appendChild(toast);
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(1rem)';
            setTimeout(() => toast.remove(), 400);
        }, 3500);
    }

    // Filter dan search
    const adminSearchInput  = document.getElementById('adminSearchInput');
    const adminStatusFilter = document.getElementById('adminStatusFilter');

    if (adminSearchInput) {
        adminSearchInput.addEventListener('input', filterAdminTable);
    }

    function filterAdminTable() {
        const query          = (adminSearchInput?.value || '').toLowerCase();
        const selectedStatus = adminStatusFilter?.value || 'all';
        document.querySelectorAll('.admin-row').forEach(row => {
            const matchStatus = selectedStatus === 'all' || row.getAttribute('data-status') === selectedStatus;
            const matchQuery  = !query || (row.getAttribute('data-text') || '').includes(query);
            row.style.display = (matchStatus && matchQuery) ? '' : 'none';
        });
    }

    // ── Auto Logout — Keluar otomatis setelah tidak ada aktivitas ──
    (function() {
        const AUTO_LOGOUT_MINUTES = {{ config('session.lifetime', 120) }};
        const WARN_BEFORE_SECONDS = 60; // Tampilkan peringatan 60 detik sebelum logout
        const TIMEOUT_MS  = AUTO_LOGOUT_MINUTES * 60 * 1000;
        const WARN_MS     = TIMEOUT_MS - (WARN_BEFORE_SECONDS * 1000);

        let logoutTimer   = null;
        let warningTimer  = null;
        let warningEl     = null;
        let countdownId   = null;

        function createWarningBanner() {
            if (warningEl) return;
            warningEl = document.createElement('div');
            warningEl.id = 'autoLogoutWarning';
            warningEl.className = 'fixed top-0 left-0 right-0 z-[200] bg-amber-500 text-black text-center py-3 px-4 text-sm font-bold shadow-lg flex items-center justify-center gap-3 transition-all';
            warningEl.innerHTML = `
                <svg class="w-5 h-5 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>
                <span>Sesi Anda akan berakhir dalam <strong id="autoLogoutCountdown">${WARN_BEFORE_SECONDS}</strong> detik karena tidak ada aktivitas.</span>
                <button onclick="window._resetAutoLogout()" class="ml-2 px-3 py-1 rounded-lg bg-black/20 hover:bg-black/30 text-xs font-bold transition-colors">Tetap Masuk</button>
            `;
            document.body.prepend(warningEl);

            let remaining = WARN_BEFORE_SECONDS;
            const cdEl = document.getElementById('autoLogoutCountdown');
            countdownId = setInterval(() => {
                remaining--;
                if (cdEl) cdEl.textContent = remaining;
                if (remaining <= 0) clearInterval(countdownId);
            }, 1000);
        }

        function removeWarningBanner() {
            if (warningEl) {
                warningEl.remove();
                warningEl = null;
            }
            if (countdownId) {
                clearInterval(countdownId);
                countdownId = null;
            }
        }

        function performLogout() {
            removeWarningBanner();
            // Submit form logout
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = "{{ route('logout') }}";
            form.innerHTML = `@csrf <input type="hidden" name="auto_logout" value="1">`;
            document.body.appendChild(form);
            form.submit();
        }

        function resetTimers() {
            removeWarningBanner();
            clearTimeout(warningTimer);
            clearTimeout(logoutTimer);

            warningTimer = setTimeout(createWarningBanner, WARN_MS);
            logoutTimer  = setTimeout(performLogout, TIMEOUT_MS);
        }

        // Expose untuk tombol "Tetap Masuk"
        window._resetAutoLogout = resetTimers;

        // Reset timer setiap ada aktivitas pengguna
        const events = ['mousemove', 'mousedown', 'keydown', 'touchstart', 'scroll', 'click'];
        events.forEach(evt => document.addEventListener(evt, resetTimers, { passive: true }));

        // Mulai timer pertama kali
        resetTimers();
    })();
</script>
@endpush
@endsection
