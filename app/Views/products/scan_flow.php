<style>
  .scan-page {
    --scan-accent: #0f766e;
    --scan-border: #e5e7eb;
    --scan-soft: #6b7280;
  }

  .scan-page .card {
    border: 1px solid var(--scan-border);
    border-radius: 16px;
    box-shadow: 0 10px 24px rgba(15, 23, 42, 0.05);
  }

  .scan-page .card-header {
    background: #fff;
    border-bottom: 1px solid #f1f5f9;
  }

  .scan-hero {
    border: 1px solid var(--scan-border);
    border-radius: 16px;
    background: linear-gradient(135deg, #f8fafc 0%, #ecfeff 100%);
  }

  .scan-hero h4 {
    margin-bottom: 4px;
    font-weight: 700;
  }

  .scan-hero p {
    margin-bottom: 0;
    color: var(--scan-soft);
  }

  .process-guide {
    background: #f8fafc;
    border: 1px solid #eef2f7;
    border-radius: 12px;
    padding: 12px;
  }

  .process-chip {
    display: inline-flex;
    align-items: center;
    padding: 6px 10px;
    border-radius: 999px;
    background: #fff;
    border: 1px solid #e2e8f0;
    color: #334155;
    font-size: 12px;
    font-weight: 600;
    margin: 0 8px 8px 0;
  }

  .scan-status-box {
    min-height: 86px;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
  }

  .scan-page .form-control,
  .scan-page .custom-select {
    border-radius: 10px;
    border-color: #dbe3ec;
  }

  .scan-page .form-control:focus,
  .scan-page .custom-select:focus {
    border-color: #14b8a6;
    box-shadow: 0 0 0 0.2rem rgba(20, 184, 166, 0.12);
  }

  .scan-page .btn-primary {
    background-color: var(--scan-accent);
    border-color: var(--scan-accent);
  }
</style>

<?php
$scanProducts = array_map(static function ($product) {
  return [
    'sku'  => strtolower((string) ($product['sku'] ?? '')),
    'name' => (string) ($product['name'] ?? ''),
  ];
}, $products ?? []);
?>

<div class="scan-page">
  <div class="row mb-4">
    <div class="col-12">
      <div class="scan-hero p-4">
        <h4>Alur Cepat Scan Barang</h4>
        <p>Gunakan halaman ini untuk operasional stok masuk berbasis scan dan perhitungan MWA.</p>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h4 class="mb-0">Scan Flow</h4>
          <a href="<?= base_url('admin/products') ?>" class="btn btn-light btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali ke Produk
          </a>
        </div>
        <div class="card-body">
          <div class="process-guide mb-4">
            <div class="d-flex flex-wrap align-items-center">
              <span class="process-chip">1. Scan Barang</span>
              <span class="process-chip">2. Cek Produk</span>
              <span class="process-chip">3. Lengkapi Data Baru</span>
              <span class="process-chip">4. Input Stok + Harga Beli</span>
              <span class="process-chip">5. Simpan</span>
            </div>
          </div>

          <form action="<?= base_url('admin/products/scan-flow') ?>" method="post" id="scan-flow-form">
            <?= csrf_field() ?>

            <div class="form-row">
              <div class="form-group col-md-7">
                <label>Scan Barang / SKU <span class="text-danger">*</span></label>
                <div class="input-group">
                  <input type="text" class="form-control" name="scan_code" id="scan_code" value="<?= old('scan_code') ?>" placeholder="Scan barcode atau input SKU" required autocomplete="off" autofocus>
                  <div class="input-group-append">
                    <button type="button" class="btn btn-outline-dark" id="btn-scan-camera">
                      <i class="fas fa-camera"></i> Kamera
                    </button>
                  </div>
                </div>
                <small class="form-text text-muted">Gunakan scanner fisik atau ketik manual kode barang.</small>
              </div>
              <div class="form-group col-md-5">
                <label class="d-block mb-2" style="visibility:hidden;">Status</label>
                <div id="scan-check-status" class="w-100 alert alert-secondary mb-0 py-2 px-3 scan-status-box">
                  <div class="d-flex align-items-start">
                    <i class="fas fa-info-circle mt-1 mr-2"></i>
                    <div>
                      <div class="font-weight-bold">Cek Status Produk</div>
                      <small class="d-block">Input kode barang untuk mengetahui produk baru atau sudah terdaftar.</small>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div id="new-product-fields" class="border rounded p-3 mb-3" style="display:none;">
              <h6 class="mb-3">Produk Baru: Input / Edit Barang</h6>
              <div class="form-row">
                <div class="form-group col-md-6">
                  <label>Nama Produk <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" name="name" id="new_name" value="<?= old('name') ?>">
                </div>
                <div class="form-group col-md-3">
                  <label>Kategori</label>
                  <select class="form-control" name="category" id="new_category">
                    <option value="">-- Pilih Kategori --</option>
                    <?php foreach ($categories as $category): ?>
                    <option value="<?= esc($category['name']) ?>" <?= old('category') === $category['name'] ? 'selected' : '' ?>>
                      <?= esc($category['name']) ?>
                    </option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="form-group col-md-3">
                  <label>Satuan</label>
                  <select class="form-control" name="unit" id="new_unit">
                    <option value="">-- Pilih Satuan --</option>
                    <?php foreach ($units as $unit): ?>
                    <option value="<?= esc($unit['name']) ?>" <?= old('unit') === $unit['name'] ? 'selected' : '' ?>>
                      <?= esc($unit['name']) ?>
                    </option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>

              <div class="form-row mb-0">
                <div class="form-group col-md-6 mb-0">
                  <label>Harga Jual <span class="text-danger">*</span></label>
                  <input type="number" step="0.01" min="0" class="form-control" name="sell_price" id="new_sell_price" value="<?= old('sell_price') ?>">
                </div>
                <div class="form-group col-md-6 mb-0">
                  <label>Stok Minimum</label>
                  <input type="number" min="0" class="form-control" name="min_stock" value="<?= old('min_stock') ?: 0 ?>">
                </div>
              </div>

              <div class="custom-control custom-checkbox mt-3">
                <input type="checkbox" class="custom-control-input" id="scan_is_active" name="is_active" value="1" <?= old('is_active', '1') ? 'checked' : '' ?>>
                <label class="custom-control-label" for="scan_is_active">Produk aktif</label>
              </div>
            </div>

            <div class="form-row">
              <div class="form-group col-md-4">
                <label>Input Stok <span class="text-danger">*</span></label>
                <input type="number" name="qty" min="1" class="form-control" value="<?= old('qty') ?: 1 ?>" required>
              </div>
              <div class="form-group col-md-4">
                <label>Input Harga Beli <span class="text-danger">*</span></label>
                <input type="number" name="cost_price" step="0.01" min="0" class="form-control" value="<?= old('cost_price') ?>" required>
              </div>
              <div class="form-group col-md-4">
                <label>Catatan</label>
                <input type="text" name="notes" class="form-control" value="<?= old('notes') ?>" placeholder="Contoh: Restock supplier A">
              </div>
            </div>

            <div class="text-right">
              <button type="submit" class="btn btn-primary">
                <i class="fas fa-barcode"></i> Proses Alur Scan
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="productCameraModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-camera mr-1"></i> Scan Barcode Produk</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div id="product-camera-alert" class="alert alert-danger d-none"></div>
        <div id="product-camera-success" class="alert alert-success d-none"></div>
        <div id="product-camera-reader" style="width:100%;"></div>
        <p class="text-muted text-center mt-2 mb-0">
          <small>Arahkan kamera ke barcode. Kode akan otomatis masuk ke field scan.</small>
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
  var scanCodeInput = document.getElementById('scan_code');
  var statusBox = document.getElementById('scan-check-status');
  var newFields = document.getElementById('new-product-fields');
  var newNameInput = document.getElementById('new_name');
  var newCategoryInput = document.getElementById('new_category');
  var newUnitInput = document.getElementById('new_unit');
  var newSellPriceInput = document.getElementById('new_sell_price');
  var btnScanCamera = document.getElementById('btn-scan-camera');
  var productCameraModal = document.getElementById('productCameraModal');
  var cameraAlertEl = document.getElementById('product-camera-alert');
  var cameraSuccessEl = document.getElementById('product-camera-success');

  if (!scanCodeInput || !statusBox || !newFields) {
    return;
  }

  if (productCameraModal && productCameraModal.parentElement !== document.body) {
    document.body.appendChild(productCameraModal);
  }

  var products = <?= json_encode($scanProducts, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;

  var skuMap = {};
  products.forEach(function(product) {
    if (product.sku) {
      skuMap[product.sku] = product;
    }
  });

  function setRequiredForNewProduct(isRequired) {
    if (newNameInput) {
      newNameInput.required = isRequired;
    }
    if (newCategoryInput) {
      newCategoryInput.required = isRequired;
    }
    if (newUnitInput) {
      newUnitInput.required = isRequired;
    }
    if (newSellPriceInput) {
      newSellPriceInput.required = isRequired;
    }
  }

  function renderScanStatus(type, title, message) {
    var config = {
      idle: { className: 'w-100 alert alert-secondary mb-0 py-2 px-3 scan-status-box', icon: 'fa-info-circle' },
      success: { className: 'w-100 alert alert-success mb-0 py-2 px-3 scan-status-box', icon: 'fa-check-circle' },
      warning: { className: 'w-100 alert alert-warning mb-0 py-2 px-3 scan-status-box', icon: 'fa-exclamation-triangle' }
    };

    var selected = config[type] || config.idle;
    statusBox.className = selected.className;
    statusBox.innerHTML = '' +
      '<div class="d-flex align-items-start">' +
      '<i class="fas ' + selected.icon + ' mt-1 mr-2"></i>' +
      '<div>' +
      '<div class="font-weight-bold">' + title + '</div>' +
      '<small class="d-block">' + message + '</small>' +
      '</div>' +
      '</div>';
  }

  function updateScanState() {
    var code = (scanCodeInput.value || '').trim().toLowerCase();

    if (!code) {
      newFields.style.display = 'none';
      renderScanStatus('idle', 'Cek Status Produk', 'Input kode barang untuk mengetahui produk baru atau sudah terdaftar.');
      setRequiredForNewProduct(false);
      return;
    }

    if (skuMap[code]) {
      newFields.style.display = 'none';
      renderScanStatus('success', 'Produk Ditemukan', skuMap[code].name + ' sudah terdaftar. Lanjut input stok dan harga beli.');
      setRequiredForNewProduct(false);
    } else {
      newFields.style.display = 'block';
      renderScanStatus('warning', 'Produk Baru', 'Lengkapi data barang terlebih dahulu, lalu input stok dan harga beli.');
      setRequiredForNewProduct(true);
    }
  }

  var html5QrCode = null;
  var cameraStarted = false;
  var lastScannedCode = null;
  var lastScannedAt = 0;

  function showCameraAlert(message) {
    if (!cameraAlertEl) {
      return;
    }
    cameraAlertEl.textContent = message;
    cameraAlertEl.classList.remove('d-none');
    if (cameraSuccessEl) {
      cameraSuccessEl.classList.add('d-none');
    }
  }

  function showCameraSuccess(message) {
    if (!cameraSuccessEl) {
      return;
    }
    cameraSuccessEl.textContent = message;
    cameraSuccessEl.classList.remove('d-none');
    if (cameraAlertEl) {
      cameraAlertEl.classList.add('d-none');
    }
  }

  function playBeep() {
    try {
      var AudioCtx = window.AudioContext || window.webkitAudioContext;
      if (!AudioCtx) {
        return;
      }

      var ctx = new AudioCtx();
      var oscillator = ctx.createOscillator();
      var gainNode = ctx.createGain();

      oscillator.type = 'sine';
      oscillator.frequency.value = 950;
      gainNode.gain.value = 0.06;

      oscillator.connect(gainNode);
      gainNode.connect(ctx.destination);

      oscillator.start();
      setTimeout(function() {
        oscillator.stop();
        ctx.close();
      }, 120);
    } catch (e) {}
  }

  function stopCamera() {
    if (html5QrCode && cameraStarted) {
      html5QrCode.stop().then(function() {
        cameraStarted = false;
      }).catch(function() {
        cameraStarted = false;
      });
    }
  }

  function startCamera() {
    if (typeof Html5Qrcode === 'undefined') {
      showCameraAlert('Library scanner kamera tidak ditemukan.');
      return;
    }

    if (!html5QrCode) {
      html5QrCode = new Html5Qrcode('product-camera-reader');
    }

    Html5Qrcode.getCameras().then(function(cameras) {
      if (!cameras || cameras.length === 0) {
        showCameraAlert('Kamera tidak ditemukan.');
        return;
      }

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
          if (decodedText === lastScannedCode && (now - lastScannedAt) < 2000) {
            return;
          }

          lastScannedCode = decodedText;
          lastScannedAt = now;

          scanCodeInput.value = decodedText;
          updateScanState();
          showCameraSuccess('Barcode terbaca: ' + decodedText);
          playBeep();
          if (productCameraModal && typeof $ !== 'undefined') {
            $(productCameraModal).modal('hide');
          }
          scanCodeInput.focus();
        },
        function onScanError() {}
      ).then(function() {
        cameraStarted = true;
      }).catch(function(err) {
        showCameraAlert('Gagal mengakses kamera: ' + err);
      });
    }).catch(function(err) {
      showCameraAlert('Izin kamera ditolak atau tidak tersedia: ' + err);
    });
  }

  if (btnScanCamera && productCameraModal && typeof $ !== 'undefined') {
    btnScanCamera.addEventListener('click', function() {
      $(productCameraModal).modal('show');
    });

    $(productCameraModal).on('shown.bs.modal', function() {
      startCamera();
    });

    $(productCameraModal).on('hidden.bs.modal', function() {
      stopCamera();
      if (cameraAlertEl) {
        cameraAlertEl.classList.add('d-none');
      }
      if (cameraSuccessEl) {
        cameraSuccessEl.classList.add('d-none');
      }
      scanCodeInput.focus();
    });
  }

  var scanInputDebounceTimer = null;
  var scanInputDebounceDelay = 350;

  scanCodeInput.addEventListener('input', function() {
    if (scanInputDebounceTimer) {
      clearTimeout(scanInputDebounceTimer);
    }

    scanInputDebounceTimer = setTimeout(function() {
      updateScanState();
    }, scanInputDebounceDelay);
  });

  scanCodeInput.addEventListener('change', function() {
    if (scanInputDebounceTimer) {
      clearTimeout(scanInputDebounceTimer);
    }
    updateScanState();
  });

  scanCodeInput.addEventListener('keydown', function(event) {
    if (event.key === 'Enter') {
      if (scanInputDebounceTimer) {
        clearTimeout(scanInputDebounceTimer);
      }
      updateScanState();
    }
  });

  updateScanState();
})();
</script>
<?= $this->endSection() ?>
