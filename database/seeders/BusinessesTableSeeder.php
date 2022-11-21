<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class BusinessesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $bussinesses = [
            ['name' => "Banca Seguros", 'enabled'=> 1],
            ['name' => "Construcción", 'enabled'=> 1],
            ['name' => "Consultoría", 'enabled'=> 1],
            ['name' => "Educación", 'enabled'=> 1],
            ['name' => "Manufactura", 'enabled'=> 1],
            ['name' => "Marketing", 'enabled'=> 1],
            ['name' => "Marketing", 'enabled'=> 1],
            ['name' => "Restaurantes", 'enabled'=> 1],
            ['name' => "Tecnologias de la informacion", 'enabled'=> 1],
            ['name' => "Transporte", 'enabled'=> 1],
        ];

        DB::table('businesses')->insert($bussinesses);
    }
}
