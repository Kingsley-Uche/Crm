<?php

namespace App\Http\Controllers;

use App\Models\BranchModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class BranchController extends Controller
{
    public function index()
    {
        $branches = BranchModel::latest()->get();
        return view('layouts.branches.index', compact('branches'));
    }

    public function create()
    {
        return view('layouts.branches.create');
    }

    public function store(Request $request)
    {
        $rules = [
            'branches' => 'required|array|min:1',

            'branches.*.name' => 'required|string|max:255|unique:branch_models,name',
            'branches.*.address' => 'nullable|string',
            'branches.*.contact_email' => 'nullable|email|max:255|unique:branch_models,contact_email',
            'branches.*.contact_phone' => 'nullable|string|max:50',

            'branches.*.manager_name' => 'nullable|string|max:255',
            'branches.*.manager_email' => 'nullable|email|max:255',
            'branches.*.manager_phone' => 'nullable|string|max:50',

            'branches.*.account_name' => 'nullable|string|max:255',
            'branches.*.account_number' => 'nullable|string|max:100',
            'branches.*.bank_name' => 'nullable|string|max:255',
        ];

        $validated = $request->validate($rules);

        $user = auth()->user();

        $newCount = count($validated['branches']);
        $currentCount = BranchModel::count();
        $total = $currentCount + $newCount;
        

        if (!$user->is_site_admin && !$this->checkBranchLimit($total)) {
            return back()->withInput()->with('error',
                "Branch limit exceeded. You can only create up to {$currentCount} number of branches. Upgrade your subscription to add more branches."
            );
        }
        

        DB::transaction(function () use ($validated) {

            foreach ($validated['branches'] as $branch) {
                BranchModel::create([
                    'name'           => trim($branch['name']),
                    'address'        => $branch['address'] ?? null,
                    'contact_email'  => $branch['contact_email'] ?? null,
                    'contact_phone'  => $branch['contact_phone'] ?? null,
                    'manager_name'   => $branch['manager_name'] ?? null,
                    'manager_email'  => $branch['manager_email'] ?? null,
                    'manager_phone'  => $branch['manager_phone'] ?? null,
                    'account_name'   => $branch['account_name'] ?? null,
                    'account_number' => $branch['account_number'] ?? null,
                    'bank_name'      => $branch['bank_name'] ?? null,
                ]);
            }
        });

        $this->refreshBranchCache();

        return redirect()
            ->route('branches.index')
            ->with('success', count($validated['branches']) . ' branch(es) created successfully.');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'contact_email' => 'nullable|email',
            'contact_phone' => 'nullable|string',
            'manager_name' => 'nullable|string',
            'manager_email' => 'nullable|email',
            'manager_phone' => 'nullable|string',
            'account_name' => 'nullable|string',
            'account_number' => 'nullable|string',
            'bank_name' => 'nullable|string',
        ]);

        $branch = BranchModel::findOrFail($id);
        $branch->update($validated);

        $this->refreshBranchCache();

        return redirect()
            ->route('branches.index')
            ->with('success', 'Branch updated successfully');
    }

    public function destroy($id)
    {
        BranchModel::findOrFail($id)->delete();

        $this->refreshBranchCache();

        return redirect()
            ->route('branches.index')
            ->with('success', 'Branch deleted successfully');
    }

    public function show($id)
    {
        $branch = BranchModel::findOrFail($id);
        return view('layouts.branches.show', compact('branch'));
    }

    public function edit($id)
    {
        $branch = BranchModel::findOrFail($id);
        return view('layouts.branches.edit', compact('branch'));
    }

    /**
     * CACHE ONLY WHAT YOU NEED
     */
    private function refreshBranchCache(): void
    {
        $data = BranchModel::select('id', 'name')->latest()->get();

        Cache::put('branch_details', $data, now()->addHours(6));
    }

    /**
     * PROPER LIMIT CHECK
     */
    private function checkBranchLimit(int $total): bool
    {
        $plan = session('subscription_status')['data'] ?? null;

        if (!$plan) {
            return false;
        }

        return $total <= $plan->number_branches;
    }
}