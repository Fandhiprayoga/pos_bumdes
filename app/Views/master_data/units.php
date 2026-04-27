<?php $this->section('css') ?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css">
<style>
  .units-page {
    --units-accent: #0f766e;
    --units-border: #e5e7eb;
    --units-text-soft: #6b7280;
  }

  .units-hero {
    border: 1px solid var(--units-border);
    border-radius: 16px;
    background: linear-gradient(135deg, #f8fafc 0%, #f0fdfa 100%);
    box-shadow: 0 10px 24px rgba(15, 23, 42, 0.06);
  }

  .units-hero h4 {
    margin-bottom: 4px;
    font-weight: 700;
  }

  .units-hero p {
    margin-bottom: 0;
    color: var(--units-text-soft);
  }

  .units-summary-card {
    border: 1px solid var(--units-border);
    border-radius: 14px;
    box-shadow: none;
    margin-bottom: 0;
  }

  .units-summary-card .summary-label {
    color: var(--units-text-soft);
    font-size: 12px;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    margin-bottom: 4px;
  }

  .units-summary-card .summary-value {
    font-size: 26px;
    font-weight: 700;
    line-height: 1;
    margin: 0;
    color: #111827;
  }

  .units-list-card {
    border: 1px solid var(--units-border);
    border-radius: 16px;
    box-shadow: 0 10px 24px rgba(15, 23, 42, 0.04);
  }

  .units-list-card .card-header {
    background: #fff;
    border-bottom: 1px solid #f1f5f9;
    border-top-left-radius: 16px;
    border-top-right-radius: 16px;
  }

  .units-page .btn-primary {
    background-color: var(--units-accent);
    border-color: var(--units-accent);
  }

  .units-page .btn-primary:hover,
  .units-page .btn-primary:focus {
    background-color: #0d665f;
    border-color: #0d665f;
  }

  .units-page table.dataTable {
    margin-top: 0 !important;
  }

  .units-page .dataTables_wrapper .dataTables_filter input {
    border-radius: 10px;
    border: 1px solid #dbe3ec;
    padding: 4px 10px;
  }

  .units-page .table thead th {
    border-bottom: 1px solid #e5e7eb;
    background: #f8fafc;
    color: #374151;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.04em;
  }

  .units-page .table td {
    vertical-align: middle;
  }

  .units-page .btn-sm {
    border-radius: 8px;
  }

  @media (max-width: 768px) {
    .units-summary-card {
      margin-bottom: 12px;
    }

    .units-hero {
      border-radius: 12px;
    }
  }
</style>
<?= $this->endSection() ?>

<?php $this->section('js') ?>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap4.min.js"></script>
<?= $this->endSection() ?>

<div class="units-page">
  <div class="row mb-4">
    <div class="col-12">
      <div class="units-hero p-4">
        <h4>Master Satuan Barang</h4>
        <p>Kelola satuan produk agar transaksi, stok, dan laporan lebih konsisten.</p>
      </div>
    </div>
  </div>

  <div class="row mb-4">
    <div class="col-md-6 mb-3 mb-md-0">
      <div class="card units-summary-card">
        <div class="card-body py-3">
          <div class="summary-label">Total Satuan</div>
          <p class="summary-value" id="stat-total-units">0</p>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card units-summary-card">
        <div class="card-body py-3">
          <div class="summary-label">Satuan Aktif</div>
          <p class="summary-value" id="stat-active-units">0</p>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-12">
      <div class="card units-list-card">
        <div class="card-header">
          <h4>Daftar Satuan Barang</h4>
          <div class="card-header-action">
            <?php if (activeGroupCan('masters.units.create')): ?>
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addUnitModal">
              <i class="fas fa-plus"></i> Tambah Satuan
            </button>
            <?php endif; ?>
          </div>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-striped table-sm" id="units-table">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Nama Satuan</th>
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

<?php if (activeGroupCan('masters.units.create')): ?>
<div class="modal fade" id="addUnitModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form action="<?= base_url('admin/master-data/units/store') ?>" method="post">
        <?= csrf_field() ?>
        <div class="modal-header">
          <h5 class="modal-title">Tambah Satuan Barang</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Nama Satuan</label>
            <input type="text" name="name" class="form-control" required>
          </div>
          <div class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" id="add-unit-active" name="is_active" value="1" checked>
            <label class="custom-control-label" for="add-unit-active">Satuan aktif</label>
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

<?php if (activeGroupCan('masters.units.edit')): ?>
<div class="modal fade" id="editUnitModalGlobal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form action="#" method="post" id="editUnitFormGlobal">
        <?= csrf_field() ?>
        <div class="modal-header">
          <h5 class="modal-title">Edit Satuan Barang</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Nama Satuan</label>
            <input type="text" name="name" class="form-control" id="edit-unit-name" required>
          </div>
          <div class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" id="edit-unit-active" name="is_active" value="1">
            <label class="custom-control-label" for="edit-unit-active">Satuan aktif</label>
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
  var statTotalUnits = document.getElementById('stat-total-units');
  var statActiveUnits = document.getElementById('stat-active-units');
  var editUnitModalGlobal = document.getElementById('editUnitModalGlobal');
  var editUnitFormGlobal = document.getElementById('editUnitFormGlobal');
  var editUnitNameInput = document.getElementById('edit-unit-name');
  var editUnitActiveInput = document.getElementById('edit-unit-active');

  var addUnitModal = document.getElementById('addUnitModal');
  if (addUnitModal && addUnitModal.parentElement !== document.body) {
    document.body.appendChild(addUnitModal);
  }

  if (editUnitModalGlobal && editUnitModalGlobal.parentElement !== document.body) {
    document.body.appendChild(editUnitModalGlobal);
  }

  function renderUnitSummary(rows) {
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

    if (statTotalUnits) {
      statTotalUnits.textContent = total;
    }
    if (statActiveUnits) {
      statActiveUnits.textContent = active;
    }
  }

  if (typeof $ !== 'undefined' && $.fn.DataTable) {
    $('#units-table').DataTable({
      ajax: {
        url: '<?= base_url('admin/master-data/units/data') ?>',
        dataSrc: function(json) {
          var rows = (json && json.data) ? json.data : [];
          renderUnitSummary(rows);
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
        emptyTable: 'Belum ada satuan barang.',
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
    var button = event.target.closest('.btn-edit-unit');
    if (!button || !editUnitFormGlobal) {
      return;
    }

    var unitId = button.getAttribute('data-unit-id') || '';
    var unitName = button.getAttribute('data-unit-name') || '';
    var unitActive = button.getAttribute('data-unit-active') || '0';

    editUnitFormGlobal.setAttribute('action', '<?= base_url('admin/master-data/units/update') ?>/' + unitId);
    if (editUnitNameInput) {
      editUnitNameInput.value = unitName;
    }
    if (editUnitActiveInput) {
      editUnitActiveInput.checked = unitActive === '1';
    }
  });
})();
</script>
<?= $this->endSection() ?>
