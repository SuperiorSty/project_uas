# 👥 Agents Assignment & Project Roles

Dokumentasi pembagian tugas, tanggung jawab, dan batasan pengerjaan fitur untuk setiap anggota kelompok dalam pengembangan aplikasi Pengingat Obat.

---

### 🧑‍💼 Agent 1: Project Manager & Database Lead (Kamu)
* **Fokus Utama:** Manajemen Repositori, Skema Basis Data, Integrasi Sesi Umum, Dashboard Utama.
* **Tanggung Jawab Teknis:**
    * Merancang arsitektur database awal (5 tabel utama: `roles`, `users`, `master_obat`, `obat`, `riwayat_obat`).
    * Mengatur konfigurasi environment (`.env`) dan transisi koneksi *native database* ke PDO Static (Singleton) di `config/database.php`.
    * Menyusun struktur folder modular (`config/`, `proses/`, `views/`, `cron/`).
    * Mengembangkan kerangka halaman publik terbuka (`index.php`) — katalog dinamis dari database, integrasi error/success alert, dark mode toggle.
    * Redesain **Dashboard Pasien** (`views/dashboard.php`) — layout 3 baris: Pengingat Jadwal Hari Ini, Statistik Kepatuhan, Aktivitas Cepat & Obat Aktif.
    * Mengubah redirect login agar langsung menuju `views/dashboard.php` (bukan `index.php`).
    * **DATABASE SCHEMA SQL BELUM ADA** — perlu dibuat file `db_pengingat_obat.sql` (skema + seeder).
    * **Dashboard Riwayat Hash-Based Navigation** — Riwayat tidak ditampilkan langsung di beranda. Akses via hash `#riwayat` menampilkan halaman riwayat penuh di dalam dashboard (tetap dengan sidebar kiri). Navigasi menggunakan `hashchange` event.
    * **Dark Mode Toggle Fix** — Perbaikan agar seluruh area baris dark mode di dropdown bisa diklik (bukan hanya teks), dengan `pointer-events: none` pada checkbox input.

### 🔐 Agent 2: Security & Authentication Specialist (Anggota 2)
* **Fokus Utama:** Alur Otentikasi, Enkripsi Keamanan, dan Sesi Hak Akses (RBAC).
* **Tanggung Jawab Teknis:**
    * ✅ `proses/prosesLogin.php` — PDO Prepared Statements + `password_verify`, RBAC via JOIN `roles`, redirect ke dashboard.
    * ✅ `proses/prosesLogout.php` — `session_destroy()` + redirect.
    * ✅ `proses/prosesRegistrasi.php` — `password_hash(PASSWORD_DEFAULT)`, role default Pasien.
    * ✅ `views/dashboard.php` & `views/riwayat.php` & `views/editObat.php` & `views/detailObat.php` — session protection aktif.
    * ⚠️ `views/login.php` — redirect jika sudah login (aktif).
    * **❌ `views/kelolaObat.php`** — **tidak ada session protection** (perlu ditambah).
    * **❌ `views/tambahObat.php`** — **tidak ada session protection** (perlu ditambah).

### 🧪 Agent 3: Core Feature & Data Specialist (Anggota 3)
* **Fokus Utama:** Fitur Inti Pencarian Master Obat, Manajemen Obat Pasien, dan Manajemen Berkas Resep.
* **Tanggung Jawab Teknis:**
    * ✅ Pencarian dinamis katalog di `index.php` — query ke `master_obat` via keyword GET.
    * ✅ `views/tambahObat.php` — form input obat baru (masih bare, perlu session protection).
    * ✅ `views/kelolaObat.php` — CRUD daftar obat dengan search/filter/stok warning (masih bare, perlu session protection).
    * ✅ `proses/prosesObat.php` — create, edit, delete obat dengan RBAC (hanya ADMIN).
    * ✅ Looping PDO Prepared Statements di `kelolaObat.php`, `riwayat.php`.
    * **❌ File Upload** resep dokter ke `uploads/` — **belum diimplementasikan** (tidak ada input file di form maupun handler upload di `prosesObat.php`).
    * **❌ `views/editObat.php` & `views/detailObat.php`** — keduanya identik (detail view), form edit sebenarnya belum ada.

### 🛠️ Agent 4: System Integration & Automation Specialist (Anggota 4)
* **Fokus Utama:** Log Pelacakan Status, Integrasi Otomatisasi (Cron Job), dan Notifikasi Mailer.
* **Tanggung Jawab Teknis:**
    * ✅ `proses/prosesKonsumsi.php` — pencatatan konsumsi (Sudah Diminum), update stok, redirect ke riwayat.
    * ✅ `views/riwayat.php` — laporan riwayat kepatuhan minum obat dengan filter status per user.
    * **❌ `proses/prosesLog.php`** — **FILE KOSONG** (harus diisi logika aksi status).
    * **❌ `config/mail.php`** — **FILE KOSONG** (harus diisi integrasi PHPMailer + SMTP Gmail).
    * **❌ `cron/kirimPengingat.php`** — **FILE KOSONG** (harus diisi skrip cron notifikasi dengan flag `is_notified`).
