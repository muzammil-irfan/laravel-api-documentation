<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class RestorePasswordController extends Controller
{
    public function store(Request $request) {
        
        if (is_null(User::where('email', $request->email)->first())) {
			return response()->json(['error' => 'Este correo no existe'], 400);        	
        }

        $realPassword = rand(111111, 999999);

        User::where('email', $request->email)->update([
			'password' => bcrypt( $realPassword )
        ]);

        $data = [
            'body' => 'haz solicitado restaurar tu contraseña. Tu nueva contraseña es <code>'. $realPassword .'</code>. En cualquier momento puedes ir al apartado de Perfil y cambiar tu contraseña desde ahí.' ,
            'title' => 'Hola,'
         ];
    
		
		MailController::simpleSendMail($data, $request->email, 'Ethos Perú - Nueva contraseña generada', false);

        return response()->json(['error' => 'Nueva contraseña generada'], 200);
    }
}
