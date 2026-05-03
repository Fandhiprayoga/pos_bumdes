<?php $this->section('css') ?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css">
<style>
  .products-page {
    --products-accent: #0f766e;
    --products-border: #e5e7eb;
    --products-text-soft: #6b7280;
  }

  .products-hero {
    border: 1px solid var(--products-border);
    border-radius: 16px;
    background: linear-gradient(135deg, #f8fafc 0%, #f0fdfa 100%);
    box-shadow: 0 10px 24px rgba(15, 23, 42, 0.06);
  }

  .products-hero h4 {
    margin-bottom: 4px;
    font-weight: 700;
  }

  .products-hero p {
    margin-bottom: 0;
    color: var(--products-text-soft);
  }

  .products-summary-card {
    border: 1px solid var(--products-border);
    border-radius: 14px;
    box-shadow: none;
    margin-bottom: 0;
  }

  .products-summary-card .summary-label {
    color: var(--products-text-soft);
    font-size: 12px;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    margin-bottom: 4px;
  }

  .products-summary-card .summary-value {
    font-size: 26px;
    font-weight: 700;
    line-height: 1;
    margin: 0;
    color: #111827;
  }

  .products-list-card {
    border: 1px solid var(--products-border);
    border-radius: 16px;
    box-shadow: 0 10px 24px rgba(15, 23, 42, 0.04);
  }

  .products-list-card .card-header {
    background: #fff;
    border-bottom: 1px solid #f1f5f9;
    border-top-left-radius: 16px;
    border-top-right-radius: 16px;
  }

  .products-page .products-list-card .card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
  }

  .products-page .products-list-card .card-header h4 {
    margin-bottom: 0;
  }

  .products-page .products-actions {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 8px;
    flex-wrap: wrap;
  }

  .products-page .products-actions .btn {
    min-height: 42px;
    border-radius: 10px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 6px;
  }

  .products-page .products-actions .icon-btn {
    width: 42px;
    min-width: 42px;
    padding: 0;
    justify-content: center;
    font-size: 15px;
  }

  .products-page .products-table-meta {
    margin-top: 10px;
    font-size: 12px;
    color: var(--products-text-soft);
  }

  .products-page .btn-primary {
    background-color: var(--products-accent);
    border-color: var(--products-accent);
  }

  .products-page .btn-primary:hover,
  .products-page .btn-primary:focus {
    background-color: #0d665f;
    border-color: #0d665f;
  }

  .products-page table.dataTable {
    margin-top: 0 !important;
  }

  .products-page .dataTables_wrapper .dataTables_filter input {
    border-radius: 10px;
    border: 1px solid #dbe3ec;
    padding: 7px 12px;
    min-width: 220px;
  }

  .products-page .dataTables_wrapper .dataTables_length select {
    border-radius: 10px;
    border: 1px solid #dbe3ec;
    min-height: 38px;
    padding: 4px 8px;
  }

  .products-page .dataTables_wrapper .dataTables_filter,
  .products-page .dataTables_wrapper .dataTables_length {
    margin-bottom: 10px;
  }

  .products-page .table thead th {
    border-bottom: 1px solid #e5e7eb;
    background: #f8fafc;
    color: #374151;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.04em;
  }

  .products-page .table td {
    vertical-align: middle;
  }

  .products-page .btn-sm {
    border-radius: 8px;
  }

  @media (max-width: 1024px) {
    .products-page .products-actions {
      justify-content: flex-end;
    }

    .products-page .products-actions .btn {
      flex: 0 0 auto;
      justify-content: center;
      min-width: 42px;
    }

    .products-page .table thead th,
    .products-page .table td {
      white-space: nowrap;
    }

    .products-page .dataTables_wrapper .dataTables_filter input {
      min-width: 180px;
    }
  }

  @media (max-width: 768px) {
    .products-summary-card {
      margin-bottom: 12px;
    }

    .products-hero {
      border-radius: 12px;
    }

    .products-page .products-actions .btn {
      flex: 0 0 auto;
      min-width: 42px;
    }
  }
</style>
<?= $this->endSection() ?>

<?php $this->section('js') ?>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap4.min.js"></script>
<?= $this->endSection() ?>

<div class="products-page">
  <div class="row mb-4">
    <div class="col-12">
      <div class="products-hero p-4">
        <h4>Manajemen Produk</h4>
        <p>Kelola master produk, pantau stok, dan gunakan scan cepat di halaman terpisah agar operasional lebih fokus.</p>
      </div>
    </div>
  </div>

  <div class="row mb-4">
    <div class="col-sm-6 col-xl-4 mb-3 mb-xl-0">
      <div class="card products-summary-card">
        <div class="card-body py-3">
          <div class="summary-label">Total Produk</div>
          <p class="summary-value" id="stat-total-products">0</p>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-xl-4 mb-3 mb-xl-0">
      <div class="card products-summary-card">
        <div class="card-body py-3">
          <div class="summary-label">Stok Menipis</div>
          <p class="summary-value" id="stat-low-stock">0</p>
        </div>
      </div>
    </div>
    <div class="col-12 col-xl-4">
      <div class="card products-summary-card">
        <div class="card-body py-3">
          <div class="summary-label">Produk Aktif</div>
          <p class="summary-value" id="stat-active-products">0</p>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-12">
      <div class="card products-list-card">
        <div class="card-header">
          <h4>Daftar Produk</h4>
          <div class="card-header-action products-actions">
            <a href="<?= base_url('admin/products/scan') ?>" class="btn btn-outline-dark icon-btn" data-toggle="tooltip" data-placement="top" title="Halaman Scan Cepat" aria-label="Halaman Scan Cepat">
              <i class="fas fa-barcode"></i>
            </a>
            <a href="<?= base_url('admin/products/mwa-history') ?>" class="btn btn-light icon-btn" data-toggle="tooltip" data-placement="top" title="Histori Mutasi Stok" aria-label="Histori Mutasi Stok">
              <i class="fas fa-history"></i>
            </a>
            <?php if (activeGroupCan('products.create')): ?>
            <a href="<?= base_url('admin/products/create') ?>" class="btn btn-primary icon-btn" data-toggle="tooltip" data-placement="top" title="Tambah Produk" aria-label="Tambah Produk">
              <i class="fas fa-plus"></i>
            </a>
            <?php endif; ?>
          </div>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-striped table-sm" id="products-table">
              <thead>
                <tr>
                  <th>#</th>
                  <th>SKU</th>
                  <th>Gambar</th>
                  <th>Nama</th>
                  <th>Kategori</th>
                  <th>Harga Jual</th>
                  <th>Stok</th>
                  <th>Min Stok</th>
                  <th>Status</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>
          <p class="products-table-meta">Tampilan dioptimalkan untuk tablet: geser horizontal tabel jika kolom tidak muat.</p>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="stockInModalGlobal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form action="#" method="post" id="stockInFormGlobal">
        <?= csrf_field() ?>
        <div class="modal-header">
          <h5 class="modal-title" id="stockInModalTitle">Stok Masuk</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Qty Masuk</label>
            <input type="number" name="qty" min="1" class="form-control" required>
          </div>
          <div class="form-group">
            <label>Harga Beli per Unit <span class="text-danger">*</span></label>
            <input type="number" name="cost_price" step="0.01" min="0" class="form-control" required>
            <small class="form-text text-muted">Dipakai untuk menghitung harga beli rata-rata bergerak (Moving Weighted Average).</small>
          </div>
          <div class="form-group mb-0">
            <label>Catatan</label>
            <input type="text" name="notes" class="form-control" placeholder="Contoh: Restock dari supplier A">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php $this->section('page_js') ?>
<script>
(function() {
  var stockInModalGlobal = document.getElementById('stockInModalGlobal');
  var stockInFormGlobal = document.getElementById('stockInFormGlobal');
  var stockInModalTitle = document.getElementById('stockInModalTitle');
  var statTotalProducts = document.getElementById('stat-total-products');
  var statLowStock = document.getElementById('stat-low-stock');
  var statActiveProducts = document.getElementById('stat-active-products');

  if (stockInModalGlobal && stockInModalGlobal.parentElement !== document.body) {
    document.body.appendChild(stockInModalGlobal);
  }

  if (typeof $ !== 'undefined' && $.fn.tooltip) {
    $('[data-toggle="tooltip"]').tooltip();
  }

  function renderProductSummary(rows) {
    if (!Array.isArray(rows)) {
      return;
    }

    var total = rows.length;
    var lowStock = 0;
    var active = 0;

    rows.forEach(function(row) {
      var stockCell = String((row && row[6]) || '');
      var statusCell = String((row && row[8]) || '');

      if (stockCell.indexOf('Menipis') !== -1) {
        lowStock++;
      }

      if (statusCell.indexOf('badge-success') !== -1) {
        active++;
      }
    });

    if (statTotalProducts) {
      statTotalProducts.textContent = total;
    }
    if (statLowStock) {
      statLowStock.textContent = lowStock;
    }
    if (statActiveProducts) {
      statActiveProducts.textContent = active;
    }
  }

  if (typeof $ !== 'undefined' && $.fn.DataTable) {
    $('#products-table').DataTable({
      ajax: {
        url: '<?= base_url('admin/products/data') ?>',
        dataSrc: function(json) {
          var rows = (json && json.data) ? json.data : [];
          renderProductSummary(rows);
          return rows;
        }
      },
      processing: true,
      deferRender: true,
      pageLength: 10,
      order: [[0, 'asc']],
       dom: "<'row align-items-center mb-2'<'col-md-6'l><'col-md-6'f>>" +
         "<'row'<'col-12'tr>>" +
         "<'row align-items-center mt-2'<'col-md-5'i><'col-md-7'p>>",
      columnDefs: [
        { targets: [2, 6, 8, 9], orderable: false },
        { targets: [2, 6, 8, 9], searchable: false },
      ],
      language: {
        emptyTable: 'Belum ada data produk.',
        search: 'Cari:',
        lengthMenu: 'Tampilkan _MENU_ data',
        info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
        paginate: {
          previous: 'Sebelumnya',
          next: 'Berikutnya'
        }
      }
    });
  }

  document.addEventListener('click', function(event) {
    var button = event.target.closest('.btn-stock-in-trigger');
    if (!button || !stockInFormGlobal) {
      return;
    }

    var stockInUrl = button.getAttribute('data-stock-in-url') || '#';
    var productName = button.getAttribute('data-product-name') || 'Produk';

    stockInFormGlobal.setAttribute('action', stockInUrl);
    if (stockInModalTitle) {
      stockInModalTitle.textContent = 'Stok Masuk: ' + productName;
    }
  });
})();
</script>
<?= $this->endSection() ?>
