<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $type = [
            ['type' => 'Cash'],
            ['type' => 'Debit'],
            ['type' => 'E-wallet'],
        ];

        DB::table('payment_method_types')->insert($type);
    }
}
