# 💊 Aplikasi Pengingat dan Pelacak Minum Obat (UAS Back-End)

Sistem informasi berbasis web menggunakan **PHP Native** untuk membantu pasien melacak rutinitas minum obat dan mengirimkan notifikasi otomatis secara terjadwal.

---

## 👥 Anggota Kelompok

* [Nama Kamu] - Project Manager & Database Lead
* [Nama Anggota 2] - Security & Auth Specialist
* [Nama Anggota 3] - Core Feature & File Upload
* [Nama Anggota 4] - Log Integration & Documentation

---

## 🛠️ Persiapan Lingkungan Jaringan (Setup Lokal)

Sebelum mulai koding, setiap anggota wajib mengikuti langkah sinkronisasi lingkungan kerja di bawah ini:

### 1. Kloning Repositori

Buka terminal/command prompt kamu, lalu jalankan perintah berikut:

```bash
git clone https://github.com/SuperiorSty/project_uas.git
cd project_uas
```

### 2. Setup Konfigurasi `.env`

1. Salin file `.env.example` yang ada di proyek dan ubah namanya menjadi `.env`.
2. Buka file `.env` tersebut menggunakan VS Code, lalu sesuaikan kredensial database (host, user, pass) dan akun SMTP Gmail uji coba milik masing-masing.

### 3. Import Database & Data Uji Coba

Seluruh skema tabel beserta data awal (*seeder*) sudah disediakan dalam file SQL.

1. Buka aplikasi database manager kamu (HeidiSQL / phpMyAdmin).
2. Import file sql "db_pengingat_obat" yang sudah diberikan

---

## 🔐 Akun Uji Coba (Untuk Pengujian Fitur Login)

Gunakan akun di bawah ini untuk menguji fungsionalitas Role-Based Access Control (RBAC):

| Peran (Role) | Username | Password |
| --- | --- | --- |
| **Admin Sistem** | `admin` | `password123` |
| **Pasien (User)** | `pasien_test` | `password123` |

---

## 📂 Struktur Proyek Modular

* `config/` : Konfigurasi database PDO dan mailer.
* `proses/` : Logika back-end murni (Login, Registrasi, CRUD Obat, Log).
* `views/` : Antarmuka/UI aplikasi web (Dashboard, Kelola Obat, Riwayat).
* `cron/` : Skrip otomatis pengirim email terjadwal.
* `uploads/` : Direktori penyimpanan berkas gambar resep dokter (di-ignore dari git).