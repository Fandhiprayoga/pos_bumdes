<?php

namespace App\Database\Seeds;

use App\Models\ProductModel;
use CodeIgniter\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $this->call(ProductMasterSeeder::class);

        $productModel = new ProductModel();

        $defaultProducts = [
            [
                'sku'        => 'BRG-001',
                'name'       => 'Beras Ramos 5 Kg',
                'category'   => 'Sembako',
                'unit'       => 'pack',
                'cost_price' => 68000,
                'sell_price' => 75000,
                'stock'      => 20,
                'min_stock'  => 5,
            ],
            [
                'sku'        => 'BRG-002',
                'name'       => 'Gula Pasir 1 Kg',
                'category'   => 'Sembako',
                'unit'       => 'pack',
                'cost_price' => 14500,
                'sell_price' => 17000,
                'stock'      => 30,
                'min_stock'  => 8,
            ],
            [
                'sku'        => 'BRG-003',
                'name'       => 'Minyak Goreng 1 Liter',
                'category'   => 'Kebutuhan Harian',
                'unit'       => 'botol',
                'cost_price' => 16000,
                'sell_price' => 18500,
                'stock'      => 24,
                'min_stock'  => 6,
            ],
            [
                'sku'        => 'BRG-004',
                'name'       => 'Air Mineral 600 ml',
                'category'   => 'Minuman',
                'unit'       => 'botol',
                'cost_price' => 2500,
                'sell_price' => 4000,
                'stock'      => 60,
                'min_stock'  => 12,
            ],
            [
                'sku'        => 'BRG-005',
                'name'       => 'Mi Instan Goreng',
                'category'   => 'Makanan Ringan',
                'unit'       => 'pcs',
                'cost_price' => 2800,
                'sell_price' => 3500,
                'stock'      => 80,
                'min_stock'  => 20,
            ],
            [
                'sku'        => 'BRG-006',
                'name'       => 'Teh Celup Isi 25',
                'category'   => 'Minuman',
                'unit'       => 'box',
                'cost_price' => 8200,
                'sell_price' => 10500,
                'stock'      => 18,
                'min_stock'  => 4,
            ],
            [
                'sku'        => 'BRG-007',
                'name'       => 'LPG 3 Kg',
                'category'   => 'Gas LPG',
                'unit'       => 'tabung',
                'cost_price' => 18500,
                'sell_price' => 22000,
                'stock'      => 15,
                'min_stock'  => 3,
            ],
            [
                'sku'        => 'BRG-008',
                'name'       => 'Pupuk Urea 5 Kg',
                'category'   => 'Pupuk',
                'unit'       => 'karung',
                'cost_price' => 52000,
                'sell_price' => 59000,
                'stock'      => 10,
                'min_stock'  => 2,
            ],
            [
                'sku'        => 'BRG-009',
                'name'       => 'Pakan Ayam Starter 5 Kg',
                'category'   => 'Pakan Ternak',
                'unit'       => 'karung',
                'cost_price' => 72000,
                'sell_price' => 79000,
                'stock'      => 12,
                'min_stock'  => 3,
            ],
            [
                'sku'        => 'BRG-010',
                'name'       => 'Buku Tulis 38 Lembar',
                'category'   => 'ATK',
                'unit'       => 'pcs',
                'cost_price' => 2800,
                'sell_price' => 4000,
                'stock'      => 40,
                'min_stock'  => 10,
            ],
            [
                'sku'        => 'BRG-011',
                'name'       => 'Sabun Cuci Piring 800 ml',
                'category'   => 'Perlengkapan Rumah Tangga',
                'unit'       => 'botol',
                'cost_price' => 9800,
                'sell_price' => 12500,
                'stock'      => 22,
                'min_stock'  => 5,
            ],
            [
                'sku'        => 'BRG-012',
                'name'       => 'Kopi Sachet',
                'category'   => 'Minuman',
                'unit'       => 'sachet',
                'cost_price' => 1800,
                'sell_price' => 2500,
                'stock'      => 100,
                'min_stock'  => 25,
            ],
        ];

        foreach ($defaultProducts as $productData) {
            $existing = $productModel->where('sku', $productData['sku'])->first();

            if ($existing) {
                echo "Produk '{$productData['sku']}' sudah ada, dilewati.\n";
                continue;
            }

            $productModel->insert([
                'sku'        => $productData['sku'],
                'name'       => $productData['name'],
                'category'   => $productData['category'],
                'unit'       => $productData['unit'],
                'cost_price' => $productData['cost_price'],
                'sell_price' => $productData['sell_price'],
                'stock'      => $productData['stock'],
                'min_stock'  => $productData['min_stock'],
                'is_active'  => 1,
            ]);

            echo "Produk '{$productData['name']}' ditambahkan.\n";
        }

        echo "\nSeeder produk contoh selesai dijalankan.\n";
    }
}
