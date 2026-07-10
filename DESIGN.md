# 🎨 UI/UX Design System & Layout Architecture

Panduan sistem desain, palet warna, tata letak, dan arsitektur komponen antarmuka yang diimplementasikan pada aplikasi Pengingat Obat.

> **Catatan revisi:** Palet warna dasar dipertahankan. Revisi ini menambahkan sistem **elevasi (shadow), gradient, radius, spacing, dan state komponen** yang sebelumnya belum diatur — ini penyebab utama tampilan terasa "flat".

---

## 🎨 1. Sistem Warna & Tipografi

| Peran | Nama | Kode |
|---|---|---|
| **Warna Utama** | Primary Blue | `#3688C9` |
| **Warna Kedua** | Secondary Teal | `#36B6C9` |
| **Warna Ketiga** | Deep Blue (Hover) | `#3659C9` |
| **Sukses** | Green | `#28a745` |
| **Gagal** | Red | `#dc3545` |
| **Peringatan** | Yellow | `#ffc107` |

* **Tipografi:** Rumpun *Sans-serif* (`Segoe UI`, `Tahoma`, `Helvetica`).
* **Skala ukuran teks** (tambahan — biar hierarki kebaca, bukan semua ukuran mirip seperti sekarang):

```css
--fs-h1: 1.75rem;   /* judul halaman, mis. "Riwayat Konsumsi" */
--fs-h2: 1.25rem;   /* judul card/section */
--fs-body: 0.95rem; /* teks umum */
--fs-small: 0.8rem; /* label, muted text */

--fw-bold: 700;
--fw-semibold: 600;
--fw-regular: 400;
```

Gunakan `--fs-h1` + `--fw-bold` untuk judul halaman, dan pastikan subteks di bawahnya pakai `--text-muted` + `--fs-small` supaya ada kontras level, bukan sama-sama abu-abu medium seperti sekarang.

---

## 🧱 2. Sistem Elevasi (Shadow) — Kunci Utama Anti-Flat

Ini yang paling kurang di tampilan sekarang: card, stat box, dan tabel warnanya beda tipis dari background tapi **tidak ada shadow yang kelihatan**, jadi semuanya kelihatan menempel di satu bidang datar.

```css
:root {
  --shadow-xs: 0 1px 2px rgba(16, 24, 40, 0.05);
  --shadow-sm: 0 2px 8px rgba(16, 24, 40, 0.08);
  --shadow-md: 0 6px 20px rgba(16, 24, 40, 0.10);
  --shadow-lg: 0 12px 32px rgba(16, 24, 40, 0.14);
  --shadow-focus: 0 0 0 3px rgba(54, 136, 201, 0.25);
}

[data-theme="dark"] {
  --shadow-xs: 0 1px 2px rgba(0,0,0,0.4);
  --shadow-sm: 0 2px 8px rgba(0,0,0,0.45);
  --shadow-md: 0 6px 20px rgba(0,0,0,0.55);
  --shadow-lg: 0 12px 32px rgba(0,0,0,0.65);
}
```

**Aturan pakai:**
- Card statis (info, katalog item) → `--shadow-sm`
- Card interaktif (hover-able, clickable) → `--shadow-sm` default, naik ke `--shadow-md` saat hover
- Modal / dropdown / popover → `--shadow-lg`
- Input saat fokus → `--shadow-focus`, bukan cuma border biru

```css
.card {
  box-shadow: var(--shadow-sm);
  transition: box-shadow 0.2s ease, transform 0.2s ease;
}
.card--interactive:hover {
  box-shadow: var(--shadow-md);
  transform: translateY(-2px);
}
```

Contoh nyata di kasusmu: card di **"Katalog Master Obat"** dan 4 stat box di dashboard (`67%`, `2 Diminum`, `1 Terlewat`, `3 Total`) — semua itu harus pakai `--shadow-sm` minimal, supaya "mengambang" dikit dari background, bukan nempel rata.

---

## 🟦 3. Radius Scale (Konsistensi Sudut)

Sekarang radius kelihatan campur — sebagian tegas, sebagian rounded. Samakan pakai skala ini:

```css
--radius-sm: 8px;   /* badge, input kecil, tag status */
--radius-md: 12px;  /* card, button */
--radius-lg: 20px;  /* hero banner, modal besar */
--radius-full: 999px; /* pill badge: "Sudah Diminum", "Ditunda" */
```

Jangan campur `border-radius: 4px` dan `border-radius: 12px` di komponen yang levelnya sama (misalnya semua card harus `--radius-md`).

---

## 📏 4. Layout Container System (Full-Bleed Hero + Bounded Content)

Sekarang hero banner dan section katalog sama-sama dibatasi di dalam satu container lebar yang sama — makanya berasa "kotak di tengah kotak", bukan hierarki.

**Pola yang benar:** hero **full-bleed** (nempel tembok kiri-kanan-atas, tanpa margin/padding luar), sedangkan konten di bawahnya (katalog, dashboard, dll) tetap dibatasi max-width supaya nyaman dibaca di layar lebar.

```css
:root {
  --content-max-width: 1200px;
  --content-padding-x: 24px; /* padding kiri-kanan saat di layar sempit */
}

/* Wrapper utama halaman: HAPUS padding/margin di sini untuk hero */
.page-shell {
  width: 100%;
  margin: 0;
  padding: 0;
}

/* Hero: full-bleed, nempel ke tepi viewport */
.hero-banner {
  width: 100%;
  border-radius: 0; /* full-bleed = tanpa radius di sisi luar, atau radius hanya di bawah jika mau ada lekukan */
  padding: var(--space-6) var(--content-padding-x);
  /* tinggi bisa fixed (mis. 280px-360px) atau proporsional, JANGAN 100vh -
     karena bukan "1 layar penuh", cukup band lebar penuh dengan tinggi wajar */
}

/* Semua section SETELAH hero: dibatasi & di-center */
.content-container {
  max-width: var(--content-max-width);
  margin: 0 auto;
  padding: var(--space-5) var(--content-padding-x);
}
```

Jadi strukturnya:

```html
<div class="page-shell">
  <section class="hero-banner">...full-bleed...</section>

  <div class="content-container">
    <section class="jadwal-hari-ini">...</section>
    <section class="obat-aktif">...</section>
  </div>
</div>
```

Kalau mau hero-nya juga ada sedikit "lekukan" transisi ke konten di bawah (biar gak potong tegas), tambahkan radius hanya di dua sudut bawah:

```css
.hero-banner {
  border-radius: 0 0 var(--radius-lg) var(--radius-lg);
}
```

---

## 🌈 5. Gradient & Aksen (Biar Hero & Button Nggak Polos)

Hero banner ("Tebus Resep Praktis, Sembuh Lebih Cepat") saat ini solid `#3688C9` polos — tambahkan gradient tipis + elevasi biar ada kedalaman:

```css
--gradient-primary: linear-gradient(135deg, #3688C9 0%, #36B6C9 100%);
--gradient-primary-hover: linear-gradient(135deg, #3659C9 0%, #3688C9 100%);
```

```css
.hero-banner {
  background: var(--gradient-primary);
  border-radius: var(--radius-lg);
  box-shadow: var(--shadow-md);
  position: relative;
  overflow: hidden;
}
/* opsional: tambahkan bentuk dekoratif blur di pojok banner
   biar nggak polos kotak-warna doang */
.hero-banner::after {
  content: "";
  position: absolute;
  width: 200px; height: 200px;
  background: rgba(255,255,255,0.08);
  border-radius: 50%;
  top: -60px; right: -60px;
}
```

**Button utama** juga pakai gradient + shadow + hover lift, bukan flat solid:

```css
.btn-primary {
  background: var(--gradient-primary);
  border: none;
  border-radius: var(--radius-md);
  box-shadow: var(--shadow-sm);
  transition: transform 0.15s ease, box-shadow 0.15s ease, filter 0.15s ease;
}
.btn-primary:hover {
  filter: brightness(1.05);
  box-shadow: var(--shadow-md);
  transform: translateY(-1px);
}
.btn-primary:active {
  transform: translateY(0);
  filter: brightness(0.95);
}
```

---

## 📐 6. Spacing Scale

```css
--space-1: 4px;
--space-2: 8px;
--space-3: 16px;
--space-4: 24px;
--space-5: 32px;
--space-6: 48px;
```

- Padding dalam card: minimal `--space-4` (24px), jangan `--space-2`. Dari screenshot, card katalog & stat box paddingnya kelihatan agak sempit — bikin longgar sedikit biar "bernapas".
- Jarak antar card dalam grid: `--space-4`.
- Jarak antar section halaman (mis. hero banner ke katalog): `--space-5` atau `--space-6`.

---

## 🧭 7. Sidebar & Navigasi (Menghilangkan Kesan Flat di Menu)

Menu aktif sekarang cuma ganti background solid biru rata. Tambahkan indicator + subtle depth:

```css
.sidebar { background-color: var(--bg-card); box-shadow: var(--shadow-xs); }

.sidebar .nav-link {
  border-radius: var(--radius-sm);
  padding: 10px 12px;
  color: var(--text-secondary);
  transition: background-color 0.15s ease, color 0.15s ease;
}
.sidebar .nav-link:hover {
  background-color: var(--table-hover);
}
.sidebar .nav-link.active {
  background: var(--gradient-primary);
  color: #ffffff;
  box-shadow: var(--shadow-sm);
}
```

Untuk bagian **"OBAT AKTIF"** (list Amoxicillin 250mg di sidebar), kasih card kecil dengan `--radius-sm` + background sedikit beda dari sidebar, bukan teks polos menggantung.

---

## 🏷️ 8. Badge & Status Pill

Status seperti `Sudah Diminum`, `Ditunda` di tabel Riwayat Konsumsi sudah cukup bagus konsepnya (pill), tambahkan icon kecil + sedikit shadow biar nggak flat:

```css
.badge-status {
  border-radius: var(--radius-full);
  padding: 4px 12px;
  font-size: var(--fs-small);
  font-weight: var(--fw-semibold);
  display: inline-flex;
  align-items: center;
  gap: 4px;
}
.badge-success { background: rgba(40,167,69,0.12); color: #1e7e34; }
.badge-danger  { background: rgba(220,53,69,0.12); color: #b02a37; }
.badge-warning { background: rgba(255,193,7,0.15); color: #8a6d00; }
.badge-muted   { background: var(--table-hover); color: var(--text-muted); }
```

Pakai warna teks yang lebih gelap dari warna dasarnya (bukan warna solid terang di atas background terang) supaya kontrasnya cukup — ini juga membantu di light mode.

---

## 📊 9. Stat Cards (Dashboard)

4 kotak statistik di beranda (`67% Kepatuhan`, `2 Diminum`, `1 Terlewat`, `3 Total`) saat ini flat & seragam semua. Bedakan lewat aksen warna tipis di kiri atau icon background, plus shadow:

```css
.stat-card {
  background-color: var(--bg-card);
  border-radius: var(--radius-md);
  box-shadow: var(--shadow-sm);
  padding: var(--space-4);
  border-left: 4px solid var(--accent);
  transition: box-shadow 0.2s ease;
}
.stat-card:hover { box-shadow: var(--shadow-md); }

.stat-card.success { border-left-color: #28a745; }
.stat-card.danger  { border-left-color: #dc3545; }
.stat-card.info    { border-left-color: var(--secondary-teal, #36B6C9); }
```

---

## 🌙 10. Dark Mode System & Contrast Fix

```css
body {
  background-color: var(--bg-body);
  color: var(--text-primary);
  transition: background-color 0.3s ease, color 0.3s ease;
}

:root {
  --bg-body: #f0f2f5;
  --bg-card: #ffffff;
  --bg-navbar: #ffffff;

  --text-primary: #1a1a2e;
  --text-secondary: #495057;
  --text-muted: #6c757d;

  --bg-input: #ffffff;
  --text-input: #1a1a2e;
  --border-color: #e8e8ef;

  --table-striped: rgba(0, 0, 0, 0.02);
  --table-hover: rgba(0, 0, 0, 0.04);

  --accent: #3688C9;
}

[data-theme="dark"] {
  --bg-body: #0d0d1a;
  --bg-card: #1a1a30;
  --bg-navbar: #141428;

  --text-primary: #f8f9fa;
  --text-secondary: #e0e0e8;
  --text-muted: #a8a8b8;

  --bg-input: #242442;
  --text-input: #ffffff;
  --border-color: #343459;

  --table-striped: rgba(255, 255, 255, 0.03);
  --table-hover: rgba(255, 255, 255, 0.05);
}

/* Base Component Binding */
.card { background-color: var(--bg-card); border-color: var(--border-color); color: var(--text-primary); border-radius: var(--radius-md); box-shadow: var(--shadow-sm); }
.navbar { background-color: var(--bg-navbar); border-bottom: 1px solid var(--border-color); box-shadow: var(--shadow-xs); }
.text-muted-custom { color: var(--text-muted) !important; }
.form-control, .form-select {
  background-color: var(--bg-input);
  color: var(--text-input);
  border-color: var(--border-color);
  border-radius: var(--radius-sm);
  transition: box-shadow 0.15s ease, border-color 0.15s ease;
}
.form-control:focus, .form-select:focus {
  background-color: var(--bg-input);
  color: var(--text-input);
  border-color: var(--accent);
  box-shadow: var(--shadow-focus);
}
```

---

## 🏠 12. Information Architecture: Homepage Difokuskan ke Pengingat

**Masalah saat ini:** section utama di beranda (setelah hero) adalah **"Katalog Master Obat"**. Padahal value proposition utama produk ini adalah *pengingat jadwal minum obat*, bukan katalog. Katalog itu tools pencarian/referensi, bukan konten yang harus dilihat pertama kali tiap buka web.

**Perbaikan struktur beranda:**

1. **Hero (full-bleed)** — tetap sebagai pengenalan/CTA singkat.
2. **🔔 Pengingat Hari Ini** (prioritas #1, langsung di bawah hero) — jadwal obat yang harus diminum hari ini, dengan status (belum/sudah/terlewat) dan tombol aksi cepat "Tandai Sudah Diminum". Ini yang harus paling menonjol secara visual (card lebih besar, shadow lebih kuat, atau warna aksen berbeda).
3. **📊 Ringkasan Kepatuhan** — 4 stat card yang sudah ada (Kepatuhan %, Diminum, Terlewat, Total) diletakkan tepat di bawah/di samping pengingat, karena ini pendukung konteks untuk pengingat, bukan berdiri sendiri.
4. **💊 Obat Aktif** — daftar obat yang sedang dipantau user (ringkas, bukan full katalog).
5. **Katalog Master Obat** — pindahkan ke halaman terpisah, diakses lewat menu **"Cari Obat"** di sidebar (yang sudah ada). Di beranda, katalog cukup muncul sebagai *entry point* kecil, misalnya tombol "+ Tambah Obat dari Katalog", bukan grid penuh kartu obat.

**Kenapa ini penting secara UX:** user buka aplikasi pengingat obat itu biasanya buru-buru (mau tahu "obat apa yang harus diminum sekarang?"). Kalau yang pertama kelihatan adalah katalog produk obat, itu menambah friction — user harus scroll/mikir dulu buat nemuin info yang relevan buat mereka saat itu.

**Layout urutan revisi (folder halaman `Beranda`):**

```html
<div class="page-shell">
  <section class="hero-banner">...</section>

  <div class="content-container">
    <section class="pengingat-hari-ini">
      <!-- card besar, shadow-md, isi jadwal + tombol aksi -->
    </section>

    <section class="ringkasan-kepatuhan">
      <!-- 4 stat card -->
    </section>

    <section class="obat-aktif-ringkas">
      <!-- list singkat, link "Lihat semua" -->
    </section>
  </div>
</div>
```

Section **Katalog Master Obat** dipindah jadi isi utama halaman **`/cari-obat`**, bukan di `/beranda` lagi.

---

## ✅ 13. Checklist Cepat Anti-Flat

Sebelum ship, cek tiap komponen utama:

- [ ] Semua `.card` punya `box-shadow` (minimal `--shadow-sm`)
- [ ] Hero banner pakai `--gradient-primary`, bukan solid color
- [ ] Semua button primary pakai gradient + hover lift
- [ ] Radius konsisten sesuai skala (`sm`/`md`/`lg`/`full`), tidak campur
- [ ] Padding card minimal `--space-4`, tidak sempit
- [ ] Menu aktif di sidebar ada shadow/gradient, bukan solid rata
- [ ] Stat card dashboard dibedakan lewat aksen warna (border-left/icon)
- [ ] Badge status punya background transparan warna + teks kontras, bukan solid terang polos
- [ ] Semua transisi hover (`0.15s–0.2s ease`) ada, tidak instan/patah
- [ ] Hero banner full-bleed (nempel tepi kiri-kanan-atas), konten di bawahnya dibatasi `--content-max-width`
- [ ] Beranda dibuka dengan "Pengingat Hari Ini", bukan katalog obat
- [ ] Katalog Master Obat sudah dipindah ke halaman `/cari-obat`