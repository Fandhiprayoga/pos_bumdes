<?php

namespace App\Controllers;

use App\Models\CashShiftModel;
use App\Models\ProductModel;
use App\Models\SaleItemModel;
use App\Models\SaleModel;
use App\Models\StockMovementModel;
use Config\Database;

class PosController extends BaseController
{
    protected ProductModel $productModel;
    protected SaleModel $saleModel;
    protected SaleItemModel $saleItemModel;
    protected CashShiftModel $cashShiftModel;
    protected StockMovementModel $stockMovementModel;

    public function __construct()
    {
        $this->productModel = new ProductModel();
        $this->saleModel = new SaleModel();
        $this->saleItemModel = new SaleItemModel();
        $this->cashShiftModel = new CashShiftModel();
        $this->stockMovementModel = new StockMovementModel();
    }

    public function index()
    {
        $userId = auth()->id();

        $openShift = $this->cashShiftModel
            ->where('user_id', $userId)
            ->where('closed_at', null)
            ->first();

        $data = [
            'title'       => 'POS Kasir',
            'page_title'  => 'POS Kasir',
            'openShift'   => $openShift,
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

    public function openShift()
    {
        $existingShift = $this->cashShiftModel
            ->where('user_id', auth()->id())
            ->where('closed_at', null)
            ->first();

        if ($existingShift) {
            return redirect()->to('/pos')->with('error', 'Shift masih terbuka. Tutup shift sebelumnya terlebih dahulu.');
        }

        $rules = [
            'opening_cash' => 'required|decimal|greater_than_equal_to[0]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->cashShiftModel->insert([
            'user_id'      => auth()->id(),
            'opened_at'    => date('Y-m-d H:i:s'),
            'opening_cash' => $this->request->getPost('opening_cash'),
        ]);

        return redirect()->to('/pos')->with('success', 'Shift berhasil dibuka.');
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
        $invoiceNo = 'INV' . date('YmdHis') . strtoupper(substr(bin2hex(random_bytes(3)), 0, 4));

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

        return redirect()->to('/pos')->with('success', 'Transaksi berhasil disimpan. Invoice: ' . $invoiceNo);
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

        $fromDateTime = $from . ' 00:00:00';
        $toDateTime   = $to . ' 23:59:59';

        $sales = $this->saleModel
            ->where('sold_at >=', $fromDateTime)
            ->where('sold_at <=', $toDateTime)
            ->orderBy('id', 'DESC')
            ->findAll();

        $summary = $this->saleModel
            ->select('COUNT(*) as total_tx, COALESCE(SUM(grand_total), 0) as total_amount')
            ->where('sold_at >=', $fromDateTime)
            ->where('sold_at <=', $toDateTime)
            ->first();

        $data = [
            'title'      => 'Riwayat Penjualan',
            'page_title' => 'Riwayat Penjualan',
            'from'       => $from,
            'to'         => $to,
            'sales'      => $sales,
            'summary'    => $summary,
        ];

        return $this->renderView('pos/history', $data);
    }
}
