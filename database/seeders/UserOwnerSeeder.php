<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class UserOwnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'first_name' => 'Owner',
            'last_name' => 'System',
            'phone' => '1234567890',
            'email' => 'admin@ethosperu.com',
            'password' => bcrypt('123456'),
            'rol' => User::ROL_OWNER,
            'photo' => '/assets/images/Portrait_Placeholder.png',
        ]);
    }
}
