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
    Route::get('/leads', function () { return 'Leads coming soon'; })->name('leads.index');
    Route::get('/broadcast', function () { return 'Broadcast coming soon'; })->name('broadcast.index');
});

// Dummy logout route for the layout
Route::post('/logout', function () {
    return redirect('/');
})->name('logout');
