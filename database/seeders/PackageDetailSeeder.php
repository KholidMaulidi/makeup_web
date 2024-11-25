<?php

namespace Database\Seeders;

use App\Models\Package;
use App\Models\PackageDetail;
use Illuminate\Database\Seeder;

class PackageDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed data for Packages
        $packagesData = [
            ["mua_id" => 2, "package_name" => "Basic", "description" => "Paket lengkap untuk Makeup anda", "price" => 60000, "service_id" => 1],
            ["mua_id" => 2, "package_name" => "Wisuda", "description" => "Paket lengkap untuk kegiatan anda", "price" => 150000, "service_id" => 6],
            ["mua_id" => 2, "package_name" => "Wedding", "description" => "Paket lengkap untuk kegiatan anda", "price" => 1000000, "service_id" => 2],
        ];

        // Insert Packages and get their IDs
        $packages = [];
        foreach ($packagesData as $packageData) {
            $packages[] = Package::create($packageData);
        }

        // Seed data for Package Details and assign them to respective Packages
        $packageDetailsData = [
            [
                "package_id" => $packages[0]->id,
                "item_name" => "Perona Pipi",
            ],
            [
                "package_id" => $packages[0]->id,
                "item_name" => "Perona Bibir",
            ],
            [
                "package_id" => $packages[0]->id,
                "item_name" => "Foundation",
            ],
            [
                "package_id" => $packages[1]->id,
                "item_name" => "Eyeliner",
            ],
            [
                "package_id" => $packages[1]->id,
                "item_name" => "Maskara",
            ],
            [
                "package_id" => $packages[1]->id,
                "item_name" => "Bedak Tabur",
            ],
            [
                "package_id" => $packages[2]->id,
                "item_name" => "Highlighter",
            ],
            [
                "package_id" => $packages[2]->id,
                "item_name" => "Contouring Powder",
            ],
            [
                "package_id" => $packages[2]->id,
                "item_name" => "Kebaya",
            ],
            [
                "package_id" => $packages[2]->id,
                "item_name" => "Blush On",
            ]
        ];

        // Insert Package Details
        foreach ($packageDetailsData as $detailData) {
            PackageDetail::create($detailData);
        }
    }
}
