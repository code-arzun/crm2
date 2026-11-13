<?php

use App\Http\Controllers\InvoiceController;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Catalog\Index as CatalogIndex;
use App\Livewire\Customer\Dashboard as CustomerDashboard;
use App\Livewire\Customers\Index as CustomersIndex;
use App\Livewire\Customers\LengkapiProfil;
use App\Livewire\Dashboard;
use App\Livewire\Orders\AdminIndex as AdminOrders;
use App\Livewire\Orders\AdminShow;
use App\Livewire\Orders\CreateOrder;
use App\Livewire\Orders\MyOrders as MyOrders;
use App\Livewire\Orders\PreviewInvoice;
use App\Livewire\Orders\Show;
use App\Livewire\Payments\VerifyIndex;
use App\Livewire\Products\Index as ProductsIndex;
use App\Livewire\Products\ManagePrices;
use App\Livewire\Settings\Index as SettingsIndex;
use App\Livewire\Users\Index as UsersIndex;
use App\Livewire\Warehouse\Index as WarehouseIndex;
use App\Livewire\Warehouse\Shipments;
use App\Livewire\Warehouse\StockLog;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', Login::class)->middleware('guest')->name('login');

Route::get('/dashboard', Dashboard::class)->middleware(['auth'])->name('dashboard');

Route::post('/logout', function () {
    auth()->logout();
    session()->invalidate();
    session()->regenerateToken();
    return redirect()->route('login');
})->middleware('auth')->name('logout');

Route::get('/settings', SettingsIndex::class)->middleware(['auth', 'role:superadmin'])->name('settings.index');
Route::get('/users', UsersIndex::class)->middleware(['auth', 'role:superadmin'])->name('users.index');

Route::get('/customers', CustomersIndex::class)->middleware(['auth'])->name('customers.index');

Route::get('/products', ProductsIndex::class)->middleware(['auth'])->name('products.index');
Route::get('/admin/products/{productId}/prices', ManagePrices::class)->middleware(['auth', 'role:superadmin|admin|sales'])->name('products.prices');

Route::get('/my-orders', MyOrders::class)->middleware(['auth'])->name('orders.my');
Route::get('/my-orders/{id}', Show::class)->middleware(['auth'])->name('orders.show');
Route::get('/orders/create', CreateOrder::class)->middleware(['auth'])->name('orders.create');
Route::get('/admin/orders', AdminOrders::class)->middleware(['auth', 'role:superadmin|admin|sales'])->name('admin.orders');
Route::get('/admin/orders/{id}', AdminShow::class)->middleware(['auth', 'role:superadmin|admin|finance|sales|warehouse'])->name('admin.orders.show');

Route::get('/payments/verify', VerifyIndex::class)->middleware(['auth', 'role:superadmin|admin|finance'])->name('payments.verify');
Route::get('/invoice/{id}/preview', PreviewInvoice::class)->middleware(['auth'])->name('invoice.preview');
Route::get('/invoice/{order}/download', [InvoiceController::class, 'download'])->middleware(['auth'])->name('invoice.download');
Route::get('/catalog', CatalogIndex::class)->middleware(['auth'])->name('catalog.index');
Route::get('/warehouse/logs', StockLog::class)->middleware(['auth', 'role:superadmin|admin|warehouse'])->name('warehouse.logs');
Route::get('/warehouse', WarehouseIndex::class)->middleware(['auth', 'role:superadmin|admin|warehouse'])->name('warehouse.index');

Route::get('/warehouse/shipments', Shipments::class)->middleware(['auth', 'role:superadmin|admin|warehouse'])->name('warehouse.shipments');
Route::get('/surat-jalan/{shipment}/download', [InvoiceController::class, 'downloadSuratJalan'])->middleware(['auth'])->name('surat-jalan.download');

Route::get('/register', Register::class)->middleware('guest')->name('register');
Route::get('/customer/dashboard', CustomerDashboard::class)->middleware(['auth', 'role:customer'])->name('customer.dashboard');
Route::get('/lengkapi-profil', LengkapiProfil::class)->middleware(['auth'])->name('customers.lengkapi');
