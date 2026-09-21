<?php

use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Admin Routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    // Placeholder routes for now to avoid errors in layout
    // Leads Management
    Route::get('/leads', [\App\Http\Controllers\Admin\LeadController::class, 'index'])->name('leads.index');
    Route::get('/leads/{lead}', [\App\Http\Controllers\Admin\LeadController::class, 'show'])->name('leads.show');
    Route::patch('/leads/{lead}/status', [\App\Http\Controllers\Admin\LeadController::class, 'updateStatus'])->name('leads.status');
    Route::patch('/leads/{lead}/priority', [\App\Http\Controllers\Admin\LeadController::class, 'togglePriority'])->name('leads.priority');
    // Broadcasts
    Route::get('/broadcast', [\App\Http\Controllers\Admin\BroadcastController::class, 'index'])->name('broadcast.index');
    Route::get('/broadcast/create', [\App\Http\Controllers\Admin\BroadcastController::class, 'create'])->name('broadcast.create');
    Route::post('/broadcast', [\App\Http\Controllers\Admin\BroadcastController::class, 'store'])->name('broadcast.store');
    Route::get('/broadcast/{broadcast}', [\App\Http\Controllers\Admin\BroadcastController::class, 'show'])->name('broadcast.show');
});

// Dummy logout route for the layout
Route::post('/logout', function () {
    return redirect('/');
})->name('logout');
