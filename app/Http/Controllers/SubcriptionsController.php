<?php

namespace App\Http\Controllers;

use App\Models\BillingHistory;
use App\Models\Subcriptions;
use App\Models\Workspace;
use App\Services\PriceHikeDetector;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubcriptionsController extends Controller
{
    public function __construct(private PriceHikeDetector $hikeDetector) {}

    public function dashboard(): View
    {
        $user = Auth::user();
        $today = Carbon::today();
        $weekFromToday = $today->copy()->addDays(7);
        $monthFromToday = $today->copy()->addDays(30);
        $subscriptions = $user->subcriptions();
        $flaggedCategories = ['accidental', 'cancelled', 'canceled'];

        $totalMonthlySpending = (clone $subscriptions)
            ->where('billing_cycle', 'monthly')
            ->whereNotIn('category', $flaggedCategories)
            ->sum('price');

        $totalYearlySpending = (clone $subscriptions)
            ->where('billing_cycle', 'yearly')
            ->whereNotIn('category', $flaggedCategories)
            ->sum('price');

        $activeSubscriptionsCount = (clone $subscriptions)
            ->whereNotIn('category', $flaggedCategories)
            ->count();

        $cancelledSubscriptionsCount = (clone $subscriptions)
            ->whereIn('category', $flaggedCategories)
            ->count();

        $upcomingRenewals = (clone $subscriptions)
            ->whereNotIn('category', $flaggedCategories)
            ->whereBetween('billing_date', [$weekFromToday, $monthFromToday])
            ->orderBy('billing_date')
            ->get();

        $weeklyBills = (clone $subscriptions)
            ->whereNotIn('category', $flaggedCategories)
            ->whereBetween('billing_date', [$today, $weekFromToday])
            ->orderBy('billing_date')
            ->get();

        $priceHikes = $this->hikeDetector->forSubcriptions(
            (clone $subscriptions)->whereNotIn('category', $flaggedCategories)->get()->all(),
        );

        return view('dashboard', compact(
            'totalMonthlySpending',
            'totalYearlySpending',
            'activeSubscriptionsCount',
            'cancelledSubscriptionsCount',
            'upcomingRenewals',
            'weeklyBills',
            'priceHikes',
        ) + ['isGmailConnected' => $user->hasGoogleConnected()]);
    }

    public function index(): RedirectResponse
    {
        return redirect()->route('dashboard');
    }

    public function billingHistory(): View
    {
        $billingHistories = BillingHistory::query()
            ->with('subcription:id,name')
            ->whereHas('subcription', function ($query): void {
                $query->where('user_id', Auth::id());
            })
            ->latest('billed_at')
            ->latest('id')
            ->paginate(15);

        return view('billing-history.index', compact('billingHistories'));
    }

    public function create(): RedirectResponse
    {
        return redirect()->route('dashboard');
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'billing_cycle' => 'required|in:monthly,yearly,weekly',
            'billing_date' => 'required|date',
            'category' => 'nullable|string|max:255',
            'workspace_id' => 'nullable|integer|exists:workspaces,id',
        ]);

        $validated['category'] = $validated['category'] ?? 'other';

        $workspaceId = $validated['workspace_id'] ?? null;
        unset($validated['workspace_id']);

        $subscription = Auth::user()->subcriptions()->create($validated);

        if ($workspaceId !== null) {
            $workspace = Workspace::find($workspaceId);

            if ($workspace === null || $workspace->owner_id !== Auth::id()) {
                return $request->expectsJson()
                    ? abort(403, 'You do not own that workspace.')
                    : redirect()->back()->withErrors(['workspace_id' => 'You do not own that workspace.']);
            }

            $subscription->workspace_id = $workspaceId;
            $subscription->save();
        }

        $this->hikeDetector->recordBilling($subscription, 'manual');

        if ($request->expectsJson()) {
            return response()->json(['redirect' => route('dashboard')]);
        }

        return redirect()->route('dashboard')->with('success', 'Subscription successfully added!');
    }

    public function edit(Subcriptions $subcriptions): View
    {
        return view('subscriptions.edit', compact('subcriptions'));
    }

    public function update(Request $request, Subcriptions $subcriptions): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'billing_cycle' => 'required|string',
            'billing_date' => 'required|date',
            'category' => 'nullable|string',
        ]);

        $validated['category'] = $validated['category'] ?? 'other';

        $previousPrice = (float) $subcriptions->price;

        $subcriptions->update($validated);

        if ((float) $validated['price'] !== $previousPrice) {
            $this->hikeDetector->recordBilling($subcriptions, 'manual');
        }

        return redirect()->route('dashboard')->with('success', 'Subscription updated successfully!');
    }

    public function destroy(Subcriptions $subcriptions): RedirectResponse
    {
        $subcriptions->delete();

        return redirect()->route('dashboard')->with('success', 'Subscription deleted successfully!');
    }
}
