<?php

namespace Tests\Feature;

use App\Models\Absensi;
use App\Models\Berita;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\MapelDiampu;
use App\Models\Nilai;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class SecurityAndCrudTest extends TestCase
{
    use RefreshDatabase;

    private function seedAcademicFixture(): array
    {
        $kelas = Kelas::create(['nama_kelas' => 'XI PPLG 1', 'jurusan' => 'PPLG']);
        $siswa = Siswa::create([
            'nisn' => '1234567890',
            'nama' => 'Budi Santoso',
            'jenis_kelamin' => 'Laki-laki',
            'id_kelas' => $kelas->id_kelas,
        ]);
        $guru = Guru::create([
            'nip' => '198001012010011001',
            'nama' => 'Pak Ahmad',
            'jenis_kelamin' => 'Laki-laki',
            'email' => 'ahmad@sekolah.sch.id',
        ]);
        $mapel = Mapel::create([
            'nama_mapel' => 'Pemrograman Web',
            'kategori' => 'Produktif',
        ]);
        $ampu = MapelDiampu::create([
            'id_guru' => $guru->id_guru,
            'id_mapel' => $mapel->id_mapel,
            'id_kelas' => $kelas->id_kelas,
        ]);

        return compact('kelas', 'siswa', 'guru', 'mapel', 'ampu');
    }

    public function test_role_isolation_blocks_siswa_and_restricts_guru(): void
    {
        $fixture = $this->seedAcademicFixture();
        $siswaUser = User::factory()->create();
        $guruUser = User::factory()->guru()->create();

        // Siswa cannot access admin or guru write endpoints or /pengguna
        $this->actingAs($siswaUser)->get(route('pengguna'))->assertForbidden();
        $this->actingAs($siswaUser)->post(route('siswa.store'), [])->assertForbidden();
        $this->actingAs($siswaUser)->delete(route('siswa.hapus', $fixture['siswa']->id_siswa))->assertForbidden();
        $this->actingAs($siswaUser)->post(route('nilai.store'), [])->assertForbidden();
        $this->actingAs($siswaUser)->post(route('absensi.store'), [])->assertForbidden();
        $this->actingAs($siswaUser)->get(route('export.preview', ['type' => 'pengguna']))->assertForbidden();
        $this->actingAs($siswaUser)->get(route('export.download', ['type' => 'berita']))->assertForbidden();

        // Guru cannot access admin-only endpoints
        $this->actingAs($guruUser)->get(route('pengguna'))->assertForbidden();
        $this->actingAs($guruUser)->post(route('kelas.store'), [
            'nama_kelas' => 'X PPLG 2',
            'jurusan' => 'PPLG',
        ])->assertForbidden();

        // Guru can write nilai and absensi
        $this->actingAs($guruUser)->post(route('nilai.store'), [
            'id_siswa' => $fixture['siswa']->id_siswa,
            'id_ampu' => $fixture['ampu']->id_ampu,
            'semester' => 'Ganjil',
            'nilai_tugas' => 80,
            'nilai_uts' => 90,
        ])->assertRedirect(route('nilai'));

        $this->actingAs($guruUser)->post(route('absensi.store'), [
            'id_siswa' => $fixture['siswa']->id_siswa,
            'id_kelas' => $fixture['kelas']->id_kelas,
            'tanggal' => '2026-11-25',
            'status' => 'hadir',
        ])->assertRedirect(route('absensi'));
    }

    public function test_admin_crud_and_server_side_nilai_calculation(): void
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin);

        // Kelas CRUD with jurusan
        $this->post(route('kelas.store'), [
            'nama_kelas' => 'XII PPLG 1',
            'jurusan' => 'Pengembangan Perangkat Lunak dan Gim',
        ])->assertRedirect(route('kelas'));

        $kelas = Kelas::where('nama_kelas', 'XII PPLG 1')->firstOrFail();
        $this->assertSame('Pengembangan Perangkat Lunak dan Gim', $kelas->jurusan);

        // Siswa CRUD
        $this->post(route('siswa.store'), [
            'nisn' => '9988776655',
            'nama' => "Ma'ruf Amin",
            'jenis_kelamin' => 'Laki-laki',
            'id_kelas' => $kelas->id_kelas,
        ])->assertRedirect(route('siswa'));
        $siswa = Siswa::where('nisn', '9988776655')->firstOrFail();

        // Guru CRUD
        $this->post(route('guru.store'), [
            'nip' => '197501012005011002',
            'nama' => 'Bu Siti',
            'jenis_kelamin' => 'Perempuan',
            'email' => 'siti@sekolah.sch.id',
        ])->assertRedirect(route('guru'));
        $guru = Guru::where('email', 'siti@sekolah.sch.id')->firstOrFail();

        // Mapel & MapelDiampu CRUD
        $this->post(route('mapel.store'), [
            'nama_mapel' => 'Basis Data',
            'kategori' => 'Produktif',
        ])->assertRedirect(route('mapel'));
        $mapel = Mapel::where('nama_mapel', 'Basis Data')->firstOrFail();

        $this->post(route('mapel_diampu.store'), [
            'id_guru' => $guru->id_guru,
            'id_mapel' => $mapel->id_mapel,
            'id_kelas' => $kelas->id_kelas,
        ])->assertRedirect(route('mapel_diampu'));
        $ampu = MapelDiampu::firstOrFail();

        // Nilai ignores tampered nilai_akhir from client
        $this->post(route('nilai.store'), [
            'id_siswa' => $siswa->id_siswa,
            'id_ampu' => $ampu->id_ampu,
            'semester' => 'Ganjil',
            'nilai_tugas' => 70,
            'nilai_uts' => 90,
            'nilai_akhir' => 100,
        ])->assertRedirect(route('nilai'));

        $nilai = Nilai::firstOrFail();
        $this->assertEquals(80.00, (float) $nilai->nilai_akhir);

        // Absensi CRUD
        $this->post(route('absensi.store'), [
            'id_siswa' => $siswa->id_siswa,
            'id_kelas' => $kelas->id_kelas,
            'tanggal' => '2026-11-25',
            'status' => 'izin',
        ])->assertRedirect(route('absensi'));
        $this->assertDatabaseHas('absensi', ['id_siswa' => $siswa->id_siswa, 'status' => 'izin']);

        // Pengguna CRUD & self-delete protection
        $otherUser = User::factory()->create(['email' => 'other@sekolah.sch.id']);
        $this->delete(route('pengguna.hapus', $admin->id))
            ->assertRedirect(route('pengguna'))
            ->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $admin->id]);

        $this->delete(route('pengguna.hapus', $otherUser->id))
            ->assertRedirect(route('pengguna'))
            ->assertSessionHas('success');
        $this->assertDatabaseMissing('users', ['id' => $otherUser->id]);
    }

    public function test_berita_moderation_and_ownership_protection(): void
    {
        $admin = User::factory()->admin()->create();
        $siswaA = User::factory()->create();
        $siswaB = User::factory()->create();

        $foto = UploadedFile::fake()->image('cover.jpg');

        $this->actingAs($siswaA)->post(route('berita.store'), [
            'judul' => 'Lomba Web Design',
            'isi' => 'Pendaftaran dibuka minggu ini.',
            'foto' => $foto,
        ])->assertRedirect(route('berita'));

        $berita = Berita::where('judul', 'Lomba Web Design')->firstOrFail();
        $this->assertSame('pending', $berita->status);

        // Other student cannot delete or approve
        $this->actingAs($siswaB)->delete(route('berita.hapus', $berita->id_berita))
            ->assertRedirect(route('berita'))
            ->assertSessionHas('error');
        $this->actingAs($siswaB)->patch(route('berita.approve', $berita->id_berita))
            ->assertForbidden();

        // Admin approves via PATCH
        $this->actingAs($admin)->patch(route('berita.approve', $berita->id_berita))
            ->assertRedirect(route('berita'));

        $this->assertSame('published', $berita->fresh()->status);
    }

    public function test_export_preview_and_download_work_for_all_valid_types(): void
    {
        $fixture = $this->seedAcademicFixture();
        $admin = User::factory()->admin()->create();

        Nilai::create([
            'id_siswa' => $fixture['siswa']->id_siswa,
            'id_ampu' => $fixture['ampu']->id_ampu,
            'semester' => 'Ganjil',
            'nilai_tugas' => 85,
            'nilai_uts' => 95,
            'nilai_akhir' => 90,
        ]);

        Absensi::create([
            'id_siswa' => $fixture['siswa']->id_siswa,
            'id_kelas' => $fixture['kelas']->id_kelas,
            'tanggal' => '2026-11-25',
            'status' => 'hadir',
        ]);

        Berita::create([
            'id_user' => $admin->id,
            'judul' => 'Pengumuman Libur',
            'isi' => 'Libur semester dimulai bulan depan.',
            'tanggal_post' => now(),
            'status' => 'published',
        ]);

        $types = ['siswa', 'guru', 'kelas', 'mapel', 'mapel_diampu', 'nilai', 'absensi', 'berita', 'pengguna'];

        foreach ($types as $type) {
            $this->actingAs($admin)->get(route('export.preview', ['type' => $type]))->assertOk();
            $this->actingAs($admin)->get(route('export.download', ['type' => $type]))->assertOk();
        }

        $this->actingAs($admin)->get(route('export.preview', ['type' => 'invalid_type']))->assertNotFound();
    }

    public function test_live_search_filters_correctly_and_hides_users_from_siswa(): void
    {
        $this->seedAcademicFixture();
        $admin = User::factory()->admin()->create(['name' => 'Admin Budi']);
        $siswaUser = User::factory()->create(['name' => 'Siswa Budi']);

        $adminResp = $this->actingAs($admin)->getJson(route('search.live', ['q' => 'Budi']));
        $adminResp->assertOk();
        $categoriesAdmin = collect($adminResp->json())->pluck('category')->all();
        $this->assertContains('Siswa', $categoriesAdmin);
        $this->assertContains('User', $categoriesAdmin);

        $siswaResp = $this->actingAs($siswaUser)->getJson(route('search.live', ['q' => 'Budi']));
        $siswaResp->assertOk();
        $categoriesSiswa = collect($siswaResp->json())->pluck('category')->all();
        $this->assertContains('Siswa', $categoriesSiswa);
        $this->assertNotContains('User', $categoriesSiswa);
    }

    public function test_student_daily_checkin_prevents_duplicates_and_rejects_alpha(): void
    {
        $fixture = $this->seedAcademicFixture();
        $today = now()->toDateString();

        // Matched automatically by name
        $siswaByName = User::factory()->create(['name' => 'Budi Santoso']);

        // Cannot submit alpha via self check-in
        $this->actingAs($siswaByName)
            ->post(route('absensi.checkin'), ['status' => 'alpha'])
            ->assertSessionHasErrors('status');

        // First check-in succeeds with server today() and student's id_kelas
        $this->actingAs($siswaByName)
            ->from(route('absensi'))
            ->post(route('absensi.checkin'), ['status' => 'hadir'])
            ->assertRedirect(route('absensi'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('absensi', [
            'id_siswa' => $fixture['siswa']->id_siswa,
            'id_kelas' => $fixture['kelas']->id_kelas,
            'tanggal' => $today,
            'status' => 'hadir',
        ]);

        // Duplicate check-in on the same day is blocked
        $this->actingAs($siswaByName)
            ->from(route('absensi'))
            ->post(route('absensi.checkin'), ['status' => 'izin'])
            ->assertRedirect(route('absensi'))
            ->assertSessionHas('error');

        $this->assertSame(1, Absensi::where('id_siswa', $fixture['siswa']->id_siswa)->count());

        // Unlinked student links via id_siswa on check-in
        $siswa2 = Siswa::create([
            'nisn' => '5556667778',
            'nama' => 'Siti Nurhaliza',
            'jenis_kelamin' => 'Perempuan',
            'id_kelas' => $fixture['kelas']->id_kelas,
        ]);
        $unlinkedUser = User::factory()->create(['name' => 'Akun Siti']);

        $this->actingAs($unlinkedUser)
            ->from(route('home'))
            ->post(route('absensi.checkin'), [
                'id_siswa' => $siswa2->id_siswa,
                'status' => 'izin',
            ])
            ->assertRedirect(route('home'))
            ->assertSessionHas('success');

        $this->assertSame($siswa2->id_siswa, $unlinkedUser->fresh()->id_siswa);
        $this->assertDatabaseHas('absensi', [
            'id_siswa' => $siswa2->id_siswa,
            'tanggal' => $today,
            'status' => 'izin',
        ]);

        // Guru and admin cannot use student self check-in endpoint
        $guru = User::factory()->guru()->create();
        $admin = User::factory()->admin()->create();
        $this->actingAs($guru)->post(route('absensi.checkin'), ['status' => 'hadir'])->assertForbidden();
        $this->actingAs($admin)->post(route('absensi.checkin'), ['status' => 'hadir'])->assertForbidden();
    }

    public function test_teacher_bulk_daily_roll_call_creates_and_updates_attendance(): void
    {
        $fixture = $this->seedAcademicFixture();
        $siswa2 = Siswa::create([
            'nisn' => '2223334445',
            'nama' => 'Ani Wijaya',
            'jenis_kelamin' => 'Perempuan',
            'id_kelas' => $fixture['kelas']->id_kelas,
        ]);

        $guru = User::factory()->guru()->create();
        $siswaUser = User::factory()->create();
        $tanggal = '2026-11-25';

        // Siswa cannot access bulk roll-call endpoint
        $this->actingAs($siswaUser)->post(route('absensi.bulk'), [
            'tanggal' => $tanggal,
            'statuses' => [$fixture['siswa']->id_siswa => 'hadir'],
        ])->assertForbidden();

        // Guru submits initial roll-call
        $this->actingAs($guru)->post(route('absensi.bulk'), [
            'tanggal' => $tanggal,
            'kelas' => $fixture['kelas']->id_kelas,
            'statuses' => [
                $fixture['siswa']->id_siswa => 'hadir',
                $siswa2->id_siswa => 'sakit',
            ],
        ])->assertRedirect(route('absensi', ['tanggal' => $tanggal, 'kelas' => $fixture['kelas']->id_kelas]));

        $this->assertDatabaseHas('absensi', [
            'id_siswa' => $fixture['siswa']->id_siswa,
            'tanggal' => $tanggal,
            'status' => 'hadir',
        ]);
        $this->assertDatabaseHas('absensi', [
            'id_siswa' => $siswa2->id_siswa,
            'tanggal' => $tanggal,
            'status' => 'sakit',
        ]);

        // Guru updates existing statuses idempotently
        $this->actingAs($guru)->post(route('absensi.bulk'), [
            'tanggal' => $tanggal,
            'statuses' => [
                $fixture['siswa']->id_siswa => 'izin',
                $siswa2->id_siswa => 'hadir',
            ],
        ])->assertRedirect(route('absensi', ['tanggal' => $tanggal]));

        $this->assertSame(1, Absensi::where('id_siswa', $fixture['siswa']->id_siswa)->whereDate('tanggal', $tanggal)->count());
        $this->assertDatabaseHas('absensi', [
            'id_siswa' => $fixture['siswa']->id_siswa,
            'tanggal' => $tanggal,
            'status' => 'izin',
        ]);
        $this->assertDatabaseHas('absensi', [
            'id_siswa' => $siswa2->id_siswa,
            'tanggal' => $tanggal,
            'status' => 'hadir',
        ]);
    }
}
