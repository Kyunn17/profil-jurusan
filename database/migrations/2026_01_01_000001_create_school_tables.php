<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('users') && ! Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('role')->default('siswa')->after('password');
            });
        }

        if (! Schema::hasTable('kelas')) {
            Schema::create('kelas', function (Blueprint $table) {
                $table->id('id_kelas');
                $table->string('nama_kelas', 100);
                $table->string('jurusan', 100)->nullable();
            });
        }

        if (! Schema::hasTable('siswa')) {
            Schema::create('siswa', function (Blueprint $table) {
                $table->id('id_siswa');
                $table->string('nisn', 30)->unique();
                $table->string('nama');
                $table->string('jenis_kelamin', 20);
                $table->date('tanggal_lahir')->nullable();
                $table->text('alamat')->nullable();
                $table->string('no_hp', 20)->nullable();
                $table->unsignedBigInteger('id_kelas');
            });
        }

        if (! Schema::hasTable('guru')) {
            Schema::create('guru', function (Blueprint $table) {
                $table->id('id_guru');
                $table->string('nip', 50)->nullable()->unique();
                $table->string('nama');
                $table->string('jenis_kelamin', 20);
                $table->string('email')->nullable()->unique();
                $table->string('no_hp', 20)->nullable();
            });
        }

        if (! Schema::hasTable('mata_pelajaran')) {
            Schema::create('mata_pelajaran', function (Blueprint $table) {
                $table->id('id_mapel');
                $table->string('nama_mapel', 100);
                $table->string('kategori', 50)->default('Umum');
            });
        }

        if (! Schema::hasTable('mapel_diampu')) {
            Schema::create('mapel_diampu', function (Blueprint $table) {
                $table->id('id_ampu');
                $table->unsignedBigInteger('id_guru');
                $table->unsignedBigInteger('id_mapel');
                $table->unsignedBigInteger('id_kelas');
            });
        }

        if (! Schema::hasTable('nilai')) {
            Schema::create('nilai', function (Blueprint $table) {
                $table->id('id_nilai');
                $table->unsignedBigInteger('id_siswa');
                $table->unsignedBigInteger('id_ampu');
                $table->string('semester', 20);
                $table->decimal('nilai_tugas', 5, 2)->default(0);
                $table->decimal('nilai_uts', 5, 2)->default(0);
                $table->decimal('nilai_akhir', 5, 2)->default(0);
            });
        }

        if (! Schema::hasTable('absensi')) {
            Schema::create('absensi', function (Blueprint $table) {
                $table->id('id_absensi');
                $table->unsignedBigInteger('id_siswa');
                $table->unsignedBigInteger('id_kelas');
                $table->date('tanggal');
                $table->string('status', 20);
            });
        }

        if (! Schema::hasTable('berita')) {
            Schema::create('berita', function (Blueprint $table) {
                $table->id('id_berita');
                $table->unsignedBigInteger('id_user');
                $table->string('judul');
                $table->text('isi');
                $table->binary('foto')->nullable();
                $table->dateTime('tanggal_post')->nullable();
                $table->string('status', 20)->default('pending');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('berita');
        Schema::dropIfExists('absensi');
        Schema::dropIfExists('nilai');
        Schema::dropIfExists('mapel_diampu');
        Schema::dropIfExists('mata_pelajaran');
        Schema::dropIfExists('guru');
        Schema::dropIfExists('siswa');
        Schema::dropIfExists('kelas');

        if (Schema::hasTable('users') && Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('role');
            });
        }
    }
};
