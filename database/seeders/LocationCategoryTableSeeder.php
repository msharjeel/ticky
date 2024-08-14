<?php

namespace Database\Seeders;

use App\Http\Controllers\Api\Dashboard\StatsController as DashboardStatsController;
use App\Models\LocationCategory;
use Exception;
use Illuminate\Database\Seeder;

class LocationCategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws Exception
     */
    public function run()
    {
        if (LocationCategory::count() === 0) {
            // Add Admin Role
            for($i=1;$i<6;$i++){
                $LocationCategory = new LocationCategory();
                $LocationCategory->id = $i;
                $LocationCategory->name = 'Category '.$i;
                $LocationCategory->save();
            }
        }
    }
}
