<?php $this->section('css') ?>
<style>
  .shift-page {
    --sf-primary: #0f766e;
    --sf-primary-dark: #115e59;
    --sf-amber: #b45309;
    --sf-amber-soft: #fffbeb;
    --sf-border: #e2e8f0;
    --sf-muted: #64748b;
  }

  .shift-page {
    width: 100%;
  }

  .shift-page .shift-hero {
    border: 1px solid var(--sf-border);
    border-radius: 20px;
    background: linear-gradient(145deg, #f8fafc 0%, #ecfeff 100%);
    padding: 24px 28px;
    margin-bottom: 18px;
    box-shadow: 0 12px 28px rgba(15, 23, 42, 0.05);
  }

  .shift-page .shift-layout {
    margin-left: -8px;
    margin-right: -8px;
  }

  .shift-page .shift-layout > [class*='col-'] {
    padding-left: 8px;
    padding-right: 8px;
  }

  .shift-page .shift-kicker {
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: var(--sf-muted);
    margin-bottom: 6px;
    font-weight: 700;
  }

  .shift-page .shift-title {
    font-size: 28px;
    font-weight: 700;
    line-height: 1.1;
    margin-bottom: 8px;
    color: #0f172a;
  }

  .shift-page .shift-subtitle {
    margin-bottom: 0;
    color: var(--sf-muted);
  }

  .shift-page .status-card {
    border: 1px solid var(--sf-border);
    border-radius: 16px;
    box-shadow: 0 10px 24px rgba(15, 23, 42, 0.04);
    overflow: hidden;
  }

  .shift-page .status-card .card-header {
    background: #ffffff;
    border-bottom: 1px solid #f1f5f9;
    padding: 16px 20px;
  }

  .shift-page .status-card .card-body {
    padding: 20px;
  }

  .shift-page .status-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 12px;
    font-weight: 600;
    border-radius: 999px;
    padding: 6px 12px;
    border: 1px solid #dbeafe;
    background: #eff6ff;
    color: #1d4ed8;
  }

  .shift-page .status-badge.blocked {
    border-color: #fde68a;
    background: var(--sf-amber-soft);
    color: var(--sf-amber);
  }

  .shift-page .status-badge.active {
    border-color: #99f6e4;
    background: #f0fdfa;
    color: var(--sf-primary);
  }

  .shift-page .status-note {
    color: var(--sf-muted);
    margin-top: 12px;
    margin-bottom: 16px;
  }

  .shift-page .info-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 12px;
    margin-bottom: 18px;
  }

  .shift-page .info-item {
    border: 1px solid #e7edf4;
    border-radius: 12px;
    padding: 12px 14px;
    background: #fcfdff;
  }

  .shift-page .info-item .label {
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    color: var(--sf-muted);
    margin-bottom: 4px;
    font-weight: 700;
  }

  .shift-page .info-item .value {
    font-size: 16px;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 0;
    line-height: 1.25;
  }

  .shift-page .form-control {
    border-radius: 10px;
    border-color: #d8e1ea;
    min-height: 42px;
  }

  .shift-page .input-group-text {
    border-color: #d8e1ea;
    background: #f8fafc;
    color: #334155;
    font-weight: 600;
    min-width: 54px;
    justify-content: center;
  }

  .shift-page .form-control:focus {
    border-color: #14b8a6;
    box-shadow: 0 0 0 0.2rem rgba(20, 184, 166, 0.12);
  }

  .shift-page .btn-primary {
    background-color: var(--sf-primary);
    border-color: var(--sf-primary);
    border-radius: 10px;
    font-weight: 600;
    min-height: 44px;
  }

  .shift-page .btn-primary:hover,
  .shift-page .btn-primary:focus {
    background-color: var(--sf-primary-dark);
    border-color: var(--sf-primary-dark);
  }

  .shift-page .btn-warning {
    border-radius: 10px;
    font-weight: 600;
    min-height: 44px;
  }

  .shift-page .btn-success {
    border-radius: 10px;
    font-weight: 600;
    min-height: 44px;
  }

  .shift-page .helper {
    font-size: 12px;
    color: var(--sf-muted);
    margin-top: 6px;
    margin-bottom: 0;
  }

  .shift-page .history-card {
    border: 1px solid var(--sf-border);
    border-radius: 16px;
    box-shadow: 0 10px 24px rgba(15, 23, 42, 0.04);
    overflow: hidden;
    margin-top: 0;
  }

  .shift-page .history-card .card-header {
    background: #ffffff;
    border-bottom: 1px solid #f1f5f9;
    padding: 16px 20px;
  }

  .shift-page .history-card .card-body {
    padding: 0;
  }

  .shift-page .history-card .table {
    margin-bottom: 0;
  }

  .shift-page .history-card .table thead th {
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: #475569;
    background: #f8fafc;
    border-bottom: 1px solid #e5e7eb;
    white-space: nowrap;
  }

  .shift-page .history-card .table td {
    vertical-align: middle;
    color: #0f172a;
  }

  .shift-page .history-status {
    display: inline-flex;
    align-items: center;
    border-radius: 999px;
    padding: 4px 10px;
    font-size: 12px;
    font-weight: 600;
    border: 1px solid #dbeafe;
    background: #eff6ff;
    color: #1d4ed8;
    white-space: nowrap;
  }

  .shift-page .history-status.closed {
    border-color: #99f6e4;
    background: #f0fdfa;
    color: var(--sf-primary);
  }

  .shift-page .history-status.open {
    border-color: #fde68a;
    background: #fffbeb;
    color: var(--sf-amber);
  }

  .shift-page .money {
    font-weight: 600;
    white-space: nowrap;
  }

  .shift-page .history-filter {
    border-top: 1px solid #f1f5f9;
    background: #fcfdff;
    padding: 14px 20px;
  }

  .shift-page .history-filter .form-control {
    min-height: 38px;
  }

  .shift-page .history-filter .btn {
    min-height: 38px;
  }

  .shift-page .history-filter-actions {
    display: flex;
    justify-content: flex-end;
    flex-wrap: wrap;
    gap: 8px;
  }

  @media (max-width: 768px) {
    .shift-page .shift-hero {
      padding: 18px;
      border-radius: 14px;
    }

    .shift-page .shift-title {
      font-size: 24px;
    }

    .shift-page .status-card .card-body,
    .shift-page .status-card .card-header {
      padding: 16px;
    }

    .shift-page .info-grid {
      grid-template-columns: 1fr;
    }

    .shift-page .history-filter {
      padding: 14px 16px;
    }
  }
</style>
<?= $this->endSection() ?>

<?php
$viewData = get_defined_vars();
$shiftHistoryRaw = $viewData['shiftHistory'] ?? [];
$historyFromRaw = $viewData['historyFrom'] ?? '';
$historyToRaw = $viewData['historyTo'] ?? '';
$historyCashierIdRaw = $viewData['historyCashierId'] ?? 0;
$historyIsManagerUpRaw = $viewData['historyIsManagerUp'] ?? false;
$historyCashierOptionsRaw = $viewData['historyCashierOptions'] ?? [];
$shiftHistory = is_array($shiftHistoryRaw) ? $shiftHistoryRaw : [];
$historyFrom = is_scalar($historyFromRaw) ? (string) $historyFromRaw : '';
$historyTo = is_scalar($historyToRaw) ? (string) $historyToRaw : '';
$historyCashierId = is_scalar($historyCashierIdRaw) ? (int) $historyCashierIdRaw : 0;
$historyIsManagerUp = (bool) $historyIsManagerUpRaw;
$historyCashierOptions = is_array($historyCashierOptionsRaw) ? $historyCashierOptionsRaw : [];
?>

<div class="shift-page">
  <div class="shift-hero">
    <div class="shift-kicker">Kasir</div>
    <h2 class="shift-title">Buka dan Tutup Shift</h2>
    <p class="shift-subtitle">Kelola shift lebih terstruktur sebelum dan sesudah transaksi penjualan.</p>
  </div>

  <div class="row shift-layout">
    <div class="col-12 col-xl-4 mb-3 mb-xl-0">
      <div class="card status-card h-100">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h4 class="mb-0">Status Shift</h4>

          <?php if (! $openShift && empty($otherOpenShift)): ?>
            <span class="status-badge"><i class="fas fa-clock"></i> Siap Dibuka</span>
          <?php elseif (! $openShift && ! empty($otherOpenShift)): ?>
            <span class="status-badge blocked"><i class="fas fa-exclamation-triangle"></i> Menunggu Shift Lain</span>
          <?php else: ?>
            <span class="status-badge active"><i class="fas fa-check-circle"></i> Shift Aktif</span>
          <?php endif; ?>
        </div>

        <div class="card-body">
          <?php if (! $openShift): ?>
            <?php if (! empty($otherOpenShift)): ?>
              <div class="alert alert-warning mb-0">
                Shift tidak dapat dibuka karena masih ada shift aktif dari user lain
                <?php if (! empty($otherOpenShiftOwner)): ?>
                  <strong><?= esc($otherOpenShiftOwner) ?></strong>
                <?php endif; ?>.
              </div>
            <?php else: ?>
              <p class="status-note">Masukkan kas awal untuk mulai shift. Setelah shift aktif, Anda bisa langsung memproses transaksi di POS.</p>

              <form action="<?= base_url('pos/open-shift') ?>" method="post">
                <?= csrf_field() ?>
                <div class="form-group">
                  <label for="opening_cash">Kas Awal</label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text">Rp</span>
                    </div>
                    <input id="opening_cash" type="number" step="0.01" min="0" name="opening_cash" class="form-control" placeholder="Contoh: 100000" required>
                  </div>
                  <p class="helper">Isi nominal kas yang ada di laci saat memulai shift.</p>
                </div>

                <button type="submit" class="btn btn-primary btn-block">
                  <i class="fas fa-door-open mr-1"></i> Buka Shift Sekarang
                </button>
              </form>
            <?php endif; ?>
          <?php else: ?>
            <div class="info-grid">
              <div class="info-item">
                <div class="label">Dibuka Pada</div>
                <p class="value"><?= esc((string) ($openShift['opened_at'] ?? '-')) ?></p>
              </div>
              <div class="info-item">
                <div class="label">Kas Awal</div>
                <p class="value">Rp <?= number_format((float) $openShift['opening_cash'], 0, ',', '.') ?></p>
              </div>
            </div>

            <form action="<?= base_url('pos/close-shift') ?>" method="post">
              <?= csrf_field() ?>
              <div class="form-group">
                <label for="closing_cash_actual">Kas Fisik Akhir</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">Rp</span>
                  </div>
                  <input id="closing_cash_actual" type="number" step="0.01" min="0" name="closing_cash_actual" class="form-control" placeholder="Contoh: 275000" required>
                </div>
                <p class="helper">Hitung kas fisik aktual sebelum menutup shift.</p>
              </div>
              <div class="form-group">
                <label for="notes">Catatan</label>
                <input id="notes" type="text" name="notes" class="form-control" placeholder="Opsional, misal ada selisih kas">
              </div>

              <button type="submit" class="btn btn-warning btn-block">
                <i class="fas fa-door-closed mr-1"></i> Tutup Shift
              </button>
            </form>

            <a href="<?= base_url('pos') ?>" class="btn btn-success btn-block mt-3">
              <i class="fas fa-cash-register mr-1"></i> Lanjut ke POS Transaksi
            </a>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <div class="col-12 col-xl-8">
      <div class="card history-card h-100">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h4 class="mb-0">History Shift</h4>
          <small class="text-muted">Maks. 50 data terbaru</small>
        </div>
        <div class="history-filter">
          <form method="get" action="<?= base_url('pos/shift') ?>">
            <div class="form-row align-items-end">
              <?php if ($historyIsManagerUp): ?>
              <div class="form-group col-lg-4 mb-2 mb-lg-0">
                <label class="mb-1">Kasir</label>
                <select class="form-control" name="cashier_id">
                  <option value="0">Semua Kasir</option>
                  <?php foreach ($historyCashierOptions as $cashier): ?>
                    <?php
                      $optionId = (int) ($cashier['id'] ?? 0);
                      $optionLabel = (string) ($cashier['label'] ?? ('User ID ' . $optionId));
                    ?>
                    <option value="<?= $optionId ?>" <?= $historyCashierId === $optionId ? 'selected' : '' ?>><?= esc($optionLabel) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <?php endif; ?>

              <div class="form-group <?= $historyIsManagerUp ? 'col-lg-3' : 'col-lg-4' ?> mb-2 mb-lg-0">
                <label class="mb-1">Dari Tanggal</label>
                <input type="date" class="form-control" name="from" value="<?= esc($historyFrom) ?>">
              </div>
              <div class="form-group <?= $historyIsManagerUp ? 'col-lg-3' : 'col-lg-4' ?> mb-2 mb-lg-0">
                <label class="mb-1">Sampai Tanggal</label>
                <input type="date" class="form-control" name="to" value="<?= esc($historyTo) ?>">
              </div>
              <div class="form-group <?= $historyIsManagerUp ? 'col-lg-2' : 'col-lg-4' ?> mb-0 history-filter-actions">
                <button type="submit" class="btn btn-primary">Terapkan</button>
                <a href="<?= base_url('pos/shift') ?>" class="btn btn-light">Reset</a>
              </div>
            </div>
          </form>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-striped mb-0">
              <thead>
                <tr>
                  <?php if ($historyIsManagerUp): ?>
                    <th>Kasir</th>
                  <?php endif; ?>
                  <th>Dibuka</th>
                  <th>Ditutup</th>
                  <th>Status</th>
                  <th>Kas Awal</th>
                  <th>Kas Sistem</th>
                  <th>Kas Aktual</th>
                  <th>Selisih</th>
                  <th>Catatan</th>
                </tr>
              </thead>
              <tbody>
                <?php if (! empty($shiftHistory)): ?>
                  <?php foreach ($shiftHistory as $shift): ?>
                    <?php
                      $isClosed = ! empty($shift['closed_at']);
                      $statusClass = $isClosed ? 'closed' : 'open';
                      $statusLabel = $isClosed ? 'Tutup' : 'Aktif';
                    ?>
                    <tr>
                      <?php if ($historyIsManagerUp): ?>
                        <td><?= esc((string) ($shift['cashier_identity'] ?? ('User ID ' . (int) ($shift['user_id'] ?? 0)))) ?></td>
                      <?php endif; ?>
                      <td><?= esc((string) ($shift['opened_at'] ?? '-')) ?></td>
                      <td><?= esc((string) ($shift['closed_at'] ?? '-')) ?></td>
                      <td><span class="history-status <?= $statusClass ?>"><?= esc($statusLabel) ?></span></td>
                      <td class="money">Rp <?= number_format((float) ($shift['opening_cash'] ?? 0), 0, ',', '.') ?></td>
                      <td class="money">Rp <?= number_format((float) ($shift['closing_cash_system'] ?? 0), 0, ',', '.') ?></td>
                      <td class="money">Rp <?= number_format((float) ($shift['closing_cash_actual'] ?? 0), 0, ',', '.') ?></td>
                      <td class="money">Rp <?= number_format((float) ($shift['variance'] ?? 0), 0, ',', '.') ?></td>
                      <td><?= esc((string) ($shift['notes'] ?? '-')) ?></td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="<?= $historyIsManagerUp ? '9' : '8' ?>" class="text-center py-4 text-muted">Belum ada history shift.</td>
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
