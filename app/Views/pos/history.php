<?php $this->section('css') ?>
<style>
  .sales-history-page {
    --sh-accent: #0f766e;
    --sh-border: #e5e7eb;
    --sh-soft-text: #64748b;
  }

  .sales-history-page .history-hero {
    border: 1px solid var(--sh-border);
    border-radius: 16px;
    padding: 20px;
    background: linear-gradient(130deg, #f8fafc 0%, #ecfeff 100%);
    box-shadow: 0 10px 24px rgba(15, 23, 42, 0.06);
  }

  .sales-history-page .history-hero h4 {
    margin-bottom: 4px;
    font-weight: 700;
  }

  .sales-history-page .history-hero p {
    margin-bottom: 0;
    color: var(--sh-soft-text);
  }

  .sales-history-page .history-scope-indicator {
    margin-top: 10px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 12px;
    font-weight: 600;
    color: #0f766e;
    background: rgba(20, 184, 166, 0.12);
    border: 1px solid rgba(20, 184, 166, 0.2);
    border-radius: 999px;
    padding: 6px 10px;
  }

  .sales-history-page .panel-card,
  .sales-history-page .summary-card {
    border: 1px solid var(--sh-border);
    border-radius: 14px;
    box-shadow: 0 8px 20px rgba(15, 23, 42, 0.04);
    margin-bottom: 0;
  }

  .sales-history-page .panel-card .card-header,
  .sales-history-page .summary-card .card-header {
    background: #fff;
    border-bottom: 1px solid #f1f5f9;
    border-top-left-radius: 14px;
    border-top-right-radius: 14px;
  }

  .sales-history-page .summary-label {
    color: var(--sh-soft-text);
    text-transform: uppercase;
    letter-spacing: 0.04em;
    font-size: 11px;
    margin-bottom: 6px;
  }

  .sales-history-page .summary-value {
    font-size: 28px;
    font-weight: 700;
    line-height: 1.1;
    margin-bottom: 0;
    color: #0f172a;
  }

  .sales-history-page .summary-meta {
    margin-top: 8px;
    color: var(--sh-soft-text);
    font-size: 12px;
  }

  .sales-history-page .btn-primary {
    background-color: var(--sh-accent);
    border-color: var(--sh-accent);
  }

  .sales-history-page .btn-primary:hover,
  .sales-history-page .btn-primary:focus {
    background-color: #0d665f;
    border-color: #0d665f;
  }

  .sales-history-page .form-control {
    border-radius: 10px;
    border-color: #dbe3ec;
  }

  .sales-history-page .form-control:focus {
    border-color: #14b8a6;
    box-shadow: 0 0 0 0.2rem rgba(20, 184, 166, 0.12);
  }

  .sales-history-page .table thead th {
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: #475569;
    background: #f8fafc;
    border-bottom: 1px solid #e5e7eb;
  }

  .sales-history-page .table td {
    vertical-align: middle;
  }

  .sales-history-page .invoice-code {
    font-weight: 600;
    color: #0f172a;
  }

  .sales-history-page .text-money {
    font-weight: 600;
    color: #0f172a;
    white-space: nowrap;
  }

  @media (max-width: 768px) {
    .sales-history-page .summary-card {
      margin-bottom: 12px;
    }

    .sales-history-page .history-hero {
      border-radius: 12px;
    }
  }
</style>
<?= $this->endSection() ?>

<?php
$viewData = get_defined_vars();
$fromRaw = $viewData['from'] ?? '';
$toRaw = $viewData['to'] ?? '';
$from = is_scalar($fromRaw) ? (string) $fromRaw : '';
$to = is_scalar($toRaw) ? (string) $toRaw : '';
$totalTx = (int) ($summary['total_tx'] ?? 0);
$totalAmount = (float) ($summary['total_amount'] ?? 0);
$avgPerTx = $totalTx > 0 ? ($totalAmount / $totalTx) : 0;
?>

<div class="sales-history-page">
  <div class="row mb-4">
    <div class="col-12">
      <div class="history-hero">
        <h4>Riwayat Penjualan</h4>
        <p>Tinjau transaksi pada rentang tanggal tertentu dengan tampilan yang lebih ringkas dan mudah dibaca.</p>
        <?php if (! empty($isCashierHistoryLimited)): ?>
          <div class="history-scope-indicator">Menampilkan transaksi Anda saja</div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <div class="row mb-4">
    <div class="col-12">
      <div class="card panel-card">
        <div class="card-header">
          <h4>Filter Riwayat</h4>
        </div>
        <div class="card-body">
          <form method="get" action="<?= base_url('pos/history') ?>">
            <div class="form-row align-items-end">
              <div class="form-group col-md-4 mb-2 mb-md-0">
                <label>Dari Tanggal</label>
                <input type="date" class="form-control" name="from" value="<?= esc($from) ?>">
              </div>
              <div class="form-group col-md-4 mb-2 mb-md-0">
                <label>Sampai Tanggal</label>
                <input type="date" class="form-control" name="to" value="<?= esc($to) ?>">
              </div>
              <div class="form-group col-md-4 mb-0 d-flex justify-content-md-end">
                <button type="submit" class="btn btn-primary mr-2">Terapkan</button>
                <a href="<?= base_url('pos/history') ?>" class="btn btn-light">Reset</a>
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
          <div class="summary-label">Total Transaksi</div>
          <p class="summary-value"><?= number_format($totalTx, 0, ',', '.') ?></p>
          <div class="summary-meta">Jumlah transaksi pada periode filter</div>
        </div>
      </div>
    </div>
    <div class="col-12 col-md-4 mb-3 mb-md-0">
      <div class="card summary-card">
        <div class="card-body">
          <div class="summary-label">Total Penjualan</div>
          <p class="summary-value">Rp <?= number_format($totalAmount, 0, ',', '.') ?></p>
          <div class="summary-meta">Akumulasi nilai transaksi</div>
        </div>
      </div>
    </div>
    <div class="col-12 col-md-4">
      <div class="card summary-card">
        <div class="card-body">
          <div class="summary-label">Rata-rata per Transaksi</div>
          <p class="summary-value">Rp <?= number_format($avgPerTx, 0, ',', '.') ?></p>
          <div class="summary-meta">Estimasi nilai rata-rata per invoice</div>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-12">
      <div class="card panel-card">
        <div class="card-header">
          <h4>Daftar Transaksi</h4>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-striped mb-0">
              <thead>
                <tr>
                  <th>Invoice</th>
                  <th>Tanggal</th>
                  <th>Pelanggan</th>
                  <th>Metode Bayar</th>
                  <th>Total</th>
                </tr>
              </thead>
              <tbody>
                <?php if (! empty($sales)): ?>
                  <?php foreach ($sales as $sale): ?>
                  <?php
                    $paymentMethod = strtoupper((string) ($sale['payment_method'] ?? ''));
                    $paymentBadgeClass = $paymentMethod === 'CASH' ? 'badge-success' : 'badge-info';
                  ?>
                  <tr>
                    <td><span class="invoice-code"><?= esc((string) $sale['invoice_no']) ?></span></td>
                    <td><?= esc((string) $sale['sold_at']) ?></td>
                    <td><?= esc((string) ($sale['customer_name'] ?: '-')) ?></td>
                    <td><span class="badge <?= $paymentBadgeClass ?>"><?= esc($paymentMethod ?: '-') ?></span></td>
                    <td class="text-money">Rp <?= number_format((float) $sale['grand_total'], 0, ',', '.') ?></td>
                  </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="5" class="text-center py-4">Belum ada data transaksi.</td>
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
