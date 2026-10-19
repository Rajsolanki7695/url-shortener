<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ShortUrlController;
use App\Http\Controllers\ShortUrlResolutionController;
use App\Http\Controllers\InvitationController;

Route::get('/', function () {
    return view('welcome');
});

// Public URL Resolution Route (publicly resolvable, redirects to original URL)
Route::get('/s/{shortCode}', [ShortUrlResolutionController::class, 'resolve'])->name('url.resolve');

// Authentication routes (Jetstream/Fortify handles login/logout scaffolding)
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified'
])->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Invitation Routes
    Route::get('/invite', [InvitationController::class, 'getInvite'])->name('invite.index');
    Route::post('/invite', [InvitationController::class, 'postInviteUser'])->name('invite.user');

    // Short URL Management Routes
    Route::prefix('admin/urls')->group(function () {
        Route::get('/', [ShortUrlController::class, 'getIndex'])->name('urls.index');
        Route::get('/add', [ShortUrlController::class, 'getAdd'])->name('urls.add');
        Route::post('/add', [ShortUrlController::class, 'postAddSave'])->name('urls.store');
        Route::get('/detail/{id}', [ShortUrlController::class, 'getDetail'])->name('urls.detail');
        Route::get('/edit/{id}', [ShortUrlController::class, 'getEdit'])->name('urls.edit');
        Route::post('/edit/{id}', [ShortUrlController::class, 'postUpdateSave'])->name('urls.update');
        Route::get('/delete/{id}', [ShortUrlController::class, 'getDelete'])->name('urls.delete');
        Route::post('/bulk-action', [ShortUrlController::class, 'postActionSelected'])->name('urls.bulk');
    });
});
