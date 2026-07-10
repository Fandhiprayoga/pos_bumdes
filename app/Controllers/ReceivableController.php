<?php

namespace App\Controllers;

use App\Models\CustomerModel;
use App\Models\ReceivablePaymentModel;
use App\Models\ReceivableModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class ReceivableController extends BaseController
{
    protected ReceivableModel $receivableModel;
    protected ReceivablePaymentModel $receivablePaymentModel;
    protected CustomerModel $customerModel;

    public function __construct()
    {
        $this->receivableModel = new ReceivableModel();
        $this->receivablePaymentModel = new ReceivablePaymentModel();
        $this->customerModel = new CustomerModel();
    }

    public function index()
    {
        $status = trim((string) ($this->request->getGet('status') ?? ''));
        $customerId = (int) ($this->request->getGet('customer_id') ?? 0);
        $format = strtolower(trim((string) ($this->request->getGet('format') ?? 'html')));

        $query = $this->receivableModel
            ->select('receivables.*, sales.invoice_no, sales.customer_name, customers.name as customer_label')
            ->join('sales', 'sales.id = receivables.sale_id', 'left')
            ->join('customers', 'customers.id = receivables.customer_id', 'left')
            ->orderBy('receivables.due_date', 'ASC')
            ->orderBy('receivables.id', 'DESC');

        if ($status !== '') {
            $query->where('receivables.status', $status);
        }

        if ($customerId > 0) {
            $query->where('receivables.customer_id', $customerId);
        }

        $rows = $query->findAll();

        if ($format === 'json' || $this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'data' => $rows,
            ]);
        }

        $summary = [
            'total_receivables' => count($rows),
            'total_outstanding' => 0.0,
            'total_overdue' => 0.0,
        ];

        $today = date('Y-m-d');
        foreach ($rows as $row) {
            $outstanding = (float) ($row['outstanding'] ?? 0);
            $summary['total_outstanding'] += $outstanding;

            $dueDate = (string) ($row['due_date'] ?? '');
            $statusValue = (string) ($row['status'] ?? '');
            if ($dueDate !== '' && $dueDate < $today && in_array($statusValue, ['open', 'partial', 'overdue'], true)) {
                $summary['total_overdue'] += $outstanding;
            }
        }

        return $this->renderView('receivables/index', [
            'title' => 'Daftar Piutang',
            'page_title' => 'Daftar Piutang',
            'receivables' => $rows,
            'summary' => $summary,
            'filters' => [
                'status' => $status,
                'customer_id' => $customerId,
            ],
            'statusOptions' => ['open', 'partial', 'settled', 'overdue', 'cancelled'],
            'customers' => $this->customerModel
                ->where('is_active', 1)
                ->orderBy('name', 'ASC')
                ->findAll(),
        ]);
    }

    public function detail(int $id)
    {
        $format = strtolower(trim((string) ($this->request->getGet('format') ?? 'html')));

        $row = $this->receivableModel
            ->select('receivables.*, sales.invoice_no, sales.customer_name, customers.name as customer_label')
            ->join('sales', 'sales.id = receivables.sale_id', 'left')
            ->join('customers', 'customers.id = receivables.customer_id', 'left')
            ->where('receivables.id', $id)
            ->first();

        if (! $row) {
            if ($format === 'json' || $this->request->isAJAX()) {
                return $this->response->setStatusCode(404)->setJSON([
                    'success' => false,
                    'message' => 'Data piutang tidak ditemukan.',
                ]);
            }

            throw PageNotFoundException::forPageNotFound('Data piutang tidak ditemukan.');
        }

        $payments = $this->receivablePaymentModel
            ->where('receivable_id', $id)
            ->orderBy('payment_date', 'DESC')
            ->findAll();

        if ($format === 'json' || $this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'data' => [
                    'receivable' => $row,
                    'payments' => $payments,
                ],
            ]);
        }

        return $this->renderView('receivables/detail', [
            'title' => 'Detail Piutang',
            'page_title' => 'Detail Piutang',
            'receivable' => $row,
            'payments' => $payments,
        ]);
    }
}