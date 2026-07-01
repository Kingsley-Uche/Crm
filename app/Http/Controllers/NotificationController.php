<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Repairs;
use App\Models\ParkPermits;
use App\Models\PestModel;
use Carbon\Carbon;

class NotificationController extends Controller
{
    public function checkDueRepairs()
    {
        $now = Carbon::now();

        $repairs = Repairs::with('block:id,name')
            ->select([
                'id', 'block_id', 'description', 'unit_number', 'received_date',
                'repair_type', 'due_date', 'appointment', 'completion_date'
            ])
            ->whereNull('completion_date')
            ->where(function ($query) use ($now) {
                $query->whereDate('appointment', '<=', $now)
                      ->orWhereDate('due_date', '<=', $now);
            })
            ->get();

        return $repairs;
    }

    public function checkParkPermits()
    {
        $now = Carbon::now();

        $permits = ParkPermits::with([
                'parkCategory:id,name',
                'park:id,name'
            ])
            ->select([
                'id', 'fname', 'lname', 'phone', 'email', 'start_time', 'end_time'
            ])
            ->whereDate('end_time', '<=', $now)
            ->where('read', 'no')
            ->get();

        return $permits;
    }

    public function checkPestControl()
    {
        $now = Carbon::now();

        $pests = PestModel::with('block:id,name')
            ->select([
                'id', 'block_id', 'apartment_id', 'issue_type', 'description', 'status',
                'ref', 'image', 'received_date', 'progress', 'deadline_timeframe',
                'appointment_timeframe', 'action_timeline', 'assigned_to', 'due_date',
                'appointment', 'completion_date', 'pest_control_fee'
            ])
            ->whereNull('completion_date')
            ->where(function ($query) use ($now) {
                $query->whereDate('appointment', '<=', $now)
                      ->orWhereDate('due_date', '<=', $now);
            })
            ->get();

        return $pests;
    }
}
