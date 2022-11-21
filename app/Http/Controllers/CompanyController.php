<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Category;
use App\Models\Company;
use App\Models\Source;
use App\Models\User;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function index()
    {
        return Company::leftJoin('users', 'users.id', '=', 'companies.user_id')
            ->select(
                'companies.*', 
                'users.first_name as user_first_name', 
                'users.last_name as user_last_name'
            )
            ->get();
    }

    public function store(Request $request)
    {
        $company = Company::create($request->get('company'));

        $companyId = $company->id;

        User::where([
            'company_id' => $companyId,
            'rol' => User::ROL_ADMIN
        ])->update(['enabled' => false]);

        if ($request->get('user')) {
            $user = $request->get('user');
            $userId = $user['id'];

            if (is_null($userId)) {
                $user['rol'] = User::ROL_ADMIN;
                $user['company_id'] = $companyId;
    
                $user['photo'] = '/assets/images/Portrait_Placeholder.png';
                
                $realPassword = !is_null($user['generate']) && $user['generate'] ? rand(111111, 999999) : $user['password'];

                $user['password'] = bcrypt( $realPassword );
                
                $userId = User::create($user)->id;

                if( (!is_null($user['generate']) && !isset($user['send_credentials'])) || (isset($user['send_credentials']) && !is_null($user['send_credentials']) && $user['send_credentials'])) {
                    $data = [
                        'body' => 'te damos la bienvenida a Ethos Perú, software de denuncias. Tú contraseña es <code>'. $realPassword .'</code>.' ,
                        'title' => $user['first_name'] . ' ' . $user['last_name'] . ','
                    ];
    

                    MailController::simpleSendMail($data, $user['email'], 'Bienvenido a Ethos Perú!');
                }
                
            } else {
                $user = User::find($userId);

                $user->company_id = $companyId;
                $user->save();
            }

            $company->user_id = $userId;
            $company->save();
        }

        Category::generteDefaultForCompany($companyId);
        Area::generteDefaultForCompany($companyId);
        Source::generteDefaultForCompany($companyId);

        return [];
    }

    public function show($slug)
    {
        if (is_numeric($slug)) {
            return Company::find($slug);
        }

        return Company::where([
            'slug' => $slug,
            'enabled' => true
        ])->first();
    }

    public function byRuc($ruc) {
        return Company::where('ruc', $ruc)->first();
    }

    public function update(Request $request, $id)
    {
        if (! is_null($request->get('user_id'))) {
            
            User::where([
                'company_id' => $id,
                'rol' => User::ROL_ADMIN
            ])->update(['enabled' => false]);

            $userId = $request->get('user_id');

            $user = User::find($userId);
            $user->company_id = $id;
            $user->enabled = true;
            $user->save();

            $company = Company::find($id);
            $company->user_id = $userId;
            $company->save();

            return [];
        }

        Company::where('id', $id)->update($request->except(['id']));
        
        return Company::where('companies.id', $id)
            ->leftJoin('users', 'users.id', '=', 'companies.user_id')
            ->select(
                'companies.*', 
                'users.first_name as user_first_name', 
                'users.last_name as user_last_name'
            )
            ->first();
    }


}
