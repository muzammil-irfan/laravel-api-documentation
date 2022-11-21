<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class SectorTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sectors = [
            ['name' => "Privada", 'enabled'=> 1],
            ['name' => "Estatal", 'enabled'=> 1],
        ];

        DB::table('sectors')->insert($sectors);
    }
}
