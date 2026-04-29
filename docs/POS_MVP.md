# POS BUMDes MVP

Dokumen ini merangkum apa saja yang sudah benar-benar dikerjakan dan tersedia di codebase untuk modul POS MVP.

## Status Implementasi Saat Ini

### 1. Master Produk dan Inventori
- CRUD produk sudah tersedia.
- Master kategori produk sudah tersedia dengan tabel terpisah.
- Master satuan produk sudah tersedia dengan tabel terpisah.
- Produk mendukung SKU, kategori, satuan, harga beli, harga jual, stok, stok minimum, dan status aktif/nonaktif.
- Stok masuk manual dari halaman daftar produk sudah tersedia.
- Penyesuaian stok dari halaman edit produk sudah tersedia dan tercatat sebagai mutasi `adjustment`.
- Alur cepat scan barang sudah tersedia untuk 2 skenario:
  - produk lama: tambah stok + hitung ulang rata-rata harga beli
  - produk baru: buat produk baru langsung dari hasil scan
- Histori mutasi stok sudah tersedia dengan filter tanggal, produk, dan tipe mutasi.
- Ringkasan mutasi stok sudah tersedia, termasuk total stock in, total sale, dan delta rata-rata modal.

### 2. Perhitungan Harga Pokok dengan MWA
- Sistem sudah memakai pendekatan Moving Weighted Average (MWA) untuk pembaruan harga beli rata-rata saat stok masuk.
- Audit kolom MWA di `stock_movements` sudah tersedia:
  - `unit_cost_in`
  - `avg_cost_before`
  - `avg_cost_after`
  - `stock_before`
  - `stock_after`
- Snapshot modal saat transaksi penjualan sudah tersedia di `sale_items`.
- Nilai HPP, diskon teralokasi, nilai bersih per item, dan laba kotor per item sudah disimpan untuk menjaga histori laporan.

### 3. POS Kasir
- Buka shift kas dengan input kas awal sudah tersedia.
- Validasi hanya satu shift terbuka per kasir sudah tersedia.
- Transaksi penjualan multi item sudah tersedia.
- Tambah item cepat di POS tersedia dari daftar produk aktif yang masih punya stok.
- Validasi stok saat checkout sudah tersedia.
- Metode pembayaran `cash` dan `transfer` sudah tersedia.
- Diskon nominal transaksi sudah tersedia.
- Perhitungan jumlah bayar dan kembalian sudah tersedia.
- Simpan header penjualan dan detail item sudah tersedia.
- Pengurangan stok otomatis setelah transaksi sudah tersedia.
- Pencatatan mutasi stok tipe `sale` setelah transaksi sudah tersedia.
- Tutup shift kas dengan hitung kas sistem, kas aktual, dan variance sudah tersedia.

### 4. Riwayat dan Laporan
- Riwayat penjualan dengan filter tanggal sudah tersedia.
- Ringkasan jumlah transaksi dan total penjualan pada halaman riwayat sudah tersedia.
- Laporan penjualan harian sudah tersedia.
- Laporan harian sudah menampilkan:
  - total transaksi
  - omzet
  - total diskon
  - total HPP
  - laba kotor
  - breakdown metode pembayaran
  - top produk
  - daftar stok menipis

### 5. Hak Akses dan Navigasi
- Role `cashier` sudah ditambahkan.
- Permission untuk produk, master data, penjualan, shift, dan laporan sudah tersedia.
- Route POS, laporan, produk, dan master data sudah diproteksi dengan permission.
- Sidebar untuk menu POS dan inventory sudah tersedia sesuai izin akses.

## File yang Sudah Ada

### Migrations
- `app/Database/Migrations/2026-04-27-000001_CreatePosMvpTables.php`
- `app/Database/Migrations/2026-04-27-000002_CreateProductMasterTables.php`
- `app/Database/Migrations/2026-04-27-000003_AddProfitSnapshotToSaleItems.php`
- `app/Database/Migrations/2026-04-27-000004_BackfillProfitSnapshotSaleItems.php`
- `app/Database/Migrations/2026-04-27-000005_AddMwaAuditColumnsToStockMovements.php`

Tabel utama yang sudah dibuat:
- `products`
- `product_categories`
- `product_units`
- `cash_shifts`
- `sales`
- `sale_items`
- `stock_movements`

### Models
- `app/Models/ProductModel.php`
- `app/Models/ProductCategoryModel.php`
- `app/Models/ProductUnitModel.php`
- `app/Models/CashShiftModel.php`
- `app/Models/SaleModel.php`
- `app/Models/SaleItemModel.php`
- `app/Models/StockMovementModel.php`

### Controllers
- `app/Controllers/ProductController.php`
- `app/Controllers/ProductCategoryController.php`
- `app/Controllers/ProductUnitController.php`
- `app/Controllers/PosController.php`
- `app/Controllers/SalesReportController.php`

### Views
- `app/Views/products/index.php`
- `app/Views/products/create.php`
- `app/Views/products/edit.php`
- `app/Views/products/scan_flow.php`
- `app/Views/products/mwa_history.php`
- `app/Views/master_data/categories.php`
- `app/Views/master_data/units.php`
- `app/Views/pos/index.php`
- `app/Views/pos/history.php`
- `app/Views/reports/sales_daily.php`

### Seeders
- `app/Database/Seeds/DatabaseSeeder.php`
- `app/Database/Seeds/UserSeeder.php`
- `app/Database/Seeds/ProductMasterSeeder.php`
- `app/Database/Seeds/ProductSeeder.php`
- `app/Database/Seeds/DummySalesSeeder.php`

## Route yang Sudah Tersedia

### POS
- `GET /pos`
- `POST /pos/open-shift`
- `POST /pos/checkout`
- `POST /pos/close-shift`
- `GET /pos/history`

### Laporan
- `GET /reports/sales-daily`

### Produk
- `GET /admin/products`
- `GET /admin/products/data`
- `GET /admin/products/create`
- `POST /admin/products/store`
- `GET /admin/products/edit/{id}`
- `POST /admin/products/update/{id}`
- `POST /admin/products/stock-in/{id}`
- `GET /admin/products/scan`
- `POST /admin/products/scan-flow`
- `GET /admin/products/mwa-history`
- `GET /admin/products/mwa-history/data`

### Master Data
- `GET /admin/master-data/categories`
- `GET /admin/master-data/categories/data`
- `POST /admin/master-data/categories/store`
- `POST /admin/master-data/categories/update/{id}`
- `GET /admin/master-data/units`
- `GET /admin/master-data/units/data`
- `POST /admin/master-data/units/store`
- `POST /admin/master-data/units/update/{id}`

## Alur Bisnis yang Sudah Berjalan

1. Admin melengkapi master kategori dan satuan.
2. Admin membuat produk atau memakai produk contoh dari seeder.
3. Admin atau petugas stok dapat memakai scan flow untuk barang baru atau restock barang lama.
4. Kasir membuka shift kas.
5. Kasir melakukan transaksi penjualan dan sistem memvalidasi stok.
6. Sistem menyimpan transaksi, snapshot modal, detail item, dan mutasi stok penjualan.
7. Kasir menutup shift dan sistem menghitung selisih kas.
8. Manager atau admin dapat melihat riwayat penjualan, laporan harian, dan histori mutasi stok.

## Data Awal yang Sudah Disediakan

### Master Kategori
- `Sembako`
- `Minuman`
- `Makanan Ringan`
- `ATK`
- `Perlengkapan Rumah Tangga`
- `Kebutuhan Harian`
- `Gas LPG`
- `Pupuk`
- `Pakan Ternak`
- `Lain-lain`

### Master Satuan
- `pcs`
- `pack`
- `box`
- `dus`
- `bungkus`
- `sachet`
- `botol`
- `kaleng`
- `kg`
- `gram`
- `liter`
- `ml`
- `karung`
- `tabung`

### Produk Contoh
- `BRG-001` Beras Ramos 5 Kg
- `BRG-002` Gula Pasir 1 Kg
- `BRG-003` Minyak Goreng 1 Liter
- `BRG-004` Air Mineral 600 ml
- `BRG-005` Mi Instan Goreng
- `BRG-006` Teh Celup Isi 25
- `BRG-007` LPG 3 Kg
- `BRG-008` Pupuk Urea 5 Kg
- `BRG-009` Pakan Ayam Starter 5 Kg
- `BRG-010` Buku Tulis 38 Lembar
- `BRG-011` Sabun Cuci Piring 800 ml
- `BRG-012` Kopi Sachet

### Transaksi Dummy
- 5 transaksi demo tersedia untuk tanggal `2026-04-26` dan `2026-04-27`.
- Mencakup pembayaran `cash` dan `transfer`.
- Otomatis membentuk data `cash_shifts`, `sales`, `sale_items`, dan `stock_movements`.
- Dapat dipakai untuk mencoba halaman riwayat, laporan harian, dan histori mutasi stok.

## Permission yang Sudah Dipakai

### Produk
- `products.list`
- `products.create`
- `products.edit`
- `products.stock-in`

### Master Data
- `masters.categories.list`
- `masters.categories.create`
- `masters.categories.edit`
- `masters.units.list`
- `masters.units.create`
- `masters.units.edit`

### Penjualan dan Shift
- `sales.create`
- `sales.list`
- `shifts.open`
- `shifts.close`

### Laporan
- `reports.view`

## Mapping Role Saat Ini

- `superadmin`: semua akses modul POS, produk, master data, laporan, dan admin.
- `admin`: akses operasional POS, laporan, produk, master data, dan user management tertentu.
- `manager`: lihat dashboard, laporan, riwayat penjualan, dan daftar produk.
- `cashier`: transaksi POS, riwayat penjualan, buka shift, tutup shift, dan lihat produk.
- `user`: akses dashboard saja.

## Cara Menjalankan

1. Jalankan migration:

```bash
php spark migrate
```

2. Jalankan seeder awal:

```bash
php spark db:seed DatabaseSeeder
```

3. Login akun kasir untuk uji alur POS.

4. Login akun admin atau superadmin untuk uji master data, produk, scan flow, dan histori mutasi stok.

## Batasan yang Masih Ada

- Belum ada modul pembelian atau purchase order formal.
- Belum ada retur penjualan.
- Belum ada cetak struk thermal.
- Belum ada multi gudang atau multi cabang.
- Belum ada export laporan.
- POS masih berbasis form web standar, belum terhubung ke printer atau perangkat kasir khusus.
- Snapshot laba-rugi transaksi lama yang dibuat sebelum fitur ini dibackfill menggunakan nilai modal produk saat migrasi backfill dijalankan.

## Rekomendasi Next Step

1. Tambah modul pembelian + penerimaan barang.
2. Tambah retur penjualan.
3. Tambah export laporan (CSV/PDF).
4. Tambah halaman detail transaksi dengan item.
5. Tambah approval untuk diskon/void besar.
