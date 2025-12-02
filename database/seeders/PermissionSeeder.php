<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $resources = [
            'apartments', 'asb', 'booking', 'estate_owner', 'fob',
            'location', 'maintenance', 'park', 'rent','pest_control',
            'property','reports', 'shelter', 'tenancy', 'tenant', 'voids','complaints',
        ];

        $actions = ['create', 'read', 'update', 'delete'];

        $permissions = [];

        foreach ($resources as $resource) {
            foreach ($actions as $action) {
                $permissions[] = [
                    'name' => "{$action} {$resource}",
                    'slug' => "{$action}_{$resource}",
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }
        }

        DB::table('permissions')->insert($permissions);
    }
}
