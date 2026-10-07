# PRD: Revisi Halaman Login — Satu Halaman dengan Tab Switcher

## Latar Belakang

Hasil dari PRD-login.md sebelumnya membuat 3 halaman login terpisah (`/mahasiswa/login`, `/dosen/login`, `/koordinator/login`), masing-masing form generik berbeda tampilan. Ini **tidak sesuai** desain referensi di `design-reference/`, yang sebenarnya satu halaman tunggal dengan **tab switcher** untuk berpindah role tanpa reload/pindah URL.

Migration, model, guard, dan provider yang sudah dibuat sebelumnya **sudah benar dan tetap dipakai** — PRD ini hanya merevisi bagian **routing, controller, dan tampilan (view)** login saja.

## Tujuan

Satu halaman login di `/login` menampilkan 3 tab (Mahasiswa, Dosen, Koordinator). User klik tab untuk berpindah konteks role (tanpa reload halaman, pakai Alpine.js), isi email + password, lalu submit — backend menentukan guard mana yang dipakai berdasarkan tab yang aktif saat submit.

## Referensi Desain (lihat `design-reference/`)

Tata letak: panel kiri warna navy solid polos (kosong, tanpa konten) mengisi sekitar 35% lebar layar, panel kanan warna krem/off-white berisi form, form diletakkan di tengah vertikal panel kanan.

Elemen pada panel kanan, urut dari atas:
1. Judul "Masuk Ke SIM-PBL" (bold, navy, besar)
2. Subjudul "Gunakan Akun Kampus Untuk Melanjutkan" (navy, lebih kecil)
3. **Tab switcher** berbentuk pill/kapsul penuh, latar biru muted, 3 opsi sejajar horizontal: "Mahasiswa", "Dosen", "Koordinator". Tab yang aktif berlatar putih dengan teks navy, tab tidak aktif teksnya putih transparan di atas latar biru muted
4. Label "Email" (navy, kecil), diikuti input berbentuk pill penuh (rounded-full), latar krem sangat terang, placeholder "ketikkan email di sini"
5. Label "Kata Sandi" (navy, kecil), diikuti input berbentuk pill penuh sama seperti email, placeholder "ketikkan sandi di sini", ada teks "Lihat" di ujung kanan dalam input (toggle show/hide password pakai Alpine, boleh non-fungsional dulu kalau waktu terbatas — prioritaskan tampilan)
6. Baris checkbox "Ingat Perangkat Ini" di kiri, link "Lupa kata sandi?" di kanan (sejajar horizontal, boleh non-fungsional/placeholder href="#")
7. Tombol submit besar berbentuk pill, latar navy solid, teks putih: **teks tombol berubah dinamis** mengikuti tab aktif — "Masuk Sebagai Mahasiswa ->", "Masuk Sebagai Dosen ->", atau "Masuk Sebagai Koordinator ->" (panah "->" sebagai teks biasa atau ikon, boleh pilih salah satu)

Gunakan warna Tailwind yang mendekati: panel kiri `bg-blue-900` atau custom hex mendekati navy gelap, panel kanan `bg-[#FDFBF0]` atau sejenis krem, tab switcher track `bg-blue-400`/`bg-slate-500` dengan opacity, tombol submit `bg-blue-900`. Tidak perlu presisi pixel-perfect, yang penting struktur dan kesan visualnya sama.

## Lingkup Pekerjaan

### 1. Hapus/nonaktifkan 3 route login lama

Di `routes/mahasiswa.php`, `routes/dosen.php`, `routes/koordinator.php` — hapus route `GET /login` dan `POST /login` yang lama dari masing-masing file (route dashboard yang sudah ada, dengan middleware `auth:<guard>`, **tetap dipertahankan, jangan dihapus**).

### 2. Route baru terpusat

Di `routes/web.php`, tambahkan:

```php
use App\Http\Controllers\Auth\LoginController;

Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
```

### 3. Controller baru terpusat

Buat `app/Http/Controllers/Auth/LoginController.php`:

- `show()` — return view `auth.login`
- `login(Request $request)` — validasi `role` (in: mahasiswa,dosen,koordinator), `email`, `password`. Berdasarkan nilai `role` yang dikirim, panggil `Auth::guard($role)->attempt([...])`. Kalau berhasil, redirect ke `route("{$role}.dashboard")`. Kalau gagal, balik ke `/login` dengan pesan error dan tetap pada tab role yang sama (pakai `old('role')` di view untuk tahu tab mana yang harus tetap aktif setelah redirect balik)
- `logout(Request $request)` — deteksi guard mana yang sedang login (cek `Auth::guard('mahasiswa')->check()`, dst satu per satu), logout dari guard yang aktif, redirect ke `/login`

### 4. View baru terpusat

Buat `resources/views/auth/login.blade.php` sesuai referensi desain di atas. Gunakan `x-data` Alpine untuk state tab aktif (default `'mahasiswa'`), form `method="POST"` ke `/login` dengan hidden input `name="role"` yang value-nya di-bind ke state Alpine aktif. Tampilkan pesan error validasi dari session kalau ada (`@error` / `session('error')`).

### 5. Hapus file view login lama

Hapus `resources/views/mahasiswa/login.blade.php`, `resources/views/dosen/login.blade.php`, `resources/views/koordinator/login.blade.php` — sudah digantikan satu view terpusat.

### 6. Hapus Controller lama

Hapus `app/Http/Controllers/Mahasiswa/AuthController.php`, `app/Http/Controllers/Dosen/AuthController.php`, `app/Http/Controllers/Koordinator/AuthController.php` — digantikan satu `LoginController` terpusat.

## Di Luar Lingkup

- Migration, model, guard, provider — **jangan diubah**, sudah benar dari PRD sebelumnya
- Fungsi toggle show/hide password yang benar-benar jalan (boleh dikerjakan kalau sempat, tapi bukan prioritas)
- Fitur "Ingat Perangkat Ini" dan "Lupa kata sandi" yang benar-benar berfungsi (cukup tampilan saja)
- Halaman dashboard — tidak diubah sama sekali

## Definition of Done

- [ ] `/login` menampilkan satu halaman dengan 3 tab (Mahasiswa, Dosen, Koordinator)
- [ ] Klik tab berpindah tanpa reload halaman, teks tombol submit ikut berubah sesuai tab aktif
- [ ] Login dengan akun seeder di tab yang sesuai berhasil redirect ke dashboard role itu
- [ ] Login dengan kredensial salah menampilkan pesan error dan tab yang sebelumnya aktif tetap aktif
- [ ] 3 route login lama (`/mahasiswa/login`, dst) sudah tidak ada lagi
- [ ] 3 Controller dan 3 view login lama sudah dihapus, diganti satu `LoginController` dan satu `auth/login.blade.php`
