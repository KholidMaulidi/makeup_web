<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            ['service_name' => 'Basic Makeup', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['service_name' => 'Weeding Makeup', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['service_name' => 'Party Makeup', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['service_name' => 'Photoshoot Makeup', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['service_name' => 'Engagement Makeup', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['service_name' => 'Graduation Makeup', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            
        ];

        DB::table('services')->insert($services);
    }
}
