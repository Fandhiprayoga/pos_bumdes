<?php

namespace App\Controllers;

use App\Models\CustomerModel;

class CustomerController extends BaseController
{
    protected CustomerModel $customerModel;

    public function __construct()
    {
        $this->customerModel = new CustomerModel();
    }

    public function index()
    {
        $data = [
            'title'      => 'Master Pelanggan',
            'page_title' => 'Master Pelanggan',
        ];

        return $this->renderView('master_data/customers', $data);
    }

    public function data()
    {
        $customers = $this->customerModel->orderBy('name', 'ASC')->findAll();
        $canEdit = activeGroupCan('customers.edit');

        $rows = [];
        $no = 1;

        foreach ($customers as $customer) {
            $statusHtml = (int) $customer['is_active'] === 1
                ? '<span class="badge badge-success">Aktif</span>'
                : '<span class="badge badge-secondary">Nonaktif</span>';

            $creditLimit = (int) ($customer['credit_limit'] ?? 0);
            $creditLimitHtml = $creditLimit > 0
                ? 'Rp ' . number_format($creditLimit, 0, ',', '.')
                : '<span class="text-muted">-</span>';

            $defaultTerm = (int) ($customer['default_credit_term'] ?? 0);
            $termHtml = $defaultTerm > 0 ? $defaultTerm . ' hari' : '<span class="text-muted">-</span>';

            $actionHtml = '';
            if ($canEdit) {
                $actionHtml .= '<button type="button" class="btn btn-sm btn-info btn-edit-customer" data-toggle="modal" data-target="#editCustomerModal"'
                    . ' data-customer-id="' . (int) $customer['id'] . '"'
                    . ' data-customer-name="' . esc((string) $customer['name']) . '"'
                    . ' data-customer-phone="' . esc((string) ($customer['phone'] ?? '')) . '"'
                    . ' data-customer-address="' . esc((string) ($customer['address'] ?? '')) . '"'
                    . ' data-customer-credit-limit="' . (int) ($customer['credit_limit'] ?? 0) . '"'
                    . ' data-customer-default-term="' . (int) ($customer['default_credit_term'] ?? 0) . '"'
                    . ' data-customer-active="' . (int) $customer['is_active'] . '">'
                    . '<i class="fas fa-edit"></i></button>';
            }

            $rows[] = [
                $no++,
                esc((string) $customer['name']),
                esc((string) ($customer['phone'] ?? '-')),
                $creditLimitHtml,
                $termHtml,
                $statusHtml,
                $actionHtml,
            ];
        }

        return $this->response->setJSON(['data' => $rows]);
    }

    public function store()
    {
        $rules = [
            'name' => 'required|min_length[2]|max_length[255]',
            'phone' => 'permit_empty|max_length[20]',
            'address' => 'permit_empty|max_length[500]',
            'credit_limit' => 'permit_empty|numeric|greater_than_equal_to[0]',
            'default_credit_term' => 'permit_empty|integer|greater_than_equal_to[0]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->customerModel->insert([
            'name'               => trim((string) $this->request->getPost('name')),
            'phone'              => trim((string) ($this->request->getPost('phone') ?? '')),
            'address'            => trim((string) ($this->request->getPost('address') ?? '')),
            'credit_limit'       => (int) ($this->request->getPost('credit_limit') ?? 0),
            'default_credit_term' => (int) ($this->request->getPost('default_credit_term') ?? 0),
            'is_active'          => $this->request->getPost('is_active') ? 1 : 0,
        ]);

        return redirect()->to('/customers')->with('success', 'Pelanggan berhasil ditambahkan.');
    }

    public function update(int $id)
    {
        $customer = $this->customerModel->find($id);

        if (! $customer) {
            return redirect()->to('/customers')->with('error', 'Pelanggan tidak ditemukan.');
        }

        $rules = [
            'name' => 'required|min_length[2]|max_length[255]',
            'phone' => 'permit_empty|max_length[20]',
            'address' => 'permit_empty|max_length[500]',
            'credit_limit' => 'permit_empty|numeric|greater_than_equal_to[0]',
            'default_credit_term' => 'permit_empty|integer|greater_than_equal_to[0]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->customerModel->update($id, [
            'name'               => trim((string) $this->request->getPost('name')),
            'phone'              => trim((string) ($this->request->getPost('phone') ?? '')),
            'address'            => trim((string) ($this->request->getPost('address') ?? '')),
            'credit_limit'       => (int) ($this->request->getPost('credit_limit') ?? 0),
            'default_credit_term' => (int) ($this->request->getPost('default_credit_term') ?? 0),
            'is_active'          => $this->request->getPost('is_active') ? 1 : 0,
        ]);

        return redirect()->to('/customers')->with('success', 'Data pelanggan berhasil diperbarui.');
    }

    public function search()
    {
        $query = trim((string) $this->request->getGet('q'));
        $limit = (int) ($this->request->getGet('limit') ?? 20);

        if ($query === '') {
            $customers = $this->customerModel
                ->where('is_active', 1)
                ->orderBy('name', 'ASC')
                ->limit($limit)
                ->findAll();
        } else {
            $customers = $this->customerModel
                ->where('is_active', 1)
                ->like('name', $query)
                ->orderBy('name', 'ASC')
                ->limit($limit)
                ->findAll();
        }

        $results = array_map(function ($c) {
            return [
                'id'                 => (int) $c['id'],
                'name'               => (string) $c['name'],
                'phone'              => (string) ($c['phone'] ?? ''),
                'credit_limit'       => (int) ($c['credit_limit'] ?? 0),
                'default_credit_term' => (int) ($c['default_credit_term'] ?? 30),
            ];
        }, $customers);

        return $this->response->setJSON($results);
    }
}
