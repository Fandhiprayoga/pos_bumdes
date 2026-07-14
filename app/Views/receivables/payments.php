<?php $this->section('css') ?>
<style>
  .receivable-payments-page {
    --rp-accent: #0f766e;
    --rp-border: #e5e7eb;
    --rp-soft-text: #64748b;
  }

  .receivable-payments-page .hero-card,
  .receivable-payments-page .filter-card,
  .receivable-payments-page .summary-card,
  .receivable-payments-page .table-card {
    border: 1px solid var(--rp-border);
    border-radius: 14px;
    box-shadow: 0 8px 20px rgba(15, 23, 42, 0.04);
    background: #fff;
  }

  .receivable-payments-page .hero-card {
    padding: 20px;
    background: linear-gradient(130deg, #f8fafc 0%, #ecfeff 100%);
  }

  .receivable-payments-page .hero-card h4 {
    margin-bottom: 4px;
    font-weight: 700;
  }

  .receivable-payments-page .hero-card p,
  .receivable-payments-page .muted-text {
    color: var(--rp-soft-text);
  }

  .receivable-payments-page .summary-label {
    color: var(--rp-soft-text);
    text-transform: uppercase;
    letter-spacing: 0.04em;
    font-size: 11px;
    margin-bottom: 6px;
  }

  .receivable-payments-page .summary-value {
    font-size: 24px;
    font-weight: 700;
    margin-bottom: 0;
    color: #0f172a;
  }

  .receivable-payments-page .card-header {
    background: #fff;
    border-bottom: 1px solid #f1f5f9;
  }

  .receivable-payments-page .btn-primary {
    background-color: var(--rp-accent);
    border-color: var(--rp-accent);
  }

  .receivable-payments-page .btn-primary:hover,
  .receivable-payments-page .btn-primary:focus {
    background-color: #0d665f;
    border-color: #0d665f;
  }

  .receivable-payments-page .form-control,
  .receivable-payments-page .custom-select {
    border-radius: 10px;
    border-color: #dbe3ec;
  }

  .receivable-payments-page .table thead th {
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: #475569;
    background: #f8fafc;
    border-bottom: 1px solid #e5e7eb;
  }

  .receivable-payments-page .text-money {
    font-weight: 600;
    color: #0f172a;
    white-space: nowrap;
  }

  .receivable-payments-page .invoice-link {
    font-weight: 600;
  }
</style>
<?= $this->endSection() ?>

<?php
$rows = $receivables ?? [];
$filterStatus = (string) (($filters['status'] ?? 'open') ?: 'open');
$filterCustomerId = (int) (($filters['customer_id'] ?? 0) ?: 0);
$filterKeyword = (string) (($filters['q'] ?? '') ?: '');
$totalReceivables = (int) ($summary['total_receivables'] ?? 0);
$totalOutstanding = (float) ($summary['total_outstanding'] ?? 0);
$totalDueToday = (float) ($summary['total_due_today'] ?? 0);
$canCollect = activeGroupCan('receivables.collect');
$modalHtml = '';

$statusLabel = static function (string $status): string {
    $map = [
        'all' => 'Semua',
        'open' => 'Open',
        'partial' => 'Partial',
        'overdue' => 'Overdue',
    ];

    return $map[$status] ?? ucfirst($status);
};

$statusBadgeClass = static function (string $status): string {
    if ($status === 'partial') {
        return 'badge-warning';
    }
    if ($status === 'overdue') {
        return 'badge-danger';
    }

    return 'badge-info';
};
?>

<div class="receivable-payments-page">
  <div class="row mb-4">
    <div class="col-12">
      <div class="hero-card d-flex justify-content-between align-items-start flex-wrap">
        <div>
          <h4>Pembayaran Piutang</h4>
          <p>Kelola piutang penjualan yang masih terbuka, catat pembayaran, dan pantau sisa tagihan.</p>
        </div>
        <div class="mt-2 mt-md-0">
          <a href="<?= base_url('receivables') ?>" class="btn btn-light">Lihat Daftar Piutang</a>
        </div>
      </div>
    </div>
  </div>

  <div class="row mb-4">
    <div class="col-12 col-md-4 mb-3 mb-md-0">
      <div class="card summary-card">
        <div class="card-body">
          <div class="summary-label">Piutang Aktif</div>
          <p class="summary-value"><?= number_format($totalReceivables, 0, ',', '.') ?></p>
        </div>
      </div>
    </div>
    <div class="col-12 col-md-4 mb-3 mb-md-0">
      <div class="card summary-card">
        <div class="card-body">
          <div class="summary-label">Total Outstanding</div>
          <p class="summary-value">Rp <?= number_format($totalOutstanding, 0, ',', '.') ?></p>
        </div>
      </div>
    </div>
    <div class="col-12 col-md-4">
      <div class="card summary-card">
        <div class="card-body">
          <div class="summary-label">Jatuh Tempo Hari Ini</div>
          <p class="summary-value">Rp <?= number_format($totalDueToday, 0, ',', '.') ?></p>
        </div>
      </div>
    </div>
  </div>

  <div class="row mb-4">
    <div class="col-12">
      <div class="card filter-card">
        <div class="card-header">
          <h4>Filter Pembayaran</h4>
        </div>
        <div class="card-body">
          <form method="get" action="<?= base_url('receivables/payments') ?>">
            <div class="form-row align-items-end">
              <div class="form-group col-md-3 mb-2 mb-md-0">
                <label for="status">Status</label>
                <select id="status" name="status" class="form-control custom-select">
                  <?php foreach (($statusOptions ?? []) as $status): ?>
                    <option value="<?= esc($status) ?>" <?= $filterStatus === $status ? 'selected' : '' ?>><?= esc($statusLabel($status)) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="form-group col-md-4 mb-2 mb-md-0">
                <label for="customer_id">Pelanggan</label>
                <select id="customer_id" name="customer_id" class="form-control custom-select">
                  <option value="0">Semua pelanggan</option>
                  <?php foreach (($customers ?? []) as $customer): ?>
                    <option value="<?= (int) ($customer['id'] ?? 0) ?>" <?= $filterCustomerId === (int) ($customer['id'] ?? 0) ? 'selected' : '' ?>>
                      <?= esc((string) ($customer['name'] ?? 'Pelanggan')) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="form-group col-md-3 mb-2 mb-md-0">
                <label for="q">Pencarian</label>
                <input type="text" id="q" name="q" class="form-control" value="<?= esc($filterKeyword) ?>" placeholder="Invoice atau nama pelanggan">
              </div>
              <div class="form-group col-md-2 mb-0 d-flex justify-content-md-end">
                <button type="submit" class="btn btn-primary mr-2">Cari</button>
                <a href="<?= base_url('receivables/payments') ?>" class="btn btn-light">Reset</a>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-12">
      <div class="card table-card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h4 class="mb-0">Tagihan Siap Dibayar</h4>
          <span class="muted-text"><?= count($rows) ?> data</span>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-striped mb-0">
              <thead>
                <tr>
                  <th>Invoice</th>
                  <th>Pelanggan</th>
                  <th>Jatuh Tempo</th>
                  <th>Status</th>
                  <th>Grand Total</th>
                  <th>Terbayar</th>
                  <th>Sisa</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php if (! empty($rows)): ?>
                  <?php foreach ($rows as $row): ?>
                  <?php
                    $rowStatus = strtolower((string) ($row['status'] ?? 'open'));
                    $customerLabel = trim((string) ($row['customer_label'] ?? ''));
                    if ($customerLabel === '') {
                        $customerLabel = (string) ($row['customer_name'] ?? '-');
                    }
                    $modalId = 'payModal' . (int) ($row['id'] ?? 0);
                  ?>
                  <tr>
                    <td>
                      <a class="invoice-link" href="<?= base_url('receivables/' . (int) ($row['id'] ?? 0)) ?>">
                        <?= esc((string) ($row['invoice_no'] ?? '-')) ?>
                      </a>
                    </td>
                    <td><?= esc($customerLabel !== '' ? $customerLabel : '-') ?></td>
                    <td><?= esc((string) (($row['due_date'] ?? '') ?: '-')) ?></td>
                    <td><span class="badge <?= $statusBadgeClass($rowStatus) ?>"><?= esc($statusLabel($rowStatus)) ?></span></td>
                    <td class="text-money">Rp <?= number_format((float) ($row['grand_total'] ?? 0), 0, ',', '.') ?></td>
                    <td class="text-money">Rp <?= number_format((float) ($row['amount_paid'] ?? 0), 0, ',', '.') ?></td>
                    <td class="text-money">Rp <?= number_format((float) ($row['outstanding'] ?? 0), 0, ',', '.') ?></td>
                    <td>
                      <?php if ($canCollect): ?>
                        <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#<?= esc($modalId) ?>">Bayar</button>
                      <?php else: ?>
                        <span class="badge badge-light">Lihat Saja</span>
                      <?php endif; ?>
                    </td>
                  </tr>
                  <?php if ($canCollect): ?>
                  <?php ob_start(); ?>
                  <div class="modal fade" id="<?= esc($modalId) ?>" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title">Pembayaran <?= esc((string) ($row['invoice_no'] ?? '-')) ?></h5>
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                          </button>
                        </div>
                        <form method="post" action="<?= base_url('receivables/' . (int) ($row['id'] ?? 0) . '/payments') ?>">
                          <?= csrf_field() ?>
                          <div class="modal-body">
                            <div class="alert alert-info">
                              Sisa piutang: Rp <?= number_format((float) ($row['outstanding'] ?? 0), 0, ',', '.') ?>
                            </div>
                            <div class="form-group">
                              <label>Nominal Pembayaran</label>
                              <input type="number" name="amount" class="form-control" min="1" step="0.01" max="<?= esc((string) (float) ($row['outstanding'] ?? 0)) ?>" required>
                            </div>
                            <div class="form-group">
                              <label>Metode Pembayaran</label>
                              <select name="payment_method" class="form-control custom-select">
                                <option value="cash">Cash</option>
                                <option value="transfer">Transfer</option>
                              </select>
                            </div>
                            <div class="form-group">
                              <label>Referensi</label>
                              <input type="text" name="reference_no" class="form-control" placeholder="Opsional">
                            </div>
                            <div class="form-group mb-0">
                              <label>Catatan</label>
                              <textarea name="notes" class="form-control" rows="3" placeholder="Opsional"></textarea>
                            </div>
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan Pembayaran</button>
                          </div>
                        </form>
                      </div>
                    </div>
                  </div>
                  <?php $modalHtml .= ob_get_clean(); ?>
                  <?php endif; ?>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="8" class="text-center py-4">Tidak ada piutang yang siap dibayar.</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?= $modalHtml ?>

<?php $this->section('page_js') ?>
<script>
(function() {
  var paymentModals = document.querySelectorAll('.modal[id^="payModal"]');

  paymentModals.forEach(function(modal) {
    if (modal.parentElement !== document.body) {
      document.body.appendChild(modal);
    }
  });

  if (typeof window.jQuery === 'undefined') {
    return;
  }

  window.jQuery(document).on('shown.bs.modal', '.modal[id^="payModal"]', function() {
    var modalZIndex = 1060;
    this.style.zIndex = String(modalZIndex);

    var backdrops = document.querySelectorAll('.modal-backdrop');
    if (backdrops.length > 0) {
      backdrops[backdrops.length - 1].style.zIndex = String(modalZIndex - 10);
    }
  });
})();
</script>
<?= $this->endSection() ?>