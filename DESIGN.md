# 🎨 UI/UX Design System & Layout Architecture

Panduan sistem desain, palet warna, tata letak, dan arsitektur komponen antarmuka yang diimplementasikan pada aplikasi Pengingat Obat — berdasarkan **"Vitality Core" Design System** dari Stitch.

> Design system ini mengadopsi tema **Corporate / Modern** dengan tonal layering, pendekatan yang bersih, lapang, dan ramah pasien. Warna hijau melambangkan pertumbuhan & vitalitas, dipasangkan dengan biru untuk aksi fungsional.

---

## 🎨 1. Sistem Warna

### 1.1 Brand Colors

| Peran | Nama | Kode |
|-------|------|------|
| **Primary** | Health Green | `#4caf50` |
| **On Primary** | White | `#ffffff` |
| **Primary Container** | Dark Green | `#006e1c` |
| **On Primary Container** | Near Black | `#003c0b` |
| **Secondary** | Trust Blue | `#0288d1` |
| **On Secondary** | White | `#ffffff` |
| **Secondary Container** | Dark Blue | `#00639a` |
| **On Secondary Container** | Near Black | `#00436a` |
| **Tertiary** | Soft Mint | `#e8f5e9` |
| **Tertiary Container** | Muted Sage | `#929e94` |
| **Error** | Red | `#ba1a1a` |
| **Error Container** | Light Red | `#ffdad6` |

### 1.2 Surface & Neutral Colors

| Peran | Light | Dark |
|-------|-------|------|
| **Surface** | `#f4faff` | — |
| **Surface Dim** | `#cfdce4` | — |
| **Surface Bright** | `#f4faff` | — |
| **Surface Container Low** | `#e9f6fd` | — |
| **Surface Container** | `#e3f0f8` | — |
| **Surface Container High** | `#ddeaf2` | — |
| **Surface Container Highest** | `#d7e4ec` | — |
| **On Surface** | `#111d23` | — |
| **On Surface Variant** | `#3f4a3c` | — |
| **Outline** | `#6f7a6b` | — |
| **Outline Variant** | `#becab9` | — |
| **Inverse Surface** | `#263238` | — |
| **Inverse On Surface** | `#e6f3fb` | — |
| **Inverse Primary** | `#78dc77` | — |

### 1.3 CSS Custom Properties

```css
:root {
  /* Primary */
  --color-primary: #4caf50;
  --color-on-primary: #ffffff;
  --color-primary-container: #006e1c;
  --color-on-primary-container: #003c0b;
  --color-primary-fixed: #94f990;
  --color-primary-fixed-dim: #78dc77;

  /* Secondary */
  --color-secondary: #0288d1;
  --color-on-secondary: #ffffff;
  --color-secondary-container: #00639a;
  --color-on-secondary-container: #00436a;

  /* Tertiary */
  --color-tertiary: #e8f5e9;
  --color-tertiary-container: #929e94;

  /* Error */
  --color-error: #ba1a1a;
  --color-error-container: #ffdad6;

  /* Surface */
  --color-surface: #f4faff;
  --color-surface-dim: #cfdce4;
  --color-surface-bright: #f4faff;
  --color-surface-container-lowest: #ffffff;
  --color-surface-container-low: #e9f6fd;
  --color-surface-container: #e3f0f8;
  --color-surface-container-high: #ddeaf2;
  --color-surface-container-highest: #d7e4ec;

  /* On Surface */
  --color-on-surface: #111d23;
  --color-on-surface-variant: #3f4a3c;

  /* Outline */
  --color-outline: #6f7a6b;
  --color-outline-variant: #becab9;

  /* Inverse */
  --color-inverse-surface: #263238;
  --color-inverse-on-surface: #e6f3fb;
  --color-inverse-primary: #78dc77;

  /* Background */
  --color-background: #f4faff;
  --color-on-background: #111d23;
  --color-surface-variant: #d7e4ec;
}
```

---

## 🔤 2. Tipografi

Font utama: **Manrope** — geometric sans-serif yang modern, legible, dan profesional.

| Level | Ukuran | Weight | Line Height | Letter Spacing |
|-------|--------|--------|-------------|----------------|
| **Headline Display** | 48px | 800 (ExtraBold) | 1.2 | -0.02em |
| **Headline LG** | 32px | 700 (Bold) | 1.3 | normal |
| **Headline LG (Mobile)** | 28px | 700 (Bold) | 1.3 | normal |
| **Headline MD** | 24px | 600 (SemiBold) | 1.4 | normal |
| **Body LG** | 18px | 400 (Regular) | 1.6 | normal |
| **Body MD** | 16px | 400 (Regular) | 1.6 | normal |
| **Label MD** | 14px | 600 (SemiBold) | 1.2 | 0.01em |
| **Label SM** | 12px | 500 (Medium) | 1.2 | normal |

```css
:root {
  --font-family: 'Manrope', sans-serif;

  --fs-display: 48px;
  --fs-headline-lg: 32px;
  --fs-headline-lg-mobile: 28px;
  --fs-headline-md: 24px;
  --fs-body-lg: 18px;
  --fs-body: 16px;
  --fs-label-md: 14px;
  --fs-label-sm: 12px;

  --fw-extrabold: 800;
  --fw-bold: 700;
  --fw-semibold: 600;
  --fw-medium: 500;
  --fw-regular: 400;

  --lh-tight: 1.2;
  --lh-normal: 1.4;
  --sh-relaxed: 1.6;
}
```

---

## 🧱 3. Sistem Elevasi (Shadow)

Menggunakan **tonal layering** dan **soft ambient occlusion** — hindari bayangan berat.

| Level | Penggunaan | Shadow |
|-------|-----------|--------|
| **Level 0 (Base)** | Background halaman | Tidak ada |
| **Level 1 (Subtle)** | Container besar | White + `--color-surface-container-low` |
| **Level 2 (Card)** | Card, stat box | `0px 4px 20px rgba(0, 0, 0, 0.04)` |
| **Level 3 (Floating)** | Modal, dropdown | `0px 10px 30px rgba(0, 0, 0, 0.08)` |

```css
:root {
  --shadow-xs: 0 1px 2px rgba(16, 24, 40, 0.05);
  --shadow-sm: 0 2px 8px rgba(16, 24, 40, 0.08);
  --shadow-md: 0 6px 20px rgba(16, 24, 40, 0.10);
  --shadow-lg: 0 12px 32px rgba(16, 24, 40, 0.14);
  --shadow-focus: 0 0 0 3px rgba(76, 175, 80, 0.25);
}

[data-theme="dark"] {
  --shadow-xs: 0 1px 2px rgba(0, 0, 0, 0.4);
  --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.45);
  --shadow-md: 0 6px 20px rgba(0, 0, 0, 0.55);
  --shadow-lg: 0 12px 32px rgba(0, 0, 0, 0.65);
}
```

**Aturan pakai:**
- Card statis → gunakan **Level 2** (shadow sm) atau surface container
- Card interaktif → shadow sm default, naik ke shadow md saat hover
- Modal/dropdown/popover → **Level 3** (shadow lg)
- Input saat fokus → `--shadow-focus`, bukan cuma border

---

## 🟦 4. Radius Scale

| Token | Value | Penggunaan |
|-------|-------|-----------|
| `--radius-sm` | 4px (0.25rem) | Badge, input kecil, tag status |
| `--radius-md` | 8px (0.5rem) | **Default** — button, input |
| `--radius-lg` | 12px (0.75rem) | Card, container sedang |
| `--radius-xl` | 16px (1rem) / 24px (1.5rem) | Hero banner, modal besar |
| `--radius-full` | 9999px | Pill badge, chip |

```css
:root {
  --radius-sm: 4px;
  --radius-md: 8px;
  --radius-lg: 12px;
  --radius-xl: 24px;
  --radius-full: 9999px;
}
```

---

## 📏 5. Spacing Scale

```css
:root {
  --space-xs: 4px;
  --space-sm: 8px;
  --space-md: 24px;
  --space-lg: 48px;
  --space-xl: 80px;
  --container-max: 1200px;
  --gutter: 24px;
}
```

- Padding dalam card: minimal `--space-md` (24px)
- Jarak antar card dalam grid: `--space-md`
- Jarak antar section halaman: `--space-lg` (48px) atau `--space-xl` (80px)

---

## 🌈 6. Gradient & Aksen

```css
:root {
  --gradient-primary: linear-gradient(135deg, #4caf50 0%, #66bb6a 100%);
  --gradient-primary-hover: linear-gradient(135deg, #43a047 0%, #4caf50 100%);
  --gradient-secondary: linear-gradient(135deg, #0288d1 0%, #039be5 100%);
}
```

**Hero banner** menggunakan gradient primary + soft shadow + decorative blur circle.

**Button utama** menggunakan gradient + shadow + hover lift.

---

## 🧭 7. Sidebar & Navigasi

```css
.sidebar {
  background-color: var(--color-surface-container);
  box-shadow: var(--shadow-xs);
}

.sidebar .nav-link {
  border-radius: var(--radius-md);
  padding: 10px 12px;
  color: var(--color-on-surface-variant);
  transition: background-color 0.15s ease, color 0.15s ease;
}

.sidebar .nav-link:hover {
  background-color: var(--color-surface-container-high);
}

.sidebar .nav-link.active {
  background: var(--color-primary);
  color: var(--color-on-primary);
  box-shadow: var(--shadow-sm);
}
```

---

## 🏷️ 8. Badge & Status Pill

```css
.badge-status {
  border-radius: var(--radius-full);
  padding: 4px 12px;
  font-size: var(--fs-label-sm);
  font-weight: var(--fw-semibold);
  display: inline-flex;
  align-items: center;
  gap: 4px;
}

.badge-success { background: rgba(76, 175, 80, 0.12); color: #1e7e34; }
.badge-danger  { background: rgba(186, 26, 26, 0.12); color: #b02a37; }
.badge-warning { background: rgba(255, 193, 7, 0.15); color: #8a6d00; }
.badge-muted   { background: var(--color-surface-container-highest); color: var(--color-on-surface-variant); }
```

---

## 📊 9. Stat Cards (Dashboard)

```css
.stat-card {
  background-color: var(--color-surface-container-lowest);
  border-radius: var(--radius-lg);
  box-shadow: var(--shadow-sm);
  padding: var(--space-md);
  border-left: 4px solid var(--color-primary);
  transition: box-shadow 0.2s ease;
}

.stat-card:hover { box-shadow: var(--shadow-md); }

.stat-card.success { border-left-color: var(--color-primary); }
.stat-card.danger  { border-left-color: var(--color-error); }
.stat-card.info    { border-left-color: var(--color-secondary); }
```

---

## 🌙 10. Dark Mode

```css
:root {
  --bg-body: var(--color-surface);
  --bg-card: var(--color-surface-container-lowest);
  --bg-elevated: var(--color-surface-container);

  --text-primary: var(--color-on-surface);
  --text-secondary: var(--color-on-surface-variant);
  --text-muted: var(--color-outline);

  --border-color: var(--color-outline-variant);
}

[data-theme="dark"] {
  --bg-body: #0d0d1a;
  --bg-card: #1a1a30;
  --bg-elevated: #141428;
  --text-primary: #f8f9fa;
  --text-secondary: #e0e0e8;
  --text-muted: #a8a8b8;
  --border-color: #343459;
}
```

---

## 📐 11. Layout Container

```css
.page-shell {
  width: 100%;
  margin: 0;
  padding: 0;
}

.hero-banner {
  width: 100%;
  background: var(--gradient-primary);
  border-radius: 0 0 var(--radius-xl) var(--radius-xl);
  padding: var(--space-lg) var(--gutter);
  box-shadow: var(--shadow-md);
  position: relative;
  overflow: hidden;
}

.content-container {
  max-width: var(--container-max);
  margin: 0 auto;
  padding: var(--space-lg) var(--gutter);
}
```

---

## ✅ Checklist Implementasi

- [ ] Semua warna konsisten dengan palet Vitality Core (hijau + biru)
- [ ] Font Manrope sudah di-load di halaman
- [ ] Semua card punya `box-shadow` (minimal `--shadow-sm`)
- [ ] Hero banner pakai gradient, bukan solid color
- [ ] Button primary pakai hover lift effect
- [ ] Radius konsisten sesuai skala
- [ ] Padding card minimal 24px
- [ ] Menu aktif sidebar punya background primary
- [ ] Stat card punya aksen border-left
- [ ] Badge status punya background transparan
- [ ] Dark mode menggunakan CSS custom properties
