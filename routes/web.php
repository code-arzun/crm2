<?php

use App\Livewire\Auth\Login;
use App\Livewire\Dashboard;
use App\Livewire\Customers\Index as CustomersIndex;
use App\Livewire\Products\Index as ProductsIndex;
use App\Livewire\Orders\MyOrders as MyOrders;
use App\Livewire\Orders\CreateOrder;
use App\Livewire\Orders\AdminIndex as AdminOrders;
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

Route::get('/customers', CustomersIndex::class)->middleware(['auth'])->name('customers.index');

Route::get('/products', ProductsIndex::class)->middleware(['auth'])->name('products.index');

Route::get('/my-orders', MyOrders::class)->middleware(['auth'])->name('orders.my');
Route::get('/orders/create', CreateOrder::class)->middleware(['auth'])->name('orders.create');
Route::get('/admin/orders', AdminOrders::class)->middleware(['auth', 'role:superadmin|admin|sales'])->name('admin.orders');