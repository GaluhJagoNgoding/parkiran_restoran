# Sistem Parkir Restoran - Setup untuk Hosting

## 🚀 Panduan Upload ke Hosting

### 1. Persiapan File
1. Upload semua file kecuali folder `.git` dan file `.env.example`
2. Pastikan file `.htaccess` ikut terupload
3. Buat file `.env` di root folder berdasarkan `.env.example`

### 2. Konfigurasi Database
1. Buat database di hosting Anda
2. Edit file `.env` dengan kredensial database hosting:
   ```
   DB_HOST=your_host
   DB_USER=your_username
   DB_PASS=your_password
   DB_NAME=your_database_name
   ```

### 3. Setup Database
1. Akses `http://yourdomain.com/setup.php` untuk membuat tabel dan data sample
2. Setelah setup selesai, **hapus file setup.php** dari server

### 4. Konfigurasi PHP
Pastikan hosting mendukung:
- PHP 7.4 atau lebih baru
- MySQLi extension
- mod_rewrite (untuk .htaccess)

### 5. URL Access
- Halaman utama: `http://yourdomain.com`
- Login: `http://yourdomain.com/auth/index`

### 6. Troubleshooting
Jika ada error:
1. Cek file `.env` sudah benar
2. Pastikan database sudah dibuat dan kredensial benar
3. Cek PHP error logs di hosting
4. Pastikan semua file terupload dengan benar

### 7. Keamanan
Setelah setup:
- Hapus file `setup.php`
- Hapus file `public/debug_users.php`
- Matikan error reporting di production dengan mengubah `APP_DEBUG=false` di `.env`

## 📋 Akun Default
- **Admin**: username: `admin`, password: `admin123`
- **Petugas**: username: `petugas`, password: `petugas123`
- **Owner**: username: `owner`, password: `owner123`