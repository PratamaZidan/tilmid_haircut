<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Service;

class ServicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

public function run(): void
{
    $data = [
        ['code'=>'reguler','name'=>'Reguler','price'=>25000,'category'=>'haircut','sort_order'=>10],
        ['code'=>'premium','name'=>'Premium','price'=>30000,'category'=>'haircut','sort_order'=>20],

        ['code'=>'maskerrambut','name'=>'Masker Rambut','price'=>20000,'category'=>'treatment','sort_order'=>10],
        ['code'=>'semirhitam','name'=>'Semir Hitam','price'=>50000,'category'=>'treatment','sort_order'=>20],
        ['code'=>'semirdasar','name'=>'Semir Dasar Lv.9','price'=>100000,'category'=>'treatment','sort_order'=>30],
        ['code'=>'semirblack','name'=>'Semir Color Black','price'=>150000,'category'=>'treatment','sort_order'=>40],
        ['code'=>'semirhairlight','name'=>'Semir Hair Light','price'=>180000,'category'=>'treatment','sort_order'=>50],

        ['code'=>'tip','name'=>'Tip','price'=>0,'category'=>'treatment','sort_order'=>999,'is_active'=>true,'is_public'=>false],
        ['code'=>'lainnya','name'=>'Lainnya','price'=>0,'category'=>'treatment','sort_order'=>1000,'is_active'=>true,'is_public'=>false],
    ];

    foreach ($data as $row) {
        Service::updateOrCreate(['code'=>$row['code']], $row);
    }
}
}
