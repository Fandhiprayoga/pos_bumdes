<?php

namespace App\Controllers;

use App\Models\CashShiftModel;
use App\Models\PendingPosTransactionModel;
use App\Models\ProductCategoryModel;
use App\Models\ProductModel;
use App\Models\SaleItemModel;
use App\Models\SaleModel;
use App\Models\StockMovementModel;
use CodeIgniter\Shield\Models\UserModel;
use Config\Database;

class PosController extends BaseController
{
    protected ProductModel $productModel;
    protected ProductCategoryModel $productCategoryModel;
    protected SaleModel $saleModel;
    protected SaleItemModel $saleItemModel;
    protected CashShiftModel $cashShiftModel;
    protected PendingPosTransactionModel $pendingPosTransactionModel;
    protected StockMovementModel $stockMovementModel;
    protected UserModel $userModel;

    public function __construct()
    {
        $this->productModel = new ProductModel();
        $this->productCategoryModel = new ProductCategoryModel();
        $this->saleModel = new SaleModel();
        $this->saleItemModel = new SaleItemModel();
        $this->cashShiftModel = new CashShiftModel();
        $this->pendingPosTransactionModel = new PendingPosTransactionModel();
        $this->stockMovementModel = new StockMovementModel();
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $userId = auth()->id();

        $openShift = $this->cashShiftModel
            ->where('user_id', $userId)
            ->where('closed_at', null)
            ->first();

        if (! $openShift) {
            return $this->shift();
        }

        $data = [
            'title'       => 'POS Kasir',
            'page_title'  => 'POS Kasir',
            'hideSectionHeader' => true,
            'nextInvoiceNo' => $this->generateInvoiceNo(),
            'openShift'   => $openShift,
            'categories'  => $this->productCategoryModel
                ->where('is_active', 1)
                ->orderBy('name', 'ASC')
                ->findAll(),
            'products'    => $this->productModel
                ->where('is_active', 1)
                ->where('stock >', 0)
                ->orderBy('name', 'ASC')
                ->findAll(),
            'recentSales' => $this->saleModel
                ->where('cashier_id', $userId)
                ->orderBy('id', 'DESC')
                ->limit(10)
                ->findAll(),
        ];

        return $this->renderView('pos/index', $data);
    }

    public function shift()
    {
        $userId = auth()->id();
        $from = trim((string) ($this->request->getGet('from') ?? ''));
        $to = trim((string) ($this->request->getGet('to') ?? ''));
        $cashierId = (int) ($this->request->getGet('cashier_id') ?? 0);
        $isManagerUp = activeGroupIs('manager', 'admin', 'superadmin');

        $openShift = $this->cashShiftModel
            ->where('user_id', $userId)
            ->where('closed_at', null)
            ->first();

        $otherOpenShift = $this->cashShiftModel
            ->where('user_id !=', $userId)
            ->where('closed_at', null)
            ->first();

        $otherOpenShiftOwner = null;
        if ($otherOpenShift) {
            $otherOpenShiftOwner = $this->getUserIdentity((int) $otherOpenShift['user_id']);
        }

        $shiftHistoryQuery = $this->cashShiftModel;

        if (! $isManagerUp) {
            $shiftHistoryQuery->where('user_id', $userId);
        } elseif ($cashierId > 0) {
            $shiftHistoryQuery->where('user_id', $cashierId);
        }

        if ($from !== '') {
            $shiftHistoryQuery->where('opened_at >=', $from . ' 00:00:00');
        }

        if ($to !== '') {
            $shiftHistoryQuery->where('opened_at <=', $to . ' 23:59:59');
        }

        $shiftHistory = $shiftHistoryQuery
            ->orderBy('id', 'DESC')
            ->limit(50)
            ->findAll();

        $userLabels = [];
        foreach ($shiftHistory as &$shift) {
            $shiftUserId = (int) ($shift['user_id'] ?? 0);
            if (! isset($userLabels[$shiftUserId])) {
                $userLabels[$shiftUserId] = $this->getUserIdentity($shiftUserId);
            }

            $shift['cashier_identity'] = $userLabels[$shiftUserId] ?? ('User ID ' . $shiftUserId);
        }
        unset($shift);

        $cashierOptions = [];
        if ($isManagerUp) {
            $cashierRows = $this->cashShiftModel
                ->select('user_id')
                ->groupBy('user_id')
                ->orderBy('user_id', 'ASC')
                ->findAll();

            foreach ($cashierRows as $row) {
                $optionUserId = (int) ($row['user_id'] ?? 0);
                if ($optionUserId <= 0) {
                    continue;
                }

                $cashierOptions[] = [
                    'id' => $optionUserId,
                    'label' => $this->getUserIdentity($optionUserId),
                ];
            }
        }

        $data = [
            'title'       => 'Shift Kasir',
            'page_title'  => 'Buka / Tutup Shift',
            'openShift'   => $openShift,
            'otherOpenShift' => $otherOpenShift,
            'otherOpenShiftOwner' => $otherOpenShiftOwner,
            'shiftHistory' => $shiftHistory,
            'historyFrom' => $from,
            'historyTo'   => $to,
            'historyCashierId' => $cashierId,
            'historyIsManagerUp' => $isManagerUp,
            'historyCashierOptions' => $cashierOptions,
        ];

        return $this->renderView('pos/shift', $data);
    }

    public function openShift()
    {
        $userId = auth()->id();

        $existingShift = $this->cashShiftModel
            ->where('user_id', $userId)
            ->where('closed_at', null)
            ->first();

        if ($existingShift) {
            return redirect()->to('/pos')->with('error', 'Shift masih terbuka. Tutup shift sebelumnya terlebih dahulu.');
        }

        $otherOpenShift = $this->cashShiftModel
            ->where('user_id !=', $userId)
            ->where('closed_at', null)
            ->first();

        if ($otherOpenShift) {
            $ownerIdentity = $this->getUserIdentity((int) $otherOpenShift['user_id']);

            return redirect()->to('/pos')->with('error', 'Tidak bisa membuka shift karena masih ada shift aktif dari user lain (' . $ownerIdentity . ').');
        }

        $rules = [
            'opening_cash' => 'required|decimal|greater_than_equal_to[0]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->cashShiftModel->insert([
            'user_id'      => $userId,
            'opened_at'    => date('Y-m-d H:i:s'),
            'opening_cash' => $this->request->getPost('opening_cash'),
        ]);

        return redirect()->to('/pos')->with('success', 'Shift berhasil dibuka.');
    }

    private function getUserIdentity(int $userId): string
    {
        $user = $this->userModel->findById($userId);
        if (! $user) {
            return 'User ID ' . $userId;
        }

        $username = trim((string) ($user->username ?? ''));
        $email = trim((string) ($user->email ?? ''));

        if ($username !== '' && $email !== '') {
            return $username . ' (' . $email . ')';
        }

        if ($username !== '') {
            return $username;
        }

        if ($email !== '') {
            return $email;
        }

        return 'User ID ' . $userId;
    }

    public function checkout()
    {
        $userId = auth()->id();

        $openShift = $this->cashShiftModel
            ->where('user_id', $userId)
            ->where('closed_at', null)
            ->first();

        if (! $openShift) {
            return redirect()->to('/pos')->with('error', 'Anda harus membuka shift sebelum transaksi.');
        }

        $productIds = $this->request->getPost('product_id');
        $qtys       = $this->request->getPost('qty');

        if (! is_array($productIds) || ! is_array($qtys) || count($productIds) === 0) {
            return redirect()->back()->with('error', 'Item transaksi tidak valid.');
        }

        $items = [];
        $subtotal = 0;

        foreach ($productIds as $i => $productId) {
            $productId = (int) $productId;
            $qty       = isset($qtys[$i]) ? (int) $qtys[$i] : 0;

            if ($productId <= 0 || $qty <= 0) {
                continue;
            }

            $product = $this->productModel->find($productId);
            if (! $product || (int) $product['is_active'] !== 1) {
                return redirect()->back()->with('error', 'Ada produk yang tidak tersedia.');
            }

            if ((int) $product['stock'] < $qty) {
                return redirect()->back()->with('error', 'Stok tidak cukup untuk produk: ' . $product['name']);
            }

            $lineTotal = $qty * (float) $product['sell_price'];
            $subtotal += $lineTotal;

            $items[] = [
                'product_id'   => $productId,
                'product_name' => $product['name'],
                'qty'          => $qty,
                'unit_price'   => (float) $product['sell_price'],
                'cost_price_snapshot' => (float) ($product['cost_price'] ?? 0),
                'line_total'   => $lineTotal,
            ];
        }

        if ($items === []) {
            return redirect()->back()->with('error', 'Silakan pilih minimal satu item dengan qty valid.');
        }

        $discountAmount = (float) ($this->request->getPost('discount_amount') ?: 0);
        if ($discountAmount < 0) {
            $discountAmount = 0;
        }

        $grandTotal = $subtotal - $discountAmount;
        if ($grandTotal < 0) {
            $grandTotal = 0;
        }

        $paymentMethod = $this->request->getPost('payment_method');
        $allowedMethod = ['cash', 'transfer'];
        if (! in_array($paymentMethod, $allowedMethod, true)) {
            return redirect()->back()->with('error', 'Metode pembayaran tidak valid.');
        }

        $amountPaid = (float) ($this->request->getPost('amount_paid') ?: 0);
        if ($amountPaid < $grandTotal) {
            return redirect()->back()->with('error', 'Jumlah bayar kurang dari total transaksi.');
        }

        $change = $amountPaid - $grandTotal;
        $requestedInvoiceNo = strtoupper(trim((string) $this->request->getPost('invoice_no')));
        $invoiceNo = $this->resolveInvoiceNo($requestedInvoiceNo);

        $db = Database::connect();
        $db->transStart();

        $saleId = $this->saleModel->insert([
            'invoice_no'     => $invoiceNo,
            'shift_id'       => $openShift['id'],
            'cashier_id'     => $userId,
            'customer_name'  => $this->request->getPost('customer_name') ?: null,
            'payment_method' => $paymentMethod,
            'subtotal'       => $subtotal,
            'discount_amount'=> $discountAmount,
            'grand_total'    => $grandTotal,
            'amount_paid'    => $amountPaid,
            'change_amount'  => $change,
            'sold_at'        => date('Y-m-d H:i:s'),
        ], true);

        $allocatedDiscount = 0.0;
        $itemsCount = count($items);

        foreach ($items as $index => $item) {
            if ($discountAmount <= 0 || $subtotal <= 0) {
                $itemDiscount = 0.0;
            } elseif ($index === $itemsCount - 1) {
                $itemDiscount = max(0, round($discountAmount - $allocatedDiscount, 2));
            } else {
                $itemDiscount = round(($item['line_total'] / $subtotal) * $discountAmount, 2);
                $allocatedDiscount += $itemDiscount;
            }

            $netLineTotal = max(0, round($item['line_total'] - $itemDiscount, 2));
            $cogsTotal = round($item['cost_price_snapshot'] * $item['qty'], 2);
            $grossProfit = round($netLineTotal - $cogsTotal, 2);

            $this->saleItemModel->insert([
                'sale_id'      => $saleId,
                'product_id'   => $item['product_id'],
                'product_name' => $item['product_name'],
                'qty'          => $item['qty'],
                'unit_price'   => $item['unit_price'],
                'cost_price_snapshot' => $item['cost_price_snapshot'],
                'cogs_total'   => $cogsTotal,
                'discount_allocated' => $itemDiscount,
                'line_total'   => $item['line_total'],
                'net_line_total' => $netLineTotal,
                'gross_profit' => $grossProfit,
            ]);

            $this->productModel
                ->where('id', $item['product_id'])
                ->set('stock', 'stock - ' . $item['qty'], false)
                ->update();

            $this->stockMovementModel->insert([
                'product_id'    => $item['product_id'],
                'movement_type' => 'sale',
                'qty'           => -$item['qty'],
                'reference_no'  => $invoiceNo,
                'notes'         => 'Transaksi POS',
                'user_id'       => $userId,
            ]);
        }

        $db->transComplete();

        if (! $db->transStatus()) {
            return redirect()->back()->with('error', 'Transaksi gagal disimpan. Silakan coba lagi.');
        }

        $checkoutAction = strtolower(trim((string) $this->request->getPost('checkout_action')));
        if ($checkoutAction === 'save_print') {
            return redirect()->to('/pos/receipt/' . $saleId)->with('success', 'Transaksi berhasil disimpan. Invoice: ' . $invoiceNo);
        }

        return redirect()->to('/pos')->with('success', 'Transaksi berhasil disimpan. Invoice: ' . $invoiceNo);
    }

    public function receipt(int $saleId)
    {
        $sale = $this->saleModel->find($saleId);
        if (! $sale) {
            return redirect()->to('/pos')->with('error', 'Data transaksi tidak ditemukan.');
        }

        $items = $this->saleItemModel
            ->where('sale_id', $saleId)
            ->orderBy('id', 'ASC')
            ->findAll();

        $cashierIdentity = $this->getUserIdentity((int) ($sale['cashier_id'] ?? 0));

        return view('pos/receipt', [
            'sale'            => $sale,
            'items'           => $items,
            'cashierIdentity' => $cashierIdentity,
        ]);
    }

    public function pendingList()
    {
        $userId = auth()->id();

        $pendingTransactions = $this->pendingPosTransactionModel
            ->where('user_id', $userId)
            ->orderBy('updated_at', 'DESC')
            ->findAll();

        return $this->response->setJSON([
            'success' => true,
            'data' => array_map(function (array $pending): array {
                return [
                    'id' => (int) $pending['id'],
                    'invoice_no' => (string) $pending['invoice_no'],
                    'customer_name' => (string) ($pending['customer_name'] ?? ''),
                    'payment_method' => (string) ($pending['payment_method'] ?? 'cash'),
                    'discount_amount' => (float) ($pending['discount_amount'] ?? 0),
                    'amount_paid' => (float) ($pending['amount_paid'] ?? 0),
                    'subtotal_amount' => (float) ($pending['subtotal_amount'] ?? 0),
                    'grand_total' => (float) ($pending['grand_total'] ?? 0),
                    'item_count' => (int) ($pending['item_count'] ?? 0),
                    'updated_at' => (string) ($pending['updated_at'] ?? $pending['created_at'] ?? ''),
                ];
            }, $pendingTransactions),
            'csrfHash' => csrf_hash(),
        ]);
    }

    public function savePending()
    {
        $userId = auth()->id();

        $openShift = $this->cashShiftModel
            ->where('user_id', $userId)
            ->where('closed_at', null)
            ->first();

        if (! $openShift) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => 'Anda harus membuka shift sebelum menyimpan transaksi tertunda.',
                'csrfHash' => csrf_hash(),
            ]);
        }

        $cartPayloadRaw = (string) $this->request->getPost('cart_payload');
        $cartPayload = json_decode($cartPayloadRaw, true);
        if (! is_array($cartPayload) || $cartPayload === []) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => 'Tidak ada item transaksi untuk disimpan.',
                'csrfHash' => csrf_hash(),
            ]);
        }

        $itemCount = 0;
        $subtotal = 0.0;
        $normalizedItems = [];

        foreach ($cartPayload as $item) {
            $productId = (int) ($item['id'] ?? 0);
            $qty = max(0, (int) ($item['qty'] ?? 0));
            $price = max(0, (float) ($item['price'] ?? 0));

            if ($productId <= 0 || $qty <= 0) {
                continue;
            }

            $normalizedItems[] = [
                'id' => $productId,
                'name' => (string) ($item['name'] ?? 'Produk'),
                'price' => $price,
                'stock' => max(0, (int) ($item['stock'] ?? 0)),
                'qty' => $qty,
            ];

            $itemCount += $qty;
            $subtotal += $qty * $price;
        }

        if ($normalizedItems === []) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => 'Item transaksi tertunda tidak valid.',
                'csrfHash' => csrf_hash(),
            ]);
        }

        $discountAmount = max(0, (float) ($this->request->getPost('discount_amount') ?: 0));
        $grandTotal = max(0, $subtotal - $discountAmount);
        $paymentMethod = (string) ($this->request->getPost('payment_method') ?: 'cash');
        if (! in_array($paymentMethod, ['cash', 'transfer'], true)) {
            $paymentMethod = 'cash';
        }

        $requestedInvoiceNo = strtoupper(trim((string) $this->request->getPost('invoice_no')));
        $invoiceNo = $this->resolvePendingInvoiceNo($requestedInvoiceNo);

        $this->pendingPosTransactionModel->insert([
            'user_id' => $userId,
            'shift_id' => (int) $openShift['id'],
            'invoice_no' => $invoiceNo,
            'customer_name' => $this->request->getPost('customer_name') ?: null,
            'payment_method' => $paymentMethod,
            'discount_amount' => $discountAmount,
            'amount_paid' => max(0, (float) ($this->request->getPost('amount_paid') ?: 0)),
            'subtotal_amount' => $subtotal,
            'grand_total' => $grandTotal,
            'item_count' => $itemCount,
            'cart_payload' => json_encode($normalizedItems, JSON_UNESCAPED_UNICODE),
        ]);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Transaksi tertunda berhasil disimpan.',
            'nextInvoiceNo' => $this->generateInvoiceNo(),
            'csrfHash' => csrf_hash(),
        ]);
    }

    public function restorePending(int $id)
    {
        $pending = $this->pendingPosTransactionModel
            ->where('id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if (! $pending) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Transaksi tertunda tidak ditemukan.',
                'csrfHash' => csrf_hash(),
            ]);
        }

        $cartPayload = json_decode((string) $pending['cart_payload'], true);
        if (! is_array($cartPayload) || $cartPayload === []) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => 'Data transaksi tertunda rusak.',
                'csrfHash' => csrf_hash(),
            ]);
        }

        // Validasi stok setiap item sebelum restore
        $stockWarnings = [];
        $productModel  = new \App\Models\ProductModel();
        foreach ($cartPayload as &$item) {
            // cart payload bisa pakai key 'id' atau 'product_id'
            $productId = $item['product_id'] ?? $item['id'] ?? null;
            $itemName  = $item['product_name'] ?? $item['name'] ?? 'Produk tidak dikenal';
            $product = $productId ? $productModel->find((int) $productId) : null;
            if (! $product) {
                $stockWarnings[] = [
                    'name'      => $itemName,
                    'requested' => (int) ($item['qty'] ?? 0),
                    'available' => 0,
                ];
                $item['qty'] = 0;
                continue;
            }
            $available = (int) ($product['stock'] ?? 0);
            $requested = (int) ($item['qty'] ?? 0);
            if ($requested > $available) {
                $stockWarnings[] = [
                    'name'      => $product['name'],
                    'requested' => $requested,
                    'available' => $available,
                ];
                $item['qty'] = $available;
            }
        }
        unset($item);

        // Hapus item dengan qty 0
        $cartPayload = array_values(array_filter($cartPayload, fn($i) => ($i['qty'] ?? 0) > 0));

        $this->pendingPosTransactionModel->delete($pending['id']);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Transaksi tertunda berhasil dimuat.',
            'stockWarnings' => $stockWarnings,
            'data' => [
                'invoice_no' => (string) $pending['invoice_no'],
                'customer_name' => (string) ($pending['customer_name'] ?? ''),
                'payment_method' => (string) ($pending['payment_method'] ?? 'cash'),
                'discount_amount' => (float) ($pending['discount_amount'] ?? 0),
                'amount_paid' => (float) ($pending['amount_paid'] ?? 0),
                'cart_items' => $cartPayload,
            ],
            'nextInvoiceNo' => $this->generateInvoiceNo(),
            'csrfHash' => csrf_hash(),
        ]);
    }

    public function deletePending(int $id)
    {
        $pending = $this->pendingPosTransactionModel
            ->where('id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if (! $pending) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Transaksi tertunda tidak ditemukan.',
                'csrfHash' => csrf_hash(),
            ]);
        }

        $this->pendingPosTransactionModel->delete($pending['id']);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Transaksi tertunda berhasil dihapus.',
            'csrfHash' => csrf_hash(),
        ]);
    }

    private function resolveInvoiceNo(string $requestedInvoiceNo): string
    {
        if ($requestedInvoiceNo !== ''
            && preg_match('/^INV[0-9]{14}[A-Z0-9]{4}$/', $requestedInvoiceNo) === 1
            && ! $this->saleModel->where('invoice_no', $requestedInvoiceNo)->first()) {
            return $requestedInvoiceNo;
        }

        return $this->generateInvoiceNo();
    }

    private function resolvePendingInvoiceNo(string $requestedInvoiceNo): string
    {
        if ($requestedInvoiceNo !== '' && preg_match('/^INV[0-9]{14}[A-Z0-9]{4}$/', $requestedInvoiceNo) === 1 && $this->isInvoiceNoAvailable($requestedInvoiceNo)) {
            return $requestedInvoiceNo;
        }

        return $this->generateInvoiceNo();
    }

    private function isInvoiceNoAvailable(string $invoiceNo): bool
    {
        if ($this->saleModel->where('invoice_no', $invoiceNo)->first()) {
            return false;
        }

        if ($this->pendingPosTransactionModel->where('invoice_no', $invoiceNo)->first()) {
            return false;
        }

        return true;
    }

    private function generateInvoiceNo(): string
    {
        do {
            $candidate = 'INV' . date('YmdHis') . strtoupper(substr(bin2hex(random_bytes(3)), 0, 4));
        } while (! $this->isInvoiceNoAvailable($candidate));

        return $candidate;
    }

    public function closeShift()
    {
        $openShift = $this->cashShiftModel
            ->where('user_id', auth()->id())
            ->where('closed_at', null)
            ->first();

        if (! $openShift) {
            return redirect()->to('/pos')->with('error', 'Tidak ada shift terbuka untuk ditutup.');
        }

        $rules = [
            'closing_cash_actual' => 'required|decimal|greater_than_equal_to[0]',
            'notes'               => 'permit_empty|max_length[255]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $cashSales = $this->saleModel
            ->selectSum('grand_total', 'cash_total')
            ->where('shift_id', $openShift['id'])
            ->where('payment_method', 'cash')
            ->first();

        $cashTotal = (float) ($cashSales['cash_total'] ?? 0);
        $systemCash = (float) $openShift['opening_cash'] + $cashTotal;
        $actualCash = (float) $this->request->getPost('closing_cash_actual');
        $variance = $actualCash - $systemCash;

        $this->cashShiftModel->update($openShift['id'], [
            'closed_at'           => date('Y-m-d H:i:s'),
            'closing_cash_system' => $systemCash,
            'closing_cash_actual' => $actualCash,
            'variance'            => $variance,
            'notes'               => $this->request->getPost('notes') ?: null,
        ]);

        return redirect()->to('/pos')->with('success', 'Shift berhasil ditutup. Selisih kas: ' . number_format($variance, 0, ',', '.'));
    }

    public function history()
    {
        $from = $this->request->getGet('from') ?: date('Y-m-d');
        $to   = $this->request->getGet('to') ?: date('Y-m-d');
        $userId = auth()->id();
        $isCashier = activeGroupIs('cashier');

        $fromDateTime = $from . ' 00:00:00';
        $toDateTime   = $to . ' 23:59:59';

        $salesQuery = $this->saleModel
            ->where('sold_at >=', $fromDateTime)
            ->where('sold_at <=', $toDateTime);

        if ($isCashier) {
            $salesQuery->where('cashier_id', $userId);
        }

        $sales = $salesQuery
            ->orderBy('id', 'DESC')
            ->findAll();

        $summaryQuery = $this->saleModel
            ->select('COUNT(*) as total_tx, COALESCE(SUM(grand_total), 0) as total_amount')
            ->where('sold_at >=', $fromDateTime)
            ->where('sold_at <=', $toDateTime);

        if ($isCashier) {
            $summaryQuery->where('cashier_id', $userId);
        }

        $summary = $summaryQuery->first();

        $data = [
            'title'      => 'Riwayat Penjualan',
            'page_title' => 'Riwayat Penjualan',
            'from'       => $from,
            'to'         => $to,
            'sales'      => $sales,
            'summary'    => $summary,
            'isCashierHistoryLimited' => $isCashier,
        ];

        return $this->renderView('pos/history', $data);
    }
}
