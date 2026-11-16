# PRD: Integrasi Database Lengkap ke Laravel — SIM-PBL (v2)

## Latar Belakang

Project SIM-PBL memakai Laravel + MySQL (database `sim_pbl`). Saat ini baru 4 tabel domain yang termigrasi: `kelas`, `koordinator`, `dosen`, `mahasiswa`. Halaman Mahasiswa (proposal, milestone, logbook, pilih semester) masih memakai **data mock** di `app/Http/Controllers/MahasiswaController.php`.

PRD ini melengkapi **seluruh skema database** (migration), Model beserta relasinya, dan seeder, mengikuti ERD final dan use case terbaru (aktor Admin digabung ke Koordinator). Skema juga diselaraskan dengan struktur data yang sudah dipakai halaman milestone dan proposal buatan tim, supaya mock data nanti mudah diganti query database.

Konvensi project: **nama tabel singular dengan underscore** (bukan plural default Laravel). Setiap Model baru wajib mendefinisikan `protected $table = '...'` secara eksplisit, seperti Model yang sudah ada (`Kelas`, `Dosen`, `Koordinator`, `Mahasiswa`).

## Tujuan

Seluruh tabel tersedia lewat migration yang bisa dijalankan berurutan tanpa error, setiap tabel punya Model dengan relasi yang benar, dan ada seeder data awal agar semua anggota tim punya data yang seragam di laptop masing-masing.

## Lingkup Pekerjaan

### 0. Baca dulu kondisi saat ini (jangan diubah)

Baca `database/migrations/`, `app/Models/`, `database/seeders/`, dan `MahasiswaController.php` (terutama method `milestone()`, `proposal()`, `storeProposal()`, `storeMilestoneUpload()`). Jangan membuat ulang tabel atau model yang sudah ada.

Kondisi tabel yang sudah ada:
- `kelas`: id, nama_kelas, timestamps
- `koordinator`: id, nama, email (unique), password, nip, nidn (unique), timestamps
- `dosen`: id, nama, email (unique), password, nip, nidn (unique), koordinator_id (FK nullable), timestamps
- `mahasiswa`: id, nama, email (unique), password, nim (unique), kelas_id (FK nullable), timestamps

### 1. Migration — urutan WAJIB sesuai dependensi foreign key

Gunakan prefix timestamp berurutan `2026_10_15_000001_...`, `2026_10_15_000002_...`, dan seterusnya, mengikuti urutan di bawah. Pakai `constrained('nama_tabel')` dengan nama tabel eksplisit. Aturan `onDelete`: `restrictOnDelete()` untuk referensi ke data master (kelas, periode, milestone, mata_kuliah, dosen); `cascadeOnDelete()` untuk anak yang tidak bermakna tanpa induknya; `nullOnDelete()` untuk referensi opsional (misal pengunggah).

**A. Tabel master baru**

| No | Tabel | Kolom |
|---|---|---|
| 1 | `periode` | id, nama_periode, tahun_akademik, tanggal_mulai (date), tanggal_selesai (date), is_aktif (boolean, default false), timestamps |
| 2 | `mata_kuliah` | id, nama_matkul, prodi (nullable), sks (unsignedTinyInteger, nullable), is_aktif (boolean, default true), timestamps |

**B. Perubahan tabel yang sudah ada (migration alter terpisah per tabel)**

| No | Tabel | Perubahan |
|---|---|---|
| 3 | `kelas` | tambah prodi (nullable), periode_id (FK nullable -> periode, nullOnDelete), is_aktif (boolean, default true) |
| 4 | `dosen` | hapus kolom nip; tambah is_aktif (boolean, default true), last_login_at (timestamp nullable) |
| 5 | `koordinator` | hapus kolom nip; tambah dosen_id (FK nullable unique -> dosen, nullOnDelete) untuk fitur switch role koordinator yang juga dosen pengampu |
| 6 | `mahasiswa` | tambah is_aktif (boolean, default true) |

**C. Tabel baru**

| No | Tabel | Kolom |
|---|---|---|
| 7 | `milestone` | id, periode_id (FK -> periode, restrict), kode (string 10, mis. "M1"), judul, deskripsi (text, nullable), urutan (unsignedTinyInteger), deadline (date, nullable), timestamps. Unique (periode_id, urutan) |
| 8 | `deliverable` | id, milestone_id (FK -> milestone, cascade), nama, urutan (unsignedTinyInteger), timestamps |
| 9 | `dosen_pengampu` | id, dosen_id (FK -> dosen, restrict), mata_kuliah_id (FK -> mata_kuliah, restrict), kelas_id (FK -> kelas, restrict), timestamps. Unique (dosen_id, mata_kuliah_id, kelas_id) |
| 10 | `kelompok` | id, nama_kelompok, dosen_id (FK -> dosen, restrict), kelas_id (FK -> kelas, restrict), periode_id (FK -> periode, restrict), jadwal_bimbingan (string, nullable), tanggal_dibuat (date), timestamps |
| 11 | `anggota_kelompok` | id, kelompok_id (FK -> kelompok, cascade), mahasiswa_id (FK -> mahasiswa, restrict), role_label (string, nullable), is_ketua (boolean, default false), timestamps. Unique (kelompok_id, mahasiswa_id) |
| 12 | `proposal` | id, kelompok_id (FK -> kelompok, cascade), judul, deskripsi (text), latar_belakang (text), tujuan (text), metode (text), file_proposal (string, nullable), status (string, default 'diajukan'), catatan_revisi (text, nullable), tanggal_pengajuan (date), timestamps |
| 13 | `riwayat_proposal` | id, proposal_id (FK -> proposal, cascade), versi (unsignedSmallInteger), judul, file_proposal (string, nullable), status (string), catatan (text, nullable), timestamps. Unique (proposal_id, versi) |
| 14 | `logbook_milestone` | id, milestone_id (FK -> milestone, restrict), kelompok_id (FK -> kelompok, cascade), mahasiswa_id (FK -> mahasiswa, restrict), tanggal (date), kegiatan (text), kendala (text, nullable), link_bukti (string, nullable), file_bukti (string, nullable), status (string, default 'diajukan'), feedback (text, nullable), timestamps |
| 15 | `milestone_kelompok` | id, milestone_id (FK -> milestone, cascade), kelompok_id (FK -> kelompok, cascade), status (string, default 'belum dimulai'), catatan (text, nullable, catatan dosen untuk milestone ini), timestamps. Unique (milestone_id, kelompok_id) |
| 16 | `pengumpulan_deliverable` | id, deliverable_id (FK -> deliverable, cascade), kelompok_id (FK -> kelompok, cascade), diunggah_oleh (FK nullable -> mahasiswa, nullOnDelete), file_name (string, nullable), file_path (string, nullable), status (string, default 'belum dimulai'), catatan (text, nullable), timestamps. Unique (deliverable_id, kelompok_id) |
| 17 | `penilaian_kelompok` | id, kelompok_id (FK -> kelompok, cascade), milestone_id (FK -> milestone, restrict), nilai (decimal 5,2), catatan (text, nullable), timestamps. Unique (kelompok_id, milestone_id) |
| 18 | `penilaian_individu` | id, mahasiswa_id (FK -> mahasiswa, restrict), kelompok_id (FK -> kelompok, cascade), milestone_id (FK -> milestone, restrict), nilai (decimal 5,2), catatan (text, nullable), timestamps. Unique (mahasiswa_id, kelompok_id, milestone_id) |
| 19 | `notifikasi` | id, `morphs('penerima')` (penerima_type + penerima_id), judul, isi (text), tautan (string, nullable), dibaca_pada (timestamp nullable), timestamps |

Semua status disimpan sebagai **string biasa**, bukan enum database dan bukan tabel master. Nilai yang dipakai: proposal dan logbook `diajukan` / `perlu revisi` / `disetujui`; deliverable `belum dimulai` / `diajukan` / `perlu revisi` / `disetujui`; milestone_kelompok `belum dimulai` / `berjalan` / `selesai`.

Setiap migration harus punya method `down()` yang membalikkan perubahannya dengan benar (termasuk migration alter: kembalikan kolom `nip` yang dihapus dengan tipe string).

### 2. Model

Buat Model baru: `Periode`, `MataKuliah`, `Milestone`, `Deliverable`, `DosenPengampu`, `Kelompok`, `AnggotaKelompok`, `Proposal`, `RiwayatProposal`, `LogbookMilestone`, `MilestoneKelompok`, `PengumpulanDeliverable`, `PenilaianKelompok`, `PenilaianIndividu`, `Notifikasi`.

Setiap Model: `protected $table` eksplisit, `protected $fillable` berisi semua kolom selain id dan timestamps, dan relasinya. Pakai `casts` yang sesuai (date, boolean, decimal).

Perbarui Model lama: `Kelas` (tambah kolom baru ke `$fillable`), `Dosen`, `Koordinator`, `Mahasiswa`. **Hapus `'nip'` dari `$fillable`** pada `Dosen` dan `Koordinator`. Jangan ubah bagian autentikasi (extends Authenticatable, trait, `$hidden`, cast `hashed` pada password).

### 3. Relasi Eloquent

- `Koordinator` hasMany `Dosen`; belongsTo `Dosen` (lewat dosen_id, nullable); morphMany `Notifikasi` sebagai penerima
- `Dosen` belongsTo `Koordinator`; hasMany `DosenPengampu`, `Kelompok`; morphMany `Notifikasi`
- `Mahasiswa` belongsTo `Kelas`; hasMany `AnggotaKelompok`, `LogbookMilestone`, `PenilaianIndividu`; belongsToMany `Kelompok` lewat `anggota_kelompok` (withPivot role_label, is_ketua); morphMany `Notifikasi`
- `Periode` hasMany `Kelas`, `Kelompok`, `Milestone`
- `MataKuliah` hasMany `DosenPengampu`
- `Kelas` belongsTo `Periode`; hasMany `Mahasiswa`, `Kelompok`, `DosenPengampu`
- `DosenPengampu` belongsTo `Dosen`, `MataKuliah`, `Kelas`
- `Kelompok` belongsTo `Dosen`, `Kelas`, `Periode`; hasMany `AnggotaKelompok`, `Proposal`, `LogbookMilestone`, `MilestoneKelompok`, `PengumpulanDeliverable`, `PenilaianKelompok`, `PenilaianIndividu`; belongsToMany `Mahasiswa` lewat `anggota_kelompok`
- `AnggotaKelompok` belongsTo `Kelompok`, `Mahasiswa`
- `Proposal` belongsTo `Kelompok`; hasMany `RiwayatProposal`
- `RiwayatProposal` belongsTo `Proposal`
- `Milestone` belongsTo `Periode`; hasMany `Deliverable`, `LogbookMilestone`, `MilestoneKelompok`, `PenilaianKelompok`, `PenilaianIndividu`
- `Deliverable` belongsTo `Milestone`; hasMany `PengumpulanDeliverable`
- `PengumpulanDeliverable` belongsTo `Deliverable`, `Kelompok`; belongsTo `Mahasiswa` sebagai pengunggah (lewat diunggah_oleh)
- `LogbookMilestone` belongsTo `Milestone`, `Kelompok`, `Mahasiswa`
- `MilestoneKelompok`, `PenilaianKelompok` belongsTo `Kelompok`, `Milestone`
- `PenilaianIndividu` belongsTo `Mahasiswa`, `Kelompok`, `Milestone`
- `Notifikasi` morphTo `penerima`

Karena nama tabel singular, daftarkan **morph map** di `AppServiceProvider::boot()` dengan `Relation::enforceMorphMap(['mahasiswa' => Mahasiswa::class, 'dosen' => Dosen::class, 'koordinator' => Koordinator::class])`.

### 4. Seeder

Daftarkan semua seeder di `DatabaseSeeder` dengan urutan yang benar. Semua seeder wajib **idempotent**: pakai `updateOrCreate` atau `firstOrCreate` dengan kunci unik, supaya aman dijalankan berulang.

- `MasterDataSeeder`: 1 periode aktif ("Ganjil 2026/2027", is_aktif true, tanggal contoh), 3 mata kuliah (Pemrograman Web Lanjut, Manajemen Proyek Sistem Informasi, Penjaminan Mutu Perangkat Lunak), 1 kelas ("SIB 3B") yang terhubung ke periode tadi
- `MilestoneSeeder`: ambil **judul, deskripsi, tenggat, dan nama deliverable dari array di `MahasiswaController::milestone()`** (5 milestone M1 sampai M5 dengan total 15 deliverable), simpan ke tabel `milestone` dan `deliverable` pada periode aktif. Jangan menulis ulang teksnya dari ingatan, salin dari kode.
- `RoleSeeder` (sudah ada): ubah menjadi idempotent (`updateOrCreate` dengan kunci nidn / nim), hapus field `nip`, pertahankan kredensial yang ada (NIM `2024001`, NIDN `0099000002` untuk dosen dan `0099000001` untuk koordinator, password `password`), isi `kelas_id` mahasiswa dengan kelas "SIB 3B" dan `koordinator_id` dosen dengan koordinator seeder. Pastikan seeder `DatabaseSeeder` yang memakai `User::factory()` juga idempotent atau dihapus bila tidak dipakai.
- `KelompokSeeder`: 1 kelompok contoh ("Kelompok Contoh") pada kelas dan periode di atas, dibimbing dosen seeder, beranggotakan mahasiswa seeder dengan `is_ketua` true dan `role_label` "PM". Tambahkan 1 baris `dosen_pengampu` yang menghubungkan dosen seeder, mata kuliah Pemrograman Web Lanjut, dan kelas SIB 3B.

## Di Luar Lingkup

- Jangan ubah route, controller, view, middleware, config auth, atau logic login apa pun
- **Jangan mengganti data mock di `MahasiswaController`**, itu dikerjakan anggota tim per fitur di PRD terpisah
- Jangan menjalankan `php artisan migrate`, `migrate:fresh`, atau `db:seed`; cukup buat file-nya, eksekusi dilakukan manual oleh pemilik project setelah review
- Aturan "satu mahasiswa hanya satu kelompok per periode" divalidasi di logic aplikasi pada fitur pembentukan kelompok nanti, bukan di database
- Pengecekan `is_aktif` saat login (akun nonaktif tidak boleh masuk) dikerjakan terpisah; PRD ini hanya menyediakan kolomnya
- Implementasi penyimpanan file upload (storage, link) tidak dikerjakan; PRD ini hanya menyediakan kolom path-nya

## Definition of Done

- [ ] Migration A sampai C (19 item) ada dengan urutan timestamp sesuai dependensi FK, dan setiap `down()` benar
- [ ] 15 Model baru dengan `$table`, `$fillable`, `casts`, dan relasi sesuai daftar
- [ ] Model lama diperbarui tanpa menyentuh bagian autentikasi, `'nip'` dihapus dari `$fillable`
- [ ] Morph map terdaftar di `AppServiceProvider`
- [ ] Seeder `MasterDataSeeder`, `MilestoneSeeder`, `RoleSeeder` (diperbarui), dan `KelompokSeeder` terdaftar di `DatabaseSeeder` dan idempotent
- [ ] Data milestone dan deliverable hasil seeder identik dengan yang ada di `MahasiswaController::milestone()`
- [ ] Tidak ada perubahan di luar folder `database/`, `app/Models/`, dan `app/Providers/AppServiceProvider.php`
- [ ] Tidak ada perintah migrate atau seed yang dijalankan otomatis
