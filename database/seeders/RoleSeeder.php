<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['role_name' => 'user', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['role_name' => 'mua', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['role_name' => 'admin', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ];

        DB::table('roles')->insert($roles);
    }
}
