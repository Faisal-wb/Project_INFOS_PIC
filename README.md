# INFOS (Informasi Sekolah) - SMK Tunas Harapan Pati

Aplikasi web modern berbasis **Decoupled Architecture** (Pemisahan Frontend & Backend) yang dirancang sebagai pusat sistem informasi dan administrasi terpadu untuk SMK Tunas Harapan Pati.

---

## 🌟 Fitur Lengkap & Teknologi Pendukung

Proyek ini memiliki fitur yang sangat kaya untuk mempermudah komunikasi dan urusan administratif sekolah. Berikut adalah daftar fitur beserta teknologi pembuatannya:

### 1. Manajemen Mading Digital (Pengumuman Sekolah)
Pusat informasi interaktif untuk membagikan pengumuman penting sekolah.
- **Fitur:** 
  - CRUD (Tambah, Baca, Edit, Hapus) untuk 3 kategori: Info Libur, Kegiatan Sekolah, dan Jadwal Rapot.
  - Mendukung unggah gambar (Banner/Poster).
  - Mendukung unggah *File Lampiran* resmi (PDF, Word, Excel) agar dapat di-*download* pengunjung.
- **Teknologi:** Dibuat menggunakan **Laravel Resource Controller**, **Eloquent ORM** untuk operasi database, dan **Laravel Storage** (Local Public Disk) untuk menangani *file upload* dengan aman. Tampilan daftarnya menggunakan **Vue.js Reactivity** dengan Paginasi Otomatis di sisi *Client*.

### 2. Cek Tagihan & Administrasi Siswa
Memudahkan staf Tata Usaha dan Siswa dalam mengelola keuangan.
- **Fitur Admin:** 
  - Admin dapat meng-*upload* tagihan ribuan siswa secara massal hanya dengan *mengunggah file Excel (.xlsx)*.
  - Admin dapat melakukan filter dan menghapus data tagihan secara massal per kelas.
- **Fitur Siswa:** Siswa dapat mencari status pembayarannya hanya dengan memasukkan **NIS** dan memilih jenis **Asesmen** (ASTS, ASAS, ASAT) tanpa perlu *login*.
- **Teknologi:** Dibangun menggunakan paket **`Maatwebsite/Laravel-Excel`** untuk proses baca/tulis (*Import*) data Excel ke dalam MySQL. Antarmukanya dibuat interaktif dengan pencarian *real-time* berbasis **Vue.js Data Binding**.

### 3. Kolom Komentar Real-Time ⚡
Forum interaktif mini pada setiap halaman pengumuman.
- **Fitur:** Siswa/Admin bisa mengirim pertanyaan/komentar dan balasannya akan langsung muncul di layar semua orang pada detik itu juga (tanpa perlu menekan tombol *refresh* browser).
- **Teknologi:** Fitur ini ditenagai murni oleh **Firebase Realtime Database (NoSQL)**. Alih-alih membebani server Laravel, kode Vue.js langsung melakukan koneksi *WebSocket* dua arah ke *cloud* Firebase.

### 4. Autentikasi & Profil Pengguna
Keamanan sistem tingkat dasar.
- **Fitur:** 
  - Registrasi, Login, dan Logout.
  - Proteksi Halaman (Dashboard Admin terkunci).
  - Manajemen Profil (Siswa dapat mengunggah Foto Profil/Avatar secara *custom*).
- **Teknologi:** Menggunakan sistem **Laravel Auth (Session)** untuk mengelola autentikasi dan *password* terenkripsi (Bcrypt) di *Backend*. Di sisi *Frontend*, menggunakan **Vue Router Navigation Guards** untuk menyeleksi hak akses di *browser*.

### 5. UI/UX "Kalender" Custom 
Antarmuka website yang bersih, responsif, dan menarik.
- **Fitur:** Menampilkan tanggal dengan desain berbentuk kalender lipat (*calendar-box*), desain form yang rapi, transisi *hover* efek kaca (*Glassmorphism*), tata letak *grid*, dan pewarnaan status yang disesuaikan.
- **Teknologi:** Ditulis menggunakan **100% Vanilla CSS 3** murni tanpa framework tambahan (seperti Bootstrap/Tailwind), menjadikannya sangat ringan, fleksibel, dan kustom.

---

## 🛠️ Persiapan & Cara Menjalankan Aplikasi (Setup Detail)

Proyek ini dipisah menjadi dua sistem (Backend `Informasi_Sekolah` dan Frontend `frondendinfos`). Berikut adalah panduan menjalankan sistemnya secara mendetail:

### Opsi 1: Setup Sangat Mudah dengan Docker (Direkomendasikan) 🐳
Ini adalah cara tercepat jika kamu memiliki aplikasi *Docker Desktop* di komputermu.

1. Buka Terminal di folder utama proyek (tempat file `docker-compose.yml` berada).
2. Jalankan perintah:
   ```bash
   docker-compose up -d --build
   ```
3. Docker akan otomatis men-*download* PHP, Node.js, dan MySQL.
4. Buat tabel database pertamanya dengan menjalankan migrasi di dalam *container* Laravel:
   ```bash
   docker-compose exec backend php artisan migrate
   ```
5. Buka Browser:
   - **Frontend (Tampilan Web):** `http://localhost:5173`
   - **Backend (API URL):** `http://localhost:8080`

---

### Opsi 2: Setup Manual Tanpa Docker
Gunakan cara ini jika kamu menggunakan XAMPP/Laragon.

#### A. Setup Backend (Laravel)
1. Buka aplikasi XAMPP/Laragon dan nyalakan **MySQL** serta **Apache**.
2. Buat database baru bernama `informasi_sekolah` melalui PhpMyAdmin.
3. Buka Terminal/CMD, arahkan ke folder backend:
   ```bash
   cd Informasi_Sekolah
   ```
4. Instal semua paket PHP:
   ```bash
   composer install
   ```
5. Buat konfigurasi `.env`:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
6. Buka file `.env` di text editor dan pastikan konfigurasi databasenya sesuai dengan MySQL milikmu:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=informasi_sekolah
   DB_USERNAME=root      # (Atau username MySQL kamu)
   DB_PASSWORD=          # (Atau password MySQL kamu)
   ```
7. Buat tabel di MySQL dan tautkan folder foto/file agar bisa diakses publik:
   ```bash
   php artisan migrate
   php artisan storage:link
   ```
8. Nyalakan server lokal Backend, wajib di **Port 8080** karena Frontend mengeksekusi API ke port ini:
   ```bash
   php artisan serve --port=8080
   ```

#### B. Setup Frontend (Vue.js)
1. Buka **Terminal Baru** (jangan tutup terminal Laravel), lalu arahkan ke folder frontend:
   ```bash
   cd frondendinfos
   ```
2. Instal semua dependensi JavaScript:
   ```bash
   npm install
   ```
3. *(Sangat Penting)* Buat file bernama `.env` di dalam folder `frondendinfos` lalu tempel konfigurasi Firebase kamu di dalamnya (agar fitur Komentar berfungsi):
   ```env
   VITE_FIREBASE_API_KEY=AIzaSy...
   VITE_FIREBASE_AUTH_DOMAIN=app-mu.firebaseapp.com
   VITE_FIREBASE_DATABASE_URL=https://app-mu...
   VITE_FIREBASE_PROJECT_ID=app-mu
   VITE_FIREBASE_STORAGE_BUCKET=app-mu...
   VITE_FIREBASE_MESSAGING_SENDER_ID=123...
   VITE_FIREBASE_APP_ID=1:123...
   ```
4. Jalankan server pengembangan Vue:
   ```bash
   npm run dev
   ```
5. Selesai! Web dapat diakses di browser pada alamat **`http://localhost:5173`**.

---

## 🔒 Catatan Keamanan
- Konfigurasi kredensial (seperti sandi database dan kunci API Firebase) tersimpan di `.env` yang secara otomatis **tidak di-upload ke GitHub** (terlindungi oleh `.gitignore`).
- File dokumen yang diunggah otomatis divalidasi MIME Type-nya di sisi server, memblokir percobaan ekstensi berbahaya (`.exe`, `.php` *shell script*, dll).
- Akses dan perusakan URL ke Dashboard / API telah diproteksi penuh oleh filter *Middleware* Admin.
