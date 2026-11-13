<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aspirasis', function (Blueprint $table) {
            $table->id();
            $table->string('kode_tiket', 32)->unique(); // Contoh: ASP-20261028-A19X
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('nama')->nullable(); // Jika anonim, bisa diisi nama samaran atau 'Anonim'
            $table->string('kontak')->nullable(); // Email atau No HP / WhatsApp
            $table->string('kelas_instansi')->nullable(); // Kelas siswa atau instansi/divisi
            $table->boolean('is_anonim')->default(false);
            $table->string('judul');
            $table->text('pesan');
            $table->string('lampiran')->nullable(); // Path file dokumen / foto bukti
            $table->enum('status', ['pending', 'diproses', 'selesai', 'ditolak'])->default('pending');
            $table->boolean('is_public')->default(true); // Opsi tampil di daftar publik
            $table->timestamps();
        });

        Schema::create('tanggapans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aspirasi_id')->constrained('aspirasis')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // Admin / Petugas penanggap
            $table->text('isi_tanggapan');
            $table->enum('status_terakhir', ['pending', 'diproses', 'selesai', 'ditolak']);
            $table->string('lampiran_bukti')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tanggapans');
        Schema::dropIfExists('aspirasis');
    }
};
