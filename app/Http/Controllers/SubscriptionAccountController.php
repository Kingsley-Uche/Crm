<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionAccountModel;
use App\Models\SubscriptionPlanModel;
use App\Models\SubscriptionTrackerModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SubscriptionAccountController extends Controller
{
    /**
     * Display a listing of subscription accounts.
     */
    public function index()
    {
        $accounts = SubscriptionAccountModel::with('plan')
            ->latest()
            ->paginate(20);

        return view('layouts.subscriptions.accounts.index', compact('accounts'));
    }

    /**
     * Show form for creating a new subscription account.
     */
    public function create()
    {
        $plans = SubscriptionPlanModel::where('is_active', 1)->get();

        return view('layouts.subscriptions.accounts.create', compact('plans'));
    }

    /**
     * Store a newly created subscription account.
     */
       public function store(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after_or_equal:start_time',
            'fee' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive,expired,pending',
        ]);

        try {

            DB::beginTransaction();

            $plan = SubscriptionPlanModel::select([
                'id',
                'name',
                'number_admins',
                'number_branches',
                'number_apartments',
                'number_property_managers',
                'discount_percentage'
            ])->findOrFail($request->plan_id);

            /**
             * Idempotency check
             */
         $existingAccount = SubscriptionAccountModel::exists();

if ($existingAccount) {
     DB::rollBack();
    return redirect()
        ->back()
        ->with('error', 'A subscription account already exists,Kindly update it');
}

            $tracker = SubscriptionTrackerModel::firstOrCreate(

                [
                    'plan_id' => $plan->id,
                    'start_time' => $request->start_time,
                    'end_time' => $request->end_time,
                ],

                [
                    'plan_name' => $plan->name,

                    'fee' => $request->fee,

                    'plan_features' => json_encode([
                        'number_admins' => $plan->number_admins,
                        'number_branches' => $plan->number_branches,
                        'number_apartments' => $plan->number_apartments,
                        'number_property_managers' => $plan->number_property_managers,
                        'discount_percentage' => $plan->discount_percentage
                    ]),

                    'status' => $request->status
                ]
            );

            SubscriptionAccountModel::create([
                'plan_id' => $plan->id,
                'tracker_id' => $tracker->id,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'fee' => $request->fee,
                'status' => $request->status,
            ]);

            DB::commit();

            return redirect()
                ->route('subscription.account.index')
                ->with(
                    'success',
                    'Subscription account created successfully.'
                );

        } catch (\Throwable $e) {

            DB::rollBack();

            Log::error('Subscription account creation failed', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'error',
                    'Unable to create subscription account.'
                );
        }
    }

    /**
     * Show single subscription account.
     */
    public function show($id)
    {
        $account = SubscriptionAccountModel::findOrFail($id);

        return view('layouts.subscriptions.accounts.show', compact('account'));
    }

    /**
     * Show edit form.
     */
 public function edit($id)
{
    $account = SubscriptionAccountModel::select([
        'id',
        'plan_id',
        'start_time',
        'end_time',
        'fee',
        'status'
    ])->findOrFail($id);

    $plans = SubscriptionPlanModel::where('is_active', 1)
        ->select([
            'id',
            'name'
        ])
        ->get();

    return view(
        'layouts.subscriptions.accounts.edit',
        compact('account', 'plans')
    );
}

    /**
     * Update subscription account.
     */
   public function update(Request $request, $id)
{
    $request->validate([
        'plan_id' => 'required|exists:plans,id',
        'start_time' => 'nullable|date',
        'end_time' => 'nullable|date|after_or_equal:start_time',
        'fee' => 'required|numeric|min:0',
        'status' => 'required|in:active,inactive,expired,pending',
    ]);

    try {

        DB::beginTransaction();

        $account = SubscriptionAccountModel::findOrFail($id);

        $oldTracker = SubscriptionTrackerModel::find(
            $account->tracker_id
        );

        $planChanged = (int)$account->plan_id !== (int)$request->plan_id;

        /**
         * PLAN CHANGED
         */
        if ($planChanged) {

            $plan = SubscriptionPlanModel::select([
                'id',
                'name',
                'number_admins',
                'number_branches',
                'number_apartments',
                'number_property_managers',
                'discount_percentage'
            ])->findOrFail($request->plan_id);

            /**
             * deactivate previous snapshot
             */
            if ($oldTracker) {

                $oldTracker->update([
                    'status' => 'inactive'
                ]);
            }

            /**
             * idempotent tracker creation
             */
            $tracker = SubscriptionTrackerModel::firstOrCreate(

                [
                    'plan_id' => $plan->id,
                    'start_time' => $request->start_time,
                    'end_time' => $request->end_time
                ],

                [
                    'plan_name' => $plan->name,

                    'fee' => $request->fee,

                    'plan_features' => json_encode([
                        'number_admins' => $plan->number_admins,
                        'number_branches' => $plan->number_branches,
                        'number_apartments' => $plan->number_apartments,
                        'number_property_managers' => $plan->number_property_managers,
                        'discount_percentage' => $plan->discount_percentage
                    ]),

                    'status' => 'active'
                ]
            );

            $account->update([

                'plan_id' => $plan->id,

                'tracker_id' => $tracker->id,

                'start_time' => $request->start_time,

                'end_time' => $request->end_time,

                'fee' => $request->fee,

                'status' => $request->status
            ]);

        }

        /**
         * PLAN NOT CHANGED
         * simple update only
         */
        else {

            $account->update([

                'start_time' => $request->start_time,

                'end_time' => $request->end_time,

                'fee' => $request->fee,

                'status' => $request->status
            ]);

            /**
             * sync existing tracker
             */
            if ($oldTracker) {

                $oldTracker->update([

                    'start_time' => $request->start_time,

                    'end_time' => $request->end_time,

                    'fee' => $request->fee,

                    'status' => $request->status
                ]);
            }
        }

        DB::commit();

        return redirect()
            ->route('subscription.account.index')
            ->with(
                'success',
                'Subscription updated successfully.'
            );

    } catch (\Throwable $e) {

        DB::rollBack();

        \Log::error(
            'Subscription update failed',
            [
                'message'=>$e->getMessage(),
                'line'=>$e->getLine()
            ]
        );

        return back()
            ->withInput()
            ->with(
                'error',
                'Unable to update subscription.'
            );
    }
}

    /**
     * Delete subscription account.
     */
    public function destroy($id)
    {
        $account = SubscriptionAccountModel::findOrFail($id);
        $account->delete();

        return redirect()
            ->back()
            ->with('success', 'Subscription account deleted successfully.');
    }
}