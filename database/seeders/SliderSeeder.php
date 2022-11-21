<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class SliderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sliders = [
            [ 'url' => 'assets/images/slider/banner1.jpg'],
            [ 'url' => 'assets/images/slider/banner2.jpg'],
            [ 'url' => 'assets/images/slider/banner3.jpg'],
            [ 'url' => 'assets/images/slider/banner4.jpg'],
        ];

        DB::table('sliders')->insert($sliders);
    }
}
