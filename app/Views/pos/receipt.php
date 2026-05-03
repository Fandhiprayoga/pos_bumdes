<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Nota <?= esc((string) ($sale['invoice_no'] ?? '-')) ?></title>
  <style>
    <?php
      $notaSettingModel = new \App\Models\NotaSettingModel();
      $notaSetting = $notaSettingModel->getSettings();
      $paperSize = $notaSetting['paper_size'] ?? '80mm';
      $customWidth = $notaSetting['custom_width'] ?? 80;
      $fontFamily = $notaSetting['font_family'] ?? 'Courier New';
      $fontSize = $notaSetting['font_size'] ?? 12;
      $logoSize = $notaSetting['logo_size'] ?? 'medium';
      
      $width = $paperSize === '58mm' ? '58mm' : ($paperSize === '80mm' ? '80mm' : $customWidth . 'mm');
      $pageSizeCss = $width . ' auto';
    ?>
    
    body {
      margin: 0;
      font-family: "<?= $fontFamily ?>", monospace;
      font-size: <?= $fontSize ?>px;
      color: #111827;
      background: #f8fafc;
    }

    .receipt-wrap {
      max-width: <?= $width ?>;
      margin: 20px auto;
      background: #fff;
      border: 1px solid #e5e7eb;
      padding: 14px;
      word-break: break-word;
    }

    .center {
      text-align: center;
    }

    .muted {
      color: #6b7280;
    }

    .divider {
      border-top: 1px dashed #94a3b8;
      margin: 10px 0;
    }

    .receipt-logo {
      display: block;
      margin: 0 auto 6px;
      object-fit: contain;
    }

    .receipt-logo.logo-small {
      max-width: 90px;
      max-height: 40px;
    }

    .receipt-logo.logo-medium {
      max-width: 120px;
      max-height: 60px;
    }

    .receipt-logo.logo-large {
      max-width: 160px;
      max-height: 84px;
    }

    .row {
      display: flex;
      justify-content: space-between;
      gap: 8px;
    }

    .item-name {
      font-weight: 700;
      margin-bottom: 2px;
      word-break: break-word;
    }

    .item-meta {
      display: flex;
      justify-content: space-between;
      gap: 8px;
      color: #334155;
    }

    .totals .row {
      margin-bottom: 4px;
    }

    .totals .grand {
      font-weight: 700;
      font-size: <?= $fontSize + 1 ?>px;
    }

    .actions {
      margin: 14px auto 22px;
      max-width: <?= $width ?>;
      display: flex;
      gap: 8px;
      justify-content: center;
    }

    .btn {
      border: 1px solid #cbd5e1;
      background: #fff;
      border-radius: 8px;
      padding: 8px 12px;
      font-size: 12px;
      cursor: pointer;
      text-decoration: none;
      color: #0f172a;
    }

    .btn-primary {
      background: #6777ef;
      border-color: #6777ef;
      color: #fff;
    }

    @media print {
      @page {
        size: <?= $pageSizeCss ?>;
        margin: 4mm;
      }

      body {
        background: #fff;
      }

      .receipt-wrap {
        margin: 0;
        border: 0;
        max-width: none;
        width: 100%;
        padding: 0;
      }

      .actions {
        display: none;
      }
    }
  </style>
</head>
<body>
  <div class="receipt-wrap">
    <div class="center">
      <?php if (! empty($notaSetting['show_logo']) && ! empty($notaSetting['header_icon'])): ?>
        <img src="<?= base_url((string) $notaSetting['header_icon']) ?>" alt="Logo Nota" class="receipt-logo logo-<?= esc($logoSize) ?>">
      <?php endif; ?>
      
      <div style="font-weight:700; font-size:<?= $fontSize + 2 ?>px;">
        <?= esc($notaSetting['header_text']) ?>
      </div>
      <div class="muted">POS BUMDes</div>
    </div>

    <div class="divider"></div>

    <div class="row"><span>Invoice</span><span><?= esc((string) ($sale['invoice_no'] ?? '-')) ?></span></div>
    <div class="row"><span>Tanggal</span><span><?= esc((string) ($sale['sold_at'] ?? '-')) ?></span></div>
    <div class="row"><span>Kasir</span><span><?= esc($cashierIdentity) ?></span></div>
    <div class="row"><span>Pelanggan</span><span><?= esc((string) ($sale['customer_name'] ?: '-')) ?></span></div>
    <div class="row"><span>Bayar</span><span><?= esc(strtoupper((string) ($sale['payment_method'] ?? '-'))) ?></span></div>

    <div class="divider"></div>

    <?php if (! empty($items)): ?>
      <?php foreach ($items as $item): ?>
        <div style="margin-bottom:8px;">
          <div class="item-name"><?= esc((string) ($item['product_name'] ?? '-')) ?></div>
          <div class="item-meta">
            <span><?= (int) ($item['qty'] ?? 0) ?> x Rp <?= number_format((float) ($item['unit_price'] ?? 0), 0, ',', '.') ?></span>
            <span>Rp <?= number_format((float) ($item['line_total'] ?? 0), 0, ',', '.') ?></span>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="muted center">Tidak ada item.</div>
    <?php endif; ?>

    <div class="divider"></div>

    <div class="totals">
      <div class="row"><span>Subtotal</span><span>Rp <?= number_format((float) ($sale['subtotal'] ?? 0), 0, ',', '.') ?></span></div>
      <div class="row"><span>Diskon</span><span>Rp <?= number_format((float) ($sale['discount_amount'] ?? 0), 0, ',', '.') ?></span></div>
      <div class="row grand"><span>Grand Total</span><span>Rp <?= number_format((float) ($sale['grand_total'] ?? 0), 0, ',', '.') ?></span></div>
      <div class="row"><span>Jumlah Bayar</span><span>Rp <?= number_format((float) ($sale['amount_paid'] ?? 0), 0, ',', '.') ?></span></div>
      <div class="row"><span>Kembalian</span><span>Rp <?= number_format((float) ($sale['change_amount'] ?? 0), 0, ',', '.') ?></span></div>
    </div>

    <div class="divider"></div>

    <div class="center muted">
      <?php if ($notaSetting['footer_text']): ?>
        <?= esc($notaSetting['footer_text']) ?>
      <?php else: ?>
        Terima kasih telah berbelanja
      <?php endif; ?>
    </div>
  </div>

  <div class="actions">
    <button type="button" class="btn btn-primary" onclick="window.print()">Cetak Ulang</button>
    <a href="<?= base_url('pos') ?>" class="btn">Kembali ke POS</a>
  </div>

  <script>
    window.addEventListener('load', function () {
      window.print();
    });
  </script>
</body>
</html>
