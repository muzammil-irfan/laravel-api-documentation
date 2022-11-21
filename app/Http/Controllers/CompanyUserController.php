<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class CompanyUserController extends Controller
{
    public function __construct() {
        $this->middleware('auditor', ['except' => ['index']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return User::where([
            ['company_id', '=' ,Auth::user()->company_id],
            ['rol', '<>', User::ROL_ADMIN]
        ])
        ->get();
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
        $user['company_id'] = Auth::user()->company_id;
        $user['photo'] = '/assets/images/Portrait_Placeholder.png';
        $user['rol'] = $user['rol_id'];

        $realPassword = !is_null($user['generate']) && $user['generate'] ? rand(111111, 999999) : $user['password'];

        $user['password'] = bcrypt( $realPassword );


                if ( (!is_null($user['generate']) && !isset($user['send_credentials'])) || (isset($user['send_credentials']) && !is_null($user['send_credentials']) && $user['send_credentials'])) {
                    $data = [
                        'body' => 'te damos la bienvenida a Ethos Perú, software de denuncias. Tú contraseña es <code>'. $realPassword .'</code>.' ,
                        'title' => $user['first_name'] . ' ' . $user['last_name'] . ','
                    ];
    

                    MailController::simpleSendMail($data, $user['email'], 'Bienvenido a Ethos Perú!');
                }
        
        return User::create($user);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        $user = $request->except(['generate', 'send_credentials', 'rol_id']);
        $user['rol'] = $request->get('rol_id');

        if (User::ROL_ADMIN !=  Auth::user()->rol) {
            unset($user['start']);
            unset($user['end']);
        }
        
        if (! empty($user['password']) && ! is_null($user['password'])) {
            $user['password'] = bcrypt($user['password']);
        } else {
            unset($user['password']);
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
