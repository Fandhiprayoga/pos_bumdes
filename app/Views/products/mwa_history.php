<?php $this->section('css') ?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css">
<?= $this->endSection() ?>

<?php $this->section('js') ?>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap4.min.js"></script>
<?= $this->endSection() ?>

<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4>Filter Histori Mutasi Stok</h4>
      </div>
      <div class="card-body">
        <form method="get" action="<?= base_url('admin/products/mwa-history') ?>">
          <div class="form-row">
            <div class="form-group col-md-3">
              <label>Dari Tanggal</label>
              <input type="date" class="form-control" name="from" value="<?= esc($from) ?>">
            </div>
            <div class="form-group col-md-3">
              <label>Sampai Tanggal</label>
              <input type="date" class="form-control" name="to" value="<?= esc($to) ?>">
            </div>
            <div class="form-group col-md-3">
              <label>Produk</label>
              <select class="form-control" name="product_id">
                <option value="0">Semua Produk</option>
                <?php foreach ($products as $product): ?>
                <option value="<?= (int) $product['id'] ?>" <?= $productId === (int) $product['id'] ? 'selected' : '' ?>>
                  <?= esc(($product['sku'] ?: '-') . ' - ' . $product['name']) ?>
                </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group col-md-3">
              <label>Tipe Mutasi</label>
              <select class="form-control" name="movement_type">
                <option value="" <?= $movementType === '' ? 'selected' : '' ?>>Semua Mutasi</option>
                <option value="stock_in" <?= $movementType === 'stock_in' ? 'selected' : '' ?>>Stock In</option>
                <option value="adjustment" <?= $movementType === 'adjustment' ? 'selected' : '' ?>>Adjustment</option>
                <option value="sale" <?= $movementType === 'sale' ? 'selected' : '' ?>>Sale</option>
              </select>
            </div>
          </div>

          <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary mr-2">Terapkan</button>
            <a href="<?= base_url('admin/products/mwa-history') ?>" class="btn btn-light">Reset</a>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4>Ringkasan Mutasi</h4>
      </div>
      <div class="card-body">
        <div class="row text-center">
          <div class="col-md-3">
            <h6>Total Mutasi</h6>
            <h4><?= (int) ($summary['total_rows'] ?? 0) ?></h4>
          </div>
          <div class="col-md-3">
            <h6>Total Qty Stock In</h6>
            <h4><?= number_format((float) ($summary['total_stock_in_qty'] ?? 0), 0, ',', '.') ?></h4>
          </div>
          <div class="col-md-3">
            <h6>Total Qty Sale</h6>
            <h4><?= number_format((float) ($summary['total_sale_qty'] ?? 0), 0, ',', '.') ?></h4>
          </div>
          <div class="col-md-3">
            <h6>Akumulasi Delta Avg Cost</h6>
            <h4>Rp <?= number_format((float) ($summary['total_avg_cost_delta'] ?? 0), 2, ',', '.') ?></h4>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4>Daftar Histori Mutasi Stok</h4>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped table-sm" id="stock-movement-history-table">
            <thead>
              <tr>
                <th>Waktu</th>
                <th>Produk</th>
                <th>Tipe</th>
                <th>Qty</th>
                <th>Harga Masuk per Unit</th>
                <th>Rata-rata Modal Sebelum</th>
                <th>Rata-rata Modal Sesudah</th>
                <th>Stok Sebelum</th>
                <th>Stok Sesudah</th>
                <th>Selisih Rata-rata Modal</th>
                <th>Referensi</th>
                <th>Catatan</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<?php $this->section('page_js') ?>
<script>
(function() {
  if (typeof $ === 'undefined' || !$.fn.DataTable) {
    return;
  }

  $('#stock-movement-history-table').DataTable({
    ajax: {
      url: '<?= base_url('admin/products/mwa-history/data?from=' . urlencode((string) $from) . '&to=' . urlencode((string) $to) . '&product_id=' . urlencode((string) $productId) . '&movement_type=' . urlencode((string) $movementType)) ?>',
      dataSrc: 'data'
    },
    processing: true,
    deferRender: true,
    pageLength: 25,
    order: [[0, 'desc']],
    columnDefs: [
      { targets: [1, 2, 9, 11], orderable: false },
      { targets: [2], searchable: false },
    ],
    language: {
      emptyTable: 'Belum ada data histori mutasi stok pada filter ini.',
      search: 'Cari:',
      lengthMenu: 'Tampilkan _MENU_ data',
      info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
      paginate: {
        previous: 'Sebelumnya',
        next: 'Berikutnya'
      }
    }
  });
})();
</script>
<?= $this->endSection() ?>
