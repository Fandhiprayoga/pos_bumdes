<?php

namespace App\Controllers;

use App\Models\CashShiftModel;
use App\Models\PendingPosTransactionModel;
use App\Models\ProductModel;
use App\Models\SaleModel;
use Config\Database;

class DashboardController extends BaseController
{
    protected SaleModel $saleModel;
    protected ProductModel $productModel;
    protected CashShiftModel $cashShiftModel;
    protected PendingPosTransactionModel $pendingTransactionModel;

    public function __construct()
    {
        $this->saleModel = new SaleModel();
        $this->productModel = new ProductModel();
        $this->cashShiftModel = new CashShiftModel();
        $this->pendingTransactionModel = new PendingPosTransactionModel();
    }

    public function index()
    {
        $user = auth()->user();

        $today = date('Y-m-d');
        $todayStart = $today . ' 00:00:00';
        $todayEnd = $today . ' 23:59:59';

        $yesterday = date('Y-m-d', strtotime('-1 day'));
        $yesterdayStart = $yesterday . ' 00:00:00';
        $yesterdayEnd = $yesterday . ' 23:59:59';

        $trendStartDate = date('Y-m-d', strtotime('-6 days'));
        $trendStart = $trendStartDate . ' 00:00:00';

        $todaySummary = $this->getSalesSummary($todayStart, $todayEnd);
        $yesterdaySummary = $this->getSalesSummary($yesterdayStart, $yesterdayEnd);
        $weeklyTrend = $this->getDailyTrend($trendStartDate, $today);
        $hourlyTrend = $this->getHourlyTrend($todayStart, $todayEnd);
        $paymentBreakdown = $this->getPaymentBreakdown($todayStart, $todayEnd);
        $topProducts = $this->getTopProducts($trendStart, $todayEnd, 6);
        $lowStockProducts = $this->getLowStockProducts(6);
        $stockSummary = $this->getStockSummary();
        $pendingCount = $this->pendingTransactionModel->countAllResults();
        $openShift = $this->getOpenShiftSummary((int) auth()->id());

        $cards = [
            'revenue' => [
                'value' => (float) $todaySummary['omzet'],
                'delta' => $this->buildDelta((float) $todaySummary['omzet'], (float) $yesterdaySummary['omzet']),
            ],
            'transactions' => [
                'value' => (int) $todaySummary['total_tx'],
                'delta' => $this->buildDelta((float) $todaySummary['total_tx'], (float) $yesterdaySummary['total_tx']),
            ],
            'profit' => [
                'value' => (float) $todaySummary['gross_profit'],
                'delta' => $this->buildDelta((float) $todaySummary['gross_profit'], (float) $yesterdaySummary['gross_profit']),
            ],
            'stock_alert' => [
                'value' => (int) $stockSummary['low_stock_count'],
                'delta' => $this->buildDelta((float) $stockSummary['low_stock_count'], 0),
            ],
        ];

        $data = [
            'title' => 'Dashboard POS',
            'page_title' => 'Dashboard POS',
            'user' => $user,
            'userGroups' => $user->getGroups(),
            'todayLabel' => date('d M Y'),
            'todaySummary' => $todaySummary,
            'yesterdaySummary' => $yesterdaySummary,
            'cards' => $cards,
            'paymentBreakdown' => $paymentBreakdown,
            'topProducts' => $topProducts,
            'lowStockProducts' => $lowStockProducts,
            'stockSummary' => $stockSummary,
            'pendingCount' => $pendingCount,
            'openShift' => $openShift,
            'charts' => [
                'weeklyTrend' => $weeklyTrend,
                'hourlyTrend' => $hourlyTrend,
                'paymentBreakdown' => $paymentBreakdown,
            ],
        ];

        return $this->renderView('dashboard/index', $data);
    }

    private function getSalesSummary(string $fromDateTime, string $toDateTime): array
    {
        $summary = $this->saleModel
            ->select('COUNT(*) as total_tx, COALESCE(SUM(grand_total), 0) as omzet, COALESCE(SUM(discount_amount), 0) as total_discount')
            ->where('sold_at >=', $fromDateTime)
            ->where('sold_at <=', $toDateTime)
            ->first() ?? [];

        $profitSummary = Database::connect()->table('sale_items si')
            ->select('COALESCE(SUM(si.qty), 0) as items_sold, COALESCE(SUM(si.cogs_total), 0) as total_hpp, COALESCE(SUM(si.gross_profit), 0) as gross_profit')
            ->join('sales s', 's.id = si.sale_id', 'inner')
            ->where('s.sold_at >=', $fromDateTime)
            ->where('s.sold_at <=', $toDateTime)
            ->get()
            ->getRowArray() ?? [];

        $totalTransactions = (int) ($summary['total_tx'] ?? 0);
        $omzet = (float) ($summary['omzet'] ?? 0);
        $grossProfit = (float) ($profitSummary['gross_profit'] ?? 0);

        return [
            'total_tx' => $totalTransactions,
            'omzet' => $omzet,
            'total_discount' => (float) ($summary['total_discount'] ?? 0),
            'items_sold' => (int) ($profitSummary['items_sold'] ?? 0),
            'total_hpp' => (float) ($profitSummary['total_hpp'] ?? 0),
            'gross_profit' => $grossProfit,
            'avg_basket' => $totalTransactions > 0 ? $omzet / $totalTransactions : 0,
            'gross_margin_pct' => $omzet > 0 ? ($grossProfit / $omzet) * 100 : 0,
        ];
    }

    private function getDailyTrend(string $fromDate, string $toDate): array
    {
        $rows = $this->saleModel
            ->select('DATE(sold_at) as sale_date, COUNT(*) as total_tx, COALESCE(SUM(grand_total), 0) as omzet')
            ->where('sold_at >=', $fromDate . ' 00:00:00')
            ->where('sold_at <=', $toDate . ' 23:59:59')
            ->groupBy('DATE(sold_at)')
            ->orderBy('sale_date', 'ASC')
            ->findAll();

        $mapped = [];
        foreach ($rows as $row) {
            $mapped[(string) $row['sale_date']] = [
                'omzet' => (float) ($row['omzet'] ?? 0),
                'total_tx' => (int) ($row['total_tx'] ?? 0),
            ];
        }

        $labels = [];
        $revenue = [];
        $transactions = [];

        $cursor = strtotime($fromDate);
        $end = strtotime($toDate);

        while ($cursor <= $end) {
            $dateKey = date('Y-m-d', $cursor);
            $labels[] = date('d M', $cursor);
            $revenue[] = $mapped[$dateKey]['omzet'] ?? 0;
            $transactions[] = $mapped[$dateKey]['total_tx'] ?? 0;
            $cursor = strtotime('+1 day', $cursor);
        }

        return [
            'labels' => $labels,
            'revenue' => $revenue,
            'transactions' => $transactions,
        ];
    }

    private function getHourlyTrend(string $fromDateTime, string $toDateTime): array
    {
        $rows = $this->saleModel
            ->select('HOUR(sold_at) as sale_hour, COUNT(*) as total_tx, COALESCE(SUM(grand_total), 0) as omzet')
            ->where('sold_at >=', $fromDateTime)
            ->where('sold_at <=', $toDateTime)
            ->groupBy('HOUR(sold_at)')
            ->orderBy('sale_hour', 'ASC')
            ->findAll();

        $mapped = [];
        foreach ($rows as $row) {
            $mapped[(int) ($row['sale_hour'] ?? 0)] = [
                'omzet' => (float) ($row['omzet'] ?? 0),
                'total_tx' => (int) ($row['total_tx'] ?? 0),
            ];
        }

        $labels = [];
        $revenue = [];
        $transactions = [];

        for ($hour = 0; $hour < 24; $hour++) {
            $labels[] = str_pad((string) $hour, 2, '0', STR_PAD_LEFT) . ':00';
            $revenue[] = $mapped[$hour]['omzet'] ?? 0;
            $transactions[] = $mapped[$hour]['total_tx'] ?? 0;
        }

        return [
            'labels' => $labels,
            'revenue' => $revenue,
            'transactions' => $transactions,
        ];
    }

    private function getPaymentBreakdown(string $fromDateTime, string $toDateTime): array
    {
        $rows = $this->saleModel
            ->select('payment_method, COUNT(*) as total_tx, COALESCE(SUM(grand_total), 0) as total_amount')
            ->where('sold_at >=', $fromDateTime)
            ->where('sold_at <=', $toDateTime)
            ->groupBy('payment_method')
            ->findAll();

        $labels = [];
        $amounts = [];
        $totals = [];

        foreach ($rows as $row) {
            $method = strtolower((string) ($row['payment_method'] ?? 'other'));
            $label = $method === 'cash' ? 'Tunai' : ($method === 'transfer' ? 'Transfer' : ucfirst($method));

            $labels[] = $label;
            $amounts[] = (float) ($row['total_amount'] ?? 0);
            $totals[] = [
                'label' => $label,
                'total_tx' => (int) ($row['total_tx'] ?? 0),
                'total_amount' => (float) ($row['total_amount'] ?? 0),
            ];
        }

        return [
            'labels' => $labels,
            'amounts' => $amounts,
            'totals' => $totals,
        ];
    }

    private function getTopProducts(string $fromDateTime, string $toDateTime, int $limit = 6): array
    {
        return Database::connect()->table('sale_items si')
            ->select('si.product_name, SUM(si.qty) as total_qty, COALESCE(SUM(si.net_line_total), 0) as total_sales, COALESCE(SUM(si.gross_profit), 0) as total_profit')
            ->join('sales s', 's.id = si.sale_id', 'inner')
            ->where('s.sold_at >=', $fromDateTime)
            ->where('s.sold_at <=', $toDateTime)
            ->groupBy('si.product_id, si.product_name')
            ->orderBy('total_qty', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }

    private function getLowStockProducts(int $limit = 6): array
    {
        return Database::connect()->table('products')
            ->select('id, name, category, unit, stock, min_stock')
            ->where('is_active', 1)
            ->where('stock <= min_stock', null, false)
            ->orderBy('stock', 'ASC')
            ->orderBy('name', 'ASC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }

    private function getStockSummary(): array
    {
        $db = Database::connect();

        $activeProducts = (int) ($db->table('products')
            ->select('COUNT(*) as total_rows')
            ->where('is_active', 1)
            ->get()
            ->getRowArray()['total_rows'] ?? 0);

        $zeroStock = (int) ($db->table('products')
            ->select('COUNT(*) as total_rows')
            ->where('is_active', 1)
            ->where('stock <=', 0)
            ->get()
            ->getRowArray()['total_rows'] ?? 0);

        $lowStock = (int) ($db->table('products')
            ->select('COUNT(*) as total_rows')
            ->where('is_active', 1)
            ->where('stock <= min_stock', null, false)
            ->get()
            ->getRowArray()['total_rows'] ?? 0);

        return [
            'active_product_count' => $activeProducts,
            'zero_stock_count' => $zeroStock,
            'low_stock_count' => $lowStock,
        ];
    }

    private function getOpenShiftSummary(int $userId): ?array
    {
        $shift = $this->cashShiftModel
            ->where('user_id', $userId)
            ->where('closed_at', null)
            ->orderBy('opened_at', 'DESC')
            ->first();

        if (! $shift) {
            return null;
        }

        $metrics = $this->saleModel
            ->select('COUNT(*) as total_tx, COALESCE(SUM(grand_total), 0) as total_amount')
            ->where('shift_id', $shift['id'])
            ->first() ?? [];

        $cashMetrics = $this->saleModel
            ->select('COALESCE(SUM(grand_total), 0) as cash_total')
            ->where('shift_id', $shift['id'])
            ->where('payment_method', 'cash')
            ->first() ?? [];

        $openingCash = (float) ($shift['opening_cash'] ?? 0);
        $cashSales = (float) ($cashMetrics['cash_total'] ?? 0);

        return [
            'opened_at' => $shift['opened_at'],
            'opening_cash' => $openingCash,
            'total_tx' => (int) ($metrics['total_tx'] ?? 0),
            'total_amount' => (float) ($metrics['total_amount'] ?? 0),
            'cash_in_drawer' => $openingCash + $cashSales,
            'duration_text' => $this->formatDuration((string) $shift['opened_at']),
        ];
    }

    private function buildDelta(float $currentValue, float $previousValue): array
    {
        $difference = $currentValue - $previousValue;

        if ($previousValue <= 0) {
            return [
                'direction' => $difference > 0 ? 'up' : 'flat',
                'difference' => $difference,
                'percent' => null,
            ];
        }

        return [
            'direction' => $difference > 0 ? 'up' : ($difference < 0 ? 'down' : 'flat'),
            'difference' => $difference,
            'percent' => ($difference / $previousValue) * 100,
        ];
    }

    private function formatDuration(string $openedAt): string
    {
        $openedTimestamp = strtotime($openedAt);

        if ($openedTimestamp === false) {
            return '-';
        }

        $seconds = max(0, time() - $openedTimestamp);
        $hours = (int) floor($seconds / 3600);
        $minutes = (int) floor(($seconds % 3600) / 60);

        if ($hours > 0) {
            return $hours . 'j ' . $minutes . 'm';
        }

        return $minutes . 'm';
    }
}
