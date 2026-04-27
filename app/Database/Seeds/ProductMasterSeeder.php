<?php

namespace App\Database\Seeds;

use App\Models\ProductCategoryModel;
use App\Models\ProductUnitModel;
use CodeIgniter\Database\Seeder;

class ProductMasterSeeder extends Seeder
{
    public function run()
    {
        $categoryModel = new ProductCategoryModel();
        $unitModel = new ProductUnitModel();

        $defaultCategories = [
            'Sembako',
            'Minuman',
            'Makanan Ringan',
            'ATK',
            'Perlengkapan Rumah Tangga',
            'Kebutuhan Harian',
            'Gas LPG',
            'Pupuk',
            'Pakan Ternak',
            'Lain-lain',
        ];

        $defaultUnits = [
            'pcs',
            'pack',
            'box',
            'dus',
            'bungkus',
            'sachet',
            'botol',
            'kaleng',
            'kg',
            'gram',
            'liter',
            'ml',
            'karung',
            'tabung',
        ];

        foreach ($defaultCategories as $categoryName) {
            $existing = $categoryModel->where('name', $categoryName)->first();

            if ($existing) {
                $categoryModel->update($existing['id'], ['is_active' => 1]);
                echo "Kategori '{$categoryName}' sudah ada, diaktifkan.\n";
                continue;
            }

            $categoryModel->insert([
                'name' => $categoryName,
                'is_active' => 1,
            ]);

            echo "Kategori '{$categoryName}' ditambahkan.\n";
        }

        foreach ($defaultUnits as $unitName) {
            $existing = $unitModel->where('name', $unitName)->first();

            if ($existing) {
                $unitModel->update($existing['id'], ['is_active' => 1]);
                echo "Satuan '{$unitName}' sudah ada, diaktifkan.\n";
                continue;
            }

            $unitModel->insert([
                'name' => $unitName,
                'is_active' => 1,
            ]);

            echo "Satuan '{$unitName}' ditambahkan.\n";
        }

        echo "\nSeeder master kategori dan satuan selesai dijalankan.\n";
    }
}
