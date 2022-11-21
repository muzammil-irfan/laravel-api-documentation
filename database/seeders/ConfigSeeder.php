<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class ConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $settings = [
            ['key' => "restore_email_name", 'value'=> ''],
			['key' => "restore_email_address", 'value'=> ''],
			['key' => "restore_email_password", 'value'=> ''],
			['key' => "notification_email_name", 'value'=> ''],
			['key' => "notification_email_address", 'value'=> ''],
			['key' => "notification_email_password", 'value'=> ''],
        ];
        
        DB::table('settings')->insert($settings);
    }
}
