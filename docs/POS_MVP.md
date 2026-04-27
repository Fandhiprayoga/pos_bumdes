# POS BUMDes MVP

Dokumen ini menjelaskan implementasi modul POS MVP yang sudah ditambahkan ke project.

## Modul yang Diimplementasikan

1. Manajemen Produk
- CRUD produk
- Master kategori barang
- Master satuan barang
- Stok minimum
- Stok masuk manual
- Status produk aktif/nonaktif
- Seeder produk contoh untuk testing cepat

2. POS Kasir
- Buka shift kas
- Transaksi penjualan multi item
- Tambah item cepat via scan barcode SKU atau cari nama barang
- Metode bayar tunai/transfer
- Diskon nominal
- Hitung kembalian
- Tutup shift kas dan hitung selisih
- Seeder transaksi dummy untuk demo riwayat dan laporan

3. Riwayat Penjualan
- Filter tanggal
- Ringkasan jumlah transaksi dan total nilai

4. Laporan Harian
- Total transaksi harian
- Omzet harian
- Total diskon
- HPP terjual (berdasarkan snapshot modal saat transaksi)
- Laba kotor (omzet - HPP)
- Breakdown metode pembayaran
- Top produk
- Daftar stok menipis

5. RBAC
- Role baru: `cashier`
- Permission produk, penjualan, dan shift
- Menu otomatis tampil sesuai permission

## File yang Ditambahkan

### Migrations
- `app/Database/Migrations/2026-04-27-000001_CreatePosMvpTables.php`
- `app/Database/Migrations/2026-04-27-000003_AddProfitSnapshotToSaleItems.php`
- `app/Database/Migrations/2026-04-27-000004_BackfillProfitSnapshotSaleItems.php`

Tabel yang dibuat:
- `products`
- `cash_shifts`
- `sales`
- `sale_items`
- `stock_movements`

Kolom tambahan `sale_items` untuk keamanan histori laba-rugi:
- `cost_price_snapshot`
- `cogs_total`
- `discount_allocated`
- `net_line_total`
- `gross_profit`

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

## File yang Diubah

- `app/Config/AuthGroups.php`
  - Tambah group `cashier`
  - Tambah permission modul POS MVP
  - Update permission matrix

- `app/Config/Routes.php`
  - Tambah route produk, master data, POS, dan laporan harian

- `app/Views/partials/sidebar.php`
  - Tambah menu POS, riwayat penjualan, laporan harian, produk, dan master data

- `app/Database/Seeds/UserSeeder.php`
  - Tambah user default `cashier@example.com`

- `app/Database/Seeds/DatabaseSeeder.php`
  - Menjalankan seluruh seeder awal dalam satu perintah
  - Termasuk user, master produk, produk contoh, dan transaksi dummy

## Alur Bisnis MVP

1. Persiapan
- Admin siapkan master kategori barang dan satuan.
- Admin input master produk dan stok awal, atau gunakan produk contoh dari seeder.
- Admin menyiapkan user kasir.

2. Mulai Shift
- Kasir buka shift dari menu POS dengan input kas awal.

3. Transaksi Penjualan
- Kasir menambah item dengan 2 cara:
  - Scan barcode (SKU) lalu Enter
  - Ketik nama barang lalu Enter atau klik tombol Tambah
- Sistem otomatis menambah baris keranjang atau menambah qty jika barang sudah ada di keranjang.
- Sistem validasi stok.
- Sistem menghitung subtotal, diskon, grand total, dan kembalian.
- Sistem menyimpan transaksi (header + item).
- Sistem menyimpan snapshot modal per item (`cost_price_snapshot`) saat transaksi terjadi.
- Sistem mengurangi stok produk dan mencatat `stock_movements`.

4. Monitoring Harian
- Kasir/manager lihat riwayat transaksi.
- Manager lihat laporan harian, HPP terjual, laba kotor, dan stok menipis.

5. Tutup Shift
- Kasir input kas fisik akhir.
- Sistem hitung kas sistem = kas awal + total penjualan tunai.
- Sistem simpan selisih kas (variance).

## Daftar Permission Baru

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

### Penjualan
- `sales.create`
- `sales.list`

### Shift Kas
- `shifts.open`
- `shifts.close`

## Mapping Role (MVP)

- `superadmin`: semua akses modul POS
- `admin`: semua akses operasional POS + produk
- `cashier`: POS transaksi, riwayat, buka/tutup shift
- `manager`: lihat laporan dan riwayat
- `user`: hanya dashboard

## Cara Menjalankan

1. Jalankan migration:

```bash
php spark migrate
```

2. Jalankan seeder awal lengkap:

```bash
php spark db:seed DatabaseSeeder
```

3. Login akun kasir:
- Email: `cashier@example.com`
- Password: `password123`

4. Login sebagai admin/superadmin untuk melihat menu master data, produk, dan produk contoh yang sudah tersedia.

## Data Awal Seeder

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
- 5 transaksi demo dibuat untuk tanggal `2026-04-26` dan `2026-04-27`
- Mencakup pembayaran `cash` dan `transfer`
- Otomatis membuat `cash_shifts`, `sales`, `sale_items`, dan `stock_movements`
- Data ini berguna untuk mencoba:
  - halaman POS history
  - laporan harian
  - stok menipis setelah penjualan

## Catatan Batasan MVP Saat Ini

- Belum ada modul pembelian/PO formal.
- Belum ada retur penjualan.
- Belum ada cetak struk thermal.
- Belum ada multi gudang atau multi cabang.
- Laporan masih level operasional harian.
- Snapshot laba-rugi transaksi lama yang dibuat sebelum fitur ini dibackfill menggunakan nilai modal produk saat migrasi backfill dijalankan.

## Rekomendasi Next Step

1. Tambah modul pembelian + penerimaan barang.
2. Tambah retur penjualan.
3. Tambah export laporan (CSV/PDF).
4. Tambah halaman detail transaksi dengan item.
5. Tambah approval untuk diskon/void besar.
