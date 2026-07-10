<?php

namespace App\Controllers;

use App\Models\ReceivableModel;
use App\Models\ReceivablePaymentModel;
use App\Models\SaleModel;
use Config\Database;
use RuntimeException;

class ReceivablePaymentController extends BaseController
{
    protected ReceivableModel $receivableModel;
    protected ReceivablePaymentModel $receivablePaymentModel;
    protected SaleModel $saleModel;

    public function __construct()
    {
        $this->receivableModel = new ReceivableModel();
        $this->receivablePaymentModel = new ReceivablePaymentModel();
        $this->saleModel = new SaleModel();
    }

    public function store(int $receivableId)
    {
        $expectsJson = $this->request->isAJAX() || strtolower(trim((string) ($this->request->getGet('format') ?? ''))) === 'json';
        $amount = (float) ($this->request->getPost('amount') ?? 0);
        $paymentMethod = strtolower(trim((string) ($this->request->getPost('payment_method') ?? 'cash')));
        $referenceNo = strtoupper(trim((string) ($this->request->getPost('reference_no') ?? '')));
        $notes = trim((string) ($this->request->getPost('notes') ?? ''));

        if ($amount <= 0) {
            return $this->failValidation('Nominal pembayaran harus lebih dari nol.', $expectsJson, $receivableId);
        }

        if (! in_array($paymentMethod, ['cash', 'transfer'], true)) {
            return $this->failValidation('Metode pembayaran tidak valid.', $expectsJson, $receivableId);
        }

        if ($referenceNo === '') {
            $referenceNo = 'ARP' . date('YmdHis') . strtoupper(substr(bin2hex(random_bytes(3)), 0, 4));
        }

        if ($this->receivablePaymentModel->where('reference_no', $referenceNo)->first()) {
            return $this->failValidation('Referensi pembayaran sudah digunakan. Mohon ulangi dengan referensi baru.', $expectsJson, $receivableId, 409);
        }

        $db = Database::connect();

        try {
            $db->transStart();

            $receivable = $db->query('SELECT * FROM receivables WHERE id = ? FOR UPDATE', [$receivableId])->getRowArray();
            if (! $receivable) {
                throw new RuntimeException('Data piutang tidak ditemukan.');
            }

            if (in_array((string) $receivable['status'], ['cancelled', 'settled'], true)) {
                throw new RuntimeException('Piutang tidak dapat dibayar karena sudah ditutup.');
            }

            $outstanding = round((float) ($receivable['outstanding'] ?? 0), 2);
            if ($amount > $outstanding) {
                throw new RuntimeException('Nominal pembayaran melebihi sisa piutang.');
            }

            $newPaid = round((float) ($receivable['amount_paid'] ?? 0) + $amount, 2);
            $newOutstanding = max(0, round((float) ($receivable['grand_total'] ?? 0) - $newPaid, 2));

            $newStatus = 'partial';
            if ($newOutstanding <= 0) {
                $newStatus = 'settled';
            } elseif ($newPaid <= 0) {
                $newStatus = 'open';
            }

            $this->receivablePaymentModel->insert([
                'receivable_id' => $receivableId,
                'payment_date' => date('Y-m-d H:i:s'),
                'amount' => $amount,
                'payment_method' => $paymentMethod,
                'reference_no' => $referenceNo,
                'status' => 'recorded',
                'notes' => $notes !== '' ? $notes : null,
                'created_by' => auth()->id(),
            ]);

            $this->receivableModel->update((int) $receivable['id'], [
                'amount_paid' => $newPaid,
                'outstanding' => $newOutstanding,
                'status' => $newStatus,
            ]);

            $this->saleModel->update((int) $receivable['sale_id'], [
                'amount_paid' => $newPaid,
                'status' => $newStatus === 'settled' ? 'completed' : 'issued',
            ]);

            $db->transComplete();
            if (! $db->transStatus()) {
                throw new RuntimeException('Penyimpanan pembayaran gagal.');
            }

            if ($expectsJson) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Pembayaran piutang berhasil dicatat.',
                    'data' => [
                        'receivable_id' => (int) $receivable['id'],
                        'status' => $newStatus,
                        'amount_paid' => $newPaid,
                        'outstanding' => $newOutstanding,
                        'reference_no' => $referenceNo,
                    ],
                ]);
            }

            return redirect()->to('/receivables/' . $receivableId)
                ->with('success', 'Pembayaran piutang berhasil dicatat.');
        } catch (RuntimeException $e) {
            $db->transRollback();

            return $this->failValidation($e->getMessage(), $expectsJson, $receivableId);
        }
    }

    private function failValidation(string $message, bool $expectsJson, int $receivableId, int $code = 422)
    {
        if ($expectsJson) {
            return $this->response->setStatusCode($code)->setJSON([
                'success' => false,
                'message' => $message,
            ]);
        }

        return redirect()->to('/receivables/' . $receivableId)
            ->withInput()
            ->with('error', $message);
    }
}