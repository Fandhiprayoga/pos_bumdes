<?php $this->section('css') ?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css">
<style>
  .customers-page {
    --customers-accent: #0f766e;
    --customers-border: #e5e7eb;
    --customers-text-soft: #6b7280;
  }

  .customers-hero {
    border: 1px solid var(--customers-border);
    border-radius: 16px;
    background: linear-gradient(135deg, #f8fafc 0%, #f0fdfa 100%);
    box-shadow: 0 10px 24px rgba(15, 23, 42, 0.06);
  }

  .customers-hero h4 {
    margin-bottom: 4px;
    font-weight: 700;
  }

  .customers-hero p {
    margin-bottom: 0;
    color: var(--customers-text-soft);
  }

  .customers-summary-card {
    border: 1px solid var(--customers-border);
    border-radius: 14px;
    box-shadow: none;
    margin-bottom: 0;
  }

  .customers-summary-card .summary-label {
    color: var(--customers-text-soft);
    font-size: 12px;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    margin-bottom: 4px;
  }

  .customers-summary-card .summary-value {
    font-size: 26px;
    font-weight: 700;
    line-height: 1;
    margin: 0;
    color: #111827;
  }

  .customers-list-card {
    border: 1px solid var(--customers-border);
    border-radius: 16px;
    box-shadow: 0 10px 24px rgba(15, 23, 42, 0.04);
  }

  .customers-list-card .card-header {
    background: #fff;
    border-bottom: 1px solid #f1f5f9;
    border-top-left-radius: 16px;
    border-top-right-radius: 16px;
  }

  .customers-page .btn-primary {
    background-color: var(--customers-accent);
    border-color: var(--customers-accent);
  }

  .customers-page .btn-primary:hover,
  .customers-page .btn-primary:focus {
    background-color: #0d665f;
    border-color: #0d665f;
  }

  .customers-page table.dataTable {
    margin-top: 0 !important;
  }

  .customers-page .dataTables_wrapper .dataTables_filter input {
    border-radius: 10px;
    border: 1px solid #dbe3ec;
    padding: 4px 10px;
  }

  .customers-page .table thead th {
    border-bottom: 1px solid #e5e7eb;
    background: #f8fafc;
    color: #374151;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.04em;
  }

  .customers-page .table td {
    vertical-align: middle;
  }

  .customers-page .btn-sm {
    border-radius: 8px;
  }

  @media (max-width: 768px) {
    .customers-summary-card {
      margin-bottom: 12px;
    }

    .customers-hero {
      border-radius: 12px;
    }
  }
</style>
<?= $this->endSection() ?>

<?php $this->section('js') ?>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap4.min.js"></script>
<?= $this->endSection() ?>

<div class="customers-page">
  <div class="row mb-4">
    <div class="col-12">
      <div class="customers-hero p-4">
        <h4>Master Pelanggan</h4>
        <p>Kelola data pelanggan untuk transaksi kredit dan penjualan.</p>
      </div>
    </div>
  </div>

  <div class="row mb-4">
    <div class="col-md-4 mb-3 mb-md-0">
      <div class="card customers-summary-card">
        <div class="card-body py-3">
          <div class="summary-label">Total Pelanggan</div>
          <p class="summary-value" id="stat-total-customers">0</p>
        </div>
      </div>
    </div>
    <div class="col-md-4 mb-3 mb-md-0">
      <div class="card customers-summary-card">
        <div class="card-body py-3">
          <div class="summary-label">Pelanggan Aktif</div>
          <p class="summary-value" id="stat-active-customers">0</p>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card customers-summary-card">
        <div class="card-body py-3">
          <div class="summary-label">Total Limit Kredit</div>
          <p class="summary-value" id="stat-total-credit-limit">Rp 0</p>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-12">
      <div class="card customers-list-card">
        <div class="card-header">
          <h4>Daftar Pelanggan</h4>
          <div class="card-header-action">
            <?php if (activeGroupCan('customers.create')): ?>
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addCustomerModal">
              <i class="fas fa-plus"></i> Tambah Pelanggan
            </button>
            <?php endif; ?>
          </div>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-striped table-sm" id="customers-table">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Nama</th>
                  <th>Telepon</th>
                  <th>Limit Kredit</th>
                  <th>Termin</th>
                  <th>Favorit</th>
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

<?php if (activeGroupCan('customers.create')): ?>
<div class="modal fade" id="addCustomerModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form action="<?= base_url('customers/store') ?>" method="post">
        <?= csrf_field() ?>
        <div class="modal-header">
          <h5 class="modal-title">Tambah Pelanggan</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Nama Pelanggan <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control" required>
          </div>
          <div class="form-group">
            <label>Telepon</label>
            <input type="text" name="phone" class="form-control" placeholder="Contoh: 0812xxxx">
          </div>
          <div class="form-group">
            <label>Alamat</label>
            <textarea name="address" class="form-control" rows="2" placeholder="Alamat lengkap"></textarea>
          </div>
          <div class="form-row">
            <div class="form-group col-md-6">
              <label>Limit Kredit (Rp)</label>
              <input type="number" name="credit_limit" class="form-control" value="0" min="0" step="1000">
              <small class="form-text text-muted">0 = tanpa limit</small>
            </div>
            <div class="form-group col-md-6">
              <label>Termin Kredit (hari)</label>
              <input type="number" name="default_credit_term" class="form-control" value="30" min="0" step="1">
              <small class="form-text text-muted">0 = tidak ada termin default</small>
            </div>
          </div>
          <div class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" id="add-customer-active" name="is_active" value="1" checked>
            <label class="custom-control-label" for="add-customer-active">Pelanggan aktif</label>
          </div>
          <div class="custom-control custom-checkbox mt-2">
            <input type="checkbox" class="custom-control-input" id="add-customer-favorite" name="is_favorite" value="1">
            <label class="custom-control-label" for="add-customer-favorite">Tandai sebagai pelanggan favorit</label>
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

<?php if (activeGroupCan('customers.edit')): ?>
<div class="modal fade" id="editCustomerModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form action="#" method="post" id="editCustomerForm">
        <?= csrf_field() ?>
        <div class="modal-header">
          <h5 class="modal-title">Edit Pelanggan</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Nama Pelanggan <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control" id="edit-customer-name" required>
          </div>
          <div class="form-group">
            <label>Telepon</label>
            <input type="text" name="phone" class="form-control" id="edit-customer-phone">
          </div>
          <div class="form-group">
            <label>Alamat</label>
            <textarea name="address" class="form-control" rows="2" id="edit-customer-address"></textarea>
          </div>
          <div class="form-row">
            <div class="form-group col-md-6">
              <label>Limit Kredit (Rp)</label>
              <input type="number" name="credit_limit" class="form-control" id="edit-customer-credit-limit" value="0" min="0" step="1000">
            </div>
            <div class="form-group col-md-6">
              <label>Termin Kredit (hari)</label>
              <input type="number" name="default_credit_term" class="form-control" id="edit-customer-default-term" value="30" min="0" step="1">
            </div>
          </div>
          <div class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" id="edit-customer-active" name="is_active" value="1">
            <label class="custom-control-label" for="edit-customer-active">Pelanggan aktif</label>
          </div>
          <div class="custom-control custom-checkbox mt-2">
            <input type="checkbox" class="custom-control-input" id="edit-customer-favorite" name="is_favorite" value="1">
            <label class="custom-control-label" for="edit-customer-favorite">Tandai sebagai pelanggan favorit</label>
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
  var statTotalCustomers = document.getElementById('stat-total-customers');
  var statActiveCustomers = document.getElementById('stat-active-customers');
  var statTotalCreditLimit = document.getElementById('stat-total-credit-limit');
  var editCustomerModal = document.getElementById('editCustomerModal');
  var editCustomerForm = document.getElementById('editCustomerForm');
  var editCustomerName = document.getElementById('edit-customer-name');
  var editCustomerPhone = document.getElementById('edit-customer-phone');
  var editCustomerAddress = document.getElementById('edit-customer-address');
  var editCustomerCreditLimit = document.getElementById('edit-customer-credit-limit');
  var editCustomerDefaultTerm = document.getElementById('edit-customer-default-term');
  var editCustomerActive = document.getElementById('edit-customer-active');
  var editCustomerFavorite = document.getElementById('edit-customer-favorite');

  var addCustomerModal = document.getElementById('addCustomerModal');
  if (addCustomerModal && addCustomerModal.parentElement !== document.body) {
    document.body.appendChild(addCustomerModal);
  }

  if (editCustomerModal && editCustomerModal.parentElement !== document.body) {
    document.body.appendChild(editCustomerModal);
  }

  function renderCustomerSummary(rows) {
    if (!Array.isArray(rows)) {
      return;
    }

    var total = rows.length;
    var active = 0;
    var totalLimit = 0;

    rows.forEach(function(row) {
      var statusCell = String((row && row[6]) || '');
      if (statusCell.indexOf('badge-success') !== -1) {
        active++;
      }

      var limitText = String((row && row[3]) || '');
      var limitNum = parseInt(limitText.replace(/[^0-9]/g, ''), 10);
      if (!isNaN(limitNum)) {
        totalLimit += limitNum;
      }
    });

    if (statTotalCustomers) {
      statTotalCustomers.textContent = total;
    }
    if (statActiveCustomers) {
      statActiveCustomers.textContent = active;
    }
    if (statTotalCreditLimit) {
      statTotalCreditLimit.textContent = 'Rp ' + Number(totalLimit).toLocaleString('id-ID');
    }
  }

  if (typeof $ !== 'undefined' && $.fn.DataTable) {
    $('#customers-table').DataTable({
      ajax: {
        url: '<?= base_url('customers/data') ?>',
        dataSrc: function(json) {
          var rows = (json && json.data) ? json.data : [];
          renderCustomerSummary(rows);
          return rows;
        }
      },
      processing: true,
      deferRender: true,
      pageLength: 10,
      order: [[1, 'asc']],
      columnDefs: [
        { targets: [2, 3, 4, 5, 6, 7], orderable: false },
        { targets: [2, 3, 4, 5, 6, 7], searchable: false },
      ],
      language: {
        emptyTable: 'Belum ada data pelanggan.',
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
    var button = event.target.closest('.btn-edit-customer');
    if (!button || !editCustomerForm) {
      return;
    }

    var customerId = button.getAttribute('data-customer-id') || '';
    var customerName = button.getAttribute('data-customer-name') || '';
    var customerPhone = button.getAttribute('data-customer-phone') || '';
    var customerAddress = button.getAttribute('data-customer-address') || '';
    var customerCreditLimit = button.getAttribute('data-customer-credit-limit') || '0';
    var customerDefaultTerm = button.getAttribute('data-customer-default-term') || '0';
    var customerFavorite = button.getAttribute('data-customer-favorite') || '0';
    var customerActive = button.getAttribute('data-customer-active') || '0';

    editCustomerForm.setAttribute('action', '<?= base_url('customers/update') ?>/' + customerId);
    if (editCustomerName) editCustomerName.value = customerName;
    if (editCustomerPhone) editCustomerPhone.value = customerPhone;
    if (editCustomerAddress) editCustomerAddress.value = customerAddress;
    if (editCustomerCreditLimit) editCustomerCreditLimit.value = customerCreditLimit;
    if (editCustomerDefaultTerm) editCustomerDefaultTerm.value = customerDefaultTerm;
    if (editCustomerFavorite) editCustomerFavorite.checked = customerFavorite === '1';
    if (editCustomerActive) editCustomerActive.checked = customerActive === '1';
  });
})();
</script>
<?= $this->endSection() ?>
