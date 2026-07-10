<?php $this->section('css') ?>
<style>
  .receivable-detail-page {
    --rd-accent: #0f766e;
    --rd-border: #e5e7eb;
    --rd-soft-text: #64748b;
  }

  .receivable-detail-page .hero-card,
  .receivable-detail-page .summary-card,
  .receivable-detail-page .panel-card {
    border: 1px solid var(--rd-border);
    border-radius: 14px;
    box-shadow: 0 8px 20px rgba(15, 23, 42, 0.04);
    background: #fff;
  }

  .receivable-detail-page .hero-card {
    padding: 20px;
    background: linear-gradient(130deg, #f8fafc 0%, #ecfeff 100%);
  }

  .receivable-detail-page .hero-card h4 {
    margin-bottom: 4px;
    font-weight: 700;
  }

  .receivable-detail-page .hero-card p {
    margin-bottom: 0;
    color: var(--rd-soft-text);
  }

  .receivable-detail-page .card-header {
    background: #fff;
    border-bottom: 1px solid #f1f5f9;
  }

  .receivable-detail-page .summary-label {
    color: var(--rd-soft-text);
    text-transform: uppercase;
    letter-spacing: 0.04em;
    font-size: 11px;
    margin-bottom: 6px;
  }

  .receivable-detail-page .summary-value {
    font-size: 24px;
    font-weight: 700;
    margin-bottom: 0;
    color: #0f172a;
  }

  .receivable-detail-page .form-control,
  .receivable-detail-page .custom-select {
    border-radius: 10px;
    border-color: #dbe3ec;
  }

  .receivable-detail-page .btn-primary {
    background-color: var(--rd-accent);
    border-color: var(--rd-accent);
  }

  .receivable-detail-page .btn-primary:hover,
  .receivable-detail-page .btn-primary:focus {
    background-color: #0d665f;
    border-color: #0d665f;
  }

  .receivable-detail-page .table thead th {
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: #475569;
    background: #f8fafc;
    border-bottom: 1px solid #e5e7eb;
  }

  .receivable-detail-page .text-money {
    font-weight: 600;
    white-space: nowrap;
  }

  .receivable-detail-page .detail-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    padding: 8px 0;
    border-bottom: 1px dashed #e2e8f0;
  }

  .receivable-detail-page .detail-row:last-child {
    border-bottom: 0;
  }

  .receivable-detail-page .detail-label {
    color: var(--rd-soft-text);
    font-size: 13px;
  }

  .receivable-detail-page .detail-value {
    font-weight: 600;
    text-align: right;
  }
</style>
<?= $this->endSection() ?>

<?php
$receivableRow = $receivable ?? [];
$paymentRows = $payments ?? [];
$status = strtolower((string) ($receivableRow['status'] ?? 'open'));
$invoiceNo = (string) ($receivableRow['invoice_no'] ?? '-');
$customerLabel = trim((string) ($receivableRow['customer_label'] ?? ''));
if ($customerLabel === '') {
    $customerLabel = (string) ($receivableRow['customer_name'] ?? '-');
}

$statusLabel = static function (string $value): string {
    $map = [
        'open' => 'Open',
        'partial' => 'Partial',
        'settled' => 'Settled',
        'overdue' => 'Overdue',
        'cancelled' => 'Cancelled',
    ];

    return $map[$value] ?? ucfirst($value);
};

$statusBadgeClass = static function (string $value): string {
    if ($value === 'settled') {
        return 'badge-success';
    }
    if ($value === 'partial') {
        return 'badge-warning';
    }
    if ($value === 'overdue') {
        return 'badge-danger';
    }
    if ($value === 'cancelled') {
        return 'badge-secondary';
    }

    return 'badge-info';
};

$canCollect = activeGroupCan('receivables.collect') && in_array($status, ['open', 'partial', 'overdue'], true);
?>

<div class="receivable-detail-page">
  <div class="row mb-4">
    <div class="col-12">
      <div class="hero-card d-flex justify-content-between align-items-start flex-wrap">
        <div>
          <h4>Detail Piutang</h4>
          <p>Invoice <?= esc($invoiceNo) ?> untuk pelanggan <?= esc($customerLabel !== '' ? $customerLabel : '-') ?>.</p>
        </div>
        <div class="mt-2 mt-md-0">
          <span class="badge <?= $statusBadgeClass($status) ?>"><?= esc($statusLabel($status)) ?></span>
        </div>
      </div>
    </div>
  </div>

  <div class="row mb-4">
    <div class="col-12 col-md-4 mb-3 mb-md-0">
      <div class="card summary-card">
        <div class="card-body">
          <div class="summary-label">Grand Total</div>
          <p class="summary-value">Rp <?= number_format((float) ($receivableRow['grand_total'] ?? 0), 0, ',', '.') ?></p>
        </div>
      </div>
    </div>
    <div class="col-12 col-md-4 mb-3 mb-md-0">
      <div class="card summary-card">
        <div class="card-body">
          <div class="summary-label">Sudah Dibayar</div>
          <p class="summary-value">Rp <?= number_format((float) ($receivableRow['amount_paid'] ?? 0), 0, ',', '.') ?></p>
        </div>
      </div>
    </div>
    <div class="col-12 col-md-4">
      <div class="card summary-card">
        <div class="card-body">
          <div class="summary-label">Sisa Piutang</div>
          <p class="summary-value">Rp <?= number_format((float) ($receivableRow['outstanding'] ?? 0), 0, ',', '.') ?></p>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-12 col-lg-5 mb-4">
      <div class="card panel-card h-100">
        <div class="card-header">
          <h4 class="mb-0">Informasi Piutang</h4>
        </div>
        <div class="card-body">
          <div class="detail-row">
            <span class="detail-label">Invoice</span>
            <span class="detail-value"><?= esc($invoiceNo) ?></span>
          </div>
          <div class="detail-row">
            <span class="detail-label">Pelanggan</span>
            <span class="detail-value"><?= esc($customerLabel !== '' ? $customerLabel : '-') ?></span>
          </div>
          <div class="detail-row">
            <span class="detail-label">Termin</span>
            <span class="detail-value"><?= (int) ($receivableRow['credit_term'] ?? 0) ?> hari</span>
          </div>
          <div class="detail-row">
            <span class="detail-label">Jatuh Tempo</span>
            <span class="detail-value"><?= esc((string) (($receivableRow['due_date'] ?? '') ?: '-')) ?></span>
          </div>
          <div class="detail-row">
            <span class="detail-label">Status</span>
            <span class="detail-value"><span class="badge <?= $statusBadgeClass($status) ?>"><?= esc($statusLabel($status)) ?></span></span>
          </div>
          <div class="detail-row">
            <span class="detail-label">Catatan</span>
            <span class="detail-value"><?= esc((string) (($receivableRow['notes'] ?? '') ?: '-')) ?></span>
          </div>
        </div>
      </div>
    </div>

    <div class="col-12 col-lg-7 mb-4">
      <div class="card panel-card h-100">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h4 class="mb-0">Riwayat Pembayaran</h4>
          <a href="<?= base_url('receivables') ?>" class="btn btn-light btn-sm">Kembali ke daftar</a>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-striped mb-0">
              <thead>
                <tr>
                  <th>Tanggal</th>
                  <th>Ref</th>
                  <th>Metode</th>
                  <th>Status</th>
                  <th>Nominal</th>
                </tr>
              </thead>
              <tbody>
                <?php if (! empty($paymentRows)): ?>
                  <?php foreach ($paymentRows as $payment): ?>
                  <tr>
                    <td><?= esc((string) ($payment['payment_date'] ?? '-')) ?></td>
                    <td><?= esc((string) ($payment['reference_no'] ?? '-')) ?></td>
                    <td><?= esc(strtoupper((string) ($payment['payment_method'] ?? '-'))) ?></td>
                    <td><?= esc(strtoupper((string) ($payment['status'] ?? '-'))) ?></td>
                    <td class="text-money">Rp <?= number_format((float) ($payment['amount'] ?? 0), 0, ',', '.') ?></td>
                  </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="5" class="text-center py-4">Belum ada pembayaran.</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <?php if ($canCollect): ?>
  <div class="row">
    <div class="col-12">
      <div class="card panel-card">
        <div class="card-header">
          <h4 class="mb-0">Catat Pembayaran</h4>
        </div>
        <div class="card-body">
          <form method="post" action="<?= base_url('receivables/' . (int) ($receivableRow['id'] ?? 0) . '/payments') ?>">
            <?= csrf_field() ?>
            <div class="form-row">
              <div class="form-group col-md-3 mb-3 mb-md-0">
                <label for="amount">Nominal</label>
                <input type="number" id="amount" name="amount" class="form-control" min="1" step="0.01" required>
              </div>
              <div class="form-group col-md-3 mb-3 mb-md-0">
                <label for="payment_method">Metode</label>
                <select id="payment_method" name="payment_method" class="form-control custom-select" required>
                  <option value="cash">Tunai</option>
                  <option value="transfer">Transfer</option>
                </select>
              </div>
              <div class="form-group col-md-3 mb-3 mb-md-0">
                <label for="reference_no">Referensi (opsional)</label>
                <input type="text" id="reference_no" name="reference_no" class="form-control" maxlength="50" placeholder="AUTO jika kosong">
              </div>
              <div class="form-group col-md-3 mb-0">
                <label for="notes">Catatan</label>
                <input type="text" id="notes" name="notes" class="form-control" maxlength="255" placeholder="Catatan pembayaran">
              </div>
            </div>
            <div class="mt-3 d-flex justify-content-end">
              <button type="submit" class="btn btn-primary">Simpan Pembayaran</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <?php endif; ?>
</div>
