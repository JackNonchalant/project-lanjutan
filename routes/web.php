<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\ProdukController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Customer\ProdukController as CustomerProdukController;

/*
|--------------------------------------------------------------------------
| HOME (ROOT)
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('customer.dashboard');
    }
    return redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/
Route::get('/login', [AuthController::class, 'show'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| MIDTRANS CALLBACK (WAJIB DI LUAR AUTH)
|--------------------------------------------------------------------------
*/
Route::post('/midtrans/callback', [OrderController::class, 'callback']);

/*
|--------------------------------------------------------------------------
| ADMIN
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    Route::resource('/produk', ProdukController::class);

    Route::get('/order', [AdminOrderController::class, 'index'])
        ->name('order.index');

    Route::post('/order/{order}/status', [AdminOrderController::class, 'updateStatus'])
        ->name('order.status');

    Route::get('/order/{order}', [AdminOrderController::class, 'show'])
<<<<<<< HEAD
    ->name('order.show');



=======
        ->name('order.show');
>>>>>>> b7b0992 (Initial commit)
});

/*
|--------------------------------------------------------------------------
| CUSTOMER
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->prefix('customer')->name('customer.')->group(function () {

    // DASHBOARD
    Route::get('/dashboard', function () {
        return view('customer.dashboard');
    })->name('dashboard');

    // PROFILE (INI YANG KAMU BUTUH)
    Route::get('/profile', function () {
    return view('customer.profile');
})->name('profile');
    Route::post('/profile/update', [AuthController::class, 'updateProfile'])
    ->name('profile.update');

    // PRODUCTS
    Route::get('/products', [CustomerProdukController::class, 'index'])
        ->name('products');

    // CART
    Route::get('/cart', [OrderController::class, 'cart'])->name('cart');
    Route::post('/cart/add', [OrderController::class, 'addToCart'])->name('cart.add');
    Route::post('/cart/remove/{id}', [OrderController::class, 'removeFromCart'])->name('cart.remove');

    // CHECKOUT
    Route::post('/checkout', [OrderController::class, 'checkout'])->name('checkout');

    // ORDERS
    Route::get('/orders', [OrderController::class, 'myOrders'])->name('orders');
<<<<<<< HEAD
    Route::get('/orders/{order}', [OrderController::class, 'orderDetail'])->name('orders.show');


});

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
        Route::resource('kategori', \App\Http\Controllers\Admin\KategoriController::class);
});


Route::middleware(['auth', 'role:admin,owner'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('customer', \App\Http\Controllers\Admin\CustomerController::class);
});

Route::middleware(['auth', 'role:owner,admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::resource('user', \App\Http\Controllers\Admin\UserController::class);
    });
=======
    Route::get('/orders/{id}', [OrderController::class, 'orderDetail'])->name('orders.show');
});
>>>>>>> b7b0992 (Initial commit)
