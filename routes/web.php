<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\NewsPostController;
use App\Http\Controllers\Admin\ParishContentController;
use App\Http\Controllers\Admin\ParishSettingController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NewsController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/secciones/{key}', [HomeController::class, 'showSection'])->name('sections.show');
Route::get('/noticias', [NewsController::class, 'index'])->name('news.index');
Route::get('/noticias/{slug}', [NewsController::class, 'show'])->name('news.show');

Route::prefix('panel')->name('admin.')->group(function (): void {
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('login.submit');
    Route::get('/forgot-password', [AdminAuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [AdminAuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [AdminAuthController::class, 'showResetPasswordForm'])->name('password.reset');
    Route::post('/reset-password', [AdminAuthController::class, 'resetPassword'])->name('password.update');

    Route::middleware('admin.panel')->group(function (): void {
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
        Route::get('/settings', [ParishSettingController::class, 'edit'])->name('settings.edit');
        Route::put('/settings', [ParishSettingController::class, 'update'])->name('settings.update');
        Route::resource('/news', NewsPostController::class)
            ->parameters(['news' => 'id'])
            ->except(['show']);
        Route::resource('/contents', ParishContentController::class)
            ->parameters(['contents' => 'id'])
            ->except(['show']);
        Route::resource('/users', AdminUserController::class)
            ->parameters(['users' => 'id'])
            ->except(['show', 'destroy']);
        Route::patch('/users/{id}/toggle-access', [AdminUserController::class, 'toggleAccess'])
            ->name('users.toggle-access');
    });
});

Route::redirect('/admin/login', '/panel/login', 301);
