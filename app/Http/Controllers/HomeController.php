<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\BlockModel;
use App\Models\Shelters;
use App\Models\Repairs;
use App\Models\VoidsModel;
use App\Models\BookingModel;
use App\Models\TenantModel as Tenant;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Http\Controllers\NoficationController;
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
public function AdminIndex()
{
    $user = Auth::user();
    Session::put('user', $user);
   $repairs = Repairs::select('id', 'progress', 'completion_date', 'progress')->get();
   
   $uncompletedRepairs = $repairs->filter(function ($repair) {
    return is_null($repair->completion_date);
})->groupBy('progress')->map->count();

$total_uncompleted = $repairs->filter(function ($repair) {
    $progress = trim(strtolower($repair->progress ?? ''));
    return $progress !== 'completed';
})->count();



$overdueCount = $repairs->filter(function ($repair) {
    return !empty($repair->completion_date) &&
           Carbon::parse($repair->completion_date)->lessThan(now());
})->count();

$uncompletedRepairs['Overdue']=+$overdueCount;
$total_incomplete = 

$repairProgressCounts =$uncompletedRepairs ;


    // Get all blocks with relationships
    $blocks = BlockModel::with([
        'shelt' => function ($query) {
            $query->select('id', 'name');
        },
        'location' => function ($query) {
            $query->select('id', 'name');
        },
        'apartments' => function ($query) {
            $query->select('id', 'block_models_id', 'block_shelter_id', 'shelter_id');
        }
    ])->get();

    // Count total blocks
    $totalBlocks = $blocks->count();

    $allShelters = \App\Models\Shelter::select('id', 'name')->get()->keyBy('id');
    $voids = VoidsModel::select('id', 'property_type')->get();
    $voids = $voids->groupBy('property_type')->map->count();
    $tenants = Tenant::count();

    // **Call the function to get booking data**
    $booked = $this->getBookingData();

    // Count apartment shelter types grouped by shelter name
    $shelterTypeCounts = [];

    foreach ($blocks as $block) {
        foreach ($block->apartments as $apartment) {
            $shelterId = $apartment->shelter_id;

            if (isset($allShelters[$shelterId])) {
                $shelterName = $allShelters[$shelterId]->name;

                if (!isset($shelterTypeCounts[$shelterName])) {
                    $shelterTypeCounts[$shelterName] = 0;
                }

                $shelterTypeCounts[$shelterName]++;
            }
        }
    }

    return view('layouts.dashboard.home.index', compact(
        'user',
        'blocks',
        'totalBlocks',
        'shelterTypeCounts',
        'repairProgressCounts',
        'total_uncompleted',
        'voids',
        'tenants',
        'booked',
    ));
}

private function getBookingData()
{
    $filterStart = date('Y-01-01');
    $filterEnd = date('Y-12-31');

    $bookings = BookingModel::select(
        DB::raw('MONTH(start_date) as month'),
        DB::raw('COUNT(id) as total')
    )
    ->where('is_cancelled', false)
    ->whereBetween('start_date', [$filterStart, $filterEnd])
    ->groupBy('month')
    ->orderBy('month')
    ->get();

    $labels = [];
    $data = [];

    for ($m = 1; $m <= 12; $m++) {
        $labels[] = Carbon::create()->month($m)->format('M'); // Jan, Feb, ...
        $monthData = $bookings->firstWhere('month', $m);
        $data[] = $monthData ? $monthData->total : 0;
    }

    return ['data' => $data, 'labels' => $labels];
}

  
}
