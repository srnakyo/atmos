<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TasksController extends Controller {

    public function getTasks(Request $request)
    {
        $email = $request->query('user');
        $senha = $request->query('senha');

        if (!$email || !$senha) {
            return response()->json(['erro' => 'Credenciais ausentes'], 403);
        }

        $usuario = DB::table('users')->where('email', $email)->first();

        if (!$usuario || !Hash::check($senha, $usuario->password)) {
            return response()->json(['erro' => 'Usuário ou senha inválidos'], 403);
        }

        $limite = now()->subMinutes(30);

        DB::table('tasks_atmos')
        ->where('user_id', $usuario->id)
        ->where('created_at', '<', $limite)
        ->delete();

        $tasks = DB::table('tasks_atmos')
        ->where('user_id', $usuario->id)
        ->where('created_at', '>=', $limite)
        ->orderByDesc('created_at')
        ->get();

        $idsParaExcluir = $tasks->pluck('id');

        $dados = $tasks->map(function ($item) {
            return [
                'link' => $item->link,
                'sku' => $item->sku,
                'tamanho' => $item->tamanho,
                'x_client_token' => $item->x_client_token,
                'access_token' => $item->access_token,
                'email_nike' => $item->email_nike,
                'mail_sender' => $item->mail_sender,
                'modo' => $item->modo,
                'cartao' => [
                    'nome_titular' => $item->nome_titular,
                    'bandeira' => $item->bandeira,
                    'numero' => $item->numero_cartao,
                    'validade' => $item->validade_cartao,
                    'cvv' => $item->cvv_cartao,
                    'parcelas' => $item->parcelas
                ]
            ];
        });

        if ($idsParaExcluir->isNotEmpty()) {
            DB::table('tasks_atmos')->whereIn('id', $idsParaExcluir)->delete();
        }

        return response()->json($dados);
    }
}
