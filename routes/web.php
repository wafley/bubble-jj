<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JejeController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\OperatorsController;

Route::get('/', function (Request $request) {
    return spaRender($request, 'landing');
})->name('landing');

/**
 * ? Guest Routes
 */
Route::middleware('guest')->group(function () {
    // Login Routes
    Route::get('/login', [LoginController::class, 'loginView'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

/**
 * ? Authenticated Routes
 */
Route::middleware('auth')->group(function () {
    Route::middleware('role:super')->group(function () {
        // Operators Routes
        Route::get('/operators/data', [OperatorsController::class, 'data'])->name('operators.data');
        Route::resource('operators', OperatorsController::class);
    });

    Route::middleware('role:super,operator')->group(function () {
        // Dashboard Route
        Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

        // Users Routes
        Route::get('/users/data', [UsersController::class, 'data'])->name('users.data');
        Route::resource('users', UsersController::class)->except(['create', 'store']);
    });

    Route::middleware('role:user')->group(function () {
        // Home Route
        Route::get('/home', [App\Http\Controllers\User\HomeController::class, 'index'])->name('home');
    });

    // Profile Routes
    Route::controller(App\Http\Controllers\ProfileController::class)->prefix('profile')->group(function () {
        Route::get('/', 'index')->name('profile');
        Route::put('/update', 'update')->name('profile.update');
        Route::patch('/picture/{slot}', 'changePicture')->name('profile.picture');
    });

    // Orders Routes
    Route::get('/orders/data', [OrdersController::class, 'data'])->name('orders.data');
    Route::delete('/orders/files/{file}', [OrdersController::class, 'destroyFile'])->name('orders.files.destroy');
    Route::resource('orders', OrdersController::class)->except(['edit']);

    // JJ Routes
    Route::get('/jeje/data', [JejeController::class, 'data'])->name('jeje.data');
    Route::resource('jeje', JejeController::class)->except(['index']);

    // Logout Route
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});

Route::get('/jeje', [JejeController::class, 'index'])->name('jeje.index');
Route::get('/jeje/{jeje}/edit', [JejeController::class, 'edit'])->name('jeje.edit');
