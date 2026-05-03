<?php
$currentUser  = auth()->user();
$currentUrl   = current_url();
$userGroups   = $currentUser->getGroups();
$active       = activeGroup();
$authGroups   = config('AuthGroups');
$uriPath      = trim((string) uri_string(), '/');
$isPosModule  = $uriPath === 'pos' || strpos($uriPath, 'pos/') === 0;
$isPosIndex   = $uriPath === 'pos';

$openShift = null;
if (activeGroupCan('sales.create')) {
  $cashShiftModel = new \App\Models\CashShiftModel();
  $openShift = $cashShiftModel
    ->where('user_id', auth()->id())
    ->where('closed_at', null)
    ->first();
}

// Badge color per group
$badgeColors = [
    'superadmin' => 'danger',
    'admin'      => 'warning',
    'manager'    => 'info',
    'user'       => 'primary',
];
?>
<nav class="navbar navbar-expand-lg main-navbar">
  <form class="form-inline mr-auto">
    <ul class="navbar-nav mr-3">
      <li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg"><i class="fas fa-bars"></i></a></li>
    </ul>
    <div class="d-none d-md-flex align-items-center">
      <?php if (activeGroupCan('sales.create')): ?>
      <a href="<?= base_url('pos/shift') ?>" class="badge badge-pill <?= $openShift ? 'badge-success' : 'badge-secondary' ?> p-2 mr-2">
        <i class="fas fa-cash-register mr-1"></i>
        <?= $openShift ? 'Shift Aktif' : 'Buka Shift' ?>
      </a>
      <?php endif; ?>

      <?php if ($isPosIndex): ?>
      <div class="badge badge-light border p-2 mr-2">
        <i class="fas fa-shopping-basket mr-1 text-muted"></i>
        <span id="nav-pos-item-count" class="font-weight-600">0 item</span>
      </div>
      <div class="badge badge-light border p-2">
        <span class="text-muted mr-1">Total:</span>
        <span id="nav-pos-grand-total" class="font-weight-700">Rp 0</span>
      </div>
      <?php elseif ($isPosModule): ?>
      <div class="badge badge-light border p-2">
        <span class="text-muted">Modul POS</span>
      </div>
      <?php endif; ?>
    </div>
  </form>
  <ul class="navbar-nav navbar-right">

    <!-- Group Switcher -->
    <?php if (count($userGroups) > 1): ?>
    <li class="dropdown">
      <a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg">
        <i class="fas fa-user-shield"></i>
        <span class="badge badge-<?= $badgeColors[$active] ?? 'secondary' ?>">
          <?= esc($authGroups->groups[$active]['title'] ?? ucfirst($active)) ?>
        </span>
      </a>
      <div class="dropdown-menu dropdown-menu-right">
        <div class="dropdown-title">Switch Role</div>
        <?php foreach ($userGroups as $grp): ?>
          <?php if ($grp === $active): ?>
            <span class="dropdown-item active disabled">
              <i class="fas fa-check mr-1"></i>
              <?= esc($authGroups->groups[$grp]['title'] ?? ucfirst($grp)) ?>
            </span>
          <?php else: ?>
            <form action="<?= base_url('switch-group') ?>" method="post" class="d-inline">
              <?= csrf_field() ?>
              <input type="hidden" name="group" value="<?= $grp ?>">
              <button type="submit" class="dropdown-item">
                <i class="far fa-circle mr-1"></i>
                <?= esc($authGroups->groups[$grp]['title'] ?? ucfirst($grp)) ?>
              </button>
            </form>
          <?php endif; ?>
        <?php endforeach; ?>
      </div>
    </li>
    <?php endif; ?>

    <!-- User Menu -->
    <li class="dropdown"><a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
      <img alt="image" src="<?= base_url('assets/img/avatar/avatar-1.png') ?>" class="rounded-circle mr-1">
      <div class="d-sm-none d-lg-inline-block">Hi, <?= esc($currentUser->username ?? 'User') ?></div></a>
      <div class="dropdown-menu dropdown-menu-right">
        <div class="dropdown-title">Logged in as <?= esc($currentUser->username ?? 'User') ?></div>
        <?php if (count($userGroups) === 1): ?>
        <div class="dropdown-item disabled text-muted">
          <i class="fas fa-user-shield"></i> Role: <span class="badge badge-<?= $badgeColors[$active] ?? 'secondary' ?>"><?= esc(activeGroupTitle()) ?></span>
        </div>
        <?php endif; ?>
        <a href="<?= base_url('profile') ?>" class="dropdown-item has-icon">
          <i class="far fa-user"></i> Profil
        </a>
        <?php if (activeGroupCan('admin.settings')): ?>
        <a href="<?= base_url('admin/settings') ?>" class="dropdown-item has-icon">
          <i class="fas fa-cog"></i> Pengaturan
        </a>
        <?php endif; ?>
        <div class="dropdown-divider"></div>
        <a href="<?= base_url('logout') ?>" class="dropdown-item has-icon text-danger">
          <i class="fas fa-sign-out-alt"></i> Logout
        </a>
      </div>
    </li>
  </ul>
</nav>
