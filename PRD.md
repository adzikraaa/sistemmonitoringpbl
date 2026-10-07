# PRD: Skeleton Struktur Folder Role-Based — SIM-PBL

## Latar Belakang

Proyek SIM-PBL (Sistem Informasi Monitoring PBL) dibangun dengan Laravel + Blade + Tailwind + Alpine.js, dikerjakan oleh tim 5 orang secara paralel. Sistem punya 3 role: **Mahasiswa**, **Dosen**, **Koordinator**.

Supaya anggota tim bisa mengerjakan fitur role masing-masing di branch terpisah tanpa saling menimpa file yang sama saat push/merge, struktur folder project perlu dipisah per role **sebelum** fitur mulai dikerjakan.

## Tujuan

Membuat **skeleton/kerangka folder dan file kosong** untuk 3 role di atas. Task ini **TIDAK** mencakup logic bisnis, query database, autentikasi, atau styling detail — murni menyiapkan struktur supaya anggota tim tinggal `git pull` lalu langsung bisa mulai isi fitur masing-masing tanpa perlu mengubah file bersama (seperti `routes/web.php`) lagi.

## Lingkup Pekerjaan

### 1. Controllers — subfolder per role

Buat struktur berikut di `app/Http/Controllers/`:

```
app/Http/Controllers/
├── Mahasiswa/
│   └── DashboardController.php
├── Dosen/
│   └── DashboardController.php
└── Koordinator/
    └── DashboardController.php
```

Setiap `DashboardController.php` cukup berisi satu method `index()` yang me-return view placeholder-nya masing-masing (lihat poin 2). Gunakan namespace sesuai folder, contoh:

```php
namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        return view('mahasiswa.dashboard');
    }
}
```

Pola yang sama untuk `Dosen\DashboardController` dan `Koordinator\DashboardController`.

### 2. Views — subfolder per role

Buat struktur berikut di `resources/views/`:

```
resources/views/
├── mahasiswa/
│   └── dashboard.blade.php
├── dosen/
│   └── dashboard.blade.php
└── koordinator/
    └── dashboard.blade.php
```

Isi tiap file cukup placeholder sederhana, contoh untuk `mahasiswa/dashboard.blade.php`:

```blade
<h1>Dashboard Mahasiswa</h1>
<p>Halaman ini masih placeholder, akan diisi fitur oleh tim.</p>
```

Sesuaikan teks "Mahasiswa" menjadi "Dosen" / "Koordinator" di file masing-masing.

### 3. Routes — pecah per role, jangan gabung di web.php

Buat 3 file baru di `routes/`:

```
routes/
├── mahasiswa.php
├── dosen.php
└── koordinator.php
```

Isi `routes/mahasiswa.php`:

```php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Mahasiswa\DashboardController;

Route::prefix('mahasiswa')->name('mahasiswa.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});
```

Pola yang sama untuk `routes/dosen.php` (prefix `dosen`, name prefix `dosen.`) dan `routes/koordinator.php` (prefix `koordinator`, name prefix `koordinator.`).

Lalu `routes/web.php` **hanya** berisi:

```php
<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

require __DIR__.'/mahasiswa.php';
require __DIR__.'/dosen.php';
require __DIR__.'/koordinator.php';
```

(baris `Route::get('/', ...)` bawaan Laravel boleh tetap dipertahankan seperti biasa)

### 4. Middleware — skeleton pengecekan role (belum ada logic)

Buat satu file `app/Http/Middleware/CheckRole.php`, isinya skeleton kosong dulu (logic pengecekan role menyusul nanti, dikerjakan terpisah):

```php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, string $role)
    {
        // TODO: implementasi pengecekan role di sini (dikerjakan terpisah)
        return $next($request);
    }
}
```

Daftarkan middleware ini di `bootstrap/app.php` (atau `app/Http/Kernel.php` tergantung versi Laravel) dengan alias `role`, tapi **jangan diterapkan ke route manapun dulu** di task ini.

## Di Luar Lingkup (Jangan Dikerjakan di Task Ini)

- Logic autentikasi/login
- Query ke database atau pemanggilan Model
- Styling Tailwind yang detail
- Implementasi isi middleware `CheckRole`
- Fitur apapun di luar 3 dashboard placeholder di atas

## Definition of Done

- [ ] 3 folder controller (`Mahasiswa/`, `Dosen/`, `Koordinator/`) masing-masing berisi `DashboardController.php`
- [ ] 3 folder view (`mahasiswa/`, `dosen/`, `koordinator/`) masing-masing berisi `dashboard.blade.php`
- [ ] 3 file route baru (`mahasiswa.php`, `dosen.php`, `koordinator.php`), dan `web.php` sudah di-require ke ketiganya
- [ ] File `CheckRole.php` middleware sudah ada (skeleton kosong, belum diterapkan ke route manapun)
- [ ] Mengakses `/mahasiswa/dashboard`, `/dosen/dashboard`, `/koordinator/dashboard` masing-masing menampilkan placeholder sesuai role, tanpa error
- [ ] Tidak ada perubahan lain di luar yang disebutkan di atas (tidak menyentuh migration, model, atau fitur lain yang sudah ada)
