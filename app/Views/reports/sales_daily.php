<?php $this->section('css') ?>
<style>
  .sales-report-page {
    --sr-accent: #0f766e;
    --sr-border: #e5e7eb;
    --sr-soft-text: #64748b;
  }

  .sales-report-page .report-hero {
    border: 1px solid var(--sr-border);
    border-radius: 16px;
    padding: 20px;
    background: linear-gradient(130deg, #f8fafc 0%, #ecfeff 100%);
    box-shadow: 0 10px 24px rgba(15, 23, 42, 0.06);
  }

  .sales-report-page .report-hero h4 {
    margin-bottom: 4px;
    font-weight: 700;
  }

  .sales-report-page .report-hero p {
    margin-bottom: 0;
    color: var(--sr-soft-text);
  }

  .sales-report-page .report-card,
  .sales-report-page .summary-card {
    border: 1px solid var(--sr-border);
    border-radius: 14px;
    box-shadow: 0 8px 20px rgba(15, 23, 42, 0.04);
    margin-bottom: 0;
  }

  .sales-report-page .summary-card .summary-label {
    color: var(--sr-soft-text);
    text-transform: uppercase;
    letter-spacing: 0.04em;
    font-size: 11px;
    margin-bottom: 6px;
  }

  .sales-report-page .summary-card .summary-value {
    font-weight: 700;
    font-size: 24px;
    line-height: 1.1;
    margin-bottom: 0;
    color: #0f172a;
  }

  .sales-report-page .summary-card .summary-meta {
    margin-top: 8px;
    color: var(--sr-soft-text);
    font-size: 12px;
  }

  .sales-report-page .card-header {
    background: #fff;
    border-bottom: 1px solid #f1f5f9;
    border-top-left-radius: 14px;
    border-top-right-radius: 14px;
  }

  .sales-report-page .card-header h4 {
    margin-bottom: 0;
    font-weight: 600;
  }

  .sales-report-page .btn-primary {
    background-color: var(--sr-accent);
    border-color: var(--sr-accent);
  }

  .sales-report-page .btn-primary:hover,
  .sales-report-page .btn-primary:focus {
    background-color: #0d665f;
    border-color: #0d665f;
  }

  .sales-report-page .form-control {
    border-radius: 10px;
    border-color: #dbe3ec;
  }

  .sales-report-page .form-control:focus {
    border-color: #14b8a6;
    box-shadow: 0 0 0 0.2rem rgba(20, 184, 166, 0.12);
  }

  .sales-report-page .table thead th {
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: #475569;
    background: #f8fafc;
    border-bottom: 1px solid #e5e7eb;
  }

  .sales-report-page .table td {
    vertical-align: middle;
  }

  @media (max-width: 768px) {
    .sales-report-page .summary-card {
      margin-bottom: 12px;
    }
  }
</style>
<?= $this->endSection() ?>

<?php
$totalTx = (int) ($summary['total_tx'] ?? 0);
$omzet = (float) ($summary['omzet'] ?? 0);
$totalDiscount = (float) ($summary['total_discount'] ?? 0);
$totalHpp = (float) ($summary['total_hpp'] ?? 0);
$labaKotor = (float) ($summary['laba_kotor'] ?? 0);
$margin = $omzet > 0 ? ($labaKotor / $omzet) * 100 : 0;
?>

<div class="sales-report-page">
  <div class="row mb-4">
    <div class="col-12">
      <div class="report-hero">
        <h4>Laporan Penjualan Harian</h4>
        <p>Pantau performa penjualan, margin, dan status stok untuk tanggal yang dipilih.</p>
      </div>
    </div>
  </div>

  <div class="row mb-4">
    <div class="col-12">
      <div class="card report-card">
        <div class="card-header">
          <h4>Filter Laporan</h4>
        </div>
        <div class="card-body">
          <form method="get" action="<?= base_url('reports/sales-daily') ?>">
            <div class="form-row align-items-end">
              <div class="form-group col-md-4 mb-2 mb-md-0">
                <label>Tanggal</label>
                <input type="date" class="form-control" name="date" value="<?= esc($date) ?>">
              </div>
              <div class="form-group col-md-8 mb-0 d-flex justify-content-md-end">
                <button type="submit" class="btn btn-primary mr-2">Tampilkan</button>
                <a href="<?= base_url('reports/sales-daily') ?>" class="btn btn-light">Reset</a>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <div class="row mb-4">
    <div class="col-12 col-md-6 col-lg-4 mb-3 mb-lg-0">
      <div class="card summary-card">
        <div class="card-body">
          <div class="summary-label">Total Transaksi</div>
          <p class="summary-value"><?= number_format($totalTx, 0, ',', '.') ?></p>
          <div class="summary-meta">Transaksi valid di tanggal terpilih</div>
        </div>
      </div>
    </div>
    <div class="col-12 col-md-6 col-lg-4 mb-3 mb-lg-0">
      <div class="card summary-card">
        <div class="card-body">
          <div class="summary-label">Omzet Harian</div>
          <p class="summary-value">Rp <?= number_format($omzet, 0, ',', '.') ?></p>
          <div class="summary-meta">Total nilai penjualan setelah diskon</div>
        </div>
      </div>
    </div>
    <div class="col-12 col-md-6 col-lg-4">
      <div class="card summary-card">
        <div class="card-body">
          <div class="summary-label">Laba Kotor</div>
          <p class="summary-value">Rp <?= number_format($labaKotor, 0, ',', '.') ?></p>
          <div class="summary-meta">Margin kotor <?= number_format($margin, 2, ',', '.') ?>%</div>
        </div>
      </div>
    </div>
  </div>

  <div class="row mb-4">
    <div class="col-12 col-md-6 col-lg-3 mb-3 mb-lg-0">
      <div class="card summary-card">
        <div class="card-body">
          <div class="summary-label">Total Diskon</div>
          <p class="summary-value">Rp <?= number_format($totalDiscount, 0, ',', '.') ?></p>
        </div>
      </div>
    </div>
    <div class="col-12 col-md-6 col-lg-3">
      <div class="card summary-card">
        <div class="card-body">
          <div class="summary-label">HPP Terjual</div>
          <p class="summary-value">Rp <?= number_format($totalHpp, 0, ',', '.') ?></p>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-12 col-lg-6 mb-4">
      <div class="card report-card h-100">
        <div class="card-header">
          <h4>Breakdown Pembayaran</h4>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-striped mb-0">
              <thead>
                <tr>
                  <th>Metode</th>
                  <th>Transaksi</th>
                  <th>Total</th>
                </tr>
              </thead>
              <tbody>
                <?php if (! empty($paymentBreakdown)): ?>
                  <?php foreach ($paymentBreakdown as $row): ?>
                  <tr>
                    <td><?= esc(strtoupper((string) $row['payment_method'])) ?></td>
                    <td><?= (int) $row['total_tx'] ?></td>
                    <td>Rp <?= number_format((float) $row['total_amount'], 0, ',', '.') ?></td>
                  </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="3" class="text-center">Belum ada data.</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <div class="col-12 col-lg-6 mb-4">
      <div class="card report-card h-100">
        <div class="card-header">
          <h4>Top Produk</h4>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-striped mb-0">
              <thead>
                <tr>
                  <th>Produk</th>
                  <th>Qty</th>
                  <th>Penjualan</th>
                  <th>HPP</th>
                  <th>Laba Kotor</th>
                </tr>
              </thead>
              <tbody>
                <?php if (! empty($topProducts)): ?>
                  <?php foreach ($topProducts as $row): ?>
                  <tr>
                    <td><?= esc((string) $row['product_name']) ?></td>
                    <td><?= (int) $row['total_qty'] ?></td>
                    <td>Rp <?= number_format((float) $row['total_sales'], 0, ',', '.') ?></td>
                    <td>Rp <?= number_format((float) $row['total_hpp'], 0, ',', '.') ?></td>
                    <td>Rp <?= number_format((float) $row['total_profit'], 0, ',', '.') ?></td>
                  </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="5" class="text-center">Belum ada data.</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <div class="col-12">
      <div class="card report-card">
        <div class="card-header">
          <h4>Stok Menipis</h4>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-striped mb-0">
              <thead>
                <tr>
                  <th>Produk</th>
                  <th>Stok</th>
                  <th>Minimal</th>
                </tr>
              </thead>
              <tbody>
                <?php if (! empty($lowStock)): ?>
                  <?php foreach ($lowStock as $row): ?>
                  <tr>
                    <td><?= esc((string) $row['name']) ?></td>
                    <td><span class="badge badge-warning"><?= (int) $row['stock'] ?></span></td>
                    <td><?= (int) $row['min_stock'] ?></td>
                  </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="3" class="text-center">Semua stok aman.</td>
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
