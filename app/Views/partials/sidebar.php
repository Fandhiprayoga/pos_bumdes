<?php
$currentUser = auth()->user();
$currentUrl  = uri_string();

/**
 * Helper untuk cek apakah menu aktif
 */
function isMenuActive(string $path): string {
    $currentUrl = uri_string();
    return (strpos($currentUrl, $path) !== false) ? 'active' : '';
}

function isDropdownActive(array $paths): string {
    $currentUrl = uri_string();
    foreach ($paths as $path) {
        if (strpos($currentUrl, $path) !== false) {
            return 'active';
        }
    }
    return '';
}
?>
<div class="main-sidebar sidebar-style-1">
  <aside id="sidebar-wrapper">
    <div class="sidebar-brand">
      <a href="<?= base_url('dashboard') ?>"><?= esc(setting('App.siteName') ?? 'CI4 RBAC') ?></a>
    </div>
    <div class="sidebar-brand sidebar-brand-sm">
      <a href="<?= base_url('dashboard') ?>"><?= esc(setting('App.siteNameShort') ?? 'C4') ?></a>
    </div>
    <ul class="sidebar-menu">

      <!-- Dashboard -->
      <li class="menu-header">Dashboard</li>
      <li class="<?= isMenuActive('dashboard') && !str_contains($currentUrl, 'admin') ? 'active' : '' ?>">
        <a class="nav-link" href="<?= base_url('dashboard') ?>"><i class="fas fa-fire"></i> <span>Dashboard</span></a>
      </li>

      <?php if (activeGroupCan('sales.create')): ?>
      <li class="<?= isMenuActive('pos') && !str_contains($currentUrl, 'history') && !str_contains($currentUrl, 'shift') ? 'active' : '' ?>">
        <a class="nav-link" href="<?= base_url('pos') ?>"><i class="fas fa-cash-register"></i> <span>POS Kasir</span></a>
      </li>
      <?php endif; ?>

      <?php if (activeGroupCan('sales.create')): ?>
      <li class="<?= isMenuActive('pos/shift') ?>">
        <a class="nav-link" href="<?= base_url('pos/shift') ?>"><i class="fas fa-door-open"></i> <span>Buka / Tutup Shift</span></a>
      </li>
      <?php endif; ?>

      <?php if (activeGroupCan('sales.list')): ?>
      <li class="<?= isMenuActive('pos/history') ?>">
        <a class="nav-link" href="<?= base_url('pos/history') ?>"><i class="fas fa-receipt"></i> <span>Riwayat Penjualan</span></a>
      </li>
      <?php endif; ?>

      <?php if (activeGroupCan('reports.view')): ?>
      <li class="<?= isMenuActive('reports/sales-daily') ?>">
        <a class="nav-link" href="<?= base_url('reports/sales-daily') ?>"><i class="fas fa-chart-line"></i> <span>Laporan Harian</span></a>
      </li>
      <?php endif; ?>

      <!-- Admin Menu (hanya untuk active group yang punya akses admin) -->
      <?php if (activeGroupCan('admin.access')): ?>
      <li class="menu-header">Administrasi</li>

      <!-- User Management -->
      <?php if (activeGroupCan('users.list')): ?>
      <li class="<?= isMenuActive('admin/users') ?>">
        <a class="nav-link" href="<?= base_url('admin/users') ?>"><i class="fas fa-users"></i> <span>Manajemen User</span></a>
      </li>
      <?php endif; ?>

      <!-- Product Management -->
      <?php if (activeGroupCan('products.list')): ?>
      <li class="<?= isMenuActive('admin/products') ?>">
        <a class="nav-link" href="<?= base_url('admin/products') ?>"><i class="fas fa-boxes"></i> <span>Produk</span></a>
      </li>
      <!-- <li class="<?= isMenuActive('admin/products/mwa-history') ?>">
        <a class="nav-link" href="<?= base_url('admin/products/mwa-history') ?>"><i class="fas fa-history"></i> <span>Histori Audit MWA</span></a>
      </li> -->
      <?php endif; ?>

      <?php if (activeGroupCan('masters.categories.list') || activeGroupCan('masters.units.list')): ?>
      <li class="nav-item dropdown <?= isDropdownActive(['admin/master-data/categories', 'admin/master-data/units']) ?>">
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-layer-group"></i> <span>Master Data</span></a>
        <ul class="dropdown-menu">
          <?php if (activeGroupCan('masters.categories.list')): ?>
          <li class="<?= isMenuActive('admin/master-data/categories') ? 'active' : '' ?>">
            <a class="nav-link" href="<?= base_url('admin/master-data/categories') ?>">Kategori Barang</a>
          </li>
          <?php endif; ?>
          <?php if (activeGroupCan('masters.units.list')): ?>
          <li class="<?= isMenuActive('admin/master-data/units') ? 'active' : '' ?>">
            <a class="nav-link" href="<?= base_url('admin/master-data/units') ?>">Satuan Barang</a>
          </li>
          <?php endif; ?>
        </ul>
      </li>
      <?php endif; ?>

      <!-- Role Management (superadmin only) -->
      <?php if (activeGroupIs('superadmin')): ?>
      <li class="nav-item dropdown <?= isDropdownActive(['admin/roles']) ?>">
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-user-shield"></i> <span>Role & Permission</span></a>
        <ul class="dropdown-menu">
          <li class="<?= isMenuActive('admin/roles') && !str_contains($currentUrl, 'permissions') ? 'active' : '' ?>">
            <a class="nav-link" href="<?= base_url('admin/roles') ?>">Daftar Role</a>
          </li>
          <li class="<?= isMenuActive('admin/roles/permissions') ? 'active' : '' ?>">
            <a class="nav-link" href="<?= base_url('admin/roles/permissions') ?>">Permission Matrix</a>
          </li>
        </ul>
      </li>
      <?php endif; ?>

      <!-- Settings -->
      <?php if (activeGroupCan('admin.settings')): ?>
      <li class="<?= isMenuActive('admin/settings') ?>">
        <a class="nav-link" href="<?= base_url('admin/settings') ?>"><i class="fas fa-cog"></i> <span>Pengaturan</span></a>
      </li>
      <?php endif; ?>
      <?php endif; ?>

      <!-- Profil -->
      <li class="menu-header">Akun</li>
      <li class="<?= isMenuActive('profile') ?>">
        <a class="nav-link" href="<?= base_url('profile') ?>"><i class="far fa-user"></i> <span>Profil Saya</span></a>
      </li>
      <li>
        <a class="nav-link text-danger" href="<?= base_url('logout') ?>"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a>
      </li>

    </ul>
  </aside>
</div>
