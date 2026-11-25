# PPLG Futuristic — School Academic Information System

**PPLG Futuristic** is a Laravel 12 web application for managing school master data, teaching assignments, student grades, attendance logs, school news, and CSV reporting for **SMKN 4 Padalarang (XI PPLG B)**.

---

## Features

- **Authentication & Profile Management**
  - Powered by Laravel Breeze (`login`, `register`, password reset, email verification, and profile management).
  - Public registration allows `siswa` and `guru` roles only; `admin` accounts are managed internally.
- **Role-Based Access Control (`RoleMiddleware`)**
  - Server-side `role` middleware (`admin`, `guru`, `siswa`) protecting routes and exports.
- **Master Data CRUD**
  - **Siswa**: Student records (`nisn`, `nama`, `jenis_kelamin`, `tanggal_lahir`, `alamat`, `no_hp`, `id_kelas`).
  - **Guru**: Teacher records (`nip`, `nama`, `jenis_kelamin`, `email`, `no_hp`).
  - **Kelas**: Classrooms and majors (`nama_kelas`, `jurusan`).
  - **Mata Pelajaran**: Subjects and categories (`Produktif`, `Umum`, `Mulok`).
  - **Pengguna**: User account management (`admin`-only, with self-deletion protection).
- **Academic Modules**
  - **Mapel Diampu**: Teacher–subject–classroom assignments.
  - **Nilai**: Grade entry (`nilai_tugas`, `nilai_uts`, `semester`) with automatic server-side `nilai_akhir` calculation (`(nilai_tugas + nilai_uts) / 2`).
  - **Absensi**: Daily attendance tracking (`hadir`, `izin`, `sakit`, `alpha`).
- **News Portal (`Berita`)**
  - Cover photo upload and moderation workflow (`pending` for `guru`/`siswa` submissions → `published` upon `admin` approval via `PATCH`).
  - Ownership checks ensure non-admin users can only edit or delete their own posts.
- **Live Global Search**
  - Real-time AJAX search (`/search` and `/ajax/live-search`) across Siswa, Guru, Mapel, Kelas, published Berita (plus pending Berita & Users for `admin`), linking directly into `?cari=` filtered views.
- **CSV Export with Preview**
  - Preview and download CSV files via `/export/preview/{type}` and `/export/download/{type}` for `siswa`, `guru`, `kelas`, `mapel`, `mapel_diampu`, `nilai`, `absensi`, `berita`, and `pengguna`.

---

## Role Permissions

| Module / Action | `admin` | `guru` | `siswa` |
| --- | :---: | :---: | :---: |
| Dashboard & Live Search | Read | Read | Read |
| Siswa, Guru, Kelas, Mapel, Mapel Diampu | Full CRUD | Read | Read |
| Pengguna (`/pengguna`) | Full CRUD | Forbidden (403) | Forbidden (403) |
| Nilai & Absensi | Full CRUD | Full CRUD | Read |
| Berita | Publish, Edit/Delete Any, Approve (`PATCH`) | Submit (`pending`), Edit/Delete Own | Submit (`pending`), Edit/Delete Own |
| CSV Export (`siswa`, `guru`, `kelas`, `mapel`, `mapel_diampu`, `nilai`, `absensi`) | Preview & Download | Preview & Download | Preview & Download |
| CSV Export (`berita`, `pengguna`) | Preview & Download | Forbidden (403) | Forbidden (403) |

---

## Tech Stack

- **Backend**: PHP 8.2+, Laravel 12, Laravel Breeze
- **Frontend**: Blade templates, custom dark UI (`public/assets/styles.css`), Font Awesome 6, Vite, Tailwind CSS, Alpine.js
- **Database**: MySQL (production/Laragon) & SQLite `:memory:` (automated tests)
- **Testing**: PHPUnit 11

---

## Installation & Setup

1. **Install PHP dependencies**
   ```bash
   composer install
   ```

2. **Create environment configuration & generate app key**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Configure database in `.env`**
   Update `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD` for your local MySQL or SQLite database.

4. **Run database migrations**
   ```bash
   php artisan migrate
   ```
   > The schema migration (`database/migrations/2026_01_01_000001_create_school_tables.php`) checks for existing tables and columns before creating them.

5. **Install frontend dependencies & build assets**
   ```bash
   npm install
   npm run build
   ```

6. **Start the application**
   ```bash
   php artisan serve
   ```
   Or run the full dev script (server, queue listener, and Vite):
   ```bash
   composer run dev
   ```

---

## Running Tests

The test suite uses an in-memory SQLite database configured in `phpunit.xml`:

```bash
php artisan test
```

---

## Author & License

- **Author**: Khairul Faridz — XI PPLG B, SMKN 4 Padalarang
- **License**: [MIT License](https://opensource.org/licenses/MIT)
