<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        //set validation
        $validator = Validator::make($request->all(), [
            'email'     => 'required',
            'password'  => 'required'
        ]);

        //if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //get credentials from request
        $credentials = $request->only('email', 'password');

        //if auth failed
        if(!$token = JWTAuth::attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau Password Anda salah'
            ], 401);
        }

        // Jika login berhasil, dapatkan informasi pengguna
        $user = auth()->user(); // Mendapatkan pengguna yang sedang login

        // Generate custom JWT token dengan menambahkan name dan email
        $customClaims = [
            'name'  => $user->name,
            
        ];

        // Membuat JWT dengan custom claims
        $token = JWTAuth::claims($customClaims)->fromUser($user);

        //return response dengan token dan data pengguna
        return response()->json([
            'success' => true,
            'user'    => $user,    
            'token'   => $token   
        ], 200);
    }
}
