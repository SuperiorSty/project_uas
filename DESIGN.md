# 🎨 UI/UX Design System & Layout Architecture

Panduan sistem desain, palet warna, tata letak, dan arsitektur komponen antarmuka yang diimplementasikan pada aplikasi Pengingat Obat.

---

## 🎨 1. Sistem Warna & Tipografi

Desain antarmuka aplikasi ini mengadopsi identitas visual ekosistem teknologi medis modern dengan skema warna yang bersih, menenangkan, dan memiliki rasio kontras tinggi untuk aksesibilitas:

| Peran | Nama | Kode |
|---|---|---|
| **Warna Utama** | Primary Blue | `#3688C9` |
| **Warna Kedua** | Secondary Teal | `#36B6C9` |
| **Warna Ketiga** | Deep Blue (Hover) | `#3659C9` |
| **Sukses** | Green | `#28a745` |
| **Gagal** | Red | `#dc3545` |
| **Peringatan** | Yellow | `#ffc107` |

* **Tipografi:** Menggunakan rumpun font *Sans-serif* (`Segoe UI`, `Tahoma`, `Helvetica`) untuk menjamin tingkat keterbacaan teks resep medis yang tinggi pada resolusi layar apa pun.

---

## 🌙 2. Dark Mode System & Contrast Fix

Untuk memastikan **tidak ada teks yang tidak terbaca (low contrast)** saat Dark Mode aktif, arsitektur CSS Variables dirombak secara total. Pastikan hindari penggunaan class utilitas Bootstrap seperti `text-dark` atau `bg-white` pada elemen HTML, dan beralihlah ke variabel di bawah ini:

```css
/* Aturan Global & Reset Kontras */
body {
  background-color: var(--bg-body);
  color: var(--text-primary);
  transition: background-color 0.3s ease, color 0.3s ease;
}

:root {
  --bg-body: #f0f2f5;
  --bg-card: #ffffff;
  --bg-navbar: #ffffff;
  
  /* Text Colors (Light Mode) */
  --text-primary: #1a1a2e;
  --text-secondary: #495057;
  --text-muted: #6c757d;
  
  /* Form & Components */
  --bg-input: #ffffff;
  --text-input: #1a1a2e;
  --border-color: #e8e8ef;
  
  /* Tables & Lists */
  --table-striped: rgba(0, 0, 0, 0.02);
  --table-hover: rgba(0, 0, 0, 0.04);
  
  --shadow-sm: 0 2px 8px rgba(0,0,0,0.06);
  --shadow-md: 0 4px 16px rgba(0,0,0,0.08);
  --accent: #3688C9;
}

[data-theme="dark"] {
  --bg-body: #0d0d1a;
  --bg-card: #1a1a30;
  --bg-navbar: #141428;
  
  /* Text Colors (Dark Mode Fix - Menjamin Keterbacaan) */
  --text-primary: #f8f9fa;   /* Putih terang untuk teks utama */
  --text-secondary: #e0e0e8; /* Abu-abu terang untuk sub-header */
  --text-muted: #a8a8b8;     /* Kontras tinggi untuk teks kecil */
  
  /* Form & Components Fix */
  --bg-input: #242442;
  --text-input: #ffffff;
  --border-color: #343459;
  
  /* Tables & Lists Fix */
  --table-striped: rgba(255, 255, 255, 0.03);
  --table-hover: rgba(255, 255, 255, 0.05);
  
  --shadow-sm: 0 2px 8px rgba(0,0,0,0.4);
  --shadow-md: 0 4px 16px rgba(0,0,0,0.6);
}

/* Base Component Binding (Terapkan ke elemen global) */
.card { background-color: var(--bg-card); border-color: var(--border-color); color: var(--text-primary); }
.navbar { background-color: var(--bg-navbar); border-bottom: 1px solid var(--border-color); }
.text-muted-custom { color: var(--text-muted) !important; }
.form-control, .form-select { background-color: var(--bg-input); color: var(--text-input); border-color: var(--border-color); }
.form-control:focus, .form-select:focus { background-color: var(--bg-input); color: var(--text-input); }
.table { color: var(--text-primary); border-color: var(--border-color); }