<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

    
class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Auth::user();
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
        $user = $request->only(['first_name', 'last_name', 'photo', 'password']);

        if (! empty($user['password'])) {
            $user = ['password' => bcrypt($user['password'])];
        } else {
        	unset($user['password']);
        }
        
        User::where('id', Auth::user()->id)->update($user);

        return User::where('id', Auth::user()->id)->first();
    }

}
