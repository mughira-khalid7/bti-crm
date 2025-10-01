<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ProposalController as AdminProposalController;
use App\Http\Controllers\Admin\GoalController as AdminGoalController;
use App\Http\Controllers\BD\BdDashboardController;
use App\Http\Controllers\BD\ProposalController as BdProposalController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AiController;

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::middleware(['auth'])->get('/dashboard', function () {
    $user = Auth::user();

    return match ($user->role) {
        'admin' => redirect()->route('admin.dashboard'),
        'bd'    => redirect()->route('bd.dashboard'),
        default => abort(403, 'Unauthorized'),
    };
})->name('dashboard');

// AI assistance endpoint (auth required)
Route::middleware(['auth'])->post('/ai/assist', [AiController::class, 'assist'])->name('ai.assist');

// Super Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/activity-logs', [AdminDashboardController::class, 'getActivityLogs'])->name('activity-logs');
    Route::resource('users', UserController::class);
    Route::post('users/{id}/restore', [UserController::class, 'restore'])->name('users.restore');
    Route::get('proposals', [AdminProposalController::class, 'index'])->name('proposals.index');
    Route::get('proposals/{proposal}', [AdminProposalController::class, 'show'])->name('proposals.show');
    Route::post('proposals/{proposal}/move-to-interviewing', [AdminProposalController::class, 'moveToInterviewing'])->name('proposals.moveToInterviewing');
    Route::delete('proposals/{proposal}', [AdminProposalController::class, 'destroy'])->name('proposals.destroy');
    Route::resource('goals', AdminGoalController::class);
});

// Business Developer Routes
Route::middleware(['auth', 'role:bd'])
    ->prefix('bd')
    ->name('bd.')
    ->group(function () {
        Route::get('/dashboard', [BdDashboardController::class, 'index'])
            ->name('dashboard');
        Route::get('/proposals', [BdProposalController::class, 'index'])->name('proposals.index');
        Route::get('/proposals/create', [BdProposalController::class, 'create'])->name('proposals.create');
        Route::post('/proposals', [BdProposalController::class, 'store'])->name('proposals.store');
        Route::get('/proposals/remaining-connects', [BdProposalController::class, 'remainingConnects'])->name('proposals.remainingConnects');
        Route::get('/proposals/{proposal}', [BdProposalController::class, 'show'])->name('proposals.show');
        Route::get('/proposals/{proposal}/edit', [BdProposalController::class, 'edit'])->name('proposals.edit');
        Route::put('/proposals/{proposal}', [BdProposalController::class, 'update'])->name('proposals.update');
        Route::delete('/proposals/{proposal}', [BdProposalController::class, 'destroy'])->name('proposals.destroy');
        Route::post('/proposals/{proposal}/move-to-interviewing', [BdProposalController::class, 'moveToInterviewing'])->name('proposals.moveToInterviewing');
        Route::get('/interviewing', [BdProposalController::class, 'interviewing'])->name('interviewing.index');
    });

require __DIR__ . '/auth.php';

// Authenticated profile routes (shared by admin and bd)
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [\App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');
});

// (Removed) settings routes
