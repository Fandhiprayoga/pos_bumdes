<?php

namespace App\Controllers;

use App\Models\ProductCategoryModel;
use App\Models\ProductModel;
use App\Models\ProductUnitModel;
use App\Models\StockMovementModel;

class ProductController extends BaseController
{
    protected ProductModel $productModel;
    protected StockMovementModel $stockMovementModel;
    protected ProductCategoryModel $categoryModel;
    protected ProductUnitModel $unitModel;

    public function __construct()
    {
        $this->productModel = new ProductModel();
        $this->stockMovementModel = new StockMovementModel();
        $this->categoryModel = new ProductCategoryModel();
        $this->unitModel = new ProductUnitModel();
    }

    public function index()
    {
        $data = [
            'title'      => 'Produk',
            'page_title' => 'Manajemen Produk',
        ];

        return $this->renderView('products/index', $data);
    }

    public function scanPage()
    {
        $data = [
            'title'      => 'Scan Cepat Produk',
            'page_title' => 'Alur Cepat Scan Barang',
            'products'   => $this->productModel->orderBy('id', 'DESC')->findAll(),
            'categories' => $this->categoryModel->where('is_active', 1)->orderBy('name', 'ASC')->findAll(),
            'units'      => $this->unitModel->where('is_active', 1)->orderBy('name', 'ASC')->findAll(),
        ];

        return $this->renderView('products/scan_flow', $data);
    }

    public function data()
    {
        $products = $this->productModel->orderBy('id', 'DESC')->findAll();
        $canEdit = activeGroupCan('products.edit');
        $canStockIn = activeGroupCan('products.stock-in');
        $canViewHistory = activeGroupCan('products.list');

        $rows = [];
        $no = 1;

        foreach ($products as $product) {
            $stock = (int) $product['stock'];
            $minStock = (int) $product['min_stock'];
            $imageUrl = ! empty($product['image']) ? base_url((string) $product['image']) : null;

            $placeholder = '<span style="display:inline-flex;align-items:center;justify-content:center;width:42px;height:42px;border-radius:8px;border:1px solid #e5e7eb;background:#f1f5f9;"><svg xmlns=\'http://www.w3.org/2000/svg\' width=\'22\' height=\'22\' fill=\'none\' viewBox=\'0 0 24 24\' stroke=\'#94a3b8\'><rect x=\'3\' y=\'3\' width=\'18\' height=\'18\' rx=\'3\' stroke-width=\'1.5\'/><circle cx=\'8.5\' cy=\'8.5\' r=\'1.5\' stroke-width=\'1.5\'/><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'1.5\' d=\'M3 16l5-5 4 4 3-3 6 6\'/></svg></span>';
            $imageHtml = $imageUrl
                ? '<img src="' . esc($imageUrl) . '" alt="' . esc((string) $product['name']) . '" style="width:42px;height:42px;object-fit:cover;border-radius:8px;border:1px solid #e5e7eb;">'
                : $placeholder;

            $stockHtml = $stock . ' ' . esc((string) $product['unit']);
            if ($stock <= $minStock) {
                $stockHtml .= ' <span class="badge badge-warning">Menipis</span>';
            }

            $statusHtml = (int) $product['is_active'] === 1
                ? '<span class="badge badge-success">Aktif</span>'
                : '<span class="badge badge-secondary">Nonaktif</span>';

            $actionHtml = '';
            if ($canEdit) {
                $actionHtml .= '<a href="' . base_url('admin/products/edit/' . $product['id']) . '" class="btn btn-sm btn-info mr-1" title="Edit"><i class="fas fa-edit"></i></a>';
            }

            if ($canStockIn) {
                $actionHtml .= '<button type="button" class="btn btn-sm btn-success btn-stock-in-trigger" title="Stok Masuk" data-toggle="modal" data-target="#stockInModalGlobal" data-product-name="' . esc((string) $product['name']) . '" data-stock-in-url="' . base_url('admin/products/stock-in/' . $product['id']) . '"><i class="fas fa-plus-circle"></i></button>';
            }

            if ($canViewHistory) {
                $actionHtml .= '<a href="' . base_url('admin/products/mwa-history?product_id=' . $product['id']) . '" class="btn btn-sm btn-light ml-1" title="Histori Mutasi"><i class="fas fa-history"></i></a>';
            }

            $rows[] = [
                $no++,
                esc((string) ($product['sku'] ?: '-')),
                $imageHtml,
                esc((string) $product['name']),
                esc((string) ($product['category'] ?: '-')),
                'Rp ' . number_format((float) $product['sell_price'], 0, ',', '.'),
                $stockHtml,
                $minStock,
                $statusHtml,
                $actionHtml,
            ];
        }

        return $this->response->setJSON(['data' => $rows]);
    }

    public function create()
    {
        $data = [
            'title'      => 'Tambah Produk',
            'page_title' => 'Tambah Produk',
            'categories' => $this->categoryModel->where('is_active', 1)->orderBy('name', 'ASC')->findAll(),
            'units'      => $this->unitModel->where('is_active', 1)->orderBy('name', 'ASC')->findAll(),
        ];

        return $this->renderView('products/create', $data);
    }

    public function store()
    {
        $activeCategories = $this->getActiveCategoryNames();
        $activeUnits = $this->getActiveUnitNames();

        if ($activeCategories === [] || $activeUnits === []) {
            return redirect()->to('/admin/products')->with('error', 'Master kategori atau satuan belum tersedia. Silakan lengkapi master data terlebih dahulu.');
        }

        $rules = [
            'name'       => 'required|min_length[2]|max_length[255]',
            'sku'        => 'permit_empty|max_length[50]|is_unique[products.sku]',
            'category'   => $this->buildInListRule($activeCategories, true),
            'unit'       => $this->buildInListRule($activeUnits, true),
            'sell_price' => 'required|decimal',
            'stock'      => 'required|integer|greater_than_equal_to[0]',
            'min_stock'  => 'permit_empty|integer|greater_than_equal_to[0]',
            'image'      => 'permit_empty|is_image[image]|mime_in[image,image/jpg,image/jpeg,image/png,image/webp]|max_size[image,2048]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $uploadedImagePath = $this->uploadProductImage();

        $this->productModel->insert([
            'sku'        => $this->request->getPost('sku') ?: null,
            'image'      => $uploadedImagePath,
            'name'       => $this->request->getPost('name'),
            'category'   => $this->request->getPost('category') ?: null,
            'unit'       => $this->request->getPost('unit'),
            'cost_price' => $this->request->getPost('cost_price') ?: null,
            'sell_price' => $this->request->getPost('sell_price'),
            'stock'      => $this->request->getPost('stock') ?: 0,
            'min_stock'  => $this->request->getPost('min_stock') ?: 0,
            'is_active'  => $this->request->getPost('is_active') ? 1 : 0,
        ]);

        return redirect()->to('/admin/products')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $product = $this->productModel->find($id);

        if (! $product) {
            return redirect()->to('/admin/products')->with('error', 'Produk tidak ditemukan.');
        }

        $data = [
            'title'      => 'Edit Produk',
            'page_title' => 'Edit Produk',
            'product'    => $product,
            'categories' => $this->categoryModel->where('is_active', 1)->orderBy('name', 'ASC')->findAll(),
            'units'      => $this->unitModel->where('is_active', 1)->orderBy('name', 'ASC')->findAll(),
        ];

        return $this->renderView('products/edit', $data);
    }

    public function update(int $id)
    {
        $product = $this->productModel->find($id);

        $activeCategories = $this->getActiveCategoryNames();
        $activeUnits = $this->getActiveUnitNames();

        if (! $product) {
            return redirect()->to('/admin/products')->with('error', 'Produk tidak ditemukan.');
        }

        $rules = [
            'name'       => 'required|min_length[2]|max_length[255]',
            'sku'        => "permit_empty|max_length[50]|is_unique[products.sku,id,{$id}]",
            'category'   => $this->buildInListRule($activeCategories, true),
            'unit'       => $this->buildInListRule($activeUnits, true),
            'sell_price' => 'required|decimal',
            'stock'      => 'required|integer|greater_than_equal_to[0]',
            'min_stock'  => 'permit_empty|integer|greater_than_equal_to[0]',
            'image'      => 'permit_empty|is_image[image]|mime_in[image,image/jpg,image/jpeg,image/png,image/webp]|max_size[image,2048]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $oldStock = (int) $product['stock'];
        $newStock = (int) $this->request->getPost('stock');
        $oldAvgCost = (float) ($product['cost_price'] ?? 0);
        $newAvgCost = (float) ($this->request->getPost('cost_price') ?: 0);
        $newImagePath = $product['image'] ?? null;

        if ($this->request->getPost('remove_image') === '1') {
            $this->removeProductImage($newImagePath);
            $newImagePath = null;
        }

        $uploadedImagePath = $this->uploadProductImage();
        if ($uploadedImagePath !== null) {
            $this->removeProductImage($newImagePath);
            $newImagePath = $uploadedImagePath;
        }

        $this->productModel->update($id, [
            'sku'        => $this->request->getPost('sku') ?: null,
            'image'      => $newImagePath,
            'name'       => $this->request->getPost('name'),
            'category'   => $this->request->getPost('category') ?: null,
            'unit'       => $this->request->getPost('unit'),
            'cost_price' => $this->request->getPost('cost_price') ?: null,
            'sell_price' => $this->request->getPost('sell_price'),
            'stock'      => $newStock,
            'min_stock'  => $this->request->getPost('min_stock') ?: 0,
            'is_active'  => $this->request->getPost('is_active') ? 1 : 0,
        ]);

        $diff = $newStock - $oldStock;
        if ($diff !== 0) {
            $this->stockMovementModel->insert([
                'product_id'    => $id,
                'movement_type' => 'adjustment',
                'qty'           => $diff,
                'unit_cost_in'  => null,
                'avg_cost_before' => $oldAvgCost,
                'avg_cost_after'  => $newAvgCost,
                'stock_before'    => $oldStock,
                'stock_after'     => $newStock,
                'reference_no'  => 'ADJ-' . date('YmdHis'),
                'notes'         => 'Penyesuaian stok dari halaman edit produk',
                'user_id'       => auth()->id(),
            ]);
        }

        return redirect()->to('/admin/products')->with('success', 'Produk berhasil diperbarui.');
    }

    public function stockIn(int $id)
    {
        $product = $this->productModel->find($id);

        if (! $product) {
            return redirect()->to('/admin/products')->with('error', 'Produk tidak ditemukan.');
        }

        $rules = [
            'qty'        => 'required|integer|greater_than[0]',
            'cost_price' => 'required|decimal|greater_than_equal_to[0]',
            'notes'      => 'permit_empty|max_length[255]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $qty = (int) $this->request->getPost('qty');
        $incomingCostPrice = (float) $this->request->getPost('cost_price');

        $currentStock = (int) ($product['stock'] ?? 0);
        $currentAvgCost = (float) ($product['cost_price'] ?? 0);

        $newStock = $currentStock + $qty;
        $newAvgCost = $this->calculateMovingWeightedAverage(
            $currentStock,
            $currentAvgCost,
            $qty,
            $incomingCostPrice
        );

        $this->productModel
            ->where('id', $id)
            ->set('stock', $newStock)
            ->set('cost_price', $newAvgCost)
            ->update();

        $this->stockMovementModel->insert([
            'product_id'    => $id,
            'movement_type' => 'stock_in',
            'qty'           => $qty,
            'unit_cost_in'    => round($incomingCostPrice, 2),
            'avg_cost_before' => round($currentAvgCost, 2),
            'avg_cost_after'  => $newAvgCost,
            'stock_before'    => $currentStock,
            'stock_after'     => $newStock,
            'reference_no'  => 'IN-' . date('YmdHis'),
            'notes'         => $this->request->getPost('notes') ?: 'Stok masuk manual (MWA)',
            'user_id'       => auth()->id(),
        ]);

        return redirect()->to('/admin/products')->with('success', 'Stok berhasil ditambahkan. Harga beli rata-rata telah diperbarui (MWA).');
    }

    public function scanFlow()
    {
        $scanCode = trim((string) $this->request->getPost('scan_code'));
        $product  = null;
        $activeCategories = $this->getActiveCategoryNames();
        $activeUnits = $this->getActiveUnitNames();

        if ($scanCode !== '') {
            $product = $this->productModel->where('sku', $scanCode)->first();
        }

        $rules = [
            'scan_code'  => 'required|max_length[50]',
            'qty'        => 'required|integer|greater_than[0]',
            'cost_price' => 'required|decimal|greater_than_equal_to[0]',
            'notes'      => 'permit_empty|max_length[255]',
        ];

        if (! $product) {
            $rules['scan_code']  = 'required|max_length[50]|is_unique[products.sku]';
            $rules['name']       = 'required|min_length[2]|max_length[255]';
            $rules['sell_price'] = 'required|decimal|greater_than_equal_to[0]';
            $rules['category']   = $this->buildInListRule($activeCategories, true);
            $rules['unit']       = $this->buildInListRule($activeUnits, true);
            $rules['min_stock']  = 'permit_empty|integer|greater_than_equal_to[0]';
        }

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $qty       = (int) $this->request->getPost('qty');
        $costPrice = (float) $this->request->getPost('cost_price');
        $notes     = $this->request->getPost('notes') ?: null;

        if ($product) {
            $currentStock = (int) ($product['stock'] ?? 0);
            $currentAvgCost = (float) ($product['cost_price'] ?? 0);
            $newStock = $currentStock + $qty;
            $newAvgCost = $this->calculateMovingWeightedAverage(
                $currentStock,
                $currentAvgCost,
                $qty,
                $costPrice
            );

            $this->productModel
                ->where('id', $product['id'])
                ->set('stock', $newStock)
                ->set('cost_price', $newAvgCost)
                ->update();

            $this->stockMovementModel->insert([
                'product_id'    => $product['id'],
                'movement_type' => 'stock_in',
                'qty'           => $qty,
                'unit_cost_in'    => round($costPrice, 2),
                'avg_cost_before' => round($currentAvgCost, 2),
                'avg_cost_after'  => $newAvgCost,
                'stock_before'    => $currentStock,
                'stock_after'     => $newStock,
                'reference_no'  => 'IN-' . date('YmdHis'),
                'notes'         => $notes ?: 'Stok masuk via alur scan barang (MWA)',
                'user_id'       => auth()->id(),
            ]);

            return redirect()->to('/admin/products')->with('success', 'Produk ditemukan. Stok dan harga beli rata-rata berhasil diperbarui (MWA).');
        }

        if (! activeGroupCan('products.create')) {
            return redirect()->to('/admin/products')->with('error', 'Anda tidak memiliki izin membuat produk baru.');
        }

        $productId = $this->productModel->insert([
            'sku'        => $scanCode,
            'name'       => $this->request->getPost('name'),
            'category'   => $this->request->getPost('category') ?: null,
            'unit'       => $this->request->getPost('unit'),
            'cost_price' => $costPrice,
            'sell_price' => $this->request->getPost('sell_price'),
            'stock'      => $qty,
            'min_stock'  => $this->request->getPost('min_stock') ?: 0,
            'is_active'  => $this->request->getPost('is_active') ? 1 : 0,
        ], true);

        $this->stockMovementModel->insert([
            'product_id'    => $productId,
            'movement_type' => 'stock_in',
            'qty'           => $qty,
            'unit_cost_in'    => round($costPrice, 2),
            'avg_cost_before' => 0,
            'avg_cost_after'  => round($costPrice, 2),
            'stock_before'    => 0,
            'stock_after'     => $qty,
            'reference_no'  => 'IN-' . date('YmdHis'),
            'notes'         => $notes ?: 'Produk baru dari alur scan barang',
            'user_id'       => auth()->id(),
        ]);

        return redirect()->to('/admin/products/edit/' . $productId)->with('success', 'Produk baru dibuat. Lanjutkan input/edit detail produk.');
    }

    public function mwaHistory()
    {
        $from = $this->request->getGet('from') ?: date('Y-m-d', strtotime('-7 days'));
        $to = $this->request->getGet('to') ?: date('Y-m-d');
        $productId = (int) ($this->request->getGet('product_id') ?: 0);
        $movementType = trim((string) ($this->request->getGet('movement_type') ?: ''));

        $fromDateTime = $from . ' 00:00:00';
        $toDateTime = $to . ' 23:59:59';

        $allowedMovementTypes = ['stock_in', 'adjustment', 'sale'];
        if ($movementType !== '' && ! in_array($movementType, $allowedMovementTypes, true)) {
            $movementType = '';
        }

        $summaryBuilder = $this->stockMovementModel
            ->select('COUNT(*) as total_rows, COALESCE(SUM(CASE WHEN movement_type = "stock_in" THEN qty ELSE 0 END), 0) as total_stock_in_qty, COALESCE(SUM(CASE WHEN movement_type = "sale" THEN ABS(qty) ELSE 0 END), 0) as total_sale_qty, COALESCE(SUM(CASE WHEN avg_cost_before IS NULL OR avg_cost_after IS NULL THEN 0 ELSE (avg_cost_after - avg_cost_before) END), 0) as total_avg_cost_delta')
            ->where('created_at >=', $fromDateTime)
            ->where('created_at <=', $toDateTime);

        if ($productId > 0) {
            $summaryBuilder->where('product_id', $productId);
        }

        if ($movementType !== '') {
            $summaryBuilder->where('movement_type', $movementType);
        }

        $summary = $summaryBuilder->first() ?: [];

        $data = [
            'title' => 'Histori Mutasi Stok',
            'page_title' => 'Histori Mutasi Stok',
            'from' => $from,
            'to' => $to,
            'productId' => $productId,
            'movementType' => $movementType,
            'summary' => $summary,
            'products' => $this->productModel->select('id, sku, name')->orderBy('name', 'ASC')->findAll(),
        ];

        return $this->renderView('products/mwa_history', $data);
    }

    public function mwaHistoryData()
    {
        $from = $this->request->getGet('from') ?: date('Y-m-d', strtotime('-7 days'));
        $to = $this->request->getGet('to') ?: date('Y-m-d');
        $productId = (int) ($this->request->getGet('product_id') ?: 0);
        $movementType = trim((string) ($this->request->getGet('movement_type') ?: ''));

        $fromDateTime = $from . ' 00:00:00';
        $toDateTime = $to . ' 23:59:59';

        $allowedMovementTypes = ['stock_in', 'adjustment', 'sale'];
        if ($movementType !== '' && ! in_array($movementType, $allowedMovementTypes, true)) {
            $movementType = '';
        }

        $builder = $this->stockMovementModel
            ->select('stock_movements.*, products.sku, products.name as product_name')
            ->join('products', 'products.id = stock_movements.product_id', 'left')
            ->where('stock_movements.created_at >=', $fromDateTime)
            ->where('stock_movements.created_at <=', $toDateTime);

        if ($productId > 0) {
            $builder->where('stock_movements.product_id', $productId);
        }

        if ($movementType !== '') {
            $builder->where('stock_movements.movement_type', $movementType);
        }

        $movements = $builder
            ->orderBy('stock_movements.id', 'DESC')
            ->findAll(1000);

        $rows = [];

        foreach ($movements as $row) {
            $hasAvgBefore = $row['avg_cost_before'] !== null;
            $hasAvgAfter = $row['avg_cost_after'] !== null;
            $avgBefore = (float) ($row['avg_cost_before'] ?? 0);
            $avgAfter = (float) ($row['avg_cost_after'] ?? 0);
            $deltaAvg = ($hasAvgBefore && $hasAvgAfter) ? ($avgAfter - $avgBefore) : null;

            $movementTypeHtml = '<span class="badge badge-secondary">' . esc(strtoupper((string) ($row['movement_type'] ?? '-'))) . '</span>';
            if (($row['movement_type'] ?? '') === 'stock_in') {
                $movementTypeHtml = '<span class="badge badge-success">STOK MASUK</span>';
            } elseif (($row['movement_type'] ?? '') === 'adjustment') {
                $movementTypeHtml = '<span class="badge badge-warning">PENYESUAIAN</span>';
            } elseif (($row['movement_type'] ?? '') === 'sale') {
                $movementTypeHtml = '<span class="badge badge-secondary">PENJUALAN</span>';
            }

            if ($deltaAvg === null) {
                $deltaAvgHtml = '<span class="text-muted">-</span>';
            } elseif ($deltaAvg > 0) {
                $deltaAvgHtml = '<span class="text-success">+Rp ' . number_format($deltaAvg, 2, ',', '.') . '</span>';
            } elseif ($deltaAvg < 0) {
                $deltaAvgHtml = '<span class="text-danger">-Rp ' . number_format(abs($deltaAvg), 2, ',', '.') . '</span>';
            } else {
                $deltaAvgHtml = '<span class="text-muted">Rp 0,00</span>';
            }

            $rows[] = [
                esc((string) ($row['created_at'] ?? '-')),
                '<div>' . esc((string) ($row['product_name'] ?? '-')) . '</div><small class="text-muted">SKU: ' . esc((string) ($row['sku'] ?? '-')) . '</small>',
                $movementTypeHtml,
                (int) ($row['qty'] ?? 0),
                $row['unit_cost_in'] !== null ? 'Rp ' . number_format((float) $row['unit_cost_in'], 2, ',', '.') : '-',
                $hasAvgBefore ? 'Rp ' . number_format($avgBefore, 2, ',', '.') : '-',
                $hasAvgAfter ? 'Rp ' . number_format($avgAfter, 2, ',', '.') : '-',
                $row['stock_before'] !== null ? (int) $row['stock_before'] : '-',
                $row['stock_after'] !== null ? (int) $row['stock_after'] : '-',
                $deltaAvgHtml,
                esc((string) ($row['reference_no'] ?? '-')),
                esc((string) ($row['notes'] ?? '-')),
            ];
        }

        return $this->response->setJSON(['data' => $rows]);
    }

    private function getActiveCategoryNames(): array
    {
        $rows = $this->categoryModel->where('is_active', 1)->orderBy('name', 'ASC')->findAll();

        return array_values(array_map(static fn ($row) => $row['name'], $rows));
    }

    private function getActiveUnitNames(): array
    {
        $rows = $this->unitModel->where('is_active', 1)->orderBy('name', 'ASC')->findAll();

        return array_values(array_map(static fn ($row) => $row['name'], $rows));
    }

    private function buildInListRule(array $values, bool $required): string
    {
        $rules = [];

        $rules[] = $required ? 'required' : 'permit_empty';

        if ($values !== []) {
            $rules[] = 'in_list[' . implode(',', $values) . ']';
        }

        return implode('|', $rules);
    }

    private function calculateMovingWeightedAverage(int $currentStock, float $currentAvgCost, int $incomingQty, float $incomingUnitCost): float
    {
        $totalQty = $currentStock + $incomingQty;
        if ($totalQty <= 0) {
            return 0.0;
        }

        if ($currentStock <= 0) {
            return round($incomingUnitCost, 2);
        }

        $currentInventoryValue = $currentStock * $currentAvgCost;
        $incomingInventoryValue = $incomingQty * $incomingUnitCost;

        return round(($currentInventoryValue + $incomingInventoryValue) / $totalQty, 2);
    }

    private function uploadProductImage(string $field = 'image'): ?string
    {
        $file = $this->request->getFile($field);

        if (! $file || ! $file->isValid() || $file->getError() === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        $uploadDir = FCPATH . 'uploads/products';
        if (! is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $newName = $file->getRandomName();
        $file->move($uploadDir, $newName);

        return 'uploads/products/' . $newName;
    }

    private function removeProductImage(?string $relativePath): void
    {
        if (! $relativePath) {
            return;
        }

        $fullPath = FCPATH . ltrim($relativePath, '/');
        if (is_file($fullPath)) {
            @unlink($fullPath);
        }
    }
}
