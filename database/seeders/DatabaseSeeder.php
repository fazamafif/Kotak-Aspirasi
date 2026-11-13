<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Aspirasi;
use App\Models\Tanggapan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Admin & Petugas
        $admin = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Administrator Utama',
                'role' => 'admin',
                'password' => Hash::make('admin123'),
            ]
        );

        $petugas = User::firstOrCreate(
            ['email' => 'petugas@kotakaspirasi.com'],
            [
                'name' => 'Tim Penanganan Sarpras',
                'role' => 'petugas',
                'password' => Hash::make('password123'),
            ]
        );

        // 2. Seed Kategori
        $kategoriList = [
            ['name' => 'Fasilitas & Sarana', 'slug' => 'fasilitas-sarana', 'color' => '#699fff', 'icon' => 'building'],
            ['name' => 'Akademik & Kurikulum', 'slug' => 'akademik-kurikulum', 'color' => '#a855f7', 'icon' => 'book-open'],
            ['name' => 'Kebersihan & Kantin', 'slug' => 'kebersihan-kantin', 'color' => '#10b981', 'icon' => 'coffee'],
            ['name' => 'Layanan & Organisasi', 'slug' => 'layanan-organisasi', 'color' => '#f59e0b', 'icon' => 'users'],
        ];

        $categories = [];
        foreach ($kategoriList as $kat) {
            $categories[$kat['name']] = Category::firstOrCreate(['slug' => $kat['slug']], $kat);
        }

        // 3. Seed Initial Aspirasi
        $asp1 = Aspirasi::firstOrCreate(
            ['kode_tiket' => 'ASP-20261028-A981'],
            [
                'category_id' => $categories['Fasilitas & Sarana']->id,
                'nama' => 'Rian Hidayat',
                'kontak' => '081234567890',
                'kelas_instansi' => 'XII MIPA 1',
                'is_anonim' => false,
                'judul' => 'Perbaikan AC & Proyektor di Ruang Lab Komputer 2',
                'pesan' => 'Mohon untuk unit AC di Lab Komputer 2 segera diservis karena sering mati mendadak saat jam praktikum berlangsung, serta kabel HDMI proyektor sudah longgar.',
                'status' => 'diproses',
                'is_public' => true,
            ]
        );

        Tanggapan::firstOrCreate(
            ['aspirasi_id' => $asp1->id],
            [
                'user_id' => $petugas->id,
                'isi_tanggapan' => 'Terima kasih atas laporannya. Teknisi sarana dan prasarana telah dijadwalkan untuk memeriksa unit AC dan mengganti kabel HDMI pada hari Kamis besok.',
                'status_terakhir' => 'diproses',
            ]
        );

        $asp2 = Aspirasi::firstOrCreate(
            ['kode_tiket' => 'ASP-20261027-C412'],
            [
                'category_id' => $categories['Kebersihan & Kantin']->id,
                'nama' => 'Anonim',
                'kontak' => null,
                'kelas_instansi' => 'Siswa',
                'is_anonim' => true,
                'judul' => 'Penambahan Tempat Sampah Daur Ulang di Area Kantin',
                'pesan' => 'Tempat sampah di dekat kantin utama sering penuh saat jam istirahat kedua sehingga sampah menumpuk. Mohon ditambah tempat sampah khusus botol plastik.',
                'status' => 'selesai',
                'is_public' => true,
            ]
        );

        Tanggapan::firstOrCreate(
            ['aspirasi_id' => $asp2->id],
            [
                'user_id' => $admin->id,
                'isi_tanggapan' => 'Sudah ditindaklanjuti. Dua set tempat sampah pilah baru telah dipasang di koridor kantin dan petugas kebersihan membersihkan secara berkala.',
                'status_terakhir' => 'selesai',
            ]
        );

        $asp3 = Aspirasi::firstOrCreate(
            ['kode_tiket' => 'ASP-20261026-F110'],
            [
                'category_id' => $categories['Layanan & Organisasi']->id,
                'nama' => 'Siti Aisyah',
                'kontak' => 'siti@example.com',
                'kelas_instansi' => 'XI IPS 3',
                'is_anonim' => false,
                'judul' => 'Usulan Jam Buka Perpustakaan Hingga Jam 17.00 WIB',
                'pesan' => 'Banyak teman-teman yang ingin belajar kelompok dan mengerjakan tugas setelah pulang sekolah, namun perpustakaan tutup pukul 15.30 WIB. Mohon dipertimbangkan.',
                'status' => 'pending',
                'is_public' => true,
            ]
        );
    }
}