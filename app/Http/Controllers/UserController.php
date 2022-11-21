<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return User::where(['rol' => User::ROL_ADMIN])
                    ->leftJoin('companies', 'companies.id', '=', 'users.company_id')
                    ->select('users.*', 'companies.name as company')
                    ->get();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function free()
    {
        return User::where([
            'rol' => User::ROL_ADMIN,
            'company_id' => null
        ])->get();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = $request->all();
        $user['rol'] =  User::ROL_ADMIN;
        $user['photo'] = '/assets/images/Portrait_Placeholder.png';
        $realPassword = !is_null($user['generate']) && $user['generate'] ? rand(111111, 999999) : $user['password'];

        $user['password'] = bcrypt( $realPassword );


         $userCreated = User::create($user);
        
        if ( (!is_null($user['generate']) && !isset($user['send_credentials'])) || (isset($user['send_credentials']) && !is_null($user['send_credentials']) && $user['send_credentials'])) {
                    $data = [
                        'body' => 'te damos la bienvenida a Ethos Perú, software de denuncias. Tú contraseña es <code>'. $realPassword .'</code>.' ,
                        'title' => $user['first_name'] . ' ' . $user['last_name'] . ','
                    ];
    
                    MailController::simpleSendMail($data, $user['email'], 'Bienvenido a Ethos Perú!');
                }


        return $userCreated;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($email)
    {
        $user = User::where('email', $email)->first();
        
        return $user ? json_encode(['email' => $user->email]) : null;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (! is_null($request->enabled) && $request->enabled) {

            $user = User::find($id);

            User::where([
                'company_id' => $user->company_id,
                'rol' => User::ROL_ADMIN
            ])->update(['enabled' => false]);
            

            $company = Company::where('id', $user->company_id)->update(['user_id' => $user->id]);

            $user->enabled = true;
            $user->save();

            return [];
        }

        $user = $request->except(['generate', 'send_credentials']);

        if (! empty($user['password'])) {
            $user['password'] = bcrypt($user['password']);
        }
        
        return User::where('id', $id)->update($user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
