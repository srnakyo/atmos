<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function index()
    {
        $user = auth()->user();
        return view('meus-dados', ['user' => $user]);
    }

    
    public function alterarSenha(Request $request)
    {
        $request->validate([
            'senha_atual' => 'required|string',
            'nova_senha' => 'required|string|min:8|confirmed',
        ]);

        $usuario = auth()->user();

        $senhaCorreta = Hash::check($request->input('senha_atual'), DB::table('users')->where('id', $usuario->id)->value('password'));

        if (!$senhaCorreta) {
            return response()->json(['erro' => 'Senha atual incorreta'], 403);
        }

        DB::table('users')->where('id', $usuario->id)->update([
            'password' => Hash::make($request->input('nova_senha')),
            'updated_at' => now()
        ]);

        return response()->json(['mensagem' => 'Senha alterada com sucesso']);
    }

    public function alterarPerfil(Request $request)
    {
        $request->validate([
            'nickname' => [
                'required',
                'string',
                'max:255',
                'unique:users,nickname,' . auth()->id(),
                'regex:/^[a-zA-Z0-9_-]+$/'
            ],
            'webhook' => 'nullable|string|max:1000'
        ]);

        DB::table('users')->where('id', auth()->id())->update([
            'nickname' => $request->input('nickname'),
            'webhook' => $request->input('webhook'),
            'updated_at' => now()
        ]);

        return response()->json(['mensagem' => 'Perfil atualizado com sucesso']);
    }



    public function verificarNickname(Request $request)
    {
        $request->validate([
            'nickname' => 'required|string'
        ]);

        $existe = DB::table('users')
        ->where('nickname', $request->input('nickname'))
        ->where('id', '<>', auth()->id())
        ->exists();

        return response()->json(['disponivel' => !$existe]);
    }
}
