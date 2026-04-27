<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// ---------------------------------------------------------------
// Auth Routes (Shield)
// ---------------------------------------------------------------
service('auth')->routes($routes);

// ---------------------------------------------------------------
// Public Routes
// ---------------------------------------------------------------
$routes->get('/', 'AuthController::login');
$routes->get('maintenance', static function () {
    return view('errors/maintenance');
});

// ---------------------------------------------------------------
// Protected Routes (require login)
// ---------------------------------------------------------------
$routes->group('', ['filter' => 'session'], static function ($routes) {

    // Dashboard
    $routes->get('dashboard', 'DashboardController::index');

    // Switch Active Group
    $routes->post('switch-group', 'GroupSwitchController::switch');

    // Profile
    $routes->get('profile', 'ProfileController::index');
    $routes->post('profile/update', 'ProfileController::update');

    // POS Routes
    $routes->get('pos', 'PosController::index', ['filter' => 'permission:sales.create']);
    $routes->post('pos/open-shift', 'PosController::openShift', ['filter' => 'permission:shifts.open']);
    $routes->post('pos/checkout', 'PosController::checkout', ['filter' => 'permission:sales.create']);
    $routes->post('pos/close-shift', 'PosController::closeShift', ['filter' => 'permission:shifts.close']);
    $routes->get('pos/history', 'PosController::history', ['filter' => 'permission:sales.list']);

    // Reports
    $routes->get('reports/sales-daily', 'SalesReportController::daily', ['filter' => 'permission:reports.view']);

    // ---------------------------------------------------------------
    // Admin Routes (require admin.access permission)
    // ---------------------------------------------------------------
    $routes->group('admin', ['filter' => 'permission:admin.access'], static function ($routes) {

        // User Management
        $routes->group('users', static function ($routes) {
            $routes->get('/', 'UserController::index', ['filter' => 'permission:users.list']);
            $routes->get('create', 'UserController::create', ['filter' => 'permission:users.create']);
            $routes->post('store', 'UserController::store', ['filter' => 'permission:users.create']);
            $routes->get('edit/(:num)', 'UserController::edit/$1', ['filter' => 'permission:users.edit']);
            $routes->post('update/(:num)', 'UserController::update/$1', ['filter' => 'permission:users.edit']);
            $routes->post('delete/(:num)', 'UserController::delete/$1', ['filter' => 'permission:users.delete']);
            $routes->post('assign-role/(:num)', 'UserController::assignRole/$1', ['filter' => 'permission:users.manage-roles']);
        });

        // Role Management (superadmin only)
        $routes->group('roles', ['filter' => 'role:superadmin'], static function ($routes) {
            $routes->get('/', 'RoleController::index');
            $routes->get('permissions', 'RoleController::permissions');
        });

        // Settings
        $routes->group('settings', ['filter' => 'permission:admin.settings'], static function ($routes) {
            $routes->get('/', 'SettingController::index');
            $routes->post('update/general', 'SettingController::updateGeneral');
            $routes->post('update/branding', 'SettingController::updateBranding');
            $routes->post('update/appearance', 'SettingController::updateAppearance');
            $routes->post('update/auth', 'SettingController::updateAuth');
            $routes->post('update/mail', 'SettingController::updateMail');
            $routes->post('test-email', 'SettingController::testEmail');
            $routes->post('reset', 'SettingController::resetDefaults');
        });

        // Product Management
        $routes->group('products', static function ($routes) {
            $routes->get('/', 'ProductController::index', ['filter' => 'permission:products.list']);
            $routes->get('data', 'ProductController::data', ['filter' => 'permission:products.list']);
            $routes->get('scan', 'ProductController::scanPage', ['filter' => 'permission:products.stock-in']);
            $routes->get('mwa-history', 'ProductController::mwaHistory', ['filter' => 'permission:products.list']);
            $routes->get('mwa-history/data', 'ProductController::mwaHistoryData', ['filter' => 'permission:products.list']);
            $routes->get('create', 'ProductController::create', ['filter' => 'permission:products.create']);
            $routes->post('store', 'ProductController::store', ['filter' => 'permission:products.create']);
            $routes->get('edit/(:num)', 'ProductController::edit/$1', ['filter' => 'permission:products.edit']);
            $routes->post('update/(:num)', 'ProductController::update/$1', ['filter' => 'permission:products.edit']);
            $routes->post('stock-in/(:num)', 'ProductController::stockIn/$1', ['filter' => 'permission:products.stock-in']);
            $routes->post('scan-flow', 'ProductController::scanFlow', ['filter' => 'permission:products.stock-in']);
        });

        // Product Master Data
        $routes->group('master-data', static function ($routes) {
            $routes->get('categories', 'ProductCategoryController::index', ['filter' => 'permission:masters.categories.list']);
            $routes->get('categories/data', 'ProductCategoryController::data', ['filter' => 'permission:masters.categories.list']);
            $routes->post('categories/store', 'ProductCategoryController::store', ['filter' => 'permission:masters.categories.create']);
            $routes->post('categories/update/(:num)', 'ProductCategoryController::update/$1', ['filter' => 'permission:masters.categories.edit']);

            $routes->get('units', 'ProductUnitController::index', ['filter' => 'permission:masters.units.list']);
            $routes->get('units/data', 'ProductUnitController::data', ['filter' => 'permission:masters.units.list']);
            $routes->post('units/store', 'ProductUnitController::store', ['filter' => 'permission:masters.units.create']);
            $routes->post('units/update/(:num)', 'ProductUnitController::update/$1', ['filter' => 'permission:masters.units.edit']);
        });
    });
});
