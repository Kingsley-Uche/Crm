<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\RentAccount;
use App\Models\RentCycle;
use App\Models\ApartmentIdentity;
use App\Models\TenantModel as Tenant;
use App\Http\Controllers\InvoiceController;
use Illuminate\Support\Facades\Mail;
use App\Mail\RentDueReminder;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SendRentDueReminder extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'app:send-rent-due-reminder';

    /**
     * The console command description.
     */
    protected $description = 'Send rent due reminders and start new cycles if expired';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Only run email reminders every 3 days
        $shouldSendEmail = Carbon::now()->dayOfYear % 3 === 0;

        // Fetch accounts along with their eager-loaded relationships
        $accounts = RentAccount::with([
            'rentCycles' => function ($query) {
                $query->orderByDesc('end_date');
            },
            'apartment:id,tenancy_type,pro_sco_code,property_ref,ownership,admin_unit,address,post_code,unique_code',
            'tenant:id,first_name,last_name,occupant_email'
        ])
        ->select('id', 'tenant_id', 'apartment_id', 'unit_number', 'start_date', 'account_type', 'status')
        ->where('status', 'active')
        ->get();

        foreach ($accounts as $account) {
            $tenant = $account->tenant;
            $latestCycle = $account->rentCycles->first();

            if (!$latestCycle || !$tenant) {
                continue; // Skip if data is incomplete
            }

            $now = Carbon::now();
            $endDate = Carbon::parse($latestCycle->end_date);

            // ✅ Start new cycle if current one has expired
            if ($endDate->isPast()) {
                $alreadyStarted = RentCycle::where('rent_account_id', $account->id)
                    ->whereDate('start_date', $now->toDateString())
                    ->exists();

                if (!$alreadyStarted) {
                    // Assign the created model instance to $newCycle
                    $newCycle = RentCycle::create([
                        'rent_account_id' => $account->id,
                        'apartment_id'     => $account->apartment_id,
                        'tenant_id'        => $account->tenant_id,
                        'unit_number'      => $account->unit_number,
                        'rent_fee'         => $latestCycle->rent_fee,
                        'payment_method'   => 'NONE',
                        'payment_made'     => 0,
                        'start_date'       => $now,
                        'end_date'         => $now->copy()->addMonths($latestCycle->duration_months),
                        'duration_months'  => $latestCycle->duration_months,
                        'escalation_policy'=> $latestCycle->escalation_policy,
                    ]);

                    // Fetch the specific apartment fields required for invoice generation
                    $apartmentData = ApartmentIdentity::where('id', $account->apartment_id)
                        ->select('location_models_id', 'branch_id', 'property_manager_id', 'id')
                        ->first();

                    // Generate invoice here
                    if ($apartmentData) {
                        $request = new Request();
                        $tenantFullName = trim($tenant->full_name);

                        $request->merge([
                            'branch_id'   => $apartmentData->branch_id,
                            'location_id' => $apartmentData->location_models_id,
                            'paid_amount' => 0,
                            'description' => 'Rent Renewal Invoice for ' .
                                $newCycle->start_date->toDateString() . ' to ' .
                                $newCycle->end_date->toDateString() . ' for ' .
                                $tenantFullName .
                                '<br>Number of Months: ' .
                                $newCycle->duration_months,

                            'amount'   => $newCycle->rent_fee,
                            'due_date' => now()->toDateString(),

                            'items' => [
                                [
                                    'name' => 'Rent Renewal Invoice for ' .
                                        $newCycle->start_date->toDateString() . ' to ' .
                                        $newCycle->end_date->toDateString() . ' for ' .
                                        $tenantFullName .
                                        '<br>Number of Months: ' .
                                        $newCycle->duration_months,

                                    'qty'         => 1,
                                    'unit_charge' => $newCycle->rent_fee,
                                    'amount'      => $newCycle->rent_fee,
                                ]
                            ]
                        ]);

                        $invoiceController = app(InvoiceController::class);
                        $invoiceController->store($request);

                        $this->info("New rent cycle and invoice started for account ID: {$account->id}");
                    }
                }
            }

            // ✅ Send email if rent cycle expires in less than 14 days (and is still upcoming)
            if ($shouldSendEmail) {
                // $now -> diffInDays($endDate) yields positive days remaining until due
                $daysUntilDue = $now->diffInDays($endDate, false);

                if ($daysUntilDue <= 14 && $daysUntilDue >= 0) {
                    try {
                        if (!empty($tenant->occupant_email)) {
                            Mail::to($tenant->occupant_email)->send(new RentDueReminder($account, $latestCycle));
                            $this->info("Reminder sent to: {$tenant->occupant_email}");
                        }
                    } catch (\Exception $e) {
                        Log::error("Failed to send reminder to {$tenant->occupant_email}: " . $e->getMessage());
                    }
                }
            }
        }

        $this->info("SendRentDueReminder process completed.");
    }
}