<?php

namespace Database\Seeders;

use App\Models\PackageDetail;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PackageDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $package_details = [
            [
                "mua_id" => 2,
                "item_name" => "Perona Pipi",
                "description" => "Perona pipi alami untuk memberikan tampilan pipi yang merona."
            ],
            [
                "mua_id" => 2,
                "item_name" => "Perona Bibir",
                "description" => "Perona bibir alami untuk memberikan tampilan bibir yang merona."
            ],
            [
                "mua_id" => 2,
                "item_name" => "Foundation",
                "description" => "Foundation dengan formula ringan untuk memberikan dasar makeup yang halus dan merata."
            ],
            [
                "mua_id" => 2,
                "item_name" => "Eyeliner",
                "description" => "Eyeliner tahan air untuk memberikan garis mata yang tajam dan dramatis."
            ],
            [
                "mua_id" => 2,
                "item_name" => "Maskara",
                "description" => "Maskara untuk memberikan efek bulu mata tebal dan panjang."
            ],
            [
                "mua_id" => 2,
                "item_name" => "Bedak Tabur",
                "description" => "Bedak tabur ringan yang membantu menyerap minyak berlebih dan mengunci makeup."
            ],
            [
                "mua_id" => 2,
                "item_name" => "Highlighter",
                "description" => "Highlighter untuk memberikan efek wajah yang bercahaya dan berkilau."
            ],
            [
                "mua_id" => 2,
                "item_name" => "Contouring Powder",
                "description" => "Powder kontur untuk memberikan dimensi pada wajah dengan teknik shading."
            ],
            [
                "mua_id" => 2,
                "item_name" => "Kebaya",
                "description" => "Kebaya tradisional dengan desain elegan, cocok untuk berbagai acara spesial."
            ],
            [
                "mua_id" => 2,
                "item_name" => "Blush On",
                "description" => "Blush on yang lembut untuk memberikan rona pipi yang segar dan natural."
            ]
        ];

        DB::table('package_details')->insert($package_details);

        $packages = [
            [
                "mua_id" => 2,
                "package_name" => "Basic",
                "description" => "Paket lengkap untuk Makeup anda",
                "price" => 60000,
            ],
            [
                "mua_id" => 2,
                "package_name" => "Wisuda",
                "description" => "Paket lengkap untuk kegiatan anda",
                "price" => 150000,
            ],
            [
                "mua_id" => 2,
                "package_name" => "Wedding",
                "description" => "Paket lengkap untuk kegiatan anda",
                "price" => 1000000,
            ],
        ];

        DB::table('packages')->insert($packages);

        $package_detail_packages = [
            [
                "package_id" => 1,
                "package_detail_id" => 1
            ],
            [
                "package_id" => 1,
                "package_detail_id" => 2
            ],
            [
                "package_id" => 1,
                "package_detail_id" => 3
            ],
            [
                "package_id" => 1,
                "package_detail_id" => 4
            ],
            [
                "package_id" => 2,
                "package_detail_id" => 1
            ],
            [
                "package_id" => 2,
                "package_detail_id" => 5
            ],
            [
                "package_id" => 2,
                "package_detail_id" => 7
            ],
            [
                "package_id" => 3,
                "package_detail_id" => 8
            ],
            [
                "package_id" => 3,
                "package_detail_id" => 9
            ],
            [
                "package_id" => 3,
                "package_detail_id" => 10
            ]
        ];
        DB::table('package_detail_packages')->insert($package_detail_packages);

    }
}
