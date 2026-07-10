<?php $this->section('css') ?>
<style>
  .receivables-aging-page {
    --ra-accent: #0f766e;
    --ra-border: #e5e7eb;
    --ra-soft-text: #64748b;
  }

  .receivables-aging-page .hero-card,
  .receivables-aging-page .summary-card,
  .receivables-aging-page .panel-card {
    border: 1px solid var(--ra-border);
    border-radius: 14px;
    box-shadow: 0 8px 20px rgba(15, 23, 42, 0.04);
    background: #fff;
  }

  .receivables-aging-page .hero-card {
    padding: 20px;
    background: linear-gradient(130deg, #f8fafc 0%, #ecfeff 100%);
  }

  .receivables-aging-page .hero-card h4 {
    margin-bottom: 4px;
    font-weight: 700;
  }

  .receivables-aging-page .hero-card p {
    margin-bottom: 0;
    color: var(--ra-soft-text);
  }

  .receivables-aging-page .summary-label {
    color: var(--ra-soft-text);
    text-transform: uppercase;
    letter-spacing: 0.04em;
    font-size: 11px;
    margin-bottom: 6px;
  }

  .receivables-aging-page .summary-value {
    font-size: 24px;
    font-weight: 700;
    margin-bottom: 0;
    color: #0f172a;
  }

  .receivables-aging-page .card-header {
    background: #fff;
    border-bottom: 1px solid #f1f5f9;
  }

  .receivables-aging-page .btn-primary {
    background-color: var(--ra-accent);
    border-color: var(--ra-accent);
  }

  .receivables-aging-page .btn-primary:hover,
  .receivables-aging-page .btn-primary:focus {
    background-color: #0d665f;
    border-color: #0d665f;
  }

  .receivables-aging-page .form-control,
  .receivables-aging-page .custom-select {
    border-radius: 10px;
    border-color: #dbe3ec;
  }

  .receivables-aging-page .table thead th {
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: #475569;
    background: #f8fafc;
    border-bottom: 1px solid #e5e7eb;
  }

  .receivables-aging-page .text-money {
    font-weight: 600;
    color: #0f172a;
    white-space: nowrap;
  }

  .receivables-aging-page .age-chip {
    display: inline-flex;
    align-items: center;
    padding: 4px 10px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 700;
  }

  .receivables-aging-page .age-chip.current {
    background: #dcfce7;
    color: #166534;
  }

  .receivables-aging-page .age-chip.warning {
    background: #ffedd5;
    color: #b45309;
  }

  .receivables-aging-page .age-chip.danger {
    background: #ffe4e6;
    color: #be123c;
  }
</style>
<?= $this->endSection() ?>

<?php
$reportRows = $rows ?? [];
$outstandingTotal = (float) ($summary['outstanding_total'] ?? 0);
$bucketCurrent = (float) ($summary['bucket_current'] ?? 0);
$bucket1To30 = (float) ($summary['bucket_1_30'] ?? 0);
$bucket31To60 = (float) ($summary['bucket_31_60'] ?? 0);
$bucketOver60 = (float) ($summary['bucket_over_60'] ?? 0);
$invoiceCount = (int) ($summary['invoice_count'] ?? 0);
$overdueCount = (int) ($summary['overdue_count'] ?? 0);

$agingLabel = static function (int $days): string {
    if ($days <= 0) {
        return 'Belum Jatuh Tempo';
    }
    if ($days <= 30) {
        return '1-30 Hari';
    }
    if ($days <= 60) {
        return '31-60 Hari';
    }

    return '>60 Hari';
};

$agingClass = static function (int $days): string {
    if ($days <= 0) {
        return 'current';
    }
    if ($days <= 30) {
        return 'warning';
    }

    return 'danger';
};
?>

<div class="receivables-aging-page">
  <div class="row mb-4">
    <div class="col-12">
      <div class="hero-card">
        <h4>Laporan Aging Piutang</h4>
        <p>Analisis umur piutang berdasarkan tanggal jatuh tempo untuk kontrol penagihan.</p>
      </div>
    </div>
  </div>

  <div class="row mb-4">
    <div class="col-12">
      <div class="card panel-card">
        <div class="card-header">
          <h4>Filter Laporan</h4>
        </div>
        <div class="card-body">
          <form method="get" action="<?= base_url('reports/receivables-aging') ?>">
            <div class="form-row align-items-end">
              <div class="form-group col-md-4 mb-2 mb-md-0">
                <label for="as_of_date">Per Tanggal</label>
                <input type="date" id="as_of_date" name="as_of_date" class="form-control" value="<?= esc((string) ($asOfDate ?? date('Y-m-d'))) ?>">
              </div>
              <div class="form-group col-md-8 mb-0 d-flex justify-content-md-end">
                <button type="submit" class="btn btn-primary mr-2">Tampilkan</button>
                <a href="<?= base_url('reports/receivables-aging') ?>" class="btn btn-light">Reset</a>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <div class="row mb-4">
    <div class="col-12 col-md-3 mb-3 mb-md-0">
      <div class="card summary-card"><div class="card-body"><div class="summary-label">Outstanding</div><p class="summary-value">Rp <?= number_format($outstandingTotal, 0, ',', '.') ?></p></div></div>
    </div>
    <div class="col-12 col-md-3 mb-3 mb-md-0">
      <div class="card summary-card"><div class="card-body"><div class="summary-label">Belum Jatuh Tempo</div><p class="summary-value">Rp <?= number_format($bucketCurrent, 0, ',', '.') ?></p></div></div>
    </div>
    <div class="col-12 col-md-3 mb-3 mb-md-0">
      <div class="card summary-card"><div class="card-body"><div class="summary-label">1-30 Hari</div><p class="summary-value">Rp <?= number_format($bucket1To30, 0, ',', '.') ?></p></div></div>
    </div>
    <div class="col-12 col-md-3">
      <div class="card summary-card"><div class="card-body"><div class="summary-label">31+ Hari</div><p class="summary-value">Rp <?= number_format($bucket31To60 + $bucketOver60, 0, ',', '.') ?></p></div></div>
    </div>
  </div>

  <div class="row">
    <div class="col-12">
      <div class="card panel-card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h4 class="mb-0">Detail Invoice Aging</h4>
          <small class="text-muted"><?= number_format($invoiceCount, 0, ',', '.') ?> invoice | <?= number_format($overdueCount, 0, ',', '.') ?> overdue</small>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-striped mb-0">
              <thead>
                <tr>
                  <th>Invoice</th>
                  <th>Pelanggan</th>
                  <th>Jatuh Tempo</th>
                  <th>Umur</th>
                  <th>Outstanding</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php if (! empty($reportRows)): ?>
                  <?php foreach ($reportRows as $row): ?>
                  <?php
                    $days = (int) ($row['aging_days'] ?? 0);
                    $customerLabel = trim((string) ($row['customer_label'] ?? ''));
                    if ($customerLabel === '') {
                        $customerLabel = (string) ($row['customer_name'] ?? '-');
                    }
                  ?>
                  <tr>
                    <td><?= esc((string) ($row['invoice_no'] ?? '-')) ?></td>
                    <td><?= esc($customerLabel !== '' ? $customerLabel : '-') ?></td>
                    <td><?= esc((string) (($row['due_date'] ?? '') ?: '-')) ?></td>
                    <td><span class="age-chip <?= esc($agingClass($days)) ?>"><?= esc($agingLabel($days)) ?></span></td>
                    <td class="text-money">Rp <?= number_format((float) ($row['outstanding'] ?? 0), 0, ',', '.') ?></td>
                    <td>
                      <a href="<?= base_url('receivables/' . (int) ($row['id'] ?? 0)) ?>" class="btn btn-sm btn-outline-primary">Detail</a>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="6" class="text-center py-4">Tidak ada data piutang terbuka.</td>
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
