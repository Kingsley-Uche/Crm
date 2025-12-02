<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AdminModel; 
use App\Models\Shelter; 
use App\Models\Amenities; 
use App\Models\PaymentTime;
use App\Models\EstateOwner;
use App\Models\BlockModel;
use Database\Seeders\PermissionSeeder;
use Illuminate\Support\Facades\Hash;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
    

AdminModel::updateOrCreate(
    ['email' => 'akanebidollz@gmail.com'],
    [
        'fName' => 'Uche',
        'lName' => 'Kamma',
        'phone' => '1234567890',
        'password' => Hash::make('exoticka@34'),
        'is_active' => 1,
        'is_system_admin' => 1,
    ]
);

AdminModel::updateOrCreate(
    ['email' => 'ucmtn2@gmail.com'],
    [
        'fName' => 'Uche',
        'lName' => 'Kamma',
        'phone' => '1234567890',
        'password' => Hash::make('password123'),
        'is_active' => 1,
        'is_system_admin' => 1,
    ]
);

       // Define shelter types as a local variable instead of a static property
$shelterTypes = [
     ['name' => 'duplex'],
      ['name' => 'warehouse'],
       ['name' => 'flat'],
        ['name' => 'shop'],
        ['name' => 'studio'],
		['name' => 'single room'],
   
];

// Loop through each shelter type and update or create
foreach ($shelterTypes as $shelter) {
    Shelter::updateOrCreate(
        [
            'name' => $shelter['name'] // Unique field to check for existing record
        ],
        [
            'name' => $shelter['name'], // Value to insert/update
            'is_active' => 1, // You can add any additional fields you want to set
        ]
    );
}

$amenities = [
    ['name'=>'swimming pool'],
    ['name'=>'toilets'],
    ['name'=>'bathroom'],
    ['name'=>'reception'],
    ['name'=>'bed'],
    ['name'=>'store'],
    ['name'=>'kitchen'],
];
foreach ($amenities as $amenity) {
    Amenities::updateOrCreate(
        [
            'name' => $amenity['name'] // Unique field to check for existing record
        ],
        [
            'name' => $amenity['name'], // Value to insert/update
            'is_active' => 1, // You can add any additional fields you want to set
        ]
    );
    

}

 $payment_times = ['daily', 'weekly', 'monthly', 'semi-annually', 'annually'];

        foreach ($payment_times as $pay_t) {
            PaymentTime::updateOrCreate(
                ['payment_frequency' => $pay_t], // Condition to check if the record exists
                ['payment_frequency' => $pay_t]  // Values to insert if the record doesn't exist
            );
        }
        


$this->call([
        PermissionSeeder::class,
    ]);
    }

}
