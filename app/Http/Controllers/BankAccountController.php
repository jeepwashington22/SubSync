<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BankAccountController extends Controller
{
    /**
     * Show the linked-account dashboard: accounts, transactions, and the
     * subscription candidates detected from inbox scanning.
     */
    public function index(): View
    {
        $accounts = BankAccount::where('user_id', Auth::id())
            ->orderBy('institution')
            ->get();

        return view('bank-accounts.index', [
            'accounts' => $accounts,
        ]);
    }

    /**
     * Link a hypothetical inbox/institution to the user's profile.
     */
    public function link(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'provider' => 'required|string|max:255',
            'display_name' => 'required|string|max:255',
            'institution' => 'nullable|string|max:255',
        ]);

        BankAccount::create([
            'user_id' => Auth::id(),
            'provider' => $validated['provider'],
            'display_name' => $validated['display_name'],
            'institution' => $validated['institution'] ?? null,
        ]);

        return redirect()->route('bank-accounts.index')
            ->with('success', 'Account linked successfully.');
    }

    /**
     * Unlink an inbox/institution.
     */
    public function unlink(BankAccount $account): RedirectResponse
    {
        if ($account->user_id !== Auth::id()) {
            return redirect()->route('bank-accounts.index');
        }

        $account->delete();

        return redirect()->route('bank-accounts.index');
    }
}
