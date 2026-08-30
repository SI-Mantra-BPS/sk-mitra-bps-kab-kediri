# 📊 SI-Mantra

### Sistem Informasi Manajemen dan Monitoring Honor Mitra BPS Kabupaten Kediri

**SI-Mantra** adalah aplikasi berbasis web yang dikembangkan untuk mendukung administrasi dan monitoring mitra BPS Kabupaten Kediri secara terintegrasi. Sistem ini mencakup pengelolaan data mitra, kegiatan survei, import data Excel, monitoring honor, pengelolaan dokumen, serta pengaturan sistem yang mendukung kebutuhan operasional internal.

Saat ini, proyek ini sudah digunakan dalam bentuk aplikasi yang lebih matang dan menyesuaikan dengan tampilan dashboard serta alur kerja yang saat ini berjalan di lapangan.

---

## 📌 Ringkasan Proyek

SI-Mantra dirancang untuk membantu BPS Kabupaten Kediri dalam mengelola:

- 👥 Data pegawai, PML, dan PCL
- 📋 Data kegiatan / survei
- 📝 Data survei per bulan, kegiatan, dan honor
- 💰 Monitoring akumulasi honor mitra
- 📈 Dashboard ringkasan dan visualisasi honor
- 📥 Import data survei dari file Excel
- 📄 Dokumen Surat Tugas dan Surat Perjanjian Kerja
- ⚙️ Pengaturan batas honor dan notifikasi

---

## ✨ Fitur Utama yang Ada Saat Ini

### 📊 Dashboard

Halaman dashboard menampilkan informasi utama secara ringkas, seperti:

- 📋 Total kegiatan
- 👥 Total mitra
- 💰 Total honor
- ⚠️ Jumlah warning honor
- 📈 Grafik honor bulanan
- 📊 Status honor mitra per bulan
- 🚨 Daftar mitra yang melebihi batas honor

Dashboard ini dilengkapi dengan indikator visual yang memudahkan pengguna melihat kondisi honor secara cepat dan real-time.

### 🗂️ Master Data

Modul master data berisi referensi utama yang digunakan dalam proses operasional, antara lain:

- 👨‍💼 Daftar Pegawai BPS
- 👤 Daftar PML
- 👥 Daftar PCL
- 📋 Daftar Kegiatan / Survei
- 📥 Import Master Data

### 📝 Data Survei

Menu Data Survei menjadi salah satu fitur utama dalam sistem. Fitur ini mencakup:

- Tampilan tabel data survei per bulan
- Filter berdasarkan bulan, kegiatan, PML, dan PCL
- Pengelolaan honor per data survei
- Tombol tambah data survei
- Tombol import data untuk upload file Excel
- Aksi edit dan delete per record

Pada halaman Data Survei, user juga dapat melakukan pencarian dan filter data secara cepat sesuai kebutuhan.

### 📥 Import Data Survei

Fitur import data merupakan penambahan yang penting pada proyek ini. Prosesnya meliputi:

1. Download template Excel yang sudah disesuaikan format sistem
2. Upload file Excel ke halaman import
3. Validasi data sebelum diproses
4. Pengolahan data survei ke database
5. Pengecekan apakah data valid, duplikat, atau membutuhkan koreksi

Flow import yang saat ini tersedia:

`Download Template` ➔ `Upload File Excel` ➔ `Validasi Data` ➔ `Simpan ke Database`

Halaman import data ini dibuat untuk mempermudah proses input massal yang biasanya banyak data dan membutuhkan efisiensi.

### 💰 Monitoring Honor

Sistem dapat memantau akumulasi honor mitra dengan kriteria:

- ✅ Masih dalam batas honor
- 🚨 Melebihi batas honor

Selain itu, dashboard juga menampilkan rekap total honor serta status mitra secara bulanan.

### 📄 Dokumen

Fitur dokumen yang tersedia:

- 📜 Surat Tugas
- 📑 Surat Perjanjian Kerja (SPK)
- Cetak PDF per data maupun massal per kegiatan

### ⚙️ Pengaturan Sistem

Modul pengaturan mencakup:

- Batas maksimal honor
- Pengaturan notifikasi honor
- Pengaturan email dan preferensi sistem

---

## 🧭 Struktur Menu Aplikasi

```text
SI-Mantra
│
├── 📊 Dashboard
│
├── MASTER DATA
│   ├── 👨‍💼 Daftar Pegawai BPS
│   ├── 👤 Daftar PML
│   ├── 👥 Daftar PCL
│   ├── 📋 Daftar Kegiatan / Survei
│   └── 📥 Import Master Data
│
├── INPUT DATA
│   └── 📝 Data Survei
│
├── MONITORING
│   └── 💰 Monitoring Honor
│
├── DOKUMEN
│   ├── 📜 Surat Tugas
│   └── 📑 Surat Perjanjian Kerja
│
└── SISTEM
    ├── ⚙️ Pengaturan
    └── 🚪 Logout
```

---

## 🔄 Alur Proses Bisnis

```text
MASTER DATA
   ↓
DATA SURVEI
   ↓
MONITORING HONOR
   ↓
DASHBOARD
   ↓
DOKUMEN / SURAT / PDF
```

Alur kerja utama aplikasi adalah:

1. Mengelola data referensi seperti pegawai, PML, PCL, dan kegiatan
2. Menyimpan data survei per bulan dan per mitra
3. Menghitung honor total berdasarkan rate honor
4. Menampilkan dashboard monitoring serta status honor
5. Menyusun dokumen seperti Surat Tugas dan SPK bila diperlukan

---

## 📦 Teknologi yang Digunakan

| Teknologi | Keterangan |
|---|---|
| 🐘 PHP | Bahasa pemrograman utama |
| 🚀 Laravel 12 | Framework backend aplikasi |
| 🎨 Filament 3 | Panel admin dan UI aplikasi |
| 🌬️ Tailwind CSS | Styling dan tampilan antarmuka |
| 🧩 Blade | Template engine Laravel |
| 🗄️ MySQL | Database utama |
| 📊 Excel Import | Import data survei melalui file Excel |
| 📦 Composer | Manajemen dependency PHP |
| 🧪 Vite | Build frontend assets |
| 🌐 Hostinger | Hosting dan deployment aplikasi di lingkungan produksi |
| 🐙 Git & GitHub | Version control & collaboration |

---

## 🚀 Instalasi Lokal

1. Clone repository

```bash
git clone https://github.com/SI-Mantra-BPS/sk-mitra-bps-kab-kediri.git
cd sk-mitra-bps-kab-kediri
```

2. Install dependency PHP dan frontend

```bash
composer install
npm install
```

3. Konfigurasi environment

```bash
cp .env.example .env
php artisan key:generate
```

Sesuaikan file `.env` dengan konfigurasi database lokal Anda:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nama_database
DB_USERNAME=root
DB_PASSWORD=
```

4. Jalankan migrasi database

```bash
php artisan migrate --seed
```

5. Jalankan aplikasi

```bash
php artisan serve
```

Jika ingin menjalankan frontend dalam mode development:

```bash
npm run dev
```

---

## ☁️ Deployment

Proyek ini sudah dipersiapkan untuk deployment di lingkungan produksi, termasuk penggunaan pada hosting seperti **Hostinger**. Pada tahap deployment, biasanya kebutuhan utama adalah:

- konfigurasi `.env` production
- setting database production
- upload project ke hosting
- menjalankan composer install pada server
- menjalankan migrasi database
- memastikan storage dan bootstrap/cache writable
- pastikan konfigurasi URL aplikasi sesuai domain yang dipakai

Contoh konfigurasi deployment yang umum dipakai:

```bash
php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force
```

> Catatan: Untuk deployment di Hostinger, sesuaikan konfigurasi PHP version, domain, dan database service sesuai kebutuhan hosting.

---

## 🧾 Perubahan Terbaru pada Project

Beberapa perubahan utama yang sudah ada pada project ini:

- Penambahan fitur Import Data pada menu Data Survei
- Kemampuan mengunduh template Excel untuk input data survei
- Validasi data upload sebelum diproses ke database
- Dashboard monitoring honor yang lebih lengkap dan informatif
- Fitur dokumentasi (Surat Tugas dan SPK) yang terintegrasi
- Pengaturan batas honor dan notifikasi sistem

---

## 🌿 Workflow Git

```bash
git status
git add .
git commit -m "[UPDATE] Deskripsi perubahan"
git push origin <branch>
```

Branch utama dan branch pengembangan dapat disesuaikan dengan kebutuhan tim. Untuk project ini umumnya digunakan pola seperti:

- `main` = branch produksi
- `tria` / branch pengembangan = branch kerja aktif

---

## 🎯 Tujuan Pengembangan

- ✅ Mengurangi proses administrasi manual
- ✅ Memusatkan data survei dan honor dalam satu sistem
- ✅ Mempercepat monitoring dan evaluasi honor mitra
- ✅ Mempermudah import data massal via Excel
- ✅ Meningkatkan transparansi dan akurasi administrasi BPS Kabupaten Kediri

---

## 📌 Status Project

🚀 **Status:** Active Development / Production Ready

Project ini sedang dikembangkan dan digunakan untuk kebutuhan internal BPS Kabupaten Kediri, serta sudah dipersiapkan untuk deployment di hosting produksi seperti Hostinger.

---

## 📜 Lisensi

Penggunaan, distribusi, atau modifikasi di luar kebutuhan internal BPS Kabupaten Kediri perlu disesuaikan dengan ketentuan dan izin resmi dari pihak terkait.
