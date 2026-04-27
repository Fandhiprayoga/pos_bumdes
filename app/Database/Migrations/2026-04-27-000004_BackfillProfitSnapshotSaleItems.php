<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class BackfillProfitSnapshotSaleItems extends Migration
{
    public function up()
    {
        $rows = $this->db->table('sale_items si')
            ->select('si.id, si.qty, si.line_total, p.cost_price')
            ->join('products p', 'p.id = si.product_id', 'left')
            ->where('si.cost_price_snapshot', null)
            ->get()
            ->getResultArray();

        foreach ($rows as $row) {
            $costPriceSnapshot = (float) ($row['cost_price'] ?? 0);
            $lineTotal = (float) ($row['line_total'] ?? 0);
            $qty = (int) ($row['qty'] ?? 0);
            $cogsTotal = round($costPriceSnapshot * $qty, 2);
            $netLineTotal = $lineTotal;
            $grossProfit = round($netLineTotal - $cogsTotal, 2);

            $this->db->table('sale_items')
                ->where('id', $row['id'])
                ->update([
                    'cost_price_snapshot' => $costPriceSnapshot,
                    'cogs_total' => $cogsTotal,
                    'discount_allocated' => 0,
                    'net_line_total' => $netLineTotal,
                    'gross_profit' => $grossProfit,
                ]);
        }
    }

    public function down()
    {
        // No-op: This migration only fills derived snapshot values for historical rows.
    }
}
