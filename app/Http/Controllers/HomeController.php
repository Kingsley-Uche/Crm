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

    // ---------------- Repairs ----------------
    $repairs = Repairs::select('id', 'progress', 'completion_date')->get();

    $repairProgressCounts = $repairs
        ->groupBy(function ($repair) {
            return $repair->progress ?: 'Unknown';
        })
        ->map->count();

    $total_uncompleted = $repairs->filter(function ($repair) {
        return trim(strtolower($repair->progress ?? '')) !== 'completed';
    })->count();

    $overdueCount = $repairs->filter(function ($repair) {
        return !empty($repair->completion_date) &&
            Carbon::parse($repair->completion_date)->lt(now());
    })->count();

    $repairProgressCounts['Overdue'] = $overdueCount;

    // ---------------- Basic Counts ----------------
    $locations = DB::table('location_models')->count();
    $tenants = Tenant::count();

    // ---------------- Shelter Apartment Types ----------------
    $shelterTypeCounts = DB::table('apartment_identities as a')
        ->join('shelters as s', 's.id', '=', 'a.shelter_id')
        ->select('s.name as shelter_name', DB::raw('COUNT(a.id) as total'))
        ->groupBy('s.name')
        ->get();
$occupancyByShelter = DB::table('shelters as s')
    ->leftJoin('apartment_identities as a', 's.id', '=', 'a.shelter_id')
    ->leftJoin('booking_models as b', function ($join) {
        $join->on('a.id', '=', 'b.apartment_id')
             ->where('b.is_cancelled', false);
    })
    ->select(
        's.id',
        's.name as shelter_name',
        DB::raw('COUNT(DISTINCT a.id) as total_units'),
        DB::raw('COUNT(DISTINCT b.apartment_id) as occupied_units')
    )
    ->groupBy('s.id', 's.name')
    ->get();
   $occupancyByShelter = $occupancyByShelter->map(function ($item) {

    $vacant = max($item->total_units - $item->occupied_units, 0);

    $occupancy_percent = $item->total_units > 0
        ? round(($item->occupied_units / $item->total_units) * 100, 2)
        : 0;

    return [
        'shelter_name' => $item->shelter_name,
        'total_units' => $item->total_units,
        'occupied_units' => $item->occupied_units,
        'vacant_units' => $vacant,
        'occupancy_percent' => $occupancy_percent
    ];
});

    

    // ---------------- Voids ----------------
    $voids = VoidsModel::select('property_type')
        ->get()
        ->groupBy('property_type')
        ->map->count();

    // ---------------- Bookings ----------------
    $booked = $this->getBookingData();


    


    return view('layouts.dashboard.home.index', compact(
    'user',
    'locations',
    'repairProgressCounts',
    'total_uncompleted',
    'shelterTypeCounts',
    'voids',
    'tenants',
    'booked',
    'occupancyByShelter'
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
