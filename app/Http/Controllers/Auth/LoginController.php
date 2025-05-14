<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            return response()->json([
                'status' => 'success',
                'redirect' => '/contas'
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Credenciais inválidas.'
        ], 401);
    }
    public function criarConta(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'senha' => 'required|string|min:4'
        ]);

        $email = $request->input('email');
        $senha = Hash::make($request->input('senha'));

        $user = DB::table('users')->where('email', $email)->first();

        if ($user) {
            DB::table('users')->where('id', $user->id)->update([
                'password' => $senha,
                'updated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'atualizado' => true
            ]);
        }

        $nickname = explode('@', $email)[0];

        DB::table('users')->insert([
            'email' => $email,
            'password' => $senha,
            'nickname' => $nickname,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'criado' => true
        ]);
    }

}
