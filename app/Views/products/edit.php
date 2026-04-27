<div class="row">
  <div class="col-12 col-md-8 offset-md-2">
    <div class="card">
      <div class="card-header">
        <h4>Edit Produk</h4>
      </div>
      <div class="card-body">
        <form action="<?= base_url('admin/products/update/' . $product['id']) ?>" method="post">
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
