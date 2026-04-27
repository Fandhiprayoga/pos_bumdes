<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\SaleModel;
use Config\Database;

class SalesReportController extends BaseController
{
    protected SaleModel $saleModel;
    protected ProductModel $productModel;

    public function __construct()
    {
        $this->saleModel = new SaleModel();
        $this->productModel = new ProductModel();
    }

    public function daily()
    {
        $date = $this->request->getGet('date') ?: date('Y-m-d');
        $fromDateTime = $date . ' 00:00:00';
        $toDateTime   = $date . ' 23:59:59';

        $summary = $this->saleModel
            ->select('COUNT(*) as total_tx, COALESCE(SUM(grand_total),0) as omzet, COALESCE(SUM(discount_amount),0) as total_discount')
            ->where('sold_at >=', $fromDateTime)
            ->where('sold_at <=', $toDateTime)
            ->first();

        $profitSummary = Database::connect()->table('sale_items si')
            ->select('COALESCE(SUM(si.cogs_total),0) as total_hpp, COALESCE(SUM(si.gross_profit),0) as gross_profit_from_items')
            ->join('sales s', 's.id = si.sale_id', 'inner')
            ->where('s.sold_at >=', $fromDateTime)
            ->where('s.sold_at <=', $toDateTime)
            ->get()
            ->getRowArray();

        $totalHpp = (float) ($profitSummary['total_hpp'] ?? 0);
        $omzet = (float) ($summary['omzet'] ?? 0);
        $labaKotor = $omzet - $totalHpp;

        $summary['total_hpp'] = $totalHpp;
        $summary['laba_kotor'] = $labaKotor;

        $paymentBreakdown = $this->saleModel
            ->select('payment_method, COUNT(*) as total_tx, COALESCE(SUM(grand_total),0) as total_amount')
            ->where('sold_at >=', $fromDateTime)
            ->where('sold_at <=', $toDateTime)
            ->groupBy('payment_method')
            ->findAll();

        $db = Database::connect();

        $topProducts = $db->table('sale_items si')
            ->select('si.product_name, SUM(si.qty) as total_qty, COALESCE(SUM(si.net_line_total),0) as total_sales, COALESCE(SUM(si.cogs_total),0) as total_hpp, COALESCE(SUM(si.gross_profit),0) as total_profit')
            ->join('sales s', 's.id = si.sale_id', 'inner')
            ->where('s.sold_at >=', $fromDateTime)
            ->where('s.sold_at <=', $toDateTime)
            ->groupBy('si.product_id, si.product_name')
            ->orderBy('total_qty', 'DESC')
            ->limit(10)
            ->get()
            ->getResultArray();

        $lowStock = $this->productModel
            ->where('is_active', 1)
            ->where('stock <= min_stock')
            ->orderBy('stock', 'ASC')
            ->findAll();

        $data = [
            'title'            => 'Laporan Penjualan Harian',
            'page_title'       => 'Laporan Penjualan Harian',
            'date'             => $date,
            'summary'          => $summary,
            'paymentBreakdown' => $paymentBreakdown,
            'topProducts'      => $topProducts,
            'lowStock'         => $lowStock,
        ];

        return $this->renderView('reports/sales_daily', $data);
    }
}
