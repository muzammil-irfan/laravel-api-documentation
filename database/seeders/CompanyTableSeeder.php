<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\Category;
use App\Models\ClosingReason;
use App\Models\Company;
use App\Models\Source;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class CompanyTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $company = Company::create([
            'name' => 'Company',
            'slug' => 'company',
            'ruc' => '10000000',
            'department' => 'Capital',
            'city' => 'Buenos Aries',
            'phone' => '1234567892',
            'address' => '100 # 15 - 69',
            'site' => 'company.com',
            'logo' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn%3AANd9GcQgwBrCndybBHQzOaO_8tKrv4b2rWsK3RT8lLGedzwaCtCpK2nj',
            'description' => 'A regular Company',
            'enabled' => 1,
            'country_id' => 1,
            'business_id' => 1,
            'sector_id' => 1,
        ]);

        DB::table('offices')->insert([
            'name' => 'Principal',
            'company_id' => $company->id,
            'country_id' => 1,
            'enabled' => 1,
        ]);

        $userId = DB::table('users')->insert([
            'first_name' => 'Jhon',
            'last_name' => 'Doe',
            'phone' => '1234567891',
            'email' => 'admin@company.com',
            'password' => bcrypt('123456'),
            'rol' => User::ROL_ADMIN,
            'company_id' => $company->id,
            'photo' => '/assets/images/Portrait_Placeholder.png',
        ]);

        $company->user_id = $userId;
        $company->save();
        
        Category::generteDefaultForCompany($company->id);
        Area::generteDefaultForCompany($company->id);
        Source::generteDefaultForCompany($company->id);
        ClosingReason::generteDefaultForCompany($company->id);
    }
}
