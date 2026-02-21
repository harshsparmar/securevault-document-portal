<?php

use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return redirect()->route('documents.index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Profile routes 
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Document routes — accessible to all authenticated users
    Route::get('/documents', [DocumentController::class, 'index'])->name('documents.index');

    // Signed routes — URL expires after 30 minutes, can't be shared
    Route::get('/documents/{document}', [DocumentController::class, 'show'])
        ->name('documents.show')
        ->middleware('signed');

    Route::get('/documents/{document}/preview', [DocumentController::class, 'preview'])
        ->name('documents.preview')
        ->middleware('signed');

    // Upload routes — restricted to uploaders via middleware + policy
    Route::middleware('role:uploader')->group(function () {
        Route::get('/upload', [DocumentController::class, 'create'])->name('documents.create');
        Route::post('/documents', [DocumentController::class, 'store'])->name('documents.store');
    });
});

require __DIR__.'/auth.php';
