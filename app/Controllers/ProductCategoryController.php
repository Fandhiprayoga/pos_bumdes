<?php

namespace App\Controllers;

use App\Models\ProductCategoryModel;

class ProductCategoryController extends BaseController
{
    protected ProductCategoryModel $categoryModel;

    public function __construct()
    {
        $this->categoryModel = new ProductCategoryModel();
    }

    public function index()
    {
        $data = [
            'title'      => 'Master Kategori Barang',
            'page_title' => 'Master Kategori Barang',
        ];

        return $this->renderView('master_data/categories', $data);
    }

    public function data()
    {
        $categories = $this->categoryModel->orderBy('name', 'ASC')->findAll();
        $canEdit = activeGroupCan('masters.categories.edit');

        $rows = [];
        $no = 1;

        foreach ($categories as $category) {
            $statusHtml = (int) $category['is_active'] === 1
                ? '<span class="badge badge-success">Aktif</span>'
                : '<span class="badge badge-secondary">Nonaktif</span>';

            $actionHtml = '';
            if ($canEdit) {
                $actionHtml .= '<button type="button" class="btn btn-sm btn-info btn-edit-category" data-toggle="modal" data-target="#editCategoryModalGlobal" data-category-id="' . (int) $category['id'] . '" data-category-name="' . esc((string) $category['name']) . '" data-category-active="' . (int) $category['is_active'] . '"><i class="fas fa-edit"></i></button>';
            }

            $rows[] = [
                $no++,
                esc((string) $category['name']),
                $statusHtml,
                $actionHtml,
            ];
        }

        return $this->response->setJSON(['data' => $rows]);
    }

    public function store()
    {
        $rules = [
            'name' => 'required|min_length[2]|max_length[100]|regex_match[/^[^,]+$/]|is_unique[product_categories.name]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->categoryModel->insert([
            'name'      => trim((string) $this->request->getPost('name')),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
        ]);

        return redirect()->to('/admin/master-data/categories')->with('success', 'Kategori barang berhasil ditambahkan.');
    }

    public function update(int $id)
    {
        $category = $this->categoryModel->find($id);

        if (! $category) {
            return redirect()->to('/admin/master-data/categories')->with('error', 'Kategori barang tidak ditemukan.');
        }

        $rules = [
            'name' => "required|min_length[2]|max_length[100]|regex_match[/^[^,]+$/]|is_unique[product_categories.name,id,{$id}]",
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->categoryModel->update($id, [
            'name'      => trim((string) $this->request->getPost('name')),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
        ]);

        return redirect()->to('/admin/master-data/categories')->with('success', 'Kategori barang berhasil diperbarui.');
    }
}
