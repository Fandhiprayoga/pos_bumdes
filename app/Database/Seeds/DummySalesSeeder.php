<?php

namespace App\Database\Seeds;

use App\Models\CashShiftModel;
use App\Models\ProductModel;
use App\Models\SaleItemModel;
use App\Models\SaleModel;
use App\Models\StockMovementModel;
use CodeIgniter\Database\Seeder;
use Config\Database;

class DummySalesSeeder extends Seeder
{
    public function run()
    {
        $productModel = new ProductModel();

        if ($productModel->countAllResults() === 0) {
            $this->call(ProductSeeder::class);
        }

        $users = auth()->getProvider();
        $cashier = $users->findByCredentials(['email' => 'cashier@example.com']);
        $admin = $users->findByCredentials(['email' => 'admin@example.com']);

        $cashierId = $cashier?->id ?? $admin?->id;

        if (! $cashierId) {
            echo "User kasir/admin tidak ditemukan. Jalankan UserSeeder terlebih dahulu.\n";
            return;
        }

        $shiftModel = new CashShiftModel();
        $saleModel = new SaleModel();
        $saleItemModel = new SaleItemModel();
        $stockMovementModel = new StockMovementModel();

        $salesBlueprint = [
            [
                'invoice_no' => 'DUMMY-20260427-001',
                'opened_at' => '2026-04-27 07:00:00',
                'opening_cash' => 250000,
                'closed_at' => '2026-04-27 14:00:00',
                'closing_cash_actual' => 562000,
                'customer_name' => 'Pelanggan Umum',
                'payment_method' => 'cash',
                'discount_amount' => 2000,
                'amount_paid' => 56000,
                'sold_at' => '2026-04-27 08:15:00',
                'items' => [
                    ['sku' => 'BRG-001', 'qty' => 1],
                    ['sku' => 'BRG-004', 'qty' => 2],
                    ['sku' => 'BRG-005', 'qty' => 4],
                ],
            ],
            [
                'invoice_no' => 'DUMMY-20260427-002',
                'opened_at' => '2026-04-27 07:00:00',
                'opening_cash' => 250000,
                'closed_at' => '2026-04-27 14:00:00',
                'closing_cash_actual' => 562000,
                'customer_name' => 'Warung RT 03',
                'payment_method' => 'transfer',
                'discount_amount' => 0,
                'amount_paid' => 132000,
                'sold_at' => '2026-04-27 10:10:00',
                'items' => [
                    ['sku' => 'BRG-002', 'qty' => 3],
                    ['sku' => 'BRG-003', 'qty' => 2],
                    ['sku' => 'BRG-012', 'qty' => 10],
                ],
            ],
            [
                'invoice_no' => 'DUMMY-20260427-003',
                'opened_at' => '2026-04-27 15:00:00',
                'opening_cash' => 300000,
                'closed_at' => '2026-04-27 21:00:00',
                'closing_cash_actual' => 511000,
                'customer_name' => 'Ibu Sari',
                'payment_method' => 'cash',
                'discount_amount' => 1000,
                'amount_paid' => 50000,
                'sold_at' => '2026-04-27 16:20:00',
                'items' => [
                    ['sku' => 'BRG-006', 'qty' => 2],
                    ['sku' => 'BRG-004', 'qty' => 3],
                    ['sku' => 'BRG-011', 'qty' => 1],
                    ['sku' => 'BRG-005', 'qty' => 2],
                ],
            ],
            [
                'invoice_no' => 'DUMMY-20260426-001',
                'opened_at' => '2026-04-26 07:00:00',
                'opening_cash' => 200000,
                'closed_at' => '2026-04-26 14:00:00',
                'closing_cash_actual' => 438500,
                'customer_name' => 'Kelompok Tani Makmur',
                'payment_method' => 'transfer',
                'discount_amount' => 5000,
                'amount_paid' => 212000,
                'sold_at' => '2026-04-26 09:30:00',
                'items' => [
                    ['sku' => 'BRG-008', 'qty' => 2],
                    ['sku' => 'BRG-009', 'qty' => 1],
                ],
            ],
            [
                'invoice_no' => 'DUMMY-20260426-002',
                'opened_at' => '2026-04-26 15:00:00',
                'opening_cash' => 150000,
                'closed_at' => '2026-04-26 20:00:00',
                'closing_cash_actual' => 267000,
                'customer_name' => 'Pak Budi',
                'payment_method' => 'cash',
                'discount_amount' => 0,
                'amount_paid' => 117000,
                'sold_at' => '2026-04-26 17:45:00',
                'items' => [
                    ['sku' => 'BRG-007', 'qty' => 2],
                    ['sku' => 'BRG-010', 'qty' => 5],
                    ['sku' => 'BRG-004', 'qty' => 3],
                ],
            ],
        ];

        foreach ($salesBlueprint as $blueprint) {
            if ($saleModel->where('invoice_no', $blueprint['invoice_no'])->first()) {
                echo "Transaksi '{$blueprint['invoice_no']}' sudah ada, dilewati.\n";
                continue;
            }

            $shift = $shiftModel
                ->where('user_id', $cashierId)
                ->where('opened_at', $blueprint['opened_at'])
                ->first();

            if (! $shift) {
                $cashSalesTarget = $blueprint['payment_method'] === 'cash' ? ((float) $blueprint['amount_paid'] - (float) $blueprint['discount_amount']) : 0;
                $systemCash = (float) $blueprint['opening_cash'] + $cashSalesTarget;
                $variance = (float) $blueprint['closing_cash_actual'] - $systemCash;

                $shiftId = $shiftModel->insert([
                    'user_id' => $cashierId,
                    'opened_at' => $blueprint['opened_at'],
                    'opening_cash' => $blueprint['opening_cash'],
                    'closed_at' => $blueprint['closed_at'],
                    'closing_cash_system' => $systemCash,
                    'closing_cash_actual' => $blueprint['closing_cash_actual'],
                    'variance' => $variance,
                    'notes' => 'Shift dummy untuk demo laporan',
                ], true);

                $shift = $shiftModel->find($shiftId);
            }

            $items = [];
            $subtotal = 0.0;

            foreach ($blueprint['items'] as $itemBlueprint) {
                $product = $productModel->where('sku', $itemBlueprint['sku'])->first();

                if (! $product) {
                    echo "Produk '{$itemBlueprint['sku']}' tidak ditemukan, transaksi '{$blueprint['invoice_no']}' dilewati.\n";
                    continue 2;
                }

                $qty = (int) $itemBlueprint['qty'];
                $unitPrice = (float) $product['sell_price'];
                $lineTotal = $qty * $unitPrice;
                $subtotal += $lineTotal;

                $items[] = [
                    'product' => $product,
                    'qty' => $qty,
                    'unit_price' => $unitPrice,
                    'cost_price_snapshot' => (float) ($product['cost_price'] ?? 0),
                    'line_total' => $lineTotal,
                ];
            }

            $grandTotal = max(0, $subtotal - (float) $blueprint['discount_amount']);
            $amountPaid = max((float) $blueprint['amount_paid'], $grandTotal);
            $changeAmount = $blueprint['payment_method'] === 'cash' ? max(0, $amountPaid - $grandTotal) : 0;

            $db = Database::connect();
            $db->transStart();

            $saleId = $saleModel->insert([
                'invoice_no' => $blueprint['invoice_no'],
                'shift_id' => $shift['id'],
                'cashier_id' => $cashierId,
                'customer_name' => $blueprint['customer_name'],
                'payment_method' => $blueprint['payment_method'],
                'subtotal' => $subtotal,
                'discount_amount' => $blueprint['discount_amount'],
                'grand_total' => $grandTotal,
                'amount_paid' => $amountPaid,
                'change_amount' => $changeAmount,
                'sold_at' => $blueprint['sold_at'],
            ], true);

            $allocatedDiscount = 0.0;
            $itemsCount = count($items);

            foreach ($items as $index => $item) {
                if ((float) $blueprint['discount_amount'] <= 0 || $subtotal <= 0) {
                    $itemDiscount = 0.0;
                } elseif ($index === $itemsCount - 1) {
                    $itemDiscount = max(0, round((float) $blueprint['discount_amount'] - $allocatedDiscount, 2));
                } else {
                    $itemDiscount = round(($item['line_total'] / $subtotal) * (float) $blueprint['discount_amount'], 2);
                    $allocatedDiscount += $itemDiscount;
                }

                $netLineTotal = max(0, round($item['line_total'] - $itemDiscount, 2));
                $cogsTotal = round($item['cost_price_snapshot'] * $item['qty'], 2);
                $grossProfit = round($netLineTotal - $cogsTotal, 2);

                $saleItemModel->insert([
                    'sale_id' => $saleId,
                    'product_id' => $item['product']['id'],
                    'product_name' => $item['product']['name'],
                    'qty' => $item['qty'],
                    'unit_price' => $item['unit_price'],
                    'cost_price_snapshot' => $item['cost_price_snapshot'],
                    'cogs_total' => $cogsTotal,
                    'discount_allocated' => $itemDiscount,
                    'line_total' => $item['line_total'],
                    'net_line_total' => $netLineTotal,
                    'gross_profit' => $grossProfit,
                ]);

                $productModel
                    ->where('id', $item['product']['id'])
                    ->set('stock', 'stock - ' . $item['qty'], false)
                    ->update();

                $stockMovementModel->insert([
                    'product_id' => $item['product']['id'],
                    'movement_type' => 'sale',
                    'qty' => -$item['qty'],
                    'reference_no' => $blueprint['invoice_no'],
                    'notes' => 'Seeder transaksi dummy',
                    'user_id' => $cashierId,
                ]);
            }

            $db->transComplete();

            if (! $db->transStatus()) {
                echo "Transaksi '{$blueprint['invoice_no']}' gagal dibuat.\n";
                continue;
            }

            echo "Transaksi '{$blueprint['invoice_no']}' ditambahkan.\n";
        }

        echo "\nSeeder transaksi dummy selesai dijalankan.\n";
    }
}
