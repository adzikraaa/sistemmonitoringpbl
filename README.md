# Sistem Monitoring PBL

Aplikasi monitoring Project Based Learning berbasis Laravel + MySQL.

---

## ⚙️ Setup Awal (Pertama Kali Clone / Pull)

### Prasyarat
- PHP 8.2+
- Composer
- MySQL (XAMPP / Laragon / MySQL langsung)
- Node.js & npm

### Langkah-langkah

**1. Clone / pull project**
```bash
git clone <url-repo>
# atau
git pull
```

**2. Install dependencies**
```bash
composer install
npm install
```

**3. Buat file `.env`**
```bash
copy .env.example .env
php artisan key:generate
```

**4. Buat database MySQL**

Buka phpMyAdmin / MySQL CLI dan buat database:
```sql
CREATE DATABASE sim_pbl;
```

**5. Sesuaikan `.env` jika perlu**

Buka `.env`, pastikan bagian DB sesuai dengan MySQL lokal kamu:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sim_pbl
DB_USERNAME=root
DB_PASSWORD=        ← isi password MySQL kamu (kosong jika XAMPP default)
```

**6. Jalankan migrasi + seeder**
```bash
php artisan migrate:fresh --seed
```

**7. Jalankan aplikasi**
```bash
php artisan serve
npm run dev
```

Akses di: `http://localhost:8000`

---

## 🔄 Setiap Kali `git pull` (Update dari Teman)

Jika ada perubahan skema database atau data seeder:
```bash
git pull
composer install        ← jika ada package baru
npm install             ← jika ada package baru
php artisan migrate --seed
```

> ⚠️ Jika ada conflict data atau error migrasi, jalankan:
> ```bash
> php artisan migrate:fresh --seed
> ```
> **Perhatian:** `migrate:fresh` akan menghapus semua data dan mengisi ulang dari seeder.

---

## 👤 Akun Login Default (dari Seeder)

| Role | Identifier | Password |
|------|-----------|----------|
| Mahasiswa | NIM: `2024001` | `password` |
| Dosen | NIDN: *(lihat seeder)* | `password` |
| Koordinator | NIDN: *(lihat seeder)* | `password` |

---

## 📋 Aturan Tim untuk Perubahan Database

> Wajib diikuti agar semua anggota tim tetap sinkron!

### Jika kamu mengubah struktur tabel:
```bash
php artisan make:migration nama_perubahan_yang_jelas
# edit file migration yang baru dibuat
git add database/migrations/
git commit -m "feat: tambah kolom X ke tabel Y"
git push
```

### Jika kamu menambah/mengubah data awal (seeder):
```bash
# Edit file database/seeders/RoleSeeder.php (atau buat seeder baru)
git add database/seeders/
git commit -m "feat: tambah data seed untuk X"
git push
```

### Setelah pull dari teman yang mengubah database:
```bash
php artisan migrate --seed
# atau jika ada konflik:
php artisan migrate:fresh --seed
```

---

## 🗂️ Struktur Penting

```
database/
├── migrations/   ← perubahan skema tabel (ikut ke Git ✅)
├── seeders/      ← data awal/default (ikut ke Git ✅)
.env              ← konfigurasi lokal (TIDAK ikut Git ❌)
.env.example      ← template .env untuk tim (ikut ke Git ✅)
```

---

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

