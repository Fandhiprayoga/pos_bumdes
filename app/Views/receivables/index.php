<?php $this->section('css') ?>
<style>
  .receivables-page {
    --rv-accent: #0f766e;
    --rv-border: #e5e7eb;
    --rv-soft-text: #64748b;
  }

  .receivables-page .hero-card,
  .receivables-page .filter-card,
  .receivables-page .summary-card,
  .receivables-page .table-card {
    border: 1px solid var(--rv-border);
    border-radius: 14px;
    box-shadow: 0 8px 20px rgba(15, 23, 42, 0.04);
    background: #fff;
  }

  .receivables-page .hero-card {
    padding: 20px;
    background: linear-gradient(130deg, #f8fafc 0%, #ecfeff 100%);
  }

  .receivables-page .hero-card h4 {
    margin-bottom: 4px;
    font-weight: 700;
  }

  .receivables-page .hero-card p {
    margin-bottom: 0;
    color: var(--rv-soft-text);
  }

  .receivables-page .summary-label {
    color: var(--rv-soft-text);
    text-transform: uppercase;
    letter-spacing: 0.04em;
    font-size: 11px;
    margin-bottom: 6px;
  }

  .receivables-page .summary-value {
    font-size: 24px;
    font-weight: 700;
    margin-bottom: 0;
    color: #0f172a;
  }

  .receivables-page .card-header {
    background: #fff;
    border-bottom: 1px solid #f1f5f9;
  }

  .receivables-page .btn-primary {
    background-color: var(--rv-accent);
    border-color: var(--rv-accent);
  }

  .receivables-page .btn-primary:hover,
  .receivables-page .btn-primary:focus {
    background-color: #0d665f;
    border-color: #0d665f;
  }

  .receivables-page .form-control,
  .receivables-page .custom-select {
    border-radius: 10px;
    border-color: #dbe3ec;
  }

  .receivables-page .table thead th {
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: #475569;
    background: #f8fafc;
    border-bottom: 1px solid #e5e7eb;
  }

  .receivables-page .text-money {
    font-weight: 600;
    color: #0f172a;
    white-space: nowrap;
  }

  .receivables-page .invoice-link {
    font-weight: 600;
  }
</style>
<?= $this->endSection() ?>

<?php
$rows = $receivables ?? [];
$filterStatus = (string) (($filters['status'] ?? '') ?: '');
$filterCustomerId = (int) (($filters['customer_id'] ?? 0) ?: 0);
$totalReceivables = (int) ($summary['total_receivables'] ?? 0);
$totalOutstanding = (float) ($summary['total_outstanding'] ?? 0);
$totalOverdue = (float) ($summary['total_overdue'] ?? 0);

$statusLabel = static function (string $status): string {
    $map = [
        'open' => 'Open',
        'partial' => 'Partial',
        'settled' => 'Settled',
        'overdue' => 'Overdue',
        'cancelled' => 'Cancelled',
    ];

    return $map[$status] ?? ucfirst($status);
};

$statusBadgeClass = static function (string $status): string {
    if ($status === 'settled') {
        return 'badge-success';
    }
    if ($status === 'partial') {
        return 'badge-warning';
    }
    if ($status === 'overdue') {
        return 'badge-danger';
    }
    if ($status === 'cancelled') {
        return 'badge-secondary';
    }

    return 'badge-info';
};
?>

<div class="receivables-page">
  <div class="row mb-4">
    <div class="col-12">
      <div class="hero-card">
        <h4>Daftar Piutang</h4>
        <p>Pantau status piutang, nilai outstanding, dan transaksi yang sudah jatuh tempo.</p>
      </div>
    </div>
  </div>

  <div class="row mb-4">
    <div class="col-12">
      <div class="card filter-card">
        <div class="card-header">
          <h4>Filter Data</h4>
        </div>
        <div class="card-body">
          <form method="get" action="<?= base_url('receivables') ?>">
            <div class="form-row align-items-end">
              <div class="form-group col-md-4 mb-2 mb-md-0">
                <label for="status">Status</label>
                <select id="status" name="status" class="form-control custom-select">
                  <option value="">Semua status</option>
                  <?php foreach (($statusOptions ?? []) as $status): ?>
                    <option value="<?= esc($status) ?>" <?= $filterStatus === $status ? 'selected' : '' ?>><?= esc($statusLabel($status)) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="form-group col-md-5 mb-2 mb-md-0">
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
              <div class="form-group col-md-3 mb-0 d-flex justify-content-md-end">
                <button type="submit" class="btn btn-primary mr-2">Terapkan</button>
                <a href="<?= base_url('receivables') ?>" class="btn btn-light">Reset</a>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <div class="row mb-4">
    <div class="col-12 col-md-4 mb-3 mb-md-0">
      <div class="card summary-card">
        <div class="card-body">
          <div class="summary-label">Total Piutang</div>
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
          <div class="summary-label">Total Overdue</div>
          <p class="summary-value">Rp <?= number_format($totalOverdue, 0, ',', '.') ?></p>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-12">
      <div class="card table-card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h4 class="mb-0">Daftar Invoice Piutang</h4>
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
                </tr>
              </thead>
              <tbody>
                <?php if (! empty($rows)): ?>
                  <?php foreach ($rows as $row): ?>
                  <?php
                    $status = strtolower((string) ($row['status'] ?? 'open'));
                    $customerLabel = trim((string) ($row['customer_label'] ?? ''));
                    if ($customerLabel === '') {
                        $customerLabel = (string) ($row['customer_name'] ?? '-');
                    }
                  ?>
                  <tr>
                    <td>
                      <a class="invoice-link" href="<?= base_url('receivables/' . (int) ($row['id'] ?? 0)) ?>">
                        <?= esc((string) ($row['invoice_no'] ?? '-')) ?>
                      </a>
                    </td>
                    <td><?= esc($customerLabel !== '' ? $customerLabel : '-') ?></td>
                    <td><?= esc((string) (($row['due_date'] ?? '') ?: '-')) ?></td>
                    <td><span class="badge <?= $statusBadgeClass($status) ?>"><?= esc($statusLabel($status)) ?></span></td>
                    <td class="text-money">Rp <?= number_format((float) ($row['grand_total'] ?? 0), 0, ',', '.') ?></td>
                    <td class="text-money">Rp <?= number_format((float) ($row['amount_paid'] ?? 0), 0, ',', '.') ?></td>
                    <td class="text-money">Rp <?= number_format((float) ($row['outstanding'] ?? 0), 0, ',', '.') ?></td>
                  </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="7" class="text-center py-4">Belum ada data piutang.</td>
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
