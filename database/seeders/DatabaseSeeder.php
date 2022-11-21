<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;


// use BusinessesTableSeeder;
// use CompanyTableSeeder;
// use ConfigSeeder;
// use CountriesTableSeeder;
// use SectorTableSeeder;
// use SliderSeeder;
// use UserOwnerSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        $this->call([
            CountriesTableSeeder::class,
            BusinessesTableSeeder::class,
            SectorTableSeeder::class,
            UserOwnerSeeder::class,
            CompanyTableSeeder::class,
            SliderSeeder::class,
            ConfigSeeder::class
        ]);
        // $this->call(CountriesTableSeeder::class);
        // $this->call(BussinessesTableSeeder::class);
        // $this->call(SectorTableSeeder::class);
        // $this->call(UserOwnerSeeder::class);
        // $this->call(CompanyTableSeeder::class);
        // $this->call(SliderSeeder::class);
        // $this->call(ConfigSeeder::class);
    }
}
