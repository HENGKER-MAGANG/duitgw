# DuitGW 💵

Aplikasi pencatatan keuangan pribadi dengan bukti foto wajib untuk setiap
transaksi masuk/keluar. Dibangun dengan PHP native (MVC custom, tanpa
framework pihak ketiga), MySQL, Tailwind CSS, dan React (build production,
tanpa perlu Node.js di server hosting).

## Fitur

- **Role Admin**: hanya bisa melihat statistik agregat semua pengguna
  (total pemasukan/pengeluaran, saldo, jumlah transaksi per pengguna) —
  tidak bisa mengubah/menghapus data pengguna.
- **Role User**: input & kelola transaksi keuangan sendiri (masuk/keluar),
  wajib melampirkan bukti foto (struk/nota/screenshot) di setiap transaksi.
- Dashboard dengan grafik tren 6 bulan terakhir (React + Recharts).
- Desain bertema merah-putih dengan nuansa struk/buku kas — bukan template
  generik.
- Responsif (mobile, tablet, desktop).

## Struktur Proyek

```
duitgw/
├── app/                  # Kode aplikasi (Controllers, Models, Views, Core)
├── config/config.php     # KONFIGURASI UTAMA — edit sebelum deploy
├── database/schema.sql   # Skema database + akun admin default
├── public/               # Document root (arahkan domain ke sini)
│   ├── index.php         # Front controller
│   ├── .htaccess
│   ├── assets/           # CSS & JS hasil build (sudah dikompilasi)
│   └── uploads/bukti/    # Tempat penyimpanan bukti foto
├── react/                # Source code React (opsional, untuk development)
└── .htaccess             # Fallback jika document root tidak bisa diarahkan ke /public
```

## Instalasi di Hosting (cPanel / shared hosting)

1. **Buat database MySQL** melalui cPanel (MySQL Databases), catat nama
   database, username, dan password.
2. **Import `database/schema.sql`** melalui phpMyAdmin. Ini akan membuat
   semua tabel dan 1 akun admin default:
   - Email: `admin@duitgw.test`
   - Password: `admin123`
   - **Segera ganti password ini setelah login pertama** (lewat query
     manual di phpMyAdmin, karena admin bersifat view-only di aplikasi).
3. **Upload seluruh folder** (kecuali `react/node_modules` jika ada) ke
   hosting via File Manager / FTP.
4. **Edit `config/config.php`**:
   - Isi `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS` sesuai database kamu.
   - Isi `BASE_URL` sesuai domain kamu (tanpa trailing slash).
5. **Arahkan document root** domain/subdomain ke folder `public/`
   (biasanya di cPanel: Domains → pilih domain → Document Root).
   - Jika hosting kamu **tidak bisa** mengatur document root ke `public/`
     (paket hosting murah biasanya document root terkunci ke root akun),
     biarkan file `.htaccess` di root project — ini akan otomatis
     meneruskan request ke folder `public/`. Dalam kasus ini, set
     `BASE_URL` ke `https://namadomainmu.com/public`.
6. **Pastikan folder `public/uploads/bukti/` bisa ditulis** (permission
   755 atau 775, tergantung konfigurasi hosting).
7. Buka domain kamu → halaman login akan muncul. Daftar akun baru sebagai
   user, atau login sebagai admin untuk melihat statistik.

## Instalasi di CapRover (home server / Docker)

Proyek ini sudah menyertakan `Dockerfile`, `captain-definition`, dan
`docker/apache-vhost.conf` — siap deploy langsung ke CapRover tanpa
konfigurasi tambahan di sisi image.

### 1. Siapkan database

Cara termudah: deploy MySQL lewat **One-Click Apps** di CapRover
(cari "MySQL"), beri nama app misalnya `duitgw-db`. Setelah jalan,
CapRover otomatis membuat service internal dengan nama seperti
`srv-captain--duitgw-db` yang bisa diakses oleh app lain di jaringan
Docker yang sama.

Setelah database jalan, import `database/schema.sql` — bisa lewat
phpMyAdmin (kalau kamu deploy phpMyAdmin juga, sesuai yang kamu pakai
untuk COM) atau lewat `docker exec` ke container MySQL:

```bash
docker exec -i $(docker ps -qf "name=srv-captain--duitgw-db") \
    mysql -uroot -p"PASSWORD_MYSQL_KAMU" < database/schema.sql
```

### 2. Buat app baru di CapRover

Di dashboard CapRover → **Apps** → **One-Click Apps/Databases** →
pilih "Create App" biasa (bukan one-click), beri nama misalnya `duitgw`.
Aktifkan **HTTPS** dan hubungkan ke domain/subdomain kamu (via
Cloudflare Tunnel yang sudah kamu pakai untuk COM, sama caranya).

### 3. Set Environment Variables

Di tab **App Configs** app `duitgw`, isi Environmental Variables:

| Key         | Value                                              |
|-------------|-----------------------------------------------------|
| `DB_HOST`   | `srv-captain--duitgw-db` (nama service MySQL kamu)  |
| `DB_NAME`   | `duitgw`                                            |
| `DB_USER`   | `root` (atau user MySQL yang kamu buat)             |
| `DB_PASS`   | password MySQL kamu                                 |
| `BASE_URL`  | `https://duitgw.namadomainmu.my.id` (tanpa trailing slash) |
| `APP_ENV`   | `production`                                        |

`config/config.php` sudah otomatis membaca env var ini (fallback ke
nilai default kalau env var tidak diset).

### 4. Set Persistent Directory (WAJIB — jangan lewati)

Tanpa ini, semua bukti foto yang diupload akan **hilang setiap kali
redeploy**, karena container CapRover bersifat ephemeral. Di tab
**App Configs** → **Persistent Directories**, tambahkan:

- Path di Container: `/var/www/html/public/uploads/bukti`
- Label: `duitgw-uploads` (bebas)

### 5. Deploy

Paling gampang pakai CapRover CLI dari folder project ini:

```bash
npm install -g caprover   # sekali saja
caprover deploy
```

Ikuti prompt untuk pilih server CapRover kamu dan nama app (`duitgw`).
CLI akan otomatis mem-package folder ini (mengikuti `.dockerignore`)
dan build image dari `Dockerfile` yang sudah disiapkan.

Alternatif: upload sebagai tarball manual lewat tab **Deployment** di
dashboard app CapRover kamu (opsi "Upload tar file").

### 6. Cek hasil

Buka `BASE_URL` yang kamu set tadi. Kalau muncul halaman login
bertema struk merah-putih, berarti deploy berhasil.

## Instalasi Lokal (XAMPP/Laragon/dll)

1. Copy folder ini ke `htdocs` (XAMPP) atau `www` (Laragon).
2. Buat database `duitgw`, import `database/schema.sql`.
3. Edit `config/config.php`:
   - `BASE_URL` → `http://localhost/duitgw/public` (sesuaikan nama folder).
   - `APP_ENV` → `'local'` (agar error PHP ditampilkan saat development).
4. Akses `http://localhost/duitgw/public`.

## Mengembangkan Ulang Bagian React/Tailwind (opsional)

Aset CSS dan JS di `public/assets/` **sudah dikompilasi** dan siap pakai —
kamu tidak perlu Node.js di server produksi. Tapi jika ingin mengubah
tampilan/komponen React:

```bash
cd react
npm install

# Build ulang Tailwind CSS (dari root project)
cd ..
npx --prefix ./react tailwindcss -c ./tailwind.config.js \
    -i ./react/src/tailwind-input.css \
    -o ./public/assets/css/app.css --minify

# Build ulang React (dari folder react/)
cd react
npx vite build
```

Setelah build, upload ulang folder `public/assets/` ke hosting.

## Catatan Keamanan

- Ganti password admin default segera setelah deploy.
- Folder `public/uploads/bukti/` sebaiknya tidak mengizinkan eksekusi PHP
  (tambahkan `.htaccess` dengan `php_flag engine off` di dalamnya jika
  hosting mendukung, untuk keamanan ekstra).
- `config/config.php` tidak boleh diakses publik — sudah otomatis aman
  karena berada di luar folder `public/`.

## Kredensial Demo

| Role  | Email                | Password  |
|-------|-----------------------|-----------|
| Admin | admin@duitgw.test      | admin123  |
| User  | *(daftar sendiri)*     | -         |
