# SIM-PBL Production Deployment

Deployment production bersifat **manual dan dua tahap**. Jangan menambahkan trigger `push` ke workflow.

## Prasyarat

- GitHub Environment `production` dengan required reviewer.
- Secrets: `EC2_SSH_KEY`, `EC2_HOST`, `EC2_USER`, `EC2_DEPLOY_PATH`.
- File kredensial backup MySQL di server, mis. `/etc/sim-pbl/backup.cnf`, permission `600`, hanya dapat dibaca user deploy/root.
- PHP-FPM service name yang benar, mis. `php8.3-fpm` (jangan menebak; periksa `systemctl list-units --type=service | grep php`).
- Backup off-instance terkonfirmasi (S3 privat atau storage eksternal).
- Target commit **full SHA** dari `main`.

## Larangan

- Jangan menjalankan `php artisan db:seed --force` di production.
- Jangan menjalankan `DatabaseSeeder`/seeder demo di production.
- Jangan melakukan `migrate:rollback` otomatis.
- Jangan melakukan `git reset --hard` sebagai bagian deployment normal.
- Jangan membuka maintenance mode jika verifikasi belum lolos.

## Tahap 1 — prepare dan migration

Trigger `Deploy SIM-PBL to EC2` dengan:

- `phase=prepare`
- `target_commit=<sha main>`
- `migration_approved=yes`
- `offsite_backup_confirmed=yes`
- `backup_cnf_path=<path file kredensial>`

Workflow akan:

1. Memverifikasi target commit berasal dari `origin/main` dan fast-forward dari HEAD.
2. Membuat backup database ke `/var/backups/sim-pbl/<timestamp>/database.sql.gz`.
3. Memvalidasi `gzip -t`, checksum SHA-256, dan isi dump memiliki `CREATE TABLE`.
4. Menulis `deploy-meta.txt` berisi previous/target commit dan path backup.
5. Mengaktifkan maintenance mode.
6. Fast-forward ke target commit.
7. Menjalankan Composer, NPM build.
8. Menjalankan `php artisan migrate --force`.
9. Memeriksa tidak ada migration pending.
10. Menjalankan `scripts/deploy-verify-schema.php`.
11. Meninggalkan aplikasi dalam maintenance mode.

Backup off-instance tidak dilakukan oleh workflow; konfirmasi manual wajib sebelum migration.

State yang perlu dicatat dari log:
- `PREPARE_OK`
- path backup
- previous commit dan target commit

## Tahap 2 — activate

Setelah Tahap 1 berhasil dan backup diverifikasi:

- `phase=activate`
- `target_commit=<sha yang sama>`
- `backup_path=<path database.sql.gz dari Tahap 1>`
- `php_fpm_service=<service PHP-FPM aktual>`

Workflow akan:

1. Memverifikasi HEAD server sama dengan target commit.
2. Memverifikasi backup dan checksum.
3. Memastikan maintenance mode aktif.
4. Memastikan tidak ada migration pending.
5. Menjalankan schema check.
6. `optimize:clear`, `config:cache`, `route:cache`, `view:cache`.
7. Reload PHP-FPM.
8. Smoke test HTTP ke `/login`.
9. Menampilkan jumlah error log terbaru (jika tersedia).
10. `php artisan up` hanya setelah pemeriksaan lolos.

## Smoke test

Production smoke bersifat read-only:

- `/login` merespons 2xx/3xx.
- Login dan halaman dashboard mahasiswa dapat dibuka dengan akun uji yang disetujui.
- Halaman milestone menampilkan tiga mata kuliah.
- Halaman logbook dan aktivitas dashboard konsisten.
- Dosen Pengampu dapat membuka halaman penyelesaian milestone tanpa submit.
- Tidak ada HTTP 5xx baru di log.

Pengujian tulis (ubah status/upload) hanya di staging/UAT, bukan production.

## Jika gagal

- Aplikasi tetap maintenance karena workflow tidak memanggil `up` saat gagal.
- Jangan menjalankan restore/rollback otomatis.
- Gunakan `deploy-meta.txt` untuk mendapatkan `previous_commit` dan path backup.
- Pemulihan harus disetujui pemilik proyek:
  1. Tetap maintenance.
  2. Kembalikan kode ke `previous_commit` melalui mekanisme deploy yang disetujui.
  3. Restore backup hanya jika schema/data tidak kompatibel.
  4. Jalankan schema check dan smoke test.
  5. Baru `php artisan up`.

Restore manual (hanya setelah persetujuan):

```bash
gunzip -c /var/backups/sim-pbl/<timestamp>/database.sql.gz \
  | mysql --defaults-extra-file=/etc/sim-pbl/backup.cnf "$DB_NAME"
```

## Kondisi wajib berhenti

- Backup gagal, kosong, atau checksum tidak valid.
- File kredensial backup tidak dapat dibaca.
- Off-instance backup belum dikonfirmasi.
- Target commit bukan fast-forward dari HEAD/`origin/main`.
- Migration gagal atau masih pending.
- Schema check gagal.
- PHP-FPM service tidak valid.
- Smoke test HTTP gagal.

## Backup tambahan

- Jangan menghapus backup lama secara otomatis.
- Simpan salinan terenkripsi di luar EC2.
- Backup hanya di EBS yang sama tidak melindungi dari kegagalan instance/disk.