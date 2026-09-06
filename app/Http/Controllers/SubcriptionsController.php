<?php

namespace App\Http\Controllers;

use App\Models\Subcriptions;
use Illuminate\Http\Request;


class SubcriptionsController extends Controller
{


    /**
     * Display a listing of the resource.
     */
    public function index(){
        $subcriptions = Auth::user()->subcriptions->get();
        return view('subcription.index',compact('subcriptions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('subcription.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'billing_cycle' => 'required|string',
            'billing_date' => 'required|date',
            'category' => 'nullable|string',
        ]);

        // 2. I-save sa database kalakip ang ID ng naka-login na user
        Auth::user()->subcriptions()->create($validated); // o subscriptions()->create(...)

        // 3. I-redirect pabalik sa listahan o dashboard kasama ang tagumpay na mensahe
        return redirect()->route('subscriptions.index')->with('success', 'Subscription successfully added!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Subcriptions $subcriptions)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Subcriptions $subcriptions)
    {
        return view('subscriptions.edit', compact('subcriptions'));
    }

    public function update(Request $request, Subcriptions $subcriptions)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'billing_cycle' => 'required|string',
            'billing_date' => 'required|date',
            'category' => 'nullable|string',
        ]);

        $subcriptions->update($validated);

        return redirect()->route('subscriptions.index')->with('success', 'Subscription updated successfully!');
    }


    public function destroy(Subcriptions $subcriptions)
    {
    $subcriptions->delete();

    return redirect()->route('subscriptions.index')->with('success', 'Subscription deleted successfully!');
    }
}
