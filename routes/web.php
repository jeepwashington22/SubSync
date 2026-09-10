<?php

use App\Http\Controllers\BankAccountController;
use App\Http\Controllers\BankWebhookController;
use App\Http\Controllers\GmailIntegrationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SubcriptionsController;
use App\Http\Controllers\WorkspaceController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [SubcriptionsController::class, 'dashboard'])
        ->middleware('verified')
        ->name('dashboard');
    Route::get('/billing-history', [SubcriptionsController::class, 'billingHistory'])
        ->name('billing-history.index');
    Route::resource('subscriptions', SubcriptionsController::class)->except(['show']);
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Workspaces & shared ledgers
    Route::get('/workspaces', [WorkspaceController::class, 'index'])->name('workspaces.index');
    Route::post('/workspaces', [WorkspaceController::class, 'store'])->name('workspaces.store');
    Route::get('/workspaces/accept/{token}', [WorkspaceController::class, 'accept'])->name('workspaces.accept');
    Route::post('/workspaces/{workspace}/invite', [WorkspaceController::class, 'invite'])->name('workspaces.invite');
    Route::post('/workspaces/{workspace}/split', [WorkspaceController::class, 'setSplit'])->name('workspaces.split');
    Route::delete('/workspaces/{workspace}/members/{user}', [WorkspaceController::class, 'removeMember'])->name('workspaces.members.remove');

    // Automated bank sync (linked accounts)
    Route::get('/bank-accounts', [BankAccountController::class, 'index'])->name('bank-accounts.index');
    Route::post('/bank-accounts', [BankAccountController::class, 'link'])->name('bank-accounts.link');
    Route::post('/bank-accounts/{bankAccount}/unlink', [BankAccountController::class, 'unlink'])->name('bank-accounts.unlink');

    Route::get('/gmail/redirect', [GmailIntegrationController::class, 'redirectToGoogle'])->name('gmail.redirect');
    Route::get('/gmail/callback', [GmailIntegrationController::class, 'handleGoogleCallback'])->name('gmail.callback');
    Route::post('/gmail/scan', [GmailIntegrationController::class, 'scanInboxForSubscriptions'])->name('gmail.scan');
    Route::post('/gmail/disconnect', [GmailIntegrationController::class, 'disconnectGoogle'])->name('gmail.disconnect');
});

// Open Banking webhook (no auth - secured by X-Webhook-Secret)
Route::post('/webhooks/bank', [BankWebhookController::class, 'handle'])->name('webhooks.bank');

require __DIR__.'/auth.php';
