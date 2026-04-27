<?php

declare(strict_types=1);

namespace Config;

use CodeIgniter\Shield\Config\AuthGroups as ShieldAuthGroups;

class AuthGroups extends ShieldAuthGroups
{
    /**
     * --------------------------------------------------------------------
     * Default Group
     * --------------------------------------------------------------------
     */
    public string $defaultGroup = 'user';

    /**
     * --------------------------------------------------------------------
     * Groups
     * --------------------------------------------------------------------
     */
    public array $groups = [
        'superadmin' => [
            'title'       => 'Super Admin',
            'description' => 'Kontrol penuh terhadap seluruh sistem.',
        ],
        'admin' => [
            'title'       => 'Admin',
            'description' => 'Administrator harian sistem.',
        ],
        'manager' => [
            'title'       => 'Manager',
            'description' => 'Manajer yang dapat melihat laporan dan mengelola data.',
        ],
        'cashier' => [
            'title'       => 'Kasir',
            'description' => 'Petugas kasir untuk operasional penjualan harian.',
        ],
        'user' => [
            'title'       => 'User',
            'description' => 'Pengguna umum dengan akses terbatas.',
        ],
    ];

    /**
     * --------------------------------------------------------------------
     * Permissions
     * --------------------------------------------------------------------
     */
    public array $permissions = [
        // Admin area
        'admin.access'        => 'Dapat mengakses area admin',
        'admin.settings'      => 'Dapat mengakses pengaturan sistem',

        // User management
        'users.list'          => 'Dapat melihat daftar pengguna',
        'users.create'        => 'Dapat membuat pengguna baru',
        'users.edit'          => 'Dapat mengedit pengguna',
        'users.delete'        => 'Dapat menghapus pengguna',
        'users.manage-roles'  => 'Dapat mengatur role pengguna',

        // Role management
        'roles.list'          => 'Dapat melihat daftar role',
        'roles.create'        => 'Dapat membuat role baru',
        'roles.edit'          => 'Dapat mengedit role',
        'roles.delete'        => 'Dapat menghapus role',

        // Dashboard
        'dashboard.access'    => 'Dapat mengakses dashboard',
        'dashboard.stats'     => 'Dapat melihat statistik',

        // Reports
        'reports.view'        => 'Dapat melihat laporan',
        'reports.export'      => 'Dapat mengekspor laporan',

        // Product management
        'products.list'       => 'Dapat melihat daftar produk',
        'products.create'     => 'Dapat menambah produk',
        'products.edit'       => 'Dapat mengubah produk',
        'products.stock-in'   => 'Dapat menambah stok produk',

        // Product master data
        'masters.categories.list'   => 'Dapat melihat master kategori barang',
        'masters.categories.create' => 'Dapat menambah master kategori barang',
        'masters.categories.edit'   => 'Dapat mengubah master kategori barang',
        'masters.units.list'        => 'Dapat melihat master satuan barang',
        'masters.units.create'      => 'Dapat menambah master satuan barang',
        'masters.units.edit'        => 'Dapat mengubah master satuan barang',

        // POS sales
        'sales.create'        => 'Dapat melakukan transaksi penjualan',
        'sales.list'          => 'Dapat melihat riwayat penjualan',

        // Cash shift
        'shifts.open'         => 'Dapat membuka shift kas',
        'shifts.close'        => 'Dapat menutup shift kas',
    ];

    /**
     * --------------------------------------------------------------------
     * Permissions Matrix
     * --------------------------------------------------------------------
     */
    public array $matrix = [
        'superadmin' => [
            'admin.*',
            'users.*',
            'roles.*',
            'dashboard.*',
            'reports.*',
            'products.*',
            'masters.*',
            'sales.*',
            'shifts.*',
        ],
        'admin' => [
            'admin.access',
            'users.list',
            'users.create',
            'users.edit',
            'users.delete',
            'dashboard.*',
            'reports.*',
            'products.*',
            'masters.*',
            'sales.*',
            'shifts.*',
        ],
        'manager' => [
            'admin.access',
            'users.list',
            'dashboard.*',
            'reports.view',
            'sales.list',
            'products.list',
        ],
        'cashier' => [
            'dashboard.access',
            'sales.create',
            'sales.list',
            'shifts.open',
            'shifts.close',
            'products.list',
        ],
        'user' => [
            'dashboard.access',
        ],
    ];
}
