<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SaleController;

// Public routes
Route::get('/', function () {
    return view('home');
});

Route::get('/about', function () {
    return view('about');
});

Route::get('/contact', function () {
    return view('contact');
});

// Authentication routes
Route::controller(LoginController::class)->group(function () {
    Route::get('/login', 'showLoginForm')->name('login');
    Route::post('/login', 'login');
    Route::post('/logout', 'logout')->name('logout');
});

// Protected admin routes
Route::middleware(['auth'])->group(function () {
    Route::get('/admin', function () {
        return view('admin.dashboard');
    })->name('admin');
    
    Route::get('/admin/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/admin/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/admin/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/admin/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/admin/users/{id}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/admin/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');

    // Customer management routes
    Route::get('/admin/customers', [CustomerController::class, 'index'])->name('admin.customers.index');
    Route::get('/admin/customers/create', [CustomerController::class, 'create'])->name('admin.customers.create');
    Route::post('/admin/customers', [CustomerController::class, 'store'])->name('admin.customers.store');
    Route::get('/admin/customers/{id}/edit', [CustomerController::class, 'edit'])->name('admin.customers.edit');
    Route::put('/admin/customers/{id}', [CustomerController::class, 'update'])->name('admin.customers.update');
    Route::delete('/admin/customers/{id}', [CustomerController::class, 'destroy'])->name('admin.customers.destroy');

    // Category management routes
    Route::get('/admin/categories', [CategoryController::class, 'index'])->name('admin.categories.index');
    Route::get('/admin/categories/create', [CategoryController::class, 'create'])->name('admin.categories.create');
    Route::post('/admin/categories', [CategoryController::class, 'store'])->name('admin.categories.store');
    Route::get('/admin/categories/{category}/edit', [CategoryController::class, 'edit'])->name('admin.categories.edit');
    Route::put('/admin/categories/{category}', [CategoryController::class, 'update'])->name('admin.categories.update');
    Route::delete('/admin/categories/{category}', [CategoryController::class, 'destroy'])->name('admin.categories.destroy');
});


// Product management routes
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('products', ProductsController::class);
});

// Sale management routes
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('sales', SaleController::class);
});