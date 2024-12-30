<?php

namespace Database\Seeders;

use App\Models\MakeupArtistProfile;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // User::create(
        //     [
        //         'name' => 'User',
        //         'email' => 'user@gmail.com',
        //         'password' => Hash::make('password'),
        //         'role_id' => 1,
        //     ],
        // );
        UserProfile::create(
            [
                "user_id" => 1,
                "gender" => "male",
                "address" => "Jl. raya kertasada",
                "district_id" => 2424,
                "postal_code" => "00979",
                "no_hp" => "081249573646"
            ],
        );

        // User::create(

        //     [
        //         'name' => 'Mua',
        //         'email' => 'mua@gmail.com',
        //         'password' => Hash::make('password'),
        //         'role_id' => 2,
        //     ]
        // );
        // MakeupArtistProfile::create(
        //     [
        //         "user_id" => 2,
        //         "gender" => "female",
        //         "address" => "Jl Raya Papar - Pare , No 469",
        //         "district_id" => 5034,
        //         "postal_code" => "64155",
        //         "no_hp" => "081249573646",
        //         "description" => "Ini Deskripsi",
        //         "latitude" => -7.7404285,
        //         "longitude" => 112.1521796
        //     ],
        // );
    }
}
