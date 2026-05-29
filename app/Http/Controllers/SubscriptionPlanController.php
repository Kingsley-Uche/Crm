<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlanModel;
use Illuminate\Http\Request;

class SubscriptionPlanController extends Controller
{
    /**
     * Display all subscription plans
     */
    public function index()
    {
        $plans = SubscriptionPlanModel::latest()->paginate(20);

        return view('layouts.subscriptions.plans.index', compact('plans'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        return view('layouts.subscriptions.plans.create');
    }

    /**
     * Store new plan
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',

            'number_admins' => 'required|integer|min:0',
            'number_branches' => 'required|integer|min:0',
            'number_apartments' => 'required|integer|min:0',
            'number_property_managers' => 'required|integer|min:0',

            'price_per_month' => 'required|numeric|min:0',

            'discount_min_months' => 'nullable|integer|min:0',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',

            'is_active' => 'required|boolean',
        ]);

        SubscriptionPlanModel::create([
            'name' => $request->name,
            'description' => $request->description,

            'number_admins' => $request->number_admins,
            'number_branches' => $request->number_branches,
            'number_apartments' => $request->number_apartments,
            'number_property_managers' => $request->number_property_managers,

            'price_per_month' => $request->price_per_month,

            'discount_min_months' => $request->discount_min_months,
            'discount_percentage' => $request->discount_percentage,

            'is_active' => $request->is_active,
        ]);

        return redirect()
            ->route('subscriptions.index')
            ->with('success', 'Subscription plan created successfully.');
    }

    /**
     * Show single plan
     */
    public function show($id)
    {
        $plan = SubscriptionPlanModel::findOrFail($id);

        return view('layouts.subscriptions.plans.show', compact('plan'));
    }

    /**
     * Edit form
     */
    public function edit($id)
    {
        $plan = SubscriptionPlanModel::findOrFail($id);

        return view('layouts.subscriptions.plans.edit', compact('plan'));
    }

    /**
     * Update plan
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',

            'number_admins' => 'required|integer|min:0',
            'number_branches' => 'required|integer|min:0',
            'number_apartments' => 'required|integer|min:0',
            'number_property_managers' => 'required|integer|min:0',

            'price_per_month' => 'required|numeric|min:0',

            'discount_min_months' => 'nullable|integer|min:0',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',

            'is_active' => 'required|boolean',
        ]);

        $plan = SubscriptionPlanModel::findOrFail($id);

        $plan->update([
            'name' => $request->name,
            'description' => $request->description,

            'number_admins' => $request->number_admins,
            'number_branches' => $request->number_branches,
            'number_apartments' => $request->number_apartments,
            'number_property_managers' => $request->number_property_managers,

            'price_per_month' => $request->price_per_month,

            'discount_min_months' => $request->discount_min_months,
            'discount_percentage' => $request->discount_percentage,

            'is_active' => $request->is_active,
        ]);

        return redirect()
            ->route('subscriptions.index')
            ->with('success', 'Subscription plan updated successfully.');
    }

    /**
     * Delete plan
     */
    public function destroy($id)
    {
        $plan = SubscriptionPlanModel::findOrFail($id);
        $plan->delete();

        return redirect()
            ->back()
            ->with('success', 'Subscription plan deleted successfully.');
    }
}