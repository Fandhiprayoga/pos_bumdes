<div class="row">
  <div class="col-12 col-md-8 offset-md-2">
    <div class="card">
      <div class="card-header">
        <h4>Tambah Produk</h4>
      </div>
      <div class="card-body">
        <form action="<?= base_url('admin/products/store') ?>" method="post">
          <?= csrf_field() ?>

          <div class="form-row">
            <div class="form-group col-md-6">
              <label>SKU</label>
              <div class="input-group">
                <input type="text" class="form-control" name="sku" id="create_sku" value="<?= old('sku') ?>" placeholder="Opsional" autocomplete="off">
                <div class="input-group-append">
                  <button type="button" class="btn btn-outline-dark" id="btn-create-scan-camera">
                    <i class="fas fa-camera"></i> Kamera
                  </button>
                </div>
              </div>
              <small class="form-text text-muted">Bisa isi manual, scanner barcode reader, atau scan lewat kamera.</small>
            </div>
            <div class="form-group col-md-6">
              <label>Nama Produk <span class="text-danger">*</span></label>
              <input type="text" class="form-control" name="name" value="<?= old('name') ?>" required>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group col-md-6">
              <label>Kategori</label>
              <select class="form-control" name="category" required>
                <option value="">-- Pilih Kategori --</option>
                <?php foreach ($categories as $category): ?>
                <option value="<?= esc($category['name']) ?>" <?= old('category') === $category['name'] ? 'selected' : '' ?>>
                  <?= esc($category['name']) ?>
                </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group col-md-6">
              <label>Satuan</label>
              <select class="form-control" name="unit" required>
                <option value="">-- Pilih Satuan --</option>
                <?php foreach ($units as $unit): ?>
                <option value="<?= esc($unit['name']) ?>" <?= old('unit') === $unit['name'] ? 'selected' : '' ?>>
                  <?= esc($unit['name']) ?>
                </option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group col-md-4">
              <label>Harga Modal</label>
              <input type="number" step="0.01" min="0" class="form-control" name="cost_price" value="<?= old('cost_price') ?>">
            </div>
            <div class="form-group col-md-4">
              <label>Harga Jual <span class="text-danger">*</span></label>
              <input type="number" step="0.01" min="0" class="form-control" name="sell_price" value="<?= old('sell_price') ?>" required>
            </div>
            <div class="form-group col-md-4">
              <label>Stok Awal <span class="text-danger">*</span></label>
              <input type="number" min="0" class="form-control" name="stock" value="<?= old('stock') ?: 0 ?>" required>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group col-md-6">
              <label>Stok Minimum</label>
              <input type="number" min="0" class="form-control" name="min_stock" value="<?= old('min_stock') ?: 0 ?>">
            </div>
            <div class="form-group col-md-6 d-flex align-items-center">
              <div class="custom-control custom-checkbox mt-4">
                <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" <?= old('is_active', '1') ? 'checked' : '' ?>>
                <label class="custom-control-label" for="is_active">Produk aktif</label>
              </div>
            </div>
          </div>

          <div class="form-group text-right mb-0">
            <a href="<?= base_url('admin/products') ?>" class="btn btn-secondary mr-1">Batal</a>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="createProductCameraModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-camera mr-1"></i> Scan Barcode SKU</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div id="create-camera-alert" class="alert alert-danger d-none"></div>
        <div id="create-camera-success" class="alert alert-success d-none"></div>
        <div id="create-camera-reader" style="width:100%;"></div>
        <p class="text-muted text-center mt-2 mb-0">
          <small>Arahkan kamera ke barcode. Nilai otomatis masuk ke kolom SKU.</small>
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
  var skuInput = document.getElementById('create_sku');
  var btnScanCamera = document.getElementById('btn-create-scan-camera');
  var cameraModal = document.getElementById('createProductCameraModal');
  var cameraAlertEl = document.getElementById('create-camera-alert');
  var cameraSuccessEl = document.getElementById('create-camera-success');

  if (!skuInput || !btnScanCamera || !cameraModal || typeof $ === 'undefined') {
    return;
  }

  if (cameraModal.parentElement !== document.body) {
    document.body.appendChild(cameraModal);
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
      html5QrCode = new Html5Qrcode('create-camera-reader');
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

          skuInput.value = decodedText;
          showCameraSuccess('Barcode terbaca: ' + decodedText);
          playBeep();
          $(cameraModal).modal('hide');
          skuInput.focus();
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

  btnScanCamera.addEventListener('click', function() {
    $(cameraModal).modal('show');
  });

  $(cameraModal).on('shown.bs.modal', function() {
    startCamera();
  });

  $(cameraModal).on('hidden.bs.modal', function() {
    stopCamera();
    if (cameraAlertEl) {
      cameraAlertEl.classList.add('d-none');
    }
    if (cameraSuccessEl) {
      cameraSuccessEl.classList.add('d-none');
    }
    skuInput.focus();
  });
})();
</script>
<?= $this->endSection() ?>
