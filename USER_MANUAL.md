# User Manual Sistem POS BUMDes

## Daftar Isi

1. [Pendahuluan](#1-pendahuluan)
2. [Memulai Sistem](#2-memulai-sistem)
3. [Dashboard](#3-dashboard)
4. [POS Kasir](#4-pos-kasir)
5. [Buka / Tutup Shift](#5-buka--tutup-shift)
6. [Riwayat Penjualan](#6-riwayat-penjualan)
7. [Laporan Harian](#7-laporan-harian)
8. [Manajemen Produk](#8-manajemen-produk)
9. [Master Data](#9-master-data)
10. [Manajemen User](#10-manajemen-user)
11. [Role & Permission](#11-role--permission)
12. [Pengaturan Sistem](#12-pengaturan-sistem)
13. [Profil Saya](#13-profil-saya)
14. [Role dan Hak Akses](#14-role-dan-hak-akses)
15. [FAQ dan Troubleshooting](#15-faq-dan-troubleshooting)

---

## 1. Pendahuluan

### 1.1 Tentang Sistem

Sistem POS (Point of Sale) BUMDes adalah aplikasi kasir digital yang dirancang khusus untuk Badan Usaha Milik Desa (BUMDes). Sistem ini dibangun menggunakan framework CodeIgniter 4 dengan fitur autentikasi dan manajemen hak akses (RBAC) menggunakan CodeIgniter Shield.

### 1.2 Fitur Utama

- **POS Kasir** — Transaksi penjualan dengan antarmuka kasir yang modern
- **Manajemen Shift** — Buka dan tutup shift kasir dengan rekonsiliasi kas
- **Manajemen Produk** — CRUD produk dengan perhitungan HPP metode Moving Weighted Average (MWA)
- **Master Data** — Pengelolaan kategori dan satuan barang
- **Laporan** — Laporan penjualan harian lengkap dengan analisis laba rugi
- **Manajemen User & Role** — Pengaturan pengguna dan hak akses berbasis RBAC
- **Pengaturan Sistem** — Konfigurasi umum, tampilan, email, dan nota

### 1.3 Syarat Sistem

- PHP versi 8.2 atau lebih tinggi
- Database MySQL/MariaDB
- Browser modern (Chrome, Firefox, Edge, Safari)
- Koneksi internet (untuk CDN assets)

---

## 2. Memulai Sistem

### 2.1 Akses Aplikasi

1. Buka browser dan ketik alamat aplikasi, misalnya: `http://localhost:8080`
2. Anda akan diarahkan ke halaman **Login**

### 2.2 Login

**Langkah-langkah:**

1. Pada halaman login, masukkan **email** yang telah terdaftar
2. Masukkan **password** Anda
3. Klik tombol **Login**
4. Jika berhasil, Anda akan diarahkan ke halaman **Dashboard**

**Akun Default (untuk pengembangan):**

| Role | Email | Password |
|------|-------|----------|
| Super Admin | superadmin@example.com | password123 |
| Admin | admin@example.com | password123 |
| Manager | manager@example.com | password123 |
| Kasir | cashier@example.com | password123 |
| User | user@example.com | password123 |

### 2.3 Logout

1. Klik **ikon profil** di pojok kanan atas navbar
2. Pilih **Logout** dari menu dropdown
3. Anda akan diarahkan kembali ke halaman login

### 2.4 Lupa Password

Jika sistem mendukung magic link login:

1. Pada halaman login, klik tautan **Lupa Password** atau **Magic Link**
2. Masukkan email terdaftar Anda
3. Sistem akan mengirimkan link login ke email Anda
4. Buka email dan klik link yang diterima untuk login

---

## 3. Dashboard

### 3.1 Mengakses Dashboard

Dashboard adalah halaman utama yang muncul setelah Anda berhasil login. Anda juga dapat mengaksesnya melalui menu **Dashboard** di sidebar.

### 3.2 Informasi di Dashboard

Dashboard menampilkan ringkasan informasi penting:

- **Statistik Penjualan** — Total transaksi, omzet, dan laba
- **Produk Terlaris** — Daftar produk dengan penjualan tertinggi
- **Stok Menipis** — Produk yang stoknya di bawah batas minimum
- **Aktivitas Terbaru** — Riwayat transaksi terakhir

### 3.3 Switch Active Group

Jika Anda memiliki lebih dari satu role, Anda dapat berpindah role aktif:

1. Klik **dropdown role** di navbar atas
2. Pilih role yang ingin diaktifkan
3. Sidebar dan hak akses akan menyesuaikan dengan role yang dipilih

---

## 4. POS Kasir

### 4.1 Mengakses POS Kasir

Klik menu **POS Kasir** di sidebar. Halaman ini hanya dapat diakses oleh user dengan permission `sales.create` (role: Kasir, Admin, Super Admin).

### 4.2 Tampilan Halaman POS

Halaman POS terbagi menjadi dua area utama:

**Area Kiri — Katalog Produk:**
- Grid kartu produk dengan gambar, nama, SKU, stok, dan harga
- Filter berdasarkan kategori (chip/button)
- Kolom pencarian produk (cari berdasarkan nama atau SKU)
- Tombol **Tambah ke Cart** pada setiap produk

**Area Kanan — Detail Transaksi (Cart):**
- Tabel item yang akan dibeli
- Input nama pelanggan (opsional)
- Input diskon nominal (Rp)
- Ringkasan: Subtotal, Diskon, Grand Total
- Tombol aksi: Reset Cart, Simpan Tertunda, Lanjut Bayar

### 4.3 Melakukan Transaksi Penjualan

#### Langkah 1: Tambah Item ke Cart

**Cara 1 — Klik Tombol:**
1. Cari produk yang diinginkan di katalog
2. Klik tombol **Tambah ke Cart** pada kartu produk
3. Item akan muncul di panel Detail Transaksi

**Cara 2 — Pencarian + Enter:**
1. Ketik nama atau SKU produk di kolom pencarian
2. Tekan **Enter** untuk menambahkan produk pertama ke cart

**Cara 3 — Scan Barcode:**
1. Klik tombol **kamera** (floating button) di pojok kanan bawah
2. Arahkan barcode produk ke kamera
3. Item akan otomatis masuk ke cart

#### Langkah 2: Kelola Item di Cart

- **Tambah Qty:** Klik tombol `+` pada baris item atau tekan `+` di keyboard
- **Kurangi Qty:** Klik tombol `-` pada baris item atau tekan `-` di keyboard
- **Hapus Item:** Klik tombol `x` pada baris item atau tekan `Del` di keyboard
- **Pindah Item Aktif:** Gunakan tombol `↑` dan `↓` di keyboard

#### Langkah 3: Isi Data Pelanggan dan Diskon

1. **Nama Pelanggan** (opsional): Ketik nama pelanggan di kolom yang tersedia
2. **Diskon** (opsional): Masukkan nominal diskon dalam Rupiah
   - Contoh: ketik `5000` untuk diskon Rp 5.000
   - Diskon akan mengurangi grand total

#### Langkah 4: Proses Pembayaran

1. Klik tombol **Lanjut Bayar** atau tekan **F9**
2. Modal pembayaran akan muncul

**Di Modal Pembayaran:**

a. **Pilih Metode Pembayaran:**
   - **Tunai (Cash)** — Pembayaran dengan uang cash
   - **Transfer** — Pembayaran via transfer bank/e-wallet

b. **Input Jumlah Bayar (untuk Tunai):**
   - Ketik nominal langsung di input
   - Gunakan **Quick Amount** untuk nominal cepat:
     - **Uang Pas** — sama dengan grand total
     - **Bulat 5rb** — dibulatkan ke atas ke kelipatan Rp 5.000
     - **Bulat 10rb** — dibulatkan ke atas ke kelipatan Rp 10.000
   - Gunakan **Keypad Virtual** jika tidak ada keyboard fisik

c. **Periksa Kembalian:**
   - Sistem otomatis menghitung kembalian
   - Kembalian ditampilkan di panel kanan bawah

3. **Simpan Transaksi:**
   - Klik **Simpan Transaksi** — transaksi disimpan tanpa cetak nota
   - Klik **Simpan & Cetak Nota** — transaksi disimpan dan halaman nota dibuka untuk dicetak

### 4.5 Simpan Transaksi Tertunda (Hold)

Fitur ini berguna ketika kasir ingin menunda transaksi sementara (misal: pelanggan lupa membawa uang).

**Langkah-langkah:**

1. Tambahkan item ke cart seperti biasa
2. Klik tombol **Simpan Tertunda**
3. Transaksi akan disimpan sebagai pending transaction
4. Untuk melanjutkan transaksi tertunda:
   - Klik tombol **ikon pause** di header cart (ada badge jumlah transaksi tertunda)
   - Pilih transaksi yang ingin dilanjutkan
   - Klik **Ambil** untuk memuat kembali transaksi ke cart
   - Klik **Hapus** untuk membatalkan transaksi tertunda

### 4.6 Cetak Nota

Setelah transaksi disimpan dengan opsi "Simpan & Cetak Nota":

1. Halaman nota akan terbuka di tab baru
2. Nota menampilkan:
   - Header (logo, nama toko, alamat)
   - Nomor invoice, tanggal, kasir
   - Daftar item, qty, harga, subtotal
   - Subtotal, diskon, grand total, bayar, kembalian
   - Footer (pesan terima kasih)
3. Tekan **Ctrl + P** atau klik tombol print di browser untuk mencetak
4. Pengaturan ukuran kertas dan font dapat dikonfigurasi di **Pengaturan > Nota**

### 4.7 Keyboard Shortcuts

| Shortcut | Fungsi |
|----------|--------|
| `Enter` | Tambah item dari pencarian ke cart |
| `Esc` | Fokus kembali ke kolom pencarian |
| `↑` / `↓` | Pindah item aktif di cart |
| `+` / `-` | Tambah/kurangi qty item aktif |
| `Del` | Hapus item aktif dari cart |
| `F2` | Fokus ke input diskon |
| `F3` | Fokus ke input jumlah bayar |
| `F9` | Simpan transaksi |
| `Ctrl + B` | Buka scanner barcode |

---

## 5. Buka / Tutup Shift

### 5.1 Mengakses Halaman Shift

Klik menu **Buka / Tutup Shift** di sidebar. Halaman ini hanya dapat diakses oleh user dengan permission `sales.create`.

### 5.2 Status Shift

Halaman shift menampilkan status dalam 3 kondisi:

1. **Siap Dibuka** — Belum ada shift aktif, siap untuk membuka shift baru
2. **Shift Aktif** — Shift sedang berjalan, dapat ditutup
3. **Menunggu Shift Lain** — Ada user lain yang masih memiliki shift aktif

### 5.3 Membuka Shift

**Langkah-langkah:**

1. Pastikan tidak ada shift aktif (status: **Siap Dibuka**)
2. Masukkan **Kas Awal** — nominal uang yang ada di laci kas saat memulai shift
   - Contoh: `100000` untuk Rp 100.000
3. Klik tombol **Buka Shift Sekarang**
4. Shift akan aktif dan Anda dapat mulai bertransaksi di POS

**Catatan:**
- Hanya satu shift yang dapat aktif per kasir
- Jika ada kasir lain yang masih memiliki shift aktif, Anda tidak dapat membuka shift baru

### 5.4 Menutup Shift

**Langkah-langkah:**

1. Pastikan shift Anda sedang aktif (status: **Shift Aktif**)
2. Hitung uang fisik yang ada di laci kas
3. Masukkan **Kas Fisik Akhir** — nominal uang fisik aktual
4. Masukkan **Catatan** (opsional) — misalnya penjelasan jika ada selisih kas
5. Klik tombol **Tutup Shift**
6. Sistem akan menghitung:
   - **Kas Sistem** — total kas yang seharusnya berdasarkan transaksi
   - **Selisih (Variance)** — perbedaan antara kas sistem dan kas fisik
   - Selisih positif = kelebihan kas
   - Selisih negatif = kekurangan kas

### 5.5 History Shift

Di bagian kanan halaman shift, terdapat tabel riwayat shift:

- **Filter:** Berdasarkan tanggal (dari - sampai) dan kasir (untuk manager/admin)
- **Kolom Tabel:**
  - Kasir — nama petugas kasir
  - Dibuka — waktu buka shift
  - Ditutup — waktu tutup shift
  - Status — Aktif atau Tutup
  - Kas Awal — modal kas saat buka shift
  - Kas Sistem — kas yang seharusnya ada
  - Kas Aktual — kas fisik yang dihitung
  - Selisih — perbedaan kas sistem dan kas aktual
  - Catatan — catatan dari kasir

---

## 6. Riwayat Penjualan

### 6.1 Mengakses Riwayat Penjualan

Klik menu **Riwayat Penjualan** di sidebar. Halaman ini dapat diakses oleh user dengan permission `sales.list`.

### 6.2 Filter Riwayat

1. Pilih **rentang tanggal** (dari - sampai)
2. Klik **Terapkan** untuk memfilter
3. Klik **Reset** untuk menghapus filter

**Catatan untuk Kasir:**
- Kasir hanya dapat melihat **transaksi miliknya sendiri**
- Terdapat indikator "Menampilkan transaksi Anda saja" di halaman

**Catatan untuk Manager/Admin/Super Admin:**
- Dapat melihat **semua transaksi** dari semua kasir
- Dapat memfilter berdasarkan kasir tertentu

### 6.3 Informasi yang Ditampilkan

- **Ringkasan:**
  - Jumlah transaksi dalam periode
  - Total omzet penjualan
- **Tabel Riwayat:**
  - Nomor Invoice
  - Tanggal & Waktu
  - Kasir
  - Nama Pelanggan
  - Metode Pembayaran (Tunai/Transfer)
  - Total Penjualan
  - Diskon
  - Grand Total

### 6.4 Melihat Detail Nota

1. Klik nomor invoice atau tombol **Lihat Nota** pada baris transaksi
2. Halaman nota akan terbuka
3. Anda dapat mencetak atau menyimpan nota sebagai PDF melalui browser

---

## 7. Laporan Harian

### 7.1 Mengakses Laporan

Klik menu **Laporan Harian** di sidebar. Halaman ini dapat diakses oleh user dengan permission `reports.view`.

### 7.2 Filter Laporan

1. Pilih **tanggal** yang ingin dilihat laporannya
2. Klik **Tampilkan** untuk memproses laporan

### 7.3 Informasi Laporan

Laporan harian menampilkan data lengkap:

**Ringkasan Keuangan:**
- **Total Transaksi** — jumlah transaksi pada hari tersebut
- **Omzet** — total pendapatan kotor
- **Total Diskon** — total diskon yang diberikan
- **Total HPP** — total harga pokok penjualan
- **Laba Kotor** — omzet dikurangi HPP dan diskon

**Breakdown Metode Pembayaran:**
- Total pembayaran Tunai
- Total pembayaran Transfer

**Top Produk:**
- Daftar produk terlaris berdasarkan qty terjual

**Stok Menipis:**
- Daftar produk yang stoknya di bawah batas minimum
- Membantu perencanaan restock

---

## 8. Manajemen Produk

### 8.1 Mengakses Manajemen Produk

Klik menu **Produk** di sidebar (bagian Administrasi). Halaman ini memerlukan permission `products.list`.

### 8.2 Halaman Daftar Produk

**Ringkasan Statistik:**
- **Total Produk** — jumlah seluruh produk
- **Stok Menipis** — jumlah produk dengan stok di bawah minimum
- **Produk Aktif** — jumlah produk yang statusnya aktif

**Tabel Produk:**
- Kolom: No, SKU, Gambar, Nama, Kategori, Harga Jual, Stok, Min Stok, Status, Aksi
- Fitur pencarian dan pagination
- Sortir berdasarkan kolom

**Tombol Aksi:**
- **Barcode** — menuju halaman scan cepat untuk stok masuk
- **History** — menuju halaman histori mutasi stok
- **Tambah Produk** — menuju form tambah produk baru

### 8.3 Menambah Produk Baru

**Langkah-langkah:**

1. Klik tombol **Tambah Produk** (+)
2. Isi form produk:
   - **SKU** — kode unik produk (wajib)
   - **Nama Produk** — nama lengkap produk (wajib)
   - **Kategori** — pilih kategori dari dropdown
   - **Satuan** — pilih satuan (pcs, kg, liter, dll)
   - **Harga Beli** — harga modal per unit
   - **Harga Jual** — harga jual per unit
   - **Stok** — stok awal
   - **Stok Minimum** — batas minimum stok (alert jika di bawah)
   - **Status** — Aktif atau Nonaktif
   - **Gambar** — upload foto produk (opsional)
3. Klik **Simpan**

### 8.4 Mengedit Produk

**Langkah-langkah:**

1. Pada tabel produk, klik tombol **Edit** (ikon pensil)
2. Ubah data yang ingin diperbarui
3. Klik **Simpan**

**Catatan:**
- Perubahan harga beli melalui form edit akan tercatat sebagai mutasi `adjustment`
- Stok dapat disesuaikan langsung di form edit

### 8.5 Stok Masuk (Restock)

**Cara 1 — Dari Tabel Produk:**

1. Klik tombol **Stok Masuk** pada baris produk
2. Modal stok masuk akan muncul
3. Isi:
   - **Qty Masuk** — jumlah barang yang masuk
   - **Harga Beli per Unit** — harga beli terbaru (untuk perhitungan MWA)
   - **Catatan** — keterangan restock (opsional)
4. Klik **Simpan**

**Cara 2 — Halaman Scan Cepat:**

1. Klik tombol **Barcode** di header tabel produk
2. Scan barcode produk atau ketik SKU manual
3. Jika produk ditemukan:
   - Masukkan qty dan harga beli
   - Klik **Simpan** untuk menambah stok
4. Jika produk tidak ditemukan:
   - Sistem menawarkan untuk membuat produk baru
   - Isi data produk baru
   - Klik **Simpan**

### 8.6 Histori Mutasi Stok (MWA History)

**Langkah-langkah:**

1. Klik tombol **History** di header tabel produk
2. Halaman histori mutasi akan terbuka
3. Filter berdasarkan:
   - Tanggal (dari - sampai)
   - Produk
   - Tipe mutasi (stock-in, sale, adjustment)
4. Klik **Terapkan**

**Informasi yang Ditampilkan:**
- Tanggal & waktu mutasi
- Nama produk
- Tipe mutasi (Stok Masuk, Penjualan, Penyesuaian)
- Qty masuk/keluar
- Harga beli per unit (unit cost)
- Rata-rata harga beli sebelum dan sesudah (avg cost before/after)
- Stok sebelum dan sesudah
- Catatan

**Ringkasan Mutasi:**
- Total stock in
- Total sale
- Delta rata-rata modal

---

## 9. Master Data

### 9.1 Kategori Barang

**Mengakses:**
Klik menu **Master Data > Kategori Barang** di sidebar. Memerlukan permission `masters.categories.list`.

**Menambah Kategori:**

1. Klik tombol **Tambah Kategori**
2. Modal form akan muncul
3. Isi **Nama Kategori**
4. Klik **Simpan**

**Mengedit Kategori:**

1. Klik tombol **Edit** pada baris kategori
2. Ubah nama kategori
3. Klik **Simpan**

**Catatan:**
- Kategori yang sudah digunakan oleh produk tidak dapat dihapus
- Kategori default yang tersedia: Sembako, Minuman, Makanan Ringan, ATK, Perlengkapan Rumah Tangga, Kebutuhan Harian, Gas LPG, Pupuk, Pakan Ternak, Lain-lain

### 9.2 Satuan Barang

**Mengakses:**
Klik menu **Master Data > Satuan Barang** di sidebar. Memerlukan permission `masters.units.list`.

**Menambah Satuan:**

1. Klik tombol **Tambah Satuan**
2. Modal form akan muncul
3. Isi **Nama Satuan** (contoh: pcs, kg, liter)
4. Klik **Simpan**

**Mengedit Satuan:**

1. Klik tombol **Edit** pada baris satuan
2. Ubah nama satuan
3. Klik **Simpan**

**Catatan:**
- Satuan yang sudah digunakan oleh produk tidak dapat dihapus
- Satuan default yang tersedia: pcs, pack, box, dus, bungkus, sachet, botol, kaleng, kg, gram, liter, ml, karung, tabung

---

## 10. Manajemen User

### 10.1 Mengakses Manajemen User

Klik menu **Manajemen User** di sidebar (bagian Administrasi). Memerlukan permission `users.list`.

### 10.2 Daftar User

Tabel user menampilkan:
- Avatar dan Username
- Email
- Role (dengan badge warna)
- Status (Aktif/Nonaktif)
- Tombol aksi: Edit, Hapus

### 10.3 Menambah User Baru

**Langkah-langkah:**

1. Klik tombol **Tambah User**
2. Isi form user:
   - **Username** — nama pengguna untuk login
   - **Email** — alamat email (wajib, unik)
   - **Password** — password minimal 8 karakter
   - **Konfirmasi Password** — ulangi password
   - **Role** — pilih role (Super Admin, Admin, Manager, Kasir, User)
   - **Status** — Aktif atau Nonaktif
3. Klik **Simpan**

### 10.4 Mengedit User

**Langkah-langkah:**

1. Klik tombol **Edit** pada baris user
2. Ubah data yang ingin diperbarui
3. Untuk mengubah password, isi field password baru
4. Untuk mengubah role, klik tombol **Assign Role**
5. Klik **Simpan**

### 10.5 Mengatur Role User

**Langkah-langkah:**

1. Pada halaman edit user, cari bagian **Role**
2. Pilih role baru dari dropdown
3. Klik **Assign Role** atau **Simpan**
4. Role user akan diperbarui

### 10.6 Menghapus User

**Langkah-langkah:**

1. Klik tombol **Hapus** (ikon tempat sampah) pada baris user
2. Konfirmasi penghapusan pada dialog yang muncul
3. User akan dihapus dari sistem

**Catatan:**
- User yang sedang login tidak dapat menghapus dirinya sendiri
- Hapus user bersifat permanen dan tidak dapat dibatalkan

---

## 11. Role & Permission

### 11.1 Mengakses Role & Permission

Menu ini hanya tersedia untuk **Super Admin**. Klik **Role & Permission** di sidebar.

### 11.2 Daftar Role

Menampilkan daftar role yang tersedia:
- **Super Admin** — kontrol penuh sistem
- **Admin** — administrator harian
- **Manager** — melihat laporan dan mengelola data
- **Kasir** — petugas kasir operasional
- **User** — akses terbatas (dashboard saja)

### 11.3 Permission Matrix

**Mengakses:**
Klik **Role & Permission > Permission Matrix** di sidebar.

**Fungsi:**
- Menampilkan mapping permission untuk setiap role
- Membantu administrator memahami hak akses setiap role
- Referensi saat mengatur permission user

**Catatan:**
- Permission matrix didefinisikan di file `app/Config/AuthGroups.php`
- Perubahan permission memerlukan akses ke source code dan deployment ulang

---

## 12. Pengaturan Sistem

### 12.1 Mengakses Pengaturan

Klik menu **Pengaturan** di sidebar (bagian Administrasi). Memerlukan permission `admin.settings`.

### 12.2 Tab Umum

**Pengaturan Dasar:**

| Field | Keterangan |
|-------|-----------|
| Nama Aplikasi | Ditampilkan di title bar dan header sidebar |
| Nama Pendek | Ditampilkan di sidebar saat diminimalkan (maks 10 karakter) |
| Deskripsi | Deskripsi singkat aplikasi |
| Teks Footer | Ditampilkan di bagian bawah halaman |
| Versi | Versi aplikasi |

**Branding:**

- **Logo Aplikasi** — Upload logo (PNG, JPG, SVG, WebP, maks 2MB)
- **Favicon** — Upload ikon browser (PNG, ICO, SVG, maks 1MB)

**Reset:**
- Klik **Reset Pengaturan Umum ke Default** untuk mengembalikan ke pengaturan awal

### 12.3 Tab Tampilan

**Pengaturan Warna:**

- **Warna Navbar** — Warna latar belakang navbar atas
  - Gunakan color picker atau ketik kode hex (contoh: #6777ef)
- **Warna Menu Aktif Sidebar** — Warna indikator menu aktif di sidebar

**Live Preview:**
- Preview tampilan akan berubah secara real-time saat Anda mengubah warna

**Reset:**
- Klik **Reset Pengaturan Tampilan ke Default**

### 12.4 Tab Autentikasi

**Pengaturan Auth:**

| Field | Keterangan |
|-------|-----------|
| Default Role | Role otomatis untuk user baru saat registrasi |
| Registrasi | Izinkan atau nonaktifkan registrasi user baru |

**Mode Pemeliharaan:**

| Field | Keterangan |
|-------|-----------|
| Maintenance Mode | Aktifkan untuk mode pemeliharaan (hanya Super Admin yang bisa akses) |
| Pesan Maintenance | Pesan yang ditampilkan saat mode pemeliharaan aktif |

**Reset:**
- Klik **Reset Pengaturan Autentikasi ke Default**

### 12.5 Tab Email

**Protokol Email:**

| Protokol | Keterangan |
|----------|-----------|
| SMTP | Kirim via server SMTP (direkomendasikan) |
| Sendmail | Kirim via sendmail server |
| PHP Mail | Kirim via fungsi mail PHP |

**Pengaturan SMTP:**

| Field | Keterangan | Contoh |
|-------|-----------|--------|
| SMTP Host | Alamat server SMTP | smtp.gmail.com |
| Port | Port SMTP | 587 |
| Enkripsi | TLS, SSL, atau Tanpa Enkripsi | TLS |
| Username | Email pengirim | email@gmail.com |
| Password | Password email atau App Password | •••••••• |

**Identitas Pengirim:**

| Field | Keterangan |
|-------|-----------|
| Email Pengirim | Alamat email yang tampil sebagai pengirim |
| Nama Pengirim | Nama yang tampil sebagai pengirim |

**Test Email:**

1. Klik tombol **Test Kirim Email**
2. Masukkan email tujuan
3. Isi subjek dan pesan (opsional)
4. Klik **Kirim**
5. Periksa hasil di bagian notifikasi

**Reset:**
- Klik **Reset Pengaturan Email ke Default**

### 12.6 Tab Nota

**Ukuran Kertas:**

| Ukuran | Keterangan |
|--------|-----------|
| Thermal 58mm | Kertas nota thermal standar kecil |
| Thermal 80mm | Kertas nota thermal standar besar |
| Custom | Atur lebar sendiri (40-210mm) |

**Tampilan Teks:**

| Field | Keterangan |
|-------|-----------|
| Jenis Font | Courier New, Arial, Times New Roman, Verdana |
| Ukuran Font | 9-18 pixel |

**Header & Footer:**

| Field | Keterangan |
|-------|-----------|
| Logo Header | Upload logo untuk header nota |
| Teks Header | Judul nota (contoh: "Nota Penjualan") |
| Teks Footer | Pesan di bagian bawah nota |
| Tampilkan Logo | Checkbox untuk menampilkan/menyembunyikan logo |
| Ukuran Logo | Small, Medium, Large |

**Reset:**
- Klik **Reset Pengaturan Nota ke Default**

---

## 13. Profil Saya

### 13.1 Mengakses Profil

Klik menu **Profil Saya** di sidebar atau klik ikon profil di navbar.

### 13.2 Melihat dan Mengedit Profil

**Informasi yang Ditampilkan:**
- Username
- Email
- Role aktif
- Tanggal bergabung

**Mengedit Profil:**

1. Ubah data yang ingin diperbarui:
   - **Username** — nama pengguna
   - **Email** — alamat email
2. Untuk mengubah password:
   - Isi **Password Saat Ini**
   - Isi **Password Baru**
   - Isi **Konfirmasi Password Baru**
3. Klik **Simpan**

---

## 14. Role dan Hak Akses

### 14.1 Daftar Role

| Role | Deskripsi | Akses Utama |
|------|-----------|-------------|
| **Super Admin** | Kontrol penuh terhadap seluruh sistem | Semua modul, pengaturan, user management |
| **Admin** | Administrator harian sistem | POS, produk, master data, laporan, user management |
| **Manager** | Manajer yang melihat laporan dan mengelola data | Dashboard, laporan, riwayat penjualan, daftar produk |
| **Kasir** | Petugas kasir operasional | POS, riwayat transaksi sendiri, buka/tutup shift |
| **User** | Pengguna umum dengan akses terbatas | Dashboard saja |

### 14.2 Permission Detail

**POS & Penjualan:**
| Permission | Keterangan |
|-----------|-----------|
| `sales.create` | Dapat melakukan transaksi penjualan |
| `sales.list` | Dapat melihat riwayat penjualan |
| `shifts.open` | Dapat membuka shift kas |
| `shifts.close` | Dapat menutup shift kas |

**Produk:**
| Permission | Keterangan |
|-----------|-----------|
| `products.list` | Dapat melihat daftar produk |
| `products.create` | Dapat menambah produk |
| `products.edit` | Dapat mengubah produk |
| `products.stock-in` | Dapat menambah stok produk |

**Master Data:**
| Permission | Keterangan |
|-----------|-----------|
| `masters.categories.list` | Dapat melihat kategori |
| `masters.categories.create` | Dapat menambah kategori |
| `masters.categories.edit` | Dapat mengubah kategori |
| `masters.units.list` | Dapat melihat satuan |
| `masters.units.create` | Dapat menambah satuan |
| `masters.units.edit` | Dapat mengubah satuan |

**Laporan:**
| Permission | Keterangan |
|-----------|-----------|
| `reports.view` | Dapat melihat laporan |
| `reports.export` | Dapat mengekspor laporan |

**User Management:**
| Permission | Keterangan |
|-----------|-----------|
| `users.list` | Dapat melihat daftar user |
| `users.create` | Dapat membuat user baru |
| `users.edit` | Dapat mengedit user |
| `users.delete` | Dapat menghapus user |
| `users.manage-roles` | Dapat mengatur role user |

**Admin:**
| Permission | Keterangan |
|-----------|-----------|
| `admin.access` | Dapat mengakses area admin |
| `admin.settings` | Dapat mengakses pengaturan sistem |

---

## 15. FAQ dan Troubleshooting

### 15.1 Pertanyaan Umum

**Q: Bagaimana cara menambah kasir baru?**
A: Login sebagai Admin atau Super Admin, buka **Manajemen User > Tambah User**, isi data kasir baru dan pilih role **Kasir**.

**Q: Mengapa saya tidak bisa membuka shift?**
A: Pastikan tidak ada shift aktif dari kasir lain. Jika ada, minta kasir tersebut untuk menutup shiftnya terlebih dahulu.

**Q: Bagaimana cara menghitung HPP?**
A: Sistem secara otomatis menghitung HPP menggunakan metode Moving Weighted Average (MWA). Setiap kali stok masuk, rata-rata harga beli akan dihitung ulang.

**Q: Bisakah saya mencetak nota thermal?**
A: Ya. Atur ukuran kertas di **Pengaturan > Nota** (58mm atau 80mm). Saat checkout, pilih **Simpan & Cetak Nota**, lalu cetak melalui browser.

**Q: Bagaimana jika ada selisih kas saat tutup shift?**
A: Selisih kas akan tercatat di history shift. Masukkan catatan penjelasan di field "Catatan" saat menutup shift.

**Q: Apakah kasir bisa melihat transaksi kasir lain?**
A: Tidak. Kasir hanya dapat melihat transaksi miliknya sendiri di halaman Riwayat Penjualan. Manager dan Admin dapat melihat semua transaksi.

### 15.2 Troubleshooting

**Masalah: Halaman tidak bisa diakses (403 Forbidden)**
- **Penyebab:** User tidak memiliki permission yang dibutuhkan
- **Solusi:** Hubungi Admin untuk memberikan permission yang sesuai

**Masalah: Produk tidak muncul di POS**
- **Penyebab:** Produk berstatus Nonaktif atau stok habis
- **Solusi:** Edit produk, ubah status menjadi Aktif dan pastikan stok > 0

**Masalah: Email test tidak terkirim**
- **Penyebab:** Konfigurasi SMTP salah
- **Solusi:** Periksa kembali host, port, username, dan password SMTP. Pastikan akun email mengizinkan akses aplikasi pihak ketiga.

**Masalah: Scanner barcode tidak berfungsi**
- **Penyebab:** Browser tidak mengizinkan akses kamera
- **Solusi:** Izinkan akses kamera di pengaturan browser. Pastikan koneksi HTTPS (beberapa browser membatasi kamera pada HTTP).

**Masalah: Nota tidak tercetak dengan benar**
- **Penyebab:** Pengaturan ukuran kertas atau margin printer tidak sesuai
- **Solusi:** Periksa pengaturan nota di sistem dan pengaturan print di browser. Sesuaikan margin dan ukuran kertas.

---

## Lampiran A: Alur Kerja Harian Kasir

1. **Login** ke sistem dengan akun kasir
2. **Buka Shift** — masukkan kas awal
3. **Buka POS Kasir** — mulai melayani pelanggan
4. **Tambah item** ke cart dari katalog atau scan barcode
5. **Proses pembayaran** — pilih metode, input jumlah bayar
6. **Cetak nota** jika diperlukan
7. **Ulangi** langkah 4-6 untuk setiap pelanggan
8. **Tutup Shift** — hitung kas fisik, input kas aktual, tutup shift
9. **Logout** dari sistem

## Lampiran B: Alur Kerja Harian Admin

1. **Login** ke sistem dengan akun admin
2. **Cek Dashboard** — pantau statistik dan stok menipis
3. **Kelola Produk** — tambah/edit produk, restock barang
4. **Cek Laporan** — review penjualan harian
5. **Kelola User** — tambah/edit user jika diperlukan
6. **Monitor Shift** — cek history shift untuk rekonsiliasi

## Lampiran C: Glosarium

| Istilah | Pengertian |
|---------|-----------|
| **POS** | Point of Sale — sistem kasir |
| **BUMDes** | Badan Usaha Milik Desa |
| **MWA** | Moving Weighted Average — metode perhitungan harga pokok rata-rata bergerak |
| **HPP** | Harga Pokok Penjualan |
| **SKU** | Stock Keeping Unit — kode unik produk |
| **Shift** | Periode kerja kasir |
| **Kas Awal** | Modal kas di laci saat buka shift |
| **Kas Sistem** | Total kas yang seharusnya berdasarkan transaksi |
| **Kas Aktual** | Kas fisik yang dihitung manual |
| **Selisih/Variance** | Perbedaan antara kas sistem dan kas aktual |
| **RBAC** | Role-Based Access Control — sistem hak akses berbasis role |
| **Invoice** | Nomor faktur transaksi |
| **Cart** | Keranjang belanja sementara sebelum checkout |
