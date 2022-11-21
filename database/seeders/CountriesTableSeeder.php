<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class CountriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $countries = [
            ['name' => "Argentina",  'enabled' => 1],
            ['name' => "Bolivia",  'enabled' => 1],
            ['name' => "Brasil",  'enabled' => 1],
            ['name' => "Colombia",  'enabled' => 1],
            ['name' => "Ecuador",  'enabled' => 1],
            ['name' => "Perú",  'enabled' => 1],
            ['name' => "Venezuela", 'enabled' => 1]
        ];
        
        DB::table('countries')->insert($countries);
    }
}
