<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\TrilaterationController;

// Authentication routes
Route::get('register', function () {
    return view('auth.register');
})->name('register');

Route::post('register', [AuthController::class, 'register']);

Route::get('login', function () {
    return view('auth.login');
})->name('login');

Route::post('login', [AuthController::class, 'login']);

// Protected routes
Route::middleware(['auth'])->group(function () {
    Route::get('dashboard', function () {
        return view('dashboard'); // Replace with your dashboard view
    })->name('dashboard');

    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    Route::resource('sites', SiteController::class);
    Route::get('/sites/create', [SiteController::class, 'create'])->name('sites.create');
    Route::post('/sites', [SiteController::class, 'store'])->name('sites.store');
    Route::get('/sites', [SiteController::class, 'index'])->name('sites.index');
    Route::get('/sites/{site}/edit', [SiteController::class, 'edit'])->name('sites.edit');
    Route::put('/sites/{site}', [SiteController::class, 'update'])->name('sites.update');
    Route::delete('/sites/{site}', [SiteController::class, 'destroy'])->name('sites.destroy');
    Route::get('/site/{id}', [SiteController::class, 'show'])->name('site.show');
    Route::post('/trilateration/latest-position', [TrilaterationController::class, 'getLatestPosition']);

});

// Redirect root to login page
Route::get('/', function () {
    return redirect()->route('login');
});
