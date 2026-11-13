<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use App\Models\Aspirasi;
use App\Models\Category;

class AspirasiController extends Controller
{
    private function buildStats(): array
    {
        return Cache::remember('landing_stats', 60, function () {
            $total       = Aspirasi::count();
            $selesai     = Aspirasi::where('status', 'selesai')->count();
            $non_pending = Aspirasi::whereIn('status', ['diproses', 'selesai'])->count();

            $persentaseTindakLanjut = $total > 0
                ? (int) round(($non_pending / $total) * 100)
                : 0;

            return [
                'total'                    => $total,
                'selesai'                  => $selesai,
                'persentase_tindak_lanjut' => $persentaseTindakLanjut,
            ];
        });
    }

    public function index()
    {
        $stats = $this->buildStats();

        return view('welcome', compact('stats'));
    }

    public function publicList(Request $request)
    {
        $query = Aspirasi::with('tanggapans', 'category')
            ->where('is_public', true)
            ->latest();

        if ($request->filled('status') && in_array($request->status, ['pending', 'diproses', 'selesai'])) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_tiket', 'like', "%{$search}%")
                  ->orWhere('judul',  'like', "%{$search}%")
                  ->orWhere('pesan',  'like', "%{$search}%");
            });
        }

        $data  = $query->get();
        $stats = $this->buildStats();

        return view('aspirasi.index', compact('data', 'stats'));
    }

    public function adminDashboard()
    {
        $data  = Aspirasi::with('tanggapans', 'category')->latest()->get();
        $stats = $this->buildStats();

        return view('admin.dashboard', compact('data', 'stats'));
    }

    public function store(Request $request)
    {
        $isAnonim = $request->boolean('is_anonim');

        $rules = [
            'judul'    => 'required|string|max:255',
            'pesan'    => 'required|string',
            'kategori' => 'nullable|string|max:100',
            'lampiran' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ];

        if (!$isAnonim) {
            $rules['nama'] = 'required|string|max:100';
        }

        $request->validate($rules);

        $kodeTiket = $this->generateKodeTiket();

        $lampiranPath = null;
        if ($request->hasFile('lampiran') && $request->file('lampiran')->isValid()) {
            $lampiranPath = $request->file('lampiran')->store('lampiran_aspirasi', 'public');
        }

        $categoryId = null;
        if ($request->filled('kategori')) {
            $cat = Category::where('name', $request->kategori)->first();
            $categoryId = $cat?->id;
        }

        $aspirasi = Aspirasi::create([
            'kode_tiket'     => $kodeTiket,
            'category_id'    => $categoryId,
            'nama'           => $isAnonim ? 'Anonim' : $request->input('nama'),
            'kontak'         => $request->input('kontak'),
            'kelas_instansi' => $request->input('kelas'),
            'is_anonim'      => $isAnonim,
            'judul'          => $request->input('judul'),
            'pesan'          => $request->input('pesan'),
            'lampiran'       => $lampiranPath,
            'status'         => 'pending',
            'is_public'      => $request->boolean('is_public', true),
        ]);

        Cache::forget('landing_stats');

        return redirect()
            ->route('aspirasi.sukses', ['tiket' => $aspirasi->kode_tiket])
            ->with('aspirasi_baru', $aspirasi);
    }

    public function sukses(Request $request)
    {
        $kodeTiket = $request->query('tiket');

        if (!$kodeTiket) {
            return redirect()->route('home');
        }

        $aspirasi = Aspirasi::where('kode_tiket', $kodeTiket)->first();

        return view('aspirasi.sukses', compact('kodeTiket', 'aspirasi'));
    }

    public function lacakTiket(Request $request)
    {
        $request->validate(['tiket' => 'required|string']);

        $aspirasi = Aspirasi::with('tanggapans')
            ->where('kode_tiket', strtoupper(trim($request->tiket)))
            ->first();

        if (!$aspirasi) {
            return response()->json([
                'found'   => false,
                'message' => 'Nomor tiket tidak ditemukan. Pastikan tiket sudah benar.'
            ]);
        }

        $tanggapan = $aspirasi->tanggapans->first();

        return response()->json([
            'found'             => true,
            'kode_tiket'        => $aspirasi->kode_tiket,
            'judul'             => $aspirasi->judul,
            'status'            => $aspirasi->status,
            'tanggal'           => $aspirasi->created_at->format('d M Y, H:i'),
            'tanggapan'         => $tanggapan?->isi_tanggapan ?? null,
            'tanggal_tanggapan' => $tanggapan?->created_at?->format('d M Y, H:i') ?? null,
        ]);
    }

    public function updateStatus(Request $request, Aspirasi $aspirasi)
    {
        $request->validate([
            'status'        => 'required|in:pending,diproses,selesai,ditolak',
            'isi_tanggapan' => 'required|string',
        ]);

        $aspirasi->update(['status' => $request->status]);

        $aspirasi->tanggapans()->create([
            'user_id'         => auth()->id() ?? 1,
            'isi_tanggapan'   => $request->isi_tanggapan,
            'status_terakhir' => $request->status,
        ]);

        Cache::forget('landing_stats');

        if ($request->expectsJson()) {
            return response()->json([
                'success'    => true,
                'new_status' => $aspirasi->fresh()->status,
                'message'    => 'Status berhasil diperbarui.',
            ]);
        }

        return redirect()->route('admin.dashboard')
            ->with('success', "Aspirasi #{$aspirasi->kode_tiket} berhasil diperbarui menjadi: " . strtoupper($request->status));
    }

    public function destroy(Aspirasi $aspirasi)
    {
        if ($aspirasi->lampiran && Storage::disk('public')->exists($aspirasi->lampiran)) {
            Storage::disk('public')->delete($aspirasi->lampiran);
        }

        $aspirasi->delete();

        Cache::forget('landing_stats');

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Aspirasi berhasil dihapus.',
            ]);
        }

        return redirect()->route('admin.dashboard')
            ->with('success', 'Aspirasi berhasil dihapus.');
    }

    private function generateKodeTiket(): string
    {
        $prefix = 'ASP-' . date('Ymd') . '-';

        do {
            $pool   = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
            $suffix = '';
            for ($i = 0; $i < 4; $i++) {
                $suffix .= $pool[random_int(0, strlen($pool) - 1)];
            }
            $kode = $prefix . $suffix;
        } while (Aspirasi::where('kode_tiket', $kode)->exists());

        return $kode;
    }
}
