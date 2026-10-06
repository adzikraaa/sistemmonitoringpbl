# PRD: Fitur Login Multi-Role — SIM-PBL

## Latar Belakang

Struktur folder per role (Mahasiswa, Dosen, Koordinator) sudah ada dari PRD sebelumnya (skeleton dashboard placeholder). Sekarang saatnya membangun fitur **login** supaya tiap role bisa masuk ke dashboard-nya masing-masing.

Sistem memakai **3 guard terpisah** (bukan 1 tabel users gabungan), karena Koordinator, Dosen, dan Mahasiswa adalah 3 tabel database yang berbeda sesuai desain ERD yang sudah disepakati.

Referensi visual (opsional, boleh disederhanakan): folder `design-reference/` berisi mockup HTML untuk 3 halaman login per role. Ambil inspirasi warna dan layout saja, tidak perlu identik.

## Tujuan

Mahasiswa, Dosen, dan Koordinator masing-masing bisa login lewat form terpisah, dan setelah berhasil diarahkan ke dashboard role masing-masing yang sudah ada (`/mahasiswa/dashboard`, `/dosen/dashboard`, `/koordinator/dashboard`).

## Lingkup Pekerjaan

### 1. Migration (buat hanya jika belum ada)

Cek dulu folder `database/migrations/` — kalau migration untuk tabel-tabel ini belum ada, buat:

- `kelas`: id, nama_kelas, timestamps
- `koordinator`: id, nama, email (unique), password, nip, timestamps
- `dosen`: id, nama, email (unique), password, nip, koordinator_id (FK -> koordinator, nullable), timestamps
- `mahasiswa`: id, nama, email (unique), password, nim, kelas_id (FK -> kelas, nullable), timestamps

Urutan migration harus benar: `kelas` dan `koordinator` dibuat sebelum `dosen` dan `mahasiswa` (karena ada foreign key).

Jangan buat tabel lain di luar 4 ini dulu (proposal, kelompok, milestone, dll menyusul di PRD terpisah).

### 2. Model

Buat/lengkapi Eloquent Model untuk `Kelas`, `Koordinator`, `Dosen`, `Mahasiswa`. Untuk `Koordinator`, `Dosen`, `Mahasiswa`, masing-masing **extends `Illuminate\Foundation\Auth\User as Authenticatable`** (bukan model biasa) dan pakai trait `Illuminate\Auth\Authenticatable`, supaya bisa dipakai sistem Auth bawaan Laravel. Tambahkan `protected $hidden = ['password'];` di masing-masing.

### 3. Konfigurasi Guard & Provider

Di `config/auth.php`, tambahkan 3 guard baru (selain `web` default):

```php
'guards' => [
    // ...default web guard tetap ada...
    'mahasiswa' => ['driver' => 'session', 'provider' => 'mahasiswa'],
    'dosen' => ['driver' => 'session', 'provider' => 'dosen'],
    'koordinator' => ['driver' => 'session', 'provider' => 'koordinator'],
],

'providers' => [
    // ...default users provider tetap ada...
    'mahasiswa' => ['driver' => 'eloquent', 'model' => App\Models\Mahasiswa::class],
    'dosen' => ['driver' => 'eloquent', 'model' => App\Models\Dosen::class],
    'koordinator' => ['driver' => 'eloquent', 'model' => App\Models\Koordinator::class],
],
```

### 4. Controller Login — satu per role, taruh di folder role masing-masing

```
app/Http/Controllers/Mahasiswa/AuthController.php
app/Http/Controllers/Dosen/AuthController.php
app/Http/Controllers/Koordinator/AuthController.php
```

Masing-masing berisi:
- `showLogin()` — return view form login
- `login(Request $request)` — validasi `email` + `password`, coba `Auth::guard('mahasiswa')->attempt(...)` (sesuaikan nama guard per role), kalau berhasil redirect ke dashboard role itu, kalau gagal balik ke form login dengan pesan error
- `logout(Request $request)` — `Auth::guard('mahasiswa')->logout()` (sesuaikan), lalu redirect ke halaman login role itu

### 5. View — taruh di folder view role masing-masing (konsisten dengan skeleton sebelumnya)

```
resources/views/mahasiswa/login.blade.php
resources/views/dosen/login.blade.php
resources/views/koordinator/login.blade.php
```

Form sederhana: input email, input password, tombol submit, tampilkan pesan error validasi kalau ada. Styling pakai Tailwind, boleh terinspirasi dari `design-reference/` kalau ada.

### 6. Routes — tambahkan di file routes per role yang sudah ada

Di `routes/mahasiswa.php` (pola sama untuk `dosen.php` dan `koordinator.php`):

```php
Route::prefix('mahasiswa')->name('mahasiswa.')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::middleware('auth:mahasiswa')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    });
});
```

Dashboard yang sudah ada dari PRD sebelumnya **dibungkus middleware `auth:mahasiswa`** (ganti sesuai guard per role) supaya tidak bisa diakses tanpa login.

### 7. Seeder untuk testing (opsional tapi disarankan)

Buat satu seeder sederhana yang menambahkan 1 akun dummy per role (email + password yang di-hash), supaya login bisa langsung dites tanpa perlu bikin form registrasi dulu (registrasi belum ada di scope ini).

## Di Luar Lingkup

- Halaman registrasi/pendaftaran akun baru
- Fitur lupa password
- Tabel selain `kelas`, `koordinator`, `dosen`, `mahasiswa`
- Styling detail/animasi kompleks
- Halaman landing/pemilihan role (nanti menyusul kalau dibutuhkan)

## Definition of Done

- [ ] Migration `kelas`, `koordinator`, `dosen`, `mahasiswa` ada dan `php artisan migrate` berjalan tanpa error
- [ ] 3 guard dan provider baru terdaftar di `config/auth.php`
- [ ] 3 `AuthController` (login, logout) di masing-masing folder role
- [ ] 3 halaman form login bisa diakses dan tampil benar
- [ ] Login dengan akun seeder berhasil redirect ke dashboard role yang sesuai
- [ ] Akses langsung ke `/mahasiswa/dashboard` tanpa login akan redirect ke halaman login (middleware bekerja)
- [ ] Logout berhasil mengembalikan ke halaman login
