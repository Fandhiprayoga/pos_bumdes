<div class="row">
  <div class="col-12 col-md-8 offset-md-2">
    <div class="card">
      <div class="card-header">
        <h4>Edit Produk</h4>
      </div>
      <div class="card-body">
        <form action="<?= base_url('admin/products/update/' . $product['id']) ?>" method="post" enctype="multipart/form-data">
          <?= csrf_field() ?>

          <div class="form-row">
            <div class="form-group col-md-6">
              <label>SKU</label>
              <input type="text" class="form-control" name="sku" value="<?= old('sku', $product['sku']) ?>" placeholder="Opsional">
            </div>
            <div class="form-group col-md-6">
              <label>Nama Produk <span class="text-danger">*</span></label>
              <input type="text" class="form-control" name="name" value="<?= old('name', $product['name']) ?>" required>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group col-md-6">
              <label>Kategori</label>
              <select class="form-control" name="category" required>
                <option value="">-- Pilih Kategori --</option>
                <?php foreach ($categories as $category): ?>
                <option value="<?= esc($category['name']) ?>" <?= old('category', $product['category']) === $category['name'] ? 'selected' : '' ?>>
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
                <option value="<?= esc($unit['name']) ?>" <?= old('unit', $product['unit']) === $unit['name'] ? 'selected' : '' ?>>
                  <?= esc($unit['name']) ?>
                </option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group col-md-4">
              <label>Harga Modal</label>
              <input type="number" step="0.01" min="0" class="form-control" name="cost_price" value="<?= old('cost_price', $product['cost_price']) ?>">
            </div>
            <div class="form-group col-md-4">
              <label>Harga Jual <span class="text-danger">*</span></label>
              <input type="number" step="0.01" min="0" class="form-control" name="sell_price" value="<?= old('sell_price', $product['sell_price']) ?>" required>
            </div>
            <div class="form-group col-md-4">
              <label>Stok <span class="text-danger">*</span></label>
              <input type="number" min="0" class="form-control" name="stock" value="<?= old('stock', $product['stock']) ?>" required>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group col-md-6">
              <label>Gambar Produk</label>
              <input type="file" class="form-control" name="image" id="input-product-image-edit" accept="image/png,image/jpeg,image/jpg,image/webp">
              <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah gambar. Maksimal 2MB.</small>

              <div class="mt-2">
                <?php if (! empty($product['image'])): ?>
                <img src="<?= base_url($product['image']) ?>" alt="<?= esc($product['name']) ?>" id="img-preview" style="width:96px;height:96px;object-fit:cover;border-radius:10px;border:1px solid #e5e7eb;">
                <?php else: ?>
                <span id="img-preview" style="display:inline-flex;align-items:center;justify-content:center;width:96px;height:96px;border-radius:10px;border:1px dashed #cbd5e1;background:#f8fafc;color:#94a3b8;font-size:12px;text-align:center;">
                  <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="none" viewBox="0 0 24 24" stroke="#cbd5e1"><rect x="3" y="3" width="18" height="18" rx="3" stroke-width="1.5"/><circle cx="8.5" cy="8.5" r="1.5" stroke-width="1.5"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 16l5-5 4 4 3-3 6 6"/></svg>
                </span>
                <?php endif; ?>
              </div>
              <?php if (! empty($product['image'])): ?>
              <div class="custom-control custom-checkbox mt-2">
                <input type="checkbox" class="custom-control-input" id="remove_image" name="remove_image" value="1">
                <label class="custom-control-label" for="remove_image">Hapus gambar saat update</label>
              </div>
              <?php endif; ?>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group col-md-6">
              <label>Stok Minimum</label>
              <input type="number" min="0" class="form-control" name="min_stock" value="<?= old('min_stock', $product['min_stock']) ?>">
            </div>
            <div class="form-group col-md-6 d-flex align-items-center">
              <div class="custom-control custom-checkbox mt-4">
                <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" <?= old('is_active', $product['is_active']) ? 'checked' : '' ?>>
                <label class="custom-control-label" for="is_active">Produk aktif</label>
              </div>
            </div>
          </div>

          <div class="form-group text-right mb-0">
            <a href="<?= base_url('admin/products') ?>" class="btn btn-secondary mr-1">Batal</a>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php $this->section('page_js') ?>
<script>
(function () {
  var inputImage = document.getElementById('input-product-image-edit');
  var imgPreview = document.getElementById('img-preview');
  var checkboxRemove = document.getElementById('remove_image');

  if (!inputImage || !imgPreview) {
    return;
  }

  inputImage.addEventListener('change', function () {
    var file = this.files && this.files[0];
    if (!file) {
      return;
    }

    var reader = new FileReader();
    reader.onload = function (e) {
      if (imgPreview.tagName.toLowerCase() === 'img') {
        imgPreview.src = e.target.result;
      } else {
        var img = document.createElement('img');
        img.src = e.target.result;
        img.id = 'img-preview';
        img.style.cssText = 'width:96px;height:96px;object-fit:cover;border-radius:10px;border:1px solid #e5e7eb;';
        imgPreview.parentNode.replaceChild(img, imgPreview);
        imgPreview = img;
      }
    };
    reader.readAsDataURL(file);

    if (checkboxRemove) {
      checkboxRemove.checked = false;
    }
  });
})();
</script>
<?= $this->endSection() ?>
