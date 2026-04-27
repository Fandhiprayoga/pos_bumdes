<?php $this->section('css') ?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css">
<style>
  .categories-page {
    --categories-accent: #0f766e;
    --categories-border: #e5e7eb;
    --categories-text-soft: #6b7280;
  }

  .categories-hero {
    border: 1px solid var(--categories-border);
    border-radius: 16px;
    background: linear-gradient(135deg, #f8fafc 0%, #f0fdfa 100%);
    box-shadow: 0 10px 24px rgba(15, 23, 42, 0.06);
  }

  .categories-hero h4 {
    margin-bottom: 4px;
    font-weight: 700;
  }

  .categories-hero p {
    margin-bottom: 0;
    color: var(--categories-text-soft);
  }

  .categories-summary-card {
    border: 1px solid var(--categories-border);
    border-radius: 14px;
    box-shadow: none;
    margin-bottom: 0;
  }

  .categories-summary-card .summary-label {
    color: var(--categories-text-soft);
    font-size: 12px;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    margin-bottom: 4px;
  }

  .categories-summary-card .summary-value {
    font-size: 26px;
    font-weight: 700;
    line-height: 1;
    margin: 0;
    color: #111827;
  }

  .categories-list-card {
    border: 1px solid var(--categories-border);
    border-radius: 16px;
    box-shadow: 0 10px 24px rgba(15, 23, 42, 0.04);
  }

  .categories-list-card .card-header {
    background: #fff;
    border-bottom: 1px solid #f1f5f9;
    border-top-left-radius: 16px;
    border-top-right-radius: 16px;
  }

  .categories-page .btn-primary {
    background-color: var(--categories-accent);
    border-color: var(--categories-accent);
  }

  .categories-page .btn-primary:hover,
  .categories-page .btn-primary:focus {
    background-color: #0d665f;
    border-color: #0d665f;
  }

  .categories-page table.dataTable {
    margin-top: 0 !important;
  }

  .categories-page .dataTables_wrapper .dataTables_filter input {
    border-radius: 10px;
    border: 1px solid #dbe3ec;
    padding: 4px 10px;
  }

  .categories-page .table thead th {
    border-bottom: 1px solid #e5e7eb;
    background: #f8fafc;
    color: #374151;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.04em;
  }

  .categories-page .table td {
    vertical-align: middle;
  }

  .categories-page .btn-sm {
    border-radius: 8px;
  }

  @media (max-width: 768px) {
    .categories-summary-card {
      margin-bottom: 12px;
    }

    .categories-hero {
      border-radius: 12px;
    }
  }
</style>
<?= $this->endSection() ?>

<?php $this->section('js') ?>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap4.min.js"></script>
<?= $this->endSection() ?>

<div class="categories-page">
  <div class="row mb-4">
    <div class="col-12">
      <div class="categories-hero p-4">
        <h4>Master Kategori Barang</h4>
        <p>Kelola kategori produk agar input barang dan laporan lebih tertata.</p>
      </div>
    </div>
  </div>

  <div class="row mb-4">
    <div class="col-md-6 mb-3 mb-md-0">
      <div class="card categories-summary-card">
        <div class="card-body py-3">
          <div class="summary-label">Total Kategori</div>
          <p class="summary-value" id="stat-total-categories">0</p>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card categories-summary-card">
        <div class="card-body py-3">
          <div class="summary-label">Kategori Aktif</div>
          <p class="summary-value" id="stat-active-categories">0</p>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-12">
      <div class="card categories-list-card">
        <div class="card-header">
          <h4>Daftar Kategori Barang</h4>
          <div class="card-header-action">
            <?php if (activeGroupCan('masters.categories.create')): ?>
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addCategoryModal">
              <i class="fas fa-plus"></i> Tambah Kategori
            </button>
            <?php endif; ?>
          </div>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-striped table-sm" id="categories-table">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Nama Kategori</th>
                  <th>Status</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php if (activeGroupCan('masters.categories.create')): ?>
<div class="modal fade" id="addCategoryModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form action="<?= base_url('admin/master-data/categories/store') ?>" method="post">
        <?= csrf_field() ?>
        <div class="modal-header">
          <h5 class="modal-title">Tambah Kategori Barang</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Nama Kategori</label>
            <input type="text" name="name" class="form-control" required>
          </div>
          <div class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" id="add-category-active" name="is_active" value="1" checked>
            <label class="custom-control-label" for="add-category-active">Kategori aktif</label>
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
<?php endif; ?>

<?php if (activeGroupCan('masters.categories.edit')): ?>
<div class="modal fade" id="editCategoryModalGlobal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form action="#" method="post" id="editCategoryFormGlobal">
        <?= csrf_field() ?>
        <div class="modal-header">
          <h5 class="modal-title">Edit Kategori Barang</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Nama Kategori</label>
            <input type="text" name="name" class="form-control" id="edit-category-name" required>
          </div>
          <div class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" id="edit-category-active" name="is_active" value="1">
            <label class="custom-control-label" for="edit-category-active">Kategori aktif</label>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Update</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php endif; ?>

<?php $this->section('page_js') ?>
<script>
(function() {
  var statTotalCategories = document.getElementById('stat-total-categories');
  var statActiveCategories = document.getElementById('stat-active-categories');
  var editCategoryModalGlobal = document.getElementById('editCategoryModalGlobal');
  var editCategoryFormGlobal = document.getElementById('editCategoryFormGlobal');
  var editCategoryNameInput = document.getElementById('edit-category-name');
  var editCategoryActiveInput = document.getElementById('edit-category-active');

  var addCategoryModal = document.getElementById('addCategoryModal');
  if (addCategoryModal && addCategoryModal.parentElement !== document.body) {
    document.body.appendChild(addCategoryModal);
  }

  if (editCategoryModalGlobal && editCategoryModalGlobal.parentElement !== document.body) {
    document.body.appendChild(editCategoryModalGlobal);
  }

  function renderCategorySummary(rows) {
    if (!Array.isArray(rows)) {
      return;
    }

    var total = rows.length;
    var active = 0;

    rows.forEach(function(row) {
      var statusCell = String((row && row[2]) || '');
      if (statusCell.indexOf('badge-success') !== -1) {
        active++;
      }
    });

    if (statTotalCategories) {
      statTotalCategories.textContent = total;
    }
    if (statActiveCategories) {
      statActiveCategories.textContent = active;
    }
  }

  if (typeof $ !== 'undefined' && $.fn.DataTable) {
    $('#categories-table').DataTable({
      ajax: {
        url: '<?= base_url('admin/master-data/categories/data') ?>',
        dataSrc: function(json) {
          var rows = (json && json.data) ? json.data : [];
          renderCategorySummary(rows);
          return rows;
        }
      },
      processing: true,
      deferRender: true,
      pageLength: 10,
      order: [[1, 'asc']],
      columnDefs: [
        { targets: [2, 3], orderable: false },
        { targets: [2, 3], searchable: false },
      ],
      language: {
        emptyTable: 'Belum ada kategori barang.',
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
    var button = event.target.closest('.btn-edit-category');
    if (!button || !editCategoryFormGlobal) {
      return;
    }

    var categoryId = button.getAttribute('data-category-id') || '';
    var categoryName = button.getAttribute('data-category-name') || '';
    var categoryActive = button.getAttribute('data-category-active') || '0';

    editCategoryFormGlobal.setAttribute('action', '<?= base_url('admin/master-data/categories/update') ?>/' + categoryId);
    if (editCategoryNameInput) {
      editCategoryNameInput.value = categoryName;
    }
    if (editCategoryActiveInput) {
      editCategoryActiveInput.checked = categoryActive === '1';
    }
  });
})();
</script>
<?= $this->endSection() ?>
