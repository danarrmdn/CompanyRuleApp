<?php

use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\CompanyRuleController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('pdf-viewer', function () {
    return view('pdf.viewer');
})->name('pdf.viewer');

Route::get('rules-file/{id}', [App\Http\Controllers\CompanyRuleController::class, 'serveFile'])->name('rules.file.show');

Route::post('/company-rules/upload', [CompanyRuleController::class, 'uploadTempFile'])->name('company-rules.upload');

Route::get('/dashboard', [CompanyRuleController::class, 'dashboard'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('notifications/read/{id}', [App\Http\Controllers\NotificationController::class, 'read'])->name('notifications.read');
Route::post('notifications/mark-all-read', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');

Route::resource('positions', PositionController::class);

Route::get('/api/company-rules/{rule}/logs', [CompanyRuleController::class, 'getLogsJson'])->name('company-rules.logs.json');

Route::post('/company-rules/next-number', [CompanyRuleController::class, 'getNextNumber'])->name('company-rules.getNextNumber');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/avatar/delete', [ProfileController::class, 'destroyAvatar'])->name('profile.avatar.destroy');

    Route::get('/profile/settings', [ProfileController::class, 'settings'])->name('profile.settings');

    Route::get('/company-rules', [CompanyRuleController::class, 'index'])->name('company-rules.index');
    Route::get('/company-rules/create', [CompanyRuleController::class, 'create'])->name('company-rules.create');
    Route::post('/company-rules', [CompanyRuleController::class, 'store'])->name('company-rules.store');

    Route::get('/company-rules/create-revision', [CompanyRuleController::class, 'createRevision'])->name('company-rules.create-revision');
    Route::get('/company-rules/{rule}/revise', [CompanyRuleController::class, 'revise'])->name('company-rules.revise');
    Route::post('/company-rules/{rule}/revise', [CompanyRuleController::class, 'storeRevision'])->name('company-rules.store-revision');

    Route::get('/company-rules/{rule}', [CompanyRuleController::class, 'show'])->name('company-rules.show');
    Route::get('/company-rules/{rule}/edit', [CompanyRuleController::class, 'edit'])->name('company-rules.edit');
    Route::put('/company-rules/{rule}', [CompanyRuleController::class, 'update'])->name('company-rules.update');
    Route::delete('/company-rules/{rule}', [CompanyRuleController::class, 'destroy'])->name('company-rules.destroy');

    // Internal API for dynamic form
    Route::get('/internal-api/company-rules/{rule}', [CompanyRuleController::class, 'getRuleDataAsJson'])->name('internal-api.company-rules.show');

    Route::resource('positions', PositionController::class);

    Route::get('/approvals', [ApprovalController::class, 'index'])->name('approvals.index');
    Route::post('/approvals/{rule}/approve', [ApprovalController::class, 'approve'])->name('approvals.approve');
    Route::post('/approvals/{rule}/reject', [ApprovalController::class, 'reject'])->name('approvals.reject');
    Route::post('/approvals/send-back/{rule}', [ApprovalController::class, 'sendBack'])->name('approvals.send-back');

    Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])
        ->name('notifications.index');
});

require __DIR__.'/auth.php';
