# INFOS (Informasi Sekolah) - SMK Tunas Harapan Pati

Sistem Informasi dan Administrasi Terpadu untuk SMK Tunas Harapan Pati. Proyek ini dibangun dengan arsitektur **Decoupled** yang memisahkan Frontend dan Backend untuk memastikan kecepatan, keamanan, dan skalabilitas.

## 🚀 Fitur Utama

- **Manajemen Informasi (Mading Digital):** CRUD Pengumuman (Info Libur, Kegiatan, Jadwal Rapot) dengan dukungan unggah gambar dan file lampiran (PDF/Word/Excel).
- **Pengecekan Administrasi Siswa:** Pengecekan status pembayaran tagihan siswa berdasarkan Asesmen. Admin dapat mengunggah tagihan secara massal menggunakan file Excel.
- **Komentar Real-Time:** Interaksi tanya jawab pada setiap pengumuman secara langsung tanpa *refresh* halaman (Powered by Firebase).
- **Manajemen Autentikasi:** Sistem Login, Register, dan Manajemen Profil pengguna.
- **UI/UX Modern:** Desain antarmuka kustom yang responsif dan modern menggunakan Vanilla CSS.

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

### 1. Persiapan Backend (Laravel)
```bash
cd Informasi_Sekolah
composer install
cp .env.example .env
php artisan key:generate
# Sesuaikan konfigurasi database di file .env
php artisan migrate
php artisan storage:link
php artisan serve
```
*Backend akan berjalan di `http://localhost:8000`*

### 2. Persiapan Frontend (Vue.js)
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
