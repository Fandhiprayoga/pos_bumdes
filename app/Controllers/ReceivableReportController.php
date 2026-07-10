<?php

namespace App\Controllers;

use App\Models\ReceivableModel;

class ReceivableReportController extends BaseController
{
    protected ReceivableModel $receivableModel;

    public function __construct()
    {
        $this->receivableModel = new ReceivableModel();
    }

    public function aging()
    {
        $asOfDate = trim((string) ($this->request->getGet('as_of_date') ?? ''));
        if ($asOfDate === '') {
            $asOfDate = date('Y-m-d');
        }

        $rows = $this->receivableModel
            ->select('receivables.*, sales.invoice_no, sales.customer_name, customers.name as customer_label')
            ->join('sales', 'sales.id = receivables.sale_id', 'left')
            ->join('customers', 'customers.id = receivables.customer_id', 'left')
            ->whereNotIn('receivables.status', ['settled', 'cancelled'])
            ->orderBy('receivables.due_date', 'ASC')
            ->findAll();

        $summary = [
            'outstanding_total' => 0.0,
            'bucket_current' => 0.0,
            'bucket_1_30' => 0.0,
            'bucket_31_60' => 0.0,
            'bucket_over_60' => 0.0,
            'invoice_count' => count($rows),
            'overdue_count' => 0,
        ];

        foreach ($rows as &$row) {
            $outstanding = (float) ($row['outstanding'] ?? 0);
            $dueDate = (string) ($row['due_date'] ?? '');
            $daysPastDue = 0;

            if ($dueDate !== '') {
                $daysPastDue = (int) floor((strtotime($asOfDate) - strtotime($dueDate)) / 86400);
            }

            $row['aging_days'] = $daysPastDue;
            $summary['outstanding_total'] += $outstanding;

            if ($daysPastDue <= 0) {
                $summary['bucket_current'] += $outstanding;
            } elseif ($daysPastDue <= 30) {
                $summary['bucket_1_30'] += $outstanding;
                $summary['overdue_count']++;
            } elseif ($daysPastDue <= 60) {
                $summary['bucket_31_60'] += $outstanding;
                $summary['overdue_count']++;
            } else {
                $summary['bucket_over_60'] += $outstanding;
                $summary['overdue_count']++;
            }
        }
        unset($row);

        return $this->renderView('reports/receivables_aging', [
            'title' => 'Laporan Aging Piutang',
            'page_title' => 'Laporan Aging Piutang',
            'asOfDate' => $asOfDate,
            'summary' => $summary,
            'rows' => $rows,
        ]);
    }
}
