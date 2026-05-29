# INFOS (Informasi Sekolah) - SMK Tunas Harapan Pati

Sistem Informasi dan Administrasi Terpadu untuk SMK Tunas Harapan Pati. Proyek ini dibangun dengan arsitektur **Decoupled** yang memisahkan Frontend dan Backend untuk memastikan kecepatan, keamanan, dan skalabilitas.

## 🚀 Fitur Utama

- **Manajemen Informasi (Mading Digital):** CRUD Pengumuman (Info Libur, Kegiatan, Jadwal Rapot) dengan dukungan unggah gambar dan file lampiran (PDF/Word/Excel).
- **Pengecekan Administrasi Siswa:** Pengecekan status pembayaran tagihan siswa berdasarkan Asesmen. Admin dapat mengunggah tagihan secara massal menggunakan file Excel.
- **Komentar Real-Time:** Interaksi tanya jawab pada setiap pengumuman secara langsung tanpa *refresh* halaman (Powered by Firebase).
- **Manajemen Autentikasi:** Sistem Login, Register, dan Manajemen Profil pengguna.
- **UI/UX Modern:** Desain antarmuka kustom bergaya kalender, dilengkapi dengan sistem Paginasi (Pagination) di halaman beranda menggunakan Vanilla CSS.

## 🛠️ Teknologi yang Digunakan

### Frontend
- **Framework:** Vue.js 3 (Composition API)
- **Build Tool:** Vite
- **Routing:** Vue Router
- **HTTP Client:** Axios
- **Real-Time Database:** Firebase Realtime Database
- **Styling:** Vanilla CSS (Custom, Glassmorphism, Responsive)

### Backend
- **Framework:** Laravel 10/11 (PHP)
- **Database:** MySQL
- **ORM:** Eloquent
- **Package Tambahan:** `Maatwebsite/Laravel-Excel` (Untuk import file Excel)
- **Authentication:** Laravel Auth / Sanctum

## 📂 Struktur Proyek

Proyek ini dibagi menjadi dua repositori/folder utama:
1. `Informasi_Sekolah/` - Berisi *source code* Backend Laravel API.
2. `frondendinfos/` - Berisi *source code* Frontend Vue.js.

## ⚙️ Panduan Instalasi & Menjalankan Aplikasi

Ada dua cara untuk menjalankan aplikasi ini: menggunakan **Docker** (Direkomendasikan) atau menjalankannya secara manual.

### Opsi 1: Menggunakan Docker (Sangat Direkomendasikan) 🐳
Aplikasi ini sudah diatur secara lengkap agar bisa berjalan otomatis menggunakan Docker.
```bash
# Buka terminal di folder root (Project_INFOS_PIC)
docker-compose up -d --build
```
- **Frontend** akan berjalan di: `http://localhost:5173`
- **Backend (API)** akan berjalan di: `http://localhost:8080`
- **Database** akan otomatis berjalan di latar belakang (port 3306).

*(Catatan: Saat pertama kali dijalankan, kamu mungkin perlu membuat tabel database dengan masuk ke dalam container backend: `docker-compose exec backend php artisan migrate`)*

---

### Opsi 2: Instalasi Manual Tanpa Docker

#### 1. Persiapan Backend (Laravel)
```bash
cd Informasi_Sekolah
composer install
cp .env.example .env
php artisan key:generate
# Sesuaikan konfigurasi database di file .env
php artisan migrate
php artisan storage:link
php artisan serve --port=8080
```
*Backend akan berjalan di `http://localhost:8080`*

#### 2. Persiapan Frontend (Vue.js)
```bash
cd frondendinfos
npm install
# Konfigurasi Firebase Anda di src/firebase.js (Jika belum disetup)
npm run dev
```
*Frontend akan berjalan di `http://localhost:5173`*

## 🔒 Keamanan
- Middleware Laravel mengamankan halaman Dashboard.
- File upload divalidasi MIME Type-nya (hanya memperbolehkan gambar dan dokumen) untuk menghindari shell upload.
- SQL Injection dicegah otomatis dengan menggunakan Eloquent ORM Laravel.

---
*Dibuat untuk mempermudah akses informasi siswa dan pihak administrasi sekolah.*
