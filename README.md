# Sistem Manajemen Sekolah SMK PGRI

Proyek ini adalah sistem manajemen berbasis website untuk SMK PGRI. Sistem ini dibangun menggunakan Framework Laravel dan mencakup fitur Authentication (termasuk via API menggunakan JWT-Auth), serta pengelolaan data dasar seperti Kelas, dan lainnya.

## Persyaratan (Prerequisites)

Sebelum menjalankan project ini, pastikan komputer Anda telah terinstall:
- [PHP](https://www.php.net/) (minimal versi 8.1 atau yang disyaratkan oleh versi Laravel yang digunakan)
- [Composer](https://getcomposer.org/)
- [MySQL](https://www.mysql.com/) / MariaDB (bisa menggunakan bawaan Laragon/XAMPP)
- [Git](https://git-scm.com/)
- [Laragon](https://laragon.org/) / XAMPP (untuk kemudahan local server)

## Langkah-langkah Instalasi (Setup)

Ikuti langkah-langkah di bawah ini untuk menjalankan project ini di komputer Anda (atau komputer anggota tim lainnya):

1. **Clone Repository**
   Buka terminal/CMD, arahkan ke folder `www` (Laragon) atau `htdocs` (XAMPP), lalu jalankan perintah:
   ```bash
   git clone <URL_REPOSITORY_ANDA>
   cd SMK_PGRI_NEWW
   ```

2. **Install Dependensi Composer**
   Jalankan perintah berikut untuk mengunduh semua package PHP (termasuk JWT-Auth) yang dibutuhkan:
   ```bash
   composer install
   ```

3. **Konfigurasi Environment (`.env`)**
   Copy file `.env.example` dan ubah namanya menjadi `.env`:
   ```bash
   cp .env.example .env
   ```
   Buka file `.env` dan sesuaikan koneksi database Anda. Misalnya:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=nama_database_smk
   DB_USERNAME=root
   DB_PASSWORD=
   ```

4. **Generate Application Key & JWT Secret**
   Jalankan kedua perintah ini untuk membuat key enkripsi aplikasi dan kunci rahasia untuk otentikasi JWT API:
   ```bash
   php artisan key:generate
   php artisan jwt:secret
   ```

5. **Migrasi Database & Seeder**
   Pastikan Laragon/XAMPP sudah menyala (layanan Apache & MySQL berjalan). Kemudian jalankan migrasi untuk membuat tabel-tabel di database lokal:
   ```bash
   php artisan migrate
   ```
   *(Jika ada seeder/data awal yang sudah Anda buat, bisa menggunakan `php artisan migrate --seed`)*

6. **Jalankan Aplikasi**
   Jika menggunakan Laragon, project bisa langsung diakses via browser dengan domain `.test` (misalnya `http://smk_pgri_neww.test`).
   Atau jika ingin menggunakan web server bawaan PHP/Laravel:
   ```bash
   php artisan serve
   ```
   Aplikasi akan berjalan di `http://localhost:8000`.

---

## Dokumentasi Akses API

Project ini menyediakan API Endpoint dengan otentikasi berbasis token JWT. Semua request ke endpoint yang dilindungi (protected routes) wajib menyertakan token pada header:
```text
Authorization: Bearer <token_anda_disini>
```
Header tersebut juga sebaiknya dibarengi dengan `Accept: application/json`.

### 1. Authentication (Auth)
- **POST** `/api/auth/login`
  - Body Params: `username`, `password`
  - Response: Mengembalikan JWT token jika login sukses. Token ini yang digunakan untuk request selanjutnya.
- **POST** `/api/auth/me`
  - Deskripsi: Mendapatkan data user yang sedang login. (Wajib bawa Bearer Token)
- **POST** `/api/auth/logout`
  - Deskripsi: Invalidate/menghancurkan token yang sedang aktif sehingga tidak bisa dipakai lagi. (Wajib bawa Bearer Token)
- **POST** `/api/auth/refresh`
  - Deskripsi: Memperbarui token. (Wajib bawa Bearer Token)

### 2. Kelas (Protected Routes)
*Semua endpoint kelas ini wajib membawa Bearer Token dari hasil Login.*

- **GET** `/api/kelas`
  - Deskripsi: Menampilkan daftar semua data kelas.
- **GET** `/api/kelas/{id}`
  - Deskripsi: Menampilkan detail satu data kelas berdasarkan ID-nya.
- **POST** `/api/kelas`
  - Deskripsi: Menambahkan data kelas baru.
  - Body Params: (sesuaikan dengan request body/form data yang diperlukan, misal `nama_kelas`, dsb).
- **PUT** `/api/kelas/{id}`
  - Deskripsi: Mengubah/Update data kelas.
- **DELETE** `/api/kelas/{id}`
  - Deskripsi: Menghapus data kelas.

---

> **Catatan untuk Tim:** Pastikan sebelum memulai ngoding/pengerjaan fitur baru, selalu lakukan `git pull origin main` (atau branch terkait) agar kode Anda sinkron dengan perubahan terbaru dari tim!
