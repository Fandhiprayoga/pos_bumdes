<div class="row">
  <div class="col-12 col-lg-4">
    <div class="card">
      <div class="card-header">
        <h4>Status Shift</h4>
      </div>
      <div class="card-body">
        <?php if (! $openShift): ?>
          <p class="text-muted">Belum ada shift aktif.</p>
          <form action="<?= base_url('pos/open-shift') ?>" method="post">
            <?= csrf_field() ?>
            <div class="form-group">
              <label>Kas Awal</label>
              <input type="number" step="0.01" min="0" name="opening_cash" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Buka Shift</button>
          </form>
        <?php else: ?>
          <ul class="list-unstyled mb-3">
            <li><strong>Dibuka:</strong> <?= esc($openShift['opened_at']) ?></li>
            <li><strong>Kas Awal:</strong> Rp <?= number_format((float) $openShift['opening_cash'], 0, ',', '.') ?></li>
          </ul>
          <form action="<?= base_url('pos/close-shift') ?>" method="post">
            <?= csrf_field() ?>
            <div class="form-group">
              <label>Kas Fisik Akhir</label>
              <input type="number" step="0.01" min="0" name="closing_cash_actual" class="form-control" required>
            </div>
            <div class="form-group">
              <label>Catatan</label>
              <input type="text" name="notes" class="form-control" placeholder="Opsional">
            </div>
            <button type="submit" class="btn btn-warning btn-block">Tutup Shift</button>
          </form>
        <?php endif; ?>
      </div>
    </div>

    <div class="card">
      <div class="card-header">
        <h4>Transaksi Terbaru</h4>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-sm table-striped mb-0">
            <thead>
              <tr>
                <th>Invoice</th>
                <th>Total</th>
              </tr>
            </thead>
            <tbody>
              <?php if (! empty($recentSales)): ?>
                <?php foreach ($recentSales as $sale): ?>
                <tr>
                  <td><?= esc($sale['invoice_no']) ?></td>
                  <td>Rp <?= number_format((float) $sale['grand_total'], 0, ',', '.') ?></td>
                </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="2" class="text-center text-muted">Belum ada transaksi.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <div class="col-12 col-lg-8">
    <div class="card">
      <div class="card-header">
        <h4>Transaksi Penjualan</h4>
      </div>
      <div class="card-body">
        <?php if (! $openShift): ?>
          <div class="alert alert-warning mb-0">Buka shift terlebih dahulu untuk mulai transaksi.</div>
        <?php else: ?>
          <form action="<?= base_url('pos/checkout') ?>" method="post" id="checkout-form">
            <?= csrf_field() ?>

            <div class="form-row">
              <div class="form-group col-md-6">
                <label>Nama Pelanggan</label>
                <input type="text" name="customer_name" class="form-control" placeholder="Opsional">
              </div>
              <div class="form-group col-md-6">
                <label>Metode Pembayaran</label>
                <select class="form-control" name="payment_method" id="payment_method" required>
                  <option value="cash">Tunai</option>
                  <option value="transfer">Transfer</option>
                </select>
              </div>
            </div>

            <div class="form-group">
              <label>Scan / Cari Barang</label>
              <div class="input-group">
                <input type="text" id="quick-product-input" class="form-control" placeholder="Scan SKU/barcode lalu Enter, atau ketik nama barang">
                <div class="input-group-append">
                  <button type="button" id="btn-camera-scan" class="btn btn-secondary" title="Scan lewat kamera">
                    <i class="fas fa-camera"></i>
                  </button>
                  <button type="button" id="quick-product-add" class="btn btn-primary">Tambah</button>
                </div>
              </div>
              <small class="form-text text-muted">Gunakan scanner barcode fisik atau klik <i class="fas fa-camera"></i> untuk scan lewat kamera HP/tablet.</small>
            </div>

            <div class="table-responsive mb-3">
              <table class="table table-bordered" id="items-table">
                <thead>
                  <tr>
                    <th style="width: 46%;">Produk</th>
                    <th style="width: 14%;">Stok</th>
                    <th style="width: 14%;">Harga</th>
                    <th style="width: 14%;">Qty</th>
                    <th style="width: 12%;"></th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>
                      <select name="product_id[]" class="form-control product-select" required>
                        <option value="">-- Pilih produk --</option>
                        <?php foreach ($products as $product): ?>
                        <option value="<?= $product['id'] ?>" data-price="<?= $product['sell_price'] ?>" data-stock="<?= $product['stock'] ?>" data-sku="<?= esc((string) ($product['sku'] ?? '')) ?>" data-name="<?= esc($product['name']) ?>">
                          <?= esc($product['name']) ?> (<?= esc($product['unit']) ?>)
                        </option>
                        <?php endforeach; ?>
                      </select>
                    </td>
                    <td><input type="text" class="form-control stock-display" readonly></td>
                    <td><input type="text" class="form-control price-display" readonly></td>
                    <td><input type="number" name="qty[]" min="1" value="1" class="form-control qty-input" required></td>
                    <td class="text-center"><button type="button" class="btn btn-sm btn-danger remove-row"><i class="fas fa-times"></i></button></td>
                  </tr>
                </tbody>
              </table>
            </div>

            <button type="button" class="btn btn-outline-primary mb-3" id="add-row">
              <i class="fas fa-plus"></i> Tambah Baris
            </button>

            <div class="form-row">
              <div class="form-group col-md-4">
                <label>Diskon (Rp)</label>
                <input type="number" step="0.01" min="0" name="discount_amount" id="discount_amount" class="form-control" value="0">
              </div>
              <div class="form-group col-md-4">
                <label>Jumlah Bayar</label>
                <input type="number" step="0.01" min="0" name="amount_paid" id="amount_paid" class="form-control" required>
              </div>
              <div class="form-group col-md-4">
                <label>Total Transaksi</label>
                <input type="text" id="grand_total_display" class="form-control" readonly>
              </div>
            </div>

            <div class="text-right">
              <button type="submit" class="btn btn-success btn-lg">
                <i class="fas fa-save"></i> Simpan Transaksi
              </button>
            </div>
          </form>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<!-- Modal Scan Kamera -->
<div class="modal fade" id="cameraScanModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-camera mr-1"></i> Scan Barcode via Kamera</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div id="camera-scan-alert" class="alert alert-danger d-none"></div>
        <div id="camera-scan-success" class="alert alert-success d-none"></div>
        <div id="camera-reader" style="width:100%;"></div>
        <p class="text-center text-muted mt-2 mb-0">
          <small>Arahkan kamera ke barcode. Barang otomatis ditambahkan ke keranjang.</small>
        </p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<?php $this->section('page_js') ?>
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
(function() {
  const tableBody = document.querySelector('#items-table tbody');
  const addRowButton = document.getElementById('add-row');
  const discountInput = document.getElementById('discount_amount');
  const totalDisplay = document.getElementById('grand_total_display');
  const quickProductInput = document.getElementById('quick-product-input');
  const quickProductAddButton = document.getElementById('quick-product-add');

  if (!tableBody || !addRowButton) {
    return;
  }

  function formatIDR(num) {
    return 'Rp ' + (num || 0).toLocaleString('id-ID');
  }

  function attachRowEvents(row) {
    const select = row.querySelector('.product-select');
    const stockDisplay = row.querySelector('.stock-display');
    const priceDisplay = row.querySelector('.price-display');
    const qtyInput = row.querySelector('.qty-input');
    const removeButton = row.querySelector('.remove-row');

    function updateRowInfo() {
      const selected = select.options[select.selectedIndex];
      const price = Number(selected?.dataset?.price || 0);
      const stock = Number(selected?.dataset?.stock || 0);

      stockDisplay.value = stock || '';
      priceDisplay.value = price ? formatIDR(price) : '';
      calculateTotal();
    }

    select.addEventListener('change', updateRowInfo);
    qtyInput.addEventListener('input', calculateTotal);

    removeButton.addEventListener('click', function() {
      if (tableBody.querySelectorAll('tr').length === 1) {
        return;
      }

      row.remove();
      calculateTotal();
    });
  }

  function getProductOptions() {
    const sampleSelect = tableBody.querySelector('.product-select');
    if (!sampleSelect) {
      return [];
    }

    return Array.from(sampleSelect.options).filter(function(option) {
      return option.value !== '';
    });
  }

  function normalizeText(text) {
    return String(text || '').trim().toLowerCase();
  }

  function findProductOptionByKeyword(keyword) {
    const cleanKeyword = normalizeText(keyword);
    if (!cleanKeyword) {
      return null;
    }

    const options = getProductOptions();

    let found = options.find(function(option) {
      return normalizeText(option.dataset.sku) === cleanKeyword;
    });
    if (found) {
      return found;
    }

    found = options.find(function(option) {
      return normalizeText(option.dataset.name) === cleanKeyword;
    });
    if (found) {
      return found;
    }

    found = options.find(function(option) {
      const sku = normalizeText(option.dataset.sku);
      const name = normalizeText(option.dataset.name);
      return sku.includes(cleanKeyword) || name.includes(cleanKeyword);
    });

    return found || null;
  }

  function syncRowInfo(row) {
    const select = row.querySelector('.product-select');
    const stockDisplay = row.querySelector('.stock-display');
    const priceDisplay = row.querySelector('.price-display');
    const selected = select.options[select.selectedIndex];
    const price = Number(selected?.dataset?.price || 0);
    const stock = Number(selected?.dataset?.stock || 0);

    stockDisplay.value = stock || '';
    priceDisplay.value = price ? formatIDR(price) : '';
  }

  function createEmptyRow() {
    const firstRow = tableBody.querySelector('tr');
    const newRow = firstRow.cloneNode(true);
    newRow.querySelector('.product-select').selectedIndex = 0;
    newRow.querySelector('.stock-display').value = '';
    newRow.querySelector('.price-display').value = '';
    newRow.querySelector('.qty-input').value = 1;
    tableBody.appendChild(newRow);
    attachRowEvents(newRow);
    return newRow;
  }

  function addProductToCartByOption(productOption) {
    if (!productOption) {
      return;
    }

    const targetValue = String(productOption.value);
    let selectedRow = null;

    tableBody.querySelectorAll('tr').forEach(function(row) {
      if (selectedRow) {
        return;
      }

      const select = row.querySelector('.product-select');
      if (select.value === targetValue) {
        selectedRow = row;
      }
    });

    if (selectedRow) {
      const qtyInput = selectedRow.querySelector('.qty-input');
      const maxStock = Number(productOption.dataset.stock || 0);
      const currentQty = Number(qtyInput.value || 0);
      if (currentQty < maxStock) {
        qtyInput.value = currentQty + 1;
      }
      calculateTotal();
      return;
    }

    let emptyRow = null;
    tableBody.querySelectorAll('tr').forEach(function(row) {
      if (emptyRow) {
        return;
      }

      const select = row.querySelector('.product-select');
      if (!select.value) {
        emptyRow = row;
      }
    });

    const targetRow = emptyRow || createEmptyRow();
    const select = targetRow.querySelector('.product-select');
    select.value = targetValue;
    targetRow.querySelector('.qty-input').value = 1;
    syncRowInfo(targetRow);
    calculateTotal();
  }

  function addProductByKeyword(keyword) {
    const option = findProductOptionByKeyword(keyword);
    if (!option) {
      alert('Barang tidak ditemukan. Gunakan SKU yang benar atau ketik nama barang.');
      return;
    }

    addProductToCartByOption(option);

    if (quickProductInput) {
      quickProductInput.value = '';
      quickProductInput.focus();
    }
  }

  function calculateTotal() {
    let subtotal = 0;
    tableBody.querySelectorAll('tr').forEach(function(row) {
      const select = row.querySelector('.product-select');
      const qtyInput = row.querySelector('.qty-input');
      const selected = select.options[select.selectedIndex];
      const price = Number(selected?.dataset?.price || 0);
      const qty = Number(qtyInput.value || 0);

      if (price > 0 && qty > 0) {
        subtotal += price * qty;
      }
    });

    const discount = Number(discountInput?.value || 0);
    const grandTotal = Math.max(0, subtotal - discount);
    totalDisplay.value = formatIDR(grandTotal);
  }

  addRowButton.addEventListener('click', function() {
    createEmptyRow();
    calculateTotal();
  });

  if (quickProductInput) {
    quickProductInput.addEventListener('keydown', function(event) {
      if (event.key === 'Enter') {
        event.preventDefault();
        addProductByKeyword(quickProductInput.value);
      }
    });
  }

  if (quickProductAddButton) {
    quickProductAddButton.addEventListener('click', function() {
      addProductByKeyword(quickProductInput ? quickProductInput.value : '');
    });
  }

  tableBody.querySelectorAll('tr').forEach(attachRowEvents);
  discountInput?.addEventListener('input', calculateTotal);
  calculateTotal();

  if (quickProductInput) {
    quickProductInput.focus();
  }

  // ------------------------------------------------------------------
  // Camera barcode scanning via html5-qrcode
  // ------------------------------------------------------------------
  var cameraScanModal   = document.getElementById('cameraScanModal');
  var btnCameraScan     = document.getElementById('btn-camera-scan');
  var cameraAlertEl     = document.getElementById('camera-scan-alert');
  var cameraSuccessEl   = document.getElementById('camera-scan-success');
  var html5QrCode       = null;
  var lastScannedCode   = '';
  var lastScannedAt     = 0;

  function stopCamera() {
    if (html5QrCode) {
      html5QrCode.stop().catch(function() {}).finally(function() {
        html5QrCode.clear();
        html5QrCode = null;
      });
    }
  }

  function showCameraAlert(msg) {
    cameraAlertEl.textContent = msg;
    cameraAlertEl.classList.remove('d-none');
    cameraSuccessEl.classList.add('d-none');
  }

  function showCameraSuccess(msg) {
    cameraSuccessEl.textContent = msg;
    cameraSuccessEl.classList.remove('d-none');
    cameraAlertEl.classList.add('d-none');
  }

  function startCamera() {
    if (typeof Html5Qrcode === 'undefined') {
      showCameraAlert('Library scan tidak tersedia. Periksa koneksi internet.');
      return;
    }

    cameraAlertEl.classList.add('d-none');
    cameraSuccessEl.classList.add('d-none');
    lastScannedCode = '';

    html5QrCode = new Html5Qrcode('camera-reader');

    Html5Qrcode.getCameras().then(function(cameras) {
      if (!cameras || cameras.length === 0) {
        showCameraAlert('Kamera tidak ditemukan pada perangkat ini.');
        return;
      }

      // Pilih kamera belakang jika tersedia (lebih cocok untuk scan barcode)
      var selectedCamera = cameras[cameras.length - 1];
      cameras.forEach(function(cam) {
        var label = (cam.label || '').toLowerCase();
        if (label.includes('back') || label.includes('rear') || label.includes('belakang') || label.includes('environment')) {
          selectedCamera = cam;
        }
      });

      html5QrCode.start(
        selectedCamera.id,
        { fps: 10, qrbox: { width: 260, height: 100 } },
        function onScanSuccess(decodedText) {
          var now = Date.now();
          if (decodedText === lastScannedCode && (now - lastScannedAt) < 3000) {
            return;
          }
          lastScannedCode = decodedText;
          lastScannedAt   = now;

          var option = findProductOptionByKeyword(decodedText);
          if (option) {
            addProductToCartByOption(option);
            showCameraSuccess('\u2714 Ditambahkan: ' + option.dataset.name);
          } else {
            showCameraAlert('Barang tidak ditemukan: ' + decodedText);
          }
        },
        function onScanError() {}
      ).catch(function(err) {
        showCameraAlert('Gagal mengakses kamera: ' + err);
      });
    }).catch(function(err) {
      showCameraAlert('Izin kamera ditolak atau tidak tersedia: ' + err);
    });
  }

  if (btnCameraScan && cameraScanModal && typeof $ !== 'undefined') {
    // Keep modal at root level so Bootstrap backdrop does not block interaction.
    if (cameraScanModal.parentElement !== document.body) {
      document.body.appendChild(cameraScanModal);
    }

    btnCameraScan.addEventListener('click', function() {
      $(cameraScanModal).modal('show');
    });

    $(cameraScanModal).on('shown.bs.modal', function() {
      startCamera();
    });

    $(cameraScanModal).on('hidden.bs.modal', function() {
      stopCamera();
      cameraAlertEl.classList.add('d-none');
      cameraSuccessEl.classList.add('d-none');
      if (quickProductInput) {
        quickProductInput.focus();
      }
    });
  }
})();
</script>
<?= $this->endSection() ?>
