<?php
$currentUser = auth()->user();
$groups = $currentUser->getGroups();
$groupLabel = activeGroupTitle();

$fmtCurrency = static function ($value): string {
    return 'Rp ' . number_format((float) $value, 0, ',', '.');
};

$fmtNumber = static function ($value): string {
    return number_format((float) $value, 0, ',', '.');
};

$deltaClass = static function (array $delta): string {
    $direction = $delta['direction'] ?? 'flat';

    if ($direction === 'up') {
        return 'is-up';
    }

    if ($direction === 'down') {
        return 'is-down';
    }

    return 'is-flat';
};

$deltaText = static function (array $delta) use ($fmtNumber): string {
    $percent = $delta['percent'] ?? null;
    $direction = $delta['direction'] ?? 'flat';

    if ($percent === null) {
        return ($delta['difference'] ?? 0) > 0 ? 'Mulai ada aktivitas hari ini' : 'Belum ada perubahan dari kemarin';
    }

    $prefix = $direction === 'up' ? 'Naik ' : ($direction === 'down' ? 'Turun ' : 'Stabil ');

    return $prefix . $fmtNumber(abs($percent)) . '% vs kemarin';
};

/** @var array<string, mixed> $todaySummary */
$todaySummary = $todaySummary ?? [];
/** @var array<string, mixed> $cards */
$cards = $cards ?? [];
/** @var array<string, mixed> $stockSummary */
$stockSummary = $stockSummary ?? [];
/** @var array<string, mixed>|null $openShift */
$openShift = $openShift ?? null;
/** @var array{totals: list<array<string, mixed>>} $paymentBreakdown */
$paymentBreakdown = $paymentBreakdown ?? ['totals' => []];
/** @var list<array<string, mixed>> $topProducts */
$topProducts = $topProducts ?? [];
/** @var list<array<string, mixed>> $lowStockProducts */
$lowStockProducts = $lowStockProducts ?? [];
$pendingCount = (int) ($pendingCount ?? 0);
/** @var array<string, mixed> $charts */
$charts = $charts ?? [];
?>

<?php $this->section('css') ?>
<style>
  .pos-dashboard {
    --pd-ink: #0f172a;
    --pd-text: #334155;
    --pd-muted: #64748b;
    --pd-border: #e2e8f0;
    --pd-surface: #ffffff;
    --pd-soft: #f8fafc;
    --pd-warning: #b45309;
    --pd-warning-soft: #ffedd5;
    --pd-danger: #be123c;
    --pd-danger-soft: #ffe4e6;
    --pd-accent: #1d4ed8;
    --pd-accent-soft: #dbeafe;
  }

  .pos-dashboard .dashboard-hero {
    border: 1px solid var(--pd-border);
    border-radius: 20px;
    background:
      radial-gradient(circle at top right, rgba(29, 78, 216, 0.14), transparent 34%),
      linear-gradient(135deg, #ffffff 0%, #f8fafc 55%, #ecfeff 100%);
    padding: 24px;
    box-shadow: 0 18px 40px rgba(15, 23, 42, 0.06);
    margin-bottom: 18px;
  }

  .pos-dashboard .dashboard-eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 6px 10px;
    border-radius: 999px;
    background: #ecfeff;
    color: #0f766e;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    margin-bottom: 12px;
  }

  .pos-dashboard .dashboard-title {
    margin: 0 0 8px;
    font-size: 29px;
    line-height: 1.15;
    color: var(--pd-ink);
    font-weight: 800;
  }

  .pos-dashboard .dashboard-subtitle {
    margin: 0;
    color: var(--pd-text);
    max-width: 760px;
  }

  .pos-dashboard .hero-pills {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 16px;
  }

  .pos-dashboard .hero-pill {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    border: 1px solid var(--pd-border);
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.85);
    padding: 8px 12px;
    color: var(--pd-text);
    font-size: 13px;
    font-weight: 600;
  }

  .pos-dashboard .hero-pill strong {
    color: var(--pd-ink);
  }

  .pos-dashboard .metric-card,
  .pos-dashboard .dashboard-panel {
    border: 1px solid var(--pd-border);
    border-radius: 18px;
    background: var(--pd-surface);
    box-shadow: 0 14px 30px rgba(15, 23, 42, 0.05);
  }

  .pos-dashboard .metric-card {
    padding: 18px;
    height: 100%;
  }

  .pos-dashboard .metric-label {
    color: var(--pd-muted);
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    margin-bottom: 8px;
  }

  .pos-dashboard .metric-value {
    color: var(--pd-ink);
    font-size: 30px;
    line-height: 1.1;
    font-weight: 800;
    margin-bottom: 10px;
  }

  .pos-dashboard .metric-meta {
    display: flex;
    justify-content: space-between;
    gap: 12px;
    font-size: 13px;
    color: var(--pd-text);
    margin-bottom: 12px;
  }

  .pos-dashboard .delta-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    border-radius: 999px;
    padding: 6px 10px;
    font-size: 12px;
    font-weight: 700;
  }

  .pos-dashboard .delta-badge.is-up {
    background: #dcfce7;
    color: #166534;
  }

  .pos-dashboard .delta-badge.is-down {
    background: #fee2e2;
    color: #991b1b;
  }

  .pos-dashboard .delta-badge.is-flat {
    background: #e2e8f0;
    color: #475569;
  }

  .pos-dashboard .dashboard-panel {
    padding: 18px;
    height: 100%;
  }

  .pos-dashboard .panel-head {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 16px;
  }

  .pos-dashboard .panel-title {
    margin: 0 0 4px;
    color: var(--pd-ink);
    font-size: 18px;
    font-weight: 800;
  }

  .pos-dashboard .panel-caption {
    margin: 0;
    color: var(--pd-muted);
    font-size: 13px;
  }

  .pos-dashboard .panel-chip {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 10px;
    border-radius: 999px;
    background: var(--pd-soft);
    color: var(--pd-text);
    font-size: 12px;
    font-weight: 700;
    white-space: nowrap;
  }

  .pos-dashboard .chart-shell {
    position: relative;
    min-height: 300px;
  }

  .pos-dashboard .chart-shell.chart-shell-sm {
    min-height: 240px;
  }

  .pos-dashboard .shift-box {
    border-radius: 16px;
    padding: 16px;
    background: linear-gradient(135deg, #0f766e 0%, #115e59 100%);
    color: #fff;
    margin-bottom: 14px;
  }

  .pos-dashboard .shift-box.is-idle {
    background: linear-gradient(135deg, #475569 0%, #334155 100%);
  }

  .pos-dashboard .shift-status {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 6px 10px;
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.14);
    font-size: 12px;
    font-weight: 700;
    margin-bottom: 12px;
  }

  .pos-dashboard .shift-box h5 {
    margin: 0 0 8px;
    font-size: 20px;
    font-weight: 800;
  }

  .pos-dashboard .shift-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 10px;
    margin-top: 14px;
  }

  .pos-dashboard .shift-item {
    border: 1px solid rgba(255, 255, 255, 0.14);
    border-radius: 14px;
    padding: 12px;
    background: rgba(255, 255, 255, 0.08);
  }

  .pos-dashboard .shift-item-label {
    display: block;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    opacity: 0.78;
    margin-bottom: 4px;
  }

  .pos-dashboard .shift-item-value {
    display: block;
    font-size: 15px;
    font-weight: 800;
  }

  .pos-dashboard .payment-list,
  .pos-dashboard .signal-list {
    display: grid;
    gap: 10px;
  }

  .pos-dashboard .payment-row,
  .pos-dashboard .signal-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    border: 1px solid var(--pd-border);
    border-radius: 14px;
    background: var(--pd-soft);
    padding: 12px 14px;
  }

  .pos-dashboard .payment-row strong,
  .pos-dashboard .signal-item strong {
    color: var(--pd-ink);
  }

  .pos-dashboard .payment-row small,
  .pos-dashboard .signal-item small {
    display: block;
    color: var(--pd-muted);
    margin-top: 2px;
  }

  .pos-dashboard .dashboard-table {
    width: 100%;
    margin-bottom: 0;
  }

  .pos-dashboard .dashboard-table th {
    color: var(--pd-muted);
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    border-top: none;
    border-bottom: 1px solid var(--pd-border);
    padding: 0 0 12px;
  }

  .pos-dashboard .dashboard-table td {
    color: var(--pd-text);
    vertical-align: middle;
    border-top: 1px solid #f1f5f9;
    padding: 12px 0;
  }

  .pos-dashboard .table-rank {
    width: 32px;
    height: 32px;
    border-radius: 999px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: var(--pd-accent-soft);
    color: var(--pd-accent);
    font-size: 13px;
    font-weight: 800;
  }

  .pos-dashboard .stock-badge {
    display: inline-flex;
    align-items: center;
    border-radius: 999px;
    padding: 5px 10px;
    font-size: 12px;
    font-weight: 700;
  }

  .pos-dashboard .stock-badge.is-critical {
    background: var(--pd-danger-soft);
    color: var(--pd-danger);
  }

  .pos-dashboard .stock-badge.is-warning {
    background: var(--pd-warning-soft);
    color: var(--pd-warning);
  }

  .pos-dashboard .quick-actions {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 12px;
  }

  .pos-dashboard .quick-action {
    border: 1px solid var(--pd-border);
    border-radius: 16px;
    background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
    padding: 16px;
    text-decoration: none;
    color: inherit;
    transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease;
  }

  .pos-dashboard .quick-action:hover {
    text-decoration: none;
    transform: translateY(-2px);
    box-shadow: 0 14px 28px rgba(15, 23, 42, 0.08);
    border-color: #cbd5e1;
  }

  .pos-dashboard .quick-action-icon {
    width: 42px;
    height: 42px;
    border-radius: 12px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 12px;
    font-size: 18px;
    color: #fff;
  }

  .pos-dashboard .quick-action h6 {
    margin: 0 0 4px;
    color: var(--pd-ink);
    font-size: 15px;
    font-weight: 800;
  }

  .pos-dashboard .quick-action p {
    margin: 0;
    color: var(--pd-muted);
    font-size: 13px;
  }

  .pos-dashboard .account-list {
    display: grid;
    gap: 10px;
  }

  .pos-dashboard .account-row {
    display: flex;
    justify-content: space-between;
    gap: 12px;
    border: 1px solid var(--pd-border);
    border-radius: 14px;
    padding: 12px 14px;
    background: var(--pd-soft);
  }

  .pos-dashboard .account-row span {
    color: var(--pd-muted);
    font-size: 13px;
  }

  .pos-dashboard .account-row strong {
    color: var(--pd-ink);
    text-align: right;
  }

  .pos-dashboard .role-badges {
    display: flex;
    flex-wrap: wrap;
    justify-content: flex-end;
    gap: 6px;
  }

  .pos-dashboard .role-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    border-radius: 999px;
    padding: 6px 10px;
    background: #e2e8f0;
    color: #334155;
    font-size: 12px;
    font-weight: 700;
  }

  .pos-dashboard .role-badge.is-active {
    background: #ccfbf1;
    color: #0f766e;
  }

  .pos-dashboard .empty-state {
    border: 1px dashed #cbd5e1;
    border-radius: 16px;
    padding: 20px;
    text-align: center;
    color: var(--pd-muted);
    background: #fcfdff;
  }

  @media (max-width: 991.98px) {
    .pos-dashboard .dashboard-title {
      font-size: 24px;
    }

    .pos-dashboard .shift-grid,
    .pos-dashboard .quick-actions {
      grid-template-columns: 1fr;
    }
  }

  @media (max-width: 575.98px) {
    .pos-dashboard .dashboard-hero,
    .pos-dashboard .metric-card,
    .pos-dashboard .dashboard-panel {
      padding: 16px;
    }

    .pos-dashboard .metric-value {
      font-size: 24px;
    }

    .pos-dashboard .panel-head {
      flex-direction: column;
      align-items: flex-start;
    }

    .pos-dashboard .hero-pills {
      flex-direction: column;
      align-items: flex-start;
    }

    .pos-dashboard .account-row {
      flex-direction: column;
    }

    .pos-dashboard .role-badges {
      justify-content: flex-start;
    }
  }
</style>
<?= $this->endSection() ?>

<div class="pos-dashboard">
  <div class="dashboard-hero">
    <span class="dashboard-eyebrow"><i class="fas fa-cash-register"></i> Ringkasan Operasional</span>
    <h2 class="dashboard-title">Pantau penjualan, ritme transaksi, dan stok tanpa pindah halaman.</h2>
    <p class="dashboard-subtitle">
      Dashboard ini difokuskan untuk kebutuhan Point of Sales: membaca omzet hari ini, melihat jam ramai,
      mengawasi metode pembayaran, menindak transaksi tertunda, dan mendeteksi stok yang perlu segera diisi ulang.
    </p>

    <div class="hero-pills">
      <span class="hero-pill"><i class="far fa-calendar-alt"></i> Hari ini <strong><?= esc($todayLabel ?? '-') ?></strong></span>
      <span class="hero-pill"><i class="far fa-user"></i> Login sebagai <strong><?= esc($groupLabel) ?></strong></span>
      <span class="hero-pill"><i class="fas fa-receipt"></i> <?= esc($fmtNumber($todaySummary['total_tx'] ?? 0)) ?> transaksi</span>
      <span class="hero-pill"><i class="fas fa-box-open"></i> <?= esc($fmtNumber($todaySummary['items_sold'] ?? 0)) ?> item terjual</span>
    </div>
  </div>

  <div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
      <div class="metric-card">
        <div class="metric-label">Omzet Hari Ini</div>
        <div class="metric-value"><?= esc($fmtCurrency($cards['revenue']['value'] ?? 0)) ?></div>
        <div class="metric-meta">
          <span>Diskon</span>
          <strong><?= esc($fmtCurrency($todaySummary['total_discount'] ?? 0)) ?></strong>
        </div>
        <span class="delta-badge <?= esc($deltaClass($cards['revenue']['delta'] ?? [])) ?>">
          <i class="fas fa-chart-line"></i> <?= esc($deltaText($cards['revenue']['delta'] ?? [])) ?>
        </span>
      </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
      <div class="metric-card">
        <div class="metric-label">Transaksi Hari Ini</div>
        <div class="metric-value"><?= esc($fmtNumber($cards['transactions']['value'] ?? 0)) ?></div>
        <div class="metric-meta">
          <span>Rata-rata basket</span>
          <strong><?= esc($fmtCurrency($todaySummary['avg_basket'] ?? 0)) ?></strong>
        </div>
        <span class="delta-badge <?= esc($deltaClass($cards['transactions']['delta'] ?? [])) ?>">
          <i class="fas fa-exchange-alt"></i> <?= esc($deltaText($cards['transactions']['delta'] ?? [])) ?>
        </span>
      </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
      <div class="metric-card">
        <div class="metric-label">Laba Kotor Hari Ini</div>
        <div class="metric-value"><?= esc($fmtCurrency($cards['profit']['value'] ?? 0)) ?></div>
        <div class="metric-meta">
          <span>Margin kotor</span>
          <strong><?= esc($fmtNumber($todaySummary['gross_margin_pct'] ?? 0)) ?>%</strong>
        </div>
        <span class="delta-badge <?= esc($deltaClass($cards['profit']['delta'] ?? [])) ?>">
          <i class="fas fa-percentage"></i> <?= esc($deltaText($cards['profit']['delta'] ?? [])) ?>
        </span>
      </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
      <div class="metric-card">
        <div class="metric-label">Alarm Stok</div>
        <div class="metric-value"><?= esc($fmtNumber($stockSummary['low_stock_count'] ?? 0)) ?></div>
        <div class="metric-meta">
          <span>Stok habis</span>
          <strong><?= esc($fmtNumber($stockSummary['zero_stock_count'] ?? 0)) ?> produk</strong>
        </div>
        <span class="delta-badge is-flat">
          <i class="fas fa-exclamation-triangle"></i> <?= esc($fmtNumber($pendingCount)) ?> transaksi masih tertunda
        </span>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-lg-8 mb-4">
      <div class="dashboard-panel">
        <div class="panel-head">
          <div>
            <h3 class="panel-title">Tren Penjualan 7 Hari</h3>
            <p class="panel-caption">Pantau omzet harian dan jumlah transaksi untuk membaca performa mingguan dengan cepat.</p>
          </div>
          <span class="panel-chip"><i class="fas fa-clock"></i> Update berdasarkan transaksi tersimpan</span>
        </div>
        <div class="chart-shell">
          <canvas id="weeklyTrendChart"></canvas>
        </div>
      </div>
    </div>

    <div class="col-lg-4 mb-4">
      <div class="dashboard-panel">
        <div class="panel-head">
          <div>
            <h3 class="panel-title">Shift & Sinyal Operasional</h3>
            <p class="panel-caption">Fokus pada status kasir, kas laci, dan pekerjaan yang masih tertunda.</p>
          </div>
        </div>

        <div class="shift-box<?= $openShift ? '' : ' is-idle' ?>">
          <?php if ($openShift): ?>
            <span class="shift-status"><i class="fas fa-circle"></i> Shift aktif</span>
            <h5>Shift sudah berjalan <?= esc((string) ($openShift['duration_text'] ?? '-')) ?></h5>
            <p class="mb-0" style="opacity:.82;">Kas operasional saat ini dihitung dari kas awal ditambah penjualan tunai pada shift aktif.</p>
            <div class="shift-grid">
              <div class="shift-item">
                <span class="shift-item-label">Kas Awal</span>
                <span class="shift-item-value"><?= esc($fmtCurrency($openShift['opening_cash'] ?? 0)) ?></span>
              </div>
              <div class="shift-item">
                <span class="shift-item-label">Kas di Laci</span>
                <span class="shift-item-value"><?= esc($fmtCurrency($openShift['cash_in_drawer'] ?? 0)) ?></span>
              </div>
              <div class="shift-item">
                <span class="shift-item-label">Transaksi Shift</span>
                <span class="shift-item-value"><?= esc($fmtNumber($openShift['total_tx'] ?? 0)) ?></span>
              </div>
              <div class="shift-item">
                <span class="shift-item-label">Nilai Penjualan</span>
                <span class="shift-item-value"><?= esc($fmtCurrency($openShift['total_amount'] ?? 0)) ?></span>
              </div>
            </div>
          <?php else: ?>
            <span class="shift-status"><i class="fas fa-moon"></i> Belum ada shift aktif</span>
            <h5>Kasir belum membuka shift hari ini</h5>
            <p class="mb-0" style="opacity:.82;">Buka shift sebelum mulai transaksi supaya kontrol kas dan laporan harian tetap rapi.</p>
          <?php endif; ?>
        </div>

        <div class="signal-list">
          <div class="signal-item">
            <div>
              <strong>Transaksi tertunda</strong>
              <small>Perlu diputuskan: lanjutkan atau batalkan</small>
            </div>
            <strong><?= esc($fmtNumber($pendingCount)) ?></strong>
          </div>
          <div class="signal-item">
            <div>
              <strong>Produk aktif</strong>
              <small>Basis item yang saat ini siap dijual</small>
            </div>
            <strong><?= esc($fmtNumber($stockSummary['active_product_count'] ?? 0)) ?></strong>
          </div>
          <div class="signal-item">
            <div>
              <strong>Produk stok kritis</strong>
              <small>Perlu restock sebelum memengaruhi penjualan</small>
            </div>
            <strong><?= esc($fmtNumber($stockSummary['low_stock_count'] ?? 0)) ?></strong>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-lg-7 mb-4">
      <div class="dashboard-panel">
        <div class="panel-head">
          <div>
            <h3 class="panel-title">Jam Ramai Hari Ini</h3>
            <p class="panel-caption">Gunakan pola jam transaksi untuk mengatur kasir, stok display, dan waktu persiapan.</p>
          </div>
          <span class="panel-chip"><i class="fas fa-hourglass-half"></i> Distribusi omzet per jam</span>
        </div>
        <div class="chart-shell chart-shell-sm">
          <canvas id="hourlyTrendChart"></canvas>
        </div>
      </div>
    </div>

    <div class="col-lg-5 mb-4">
      <div class="dashboard-panel">
        <div class="panel-head">
          <div>
            <h3 class="panel-title">Metode Pembayaran</h3>
            <p class="panel-caption">Pastikan komposisi tunai dan transfer sesuai alur operasional kas.</p>
          </div>
        </div>

        <div class="chart-shell chart-shell-sm mb-3">
          <canvas id="paymentBreakdownChart"></canvas>
        </div>

        <div class="payment-list">
          <?php if (! empty($paymentBreakdown['totals'])): ?>
            <?php foreach ($paymentBreakdown['totals'] as $row): ?>
              <div class="payment-row">
                <div>
                  <strong><?= esc((string) ($row['label'] ?? '-')) ?></strong>
                  <small><?= esc($fmtNumber($row['total_tx'] ?? 0)) ?> transaksi</small>
                </div>
                <strong><?= esc($fmtCurrency($row['total_amount'] ?? 0)) ?></strong>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="empty-state">Belum ada pembayaran tersimpan hari ini.</div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-lg-7 mb-4">
      <div class="dashboard-panel">
        <div class="panel-head">
          <div>
            <h3 class="panel-title">Produk Terlaris 7 Hari</h3>
            <p class="panel-caption">Produk teratas membantu keputusan display, promo cepat, dan prioritas restock.</p>
          </div>
        </div>

        <?php if (! empty($topProducts)): ?>
          <div class="table-responsive">
            <table class="dashboard-table table">
              <thead>
                <tr>
                  <th style="width:52px;">#</th>
                  <th>Produk</th>
                  <th class="text-right">Qty</th>
                  <th class="text-right">Omzet</th>
                  <th class="text-right">Laba Kotor</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($topProducts as $index => $row): ?>
                  <tr>
                    <td><span class="table-rank"><?= esc((string) ($index + 1)) ?></span></td>
                    <td>
                      <strong><?= esc((string) ($row['product_name'] ?? '-')) ?></strong>
                    </td>
                    <td class="text-right"><?= esc($fmtNumber($row['total_qty'] ?? 0)) ?></td>
                    <td class="text-right"><?= esc($fmtCurrency($row['total_sales'] ?? 0)) ?></td>
                    <td class="text-right"><?= esc($fmtCurrency($row['total_profit'] ?? 0)) ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php else: ?>
          <div class="empty-state">Belum ada data penjualan untuk 7 hari terakhir.</div>
        <?php endif; ?>
      </div>
    </div>

    <div class="col-lg-5 mb-4">
      <div class="dashboard-panel">
        <div class="panel-head">
          <div>
            <h3 class="panel-title">Stok Prioritas Restock</h3>
            <p class="panel-caption">Daftar pendek ini sebaiknya dipantau setiap hari agar item fast-moving tidak kosong.</p>
          </div>
        </div>

        <?php if (! empty($lowStockProducts)): ?>
          <div class="table-responsive">
            <table class="dashboard-table table">
              <thead>
                <tr>
                  <th>Produk</th>
                  <th class="text-right">Stok</th>
                  <th class="text-right">Target Min.</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($lowStockProducts as $row): ?>
                  <?php $isCritical = (int) ($row['stock'] ?? 0) <= 0; ?>
                  <tr>
                    <td>
                      <strong><?= esc((string) ($row['name'] ?? '-')) ?></strong>
                      <small class="d-block text-muted"><?= esc((string) ($row['category'] ?? 'Tanpa kategori')) ?></small>
                    </td>
                    <td class="text-right">
                      <span class="stock-badge <?= $isCritical ? 'is-critical' : 'is-warning' ?>">
                        <?= esc($fmtNumber($row['stock'] ?? 0)) ?> <?= esc((string) ($row['unit'] ?? 'pcs')) ?>
                      </span>
                    </td>
                    <td class="text-right"><?= esc($fmtNumber($row['min_stock'] ?? 0)) ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php else: ?>
          <div class="empty-state">Belum ada produk yang berada di bawah batas minimum stok.</div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-lg-6 mb-4">
      <div class="dashboard-panel">
        <div class="panel-head">
          <div>
            <h3 class="panel-title">Akses Cepat</h3>
            <p class="panel-caption">Shortcut ke area yang paling sering dipakai saat operasional toko berjalan.</p>
          </div>
        </div>

        <div class="quick-actions">
          <?php if (activeGroupCan('sales.create')): ?>
            <a href="<?= base_url('pos') ?>" class="quick-action">
              <span class="quick-action-icon" style="background:#0f766e;"><i class="fas fa-cash-register"></i></span>
              <h6>Buka POS</h6>
              <p>Mulai transaksi baru, kelola cart, dan lanjut ke pembayaran.</p>
            </a>
          <?php endif; ?>

          <?php if (activeGroupCan('sales.list')): ?>
            <a href="<?= base_url('pos/history') ?>" class="quick-action">
              <span class="quick-action-icon" style="background:#1d4ed8;"><i class="fas fa-receipt"></i></span>
              <h6>Riwayat Penjualan</h6>
              <p>Lihat transaksi yang sudah tersimpan berdasarkan rentang tanggal.</p>
            </a>
          <?php endif; ?>

          <?php if (activeGroupCan('reports.view')): ?>
            <a href="<?= base_url('reports/sales-daily') ?>" class="quick-action">
              <span class="quick-action-icon" style="background:#b45309;"><i class="fas fa-chart-line"></i></span>
              <h6>Laporan Harian</h6>
              <p>Analisis detail omzet, laba kotor, metode bayar, dan produk laris.</p>
            </a>
          <?php endif; ?>

          <?php if (activeGroupCan('products.list')): ?>
            <a href="<?= base_url('admin/products') ?>" class="quick-action">
              <span class="quick-action-icon" style="background:#7c3aed;"><i class="fas fa-boxes"></i></span>
              <h6>Kelola Produk</h6>
              <p>Perbarui harga jual, stok, kategori, dan batas minimum persediaan.</p>
            </a>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <div class="col-lg-6 mb-4">
      <div class="dashboard-panel">
        <div class="panel-head">
          <div>
            <h3 class="panel-title">Informasi Akun</h3>
            <p class="panel-caption">Ringkasan user aktif untuk memastikan akses operasional sesuai role yang dipakai.</p>
          </div>
        </div>

        <div class="account-list">
          <div class="account-row">
            <span>Username</span>
            <strong><?= esc($currentUser->username) ?></strong>
          </div>
          <div class="account-row">
            <span>Email</span>
            <strong><?= esc($currentUser->email) ?></strong>
          </div>
          <div class="account-row">
            <span>Role aktif</span>
            <strong><?= esc($groupLabel) ?></strong>
          </div>
          <div class="account-row">
            <span>Semua role</span>
            <div class="role-badges">
              <?php foreach ($groups as $group): ?>
                <span class="role-badge <?= $group === activeGroup() ? 'is-active' : '' ?>">
                  <?= esc(ucfirst($group)) ?>
                  <?php if ($group === activeGroup()): ?>
                    <i class="fas fa-check"></i>
                  <?php endif; ?>
                </span>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php $this->section('page_js') ?>
<script src="<?= base_url('assets/modules/chart.min.js') ?>"></script>
<script>
  (function () {
    if (typeof Chart === 'undefined') {
      return;
    }

    var weeklyTrend = <?= json_encode($charts['weeklyTrend'] ?? ['labels' => [], 'revenue' => [], 'transactions' => []], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
    var hourlyTrend = <?= json_encode($charts['hourlyTrend'] ?? ['labels' => [], 'revenue' => [], 'transactions' => []], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
    var paymentChartData = <?= json_encode($charts['paymentBreakdown'] ?? ['labels' => [], 'amounts' => []], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;

    var currencyFormatter = new Intl.NumberFormat('id-ID', {
      maximumFractionDigits: 0
    });

    function formatCurrency(value) {
      return 'Rp ' + currencyFormatter.format(Number(value || 0));
    }

    Chart.defaults.global.defaultFontFamily = 'Nunito, system-ui, sans-serif';
    Chart.defaults.global.defaultFontColor = '#475569';

    var weeklyCanvas = document.getElementById('weeklyTrendChart');
    if (weeklyCanvas) {
      new Chart(weeklyCanvas.getContext('2d'), {
        type: 'bar',
        data: {
          labels: weeklyTrend.labels || [],
          datasets: [
            {
              type: 'bar',
              label: 'Omzet',
              data: weeklyTrend.revenue || [],
              backgroundColor: 'rgba(15, 118, 110, 0.18)',
              borderColor: '#0f766e',
              borderWidth: 1.5,
              yAxisID: 'yRevenue'
            },
            {
              type: 'line',
              label: 'Transaksi',
              data: weeklyTrend.transactions || [],
              borderColor: '#1d4ed8',
              backgroundColor: 'rgba(29, 78, 216, 0.12)',
              fill: false,
              lineTension: 0.28,
              pointRadius: 4,
              pointBackgroundColor: '#1d4ed8',
              pointBorderColor: '#ffffff',
              pointBorderWidth: 2,
              yAxisID: 'yTransactions'
            }
          ]
        },
        options: {
          maintainAspectRatio: false,
          legend: {
            display: true,
            position: 'bottom'
          },
          tooltips: {
            callbacks: {
              label: function (tooltipItem, data) {
                var dataset = data.datasets[tooltipItem.datasetIndex] || {};
                var label = dataset.label ? dataset.label + ': ' : '';
                if (dataset.yAxisID === 'yRevenue') {
                  return label + formatCurrency(tooltipItem.yLabel);
                }

                return label + currencyFormatter.format(tooltipItem.yLabel) + ' transaksi';
              }
            }
          },
          scales: {
            xAxes: [{
              gridLines: {
                display: false
              }
            }],
            yAxes: [
              {
                id: 'yRevenue',
                position: 'left',
                ticks: {
                  beginAtZero: true,
                  callback: function (value) {
                    return formatCurrency(value);
                  }
                },
                gridLines: {
                  color: 'rgba(148, 163, 184, 0.18)'
                }
              },
              {
                id: 'yTransactions',
                position: 'right',
                ticks: {
                  beginAtZero: true,
                  precision: 0,
                  callback: function (value) {
                    return currencyFormatter.format(value);
                  }
                },
                gridLines: {
                  drawOnChartArea: false
                }
              }
            ]
          }
        }
      });
    }

    var hourlyCanvas = document.getElementById('hourlyTrendChart');
    if (hourlyCanvas) {
      new Chart(hourlyCanvas.getContext('2d'), {
        type: 'line',
        data: {
          labels: hourlyTrend.labels || [],
          datasets: [
            {
              label: 'Omzet per jam',
              data: hourlyTrend.revenue || [],
              borderColor: '#b45309',
              backgroundColor: 'rgba(180, 83, 9, 0.12)',
              fill: true,
              lineTension: 0.26,
              pointRadius: 2,
              pointHoverRadius: 5
            }
          ]
        },
        options: {
          maintainAspectRatio: false,
          legend: {
            display: false
          },
          tooltips: {
            callbacks: {
              label: function (tooltipItem) {
                return formatCurrency(tooltipItem.yLabel);
              }
            }
          },
          scales: {
            xAxes: [{
              ticks: {
                maxTicksLimit: 12
              },
              gridLines: {
                display: false
              }
            }],
            yAxes: [{
              ticks: {
                beginAtZero: true,
                callback: function (value) {
                  return formatCurrency(value);
                }
              },
              gridLines: {
                color: 'rgba(148, 163, 184, 0.18)'
              }
            }]
          }
        }
      });
    }

    var paymentCanvas = document.getElementById('paymentBreakdownChart');
    if (paymentCanvas) {
      new Chart(paymentCanvas.getContext('2d'), {
        type: 'doughnut',
        data: {
          labels: paymentChartData.labels || [],
          datasets: [{
            data: paymentChartData.amounts || [],
            backgroundColor: ['#0f766e', '#1d4ed8', '#b45309', '#7c3aed', '#e11d48'],
            borderColor: '#ffffff',
            borderWidth: 4
          }]
        },
        options: {
          maintainAspectRatio: false,
          legend: {
            position: 'bottom'
          },
          tooltips: {
            callbacks: {
              label: function (tooltipItem, data) {
                var label = data.labels[tooltipItem.index] || '';
                var amount = (data.datasets[0].data || [])[tooltipItem.index] || 0;
                return label + ': ' + formatCurrency(amount);
              }
            }
          }
        }
      });
    }
  })();
</script>
<?= $this->endSection() ?>
