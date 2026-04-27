<?php

namespace App\Controllers;

use App\Models\ProductUnitModel;

class ProductUnitController extends BaseController
{
    protected ProductUnitModel $unitModel;

    public function __construct()
    {
        $this->unitModel = new ProductUnitModel();
    }

    public function index()
    {
        $data = [
            'title'      => 'Master Satuan Barang',
            'page_title' => 'Master Satuan Barang',
        ];

        return $this->renderView('master_data/units', $data);
    }

    public function data()
    {
        $units = $this->unitModel->orderBy('name', 'ASC')->findAll();
        $canEdit = activeGroupCan('masters.units.edit');

        $rows = [];
        $no = 1;

        foreach ($units as $unit) {
            $statusHtml = (int) $unit['is_active'] === 1
                ? '<span class="badge badge-success">Aktif</span>'
                : '<span class="badge badge-secondary">Nonaktif</span>';

            $actionHtml = '';
            if ($canEdit) {
                $actionHtml .= '<button type="button" class="btn btn-sm btn-info btn-edit-unit" data-toggle="modal" data-target="#editUnitModalGlobal" data-unit-id="' . (int) $unit['id'] . '" data-unit-name="' . esc((string) $unit['name']) . '" data-unit-active="' . (int) $unit['is_active'] . '"><i class="fas fa-edit"></i></button>';
            }

            $rows[] = [
                $no++,
                esc((string) $unit['name']),
                $statusHtml,
                $actionHtml,
            ];
        }

        return $this->response->setJSON(['data' => $rows]);
    }

    public function store()
    {
        $rules = [
            'name' => 'required|min_length[1]|max_length[50]|regex_match[/^[^,]+$/]|is_unique[product_units.name]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->unitModel->insert([
            'name'      => trim((string) $this->request->getPost('name')),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
        ]);

        return redirect()->to('/admin/master-data/units')->with('success', 'Satuan barang berhasil ditambahkan.');
    }

    public function update(int $id)
    {
        $unit = $this->unitModel->find($id);

        if (! $unit) {
            return redirect()->to('/admin/master-data/units')->with('error', 'Satuan barang tidak ditemukan.');
        }

        $rules = [
            'name' => "required|min_length[1]|max_length[50]|regex_match[/^[^,]+$/]|is_unique[product_units.name,id,{$id}]",
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->unitModel->update($id, [
            'name'      => trim((string) $this->request->getPost('name')),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
        ]);

        return redirect()->to('/admin/master-data/units')->with('success', 'Satuan barang berhasil diperbarui.');
    }
}
