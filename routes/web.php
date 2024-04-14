<?php

use App\Http\Controllers\CardController;
use App\Http\Controllers\GroupCardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use App\Http\Middleware\CheckGroupCardOwnership;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
if(Auth::check()){
return redirect()->route('groupCards.index');
}
return redirect('/login');
});


Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // GroupCards routes
    Route::resource('groupCards',GroupCardController::class)->only(['index','store','update','destroy']);
    // Cards routes
    Route::middleware(CheckGroupCardOwnership::class)->group(function () {
        Route::get('/all-cards/{id}',[CardController::class,'index'])->name('cards.index');
    });
        Route::delete('/delete-card/{id}',[CardController::class,'destroy'])->name('cards.destroy');
        Route::post('/card-store',[CardController::class,'store'])->name('cards.store');
        Route::put('/card-update/{id}',[CardController::class,'update'])->name('cards.update');
    Route::prefix('tasks')->group(function () {
        Route::put('/{id}/toggleCompleted', [TaskController::class, 'toggleCompleted'])->name('task.toggleCompleted');
        Route::post('/', [TaskController::class, 'store'])->name('task.store');
        Route::put('/{id}', [TaskController::class, 'update'])->name('task.update');
    });
    // Profile routes 
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
