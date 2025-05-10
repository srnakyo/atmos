<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CompraController extends Controller
{

   public function listarComprasPorSku(Request $request)
{
    $request->validate([
        'sku' => 'required|string',
    ]);

    $skuBuscado = str_replace(' ', '', $request->input('sku'));

    $monitoramentos = DB::table('monitorings')
        ->select('user_id', 'parcelas')
        ->whereRaw("FIND_IN_SET(?, REPLACE(sku, ' ', ''))", [$skuBuscado])
        ->get()
        ->unique('user_id');

    if ($monitoramentos->isEmpty()) {
        return response()->json([]);
    }

    $accountDelay = (int) env('ACCOUNT_DELAY', 90);
    $dados = [];

    foreach ($monitoramentos as $monitoramento) {
        $userId = $monitoramento->user_id;

        $contaNike = DB::table('nike_accounts')
            ->join('users', 'users.id', '=', 'nike_accounts.user_id')
            ->where('nike_accounts.user_id', $userId)
            ->where('nike_accounts.sincronizado', 1)
            ->where('nike_accounts.last_sync', '>=', now()->subMinute())
            ->select(
                'nike_accounts.email_nike',
                'nike_accounts.x_client_token',
                'nike_accounts.accessToken',
                'nike_accounts.accessTokenExpires',
                'users.email as email_atmos'
            )
            ->first();

        if (!$contaNike) {
            continue;
        }

        $cacheKey = 'lock_account_' . $contaNike->email_nike;

        if (Cache::has($cacheKey)) {
            continue;
        }

        $cartoesDisponiveis = DB::table('credit_cards')
            ->where('user_id', $userId)
            ->inRandomOrder()
            ->get([
                'nome_titular',
                'numero',
                'validade_mes',
                'validade_ano',
                'bandeira',
                'cvv'
            ]);

        if ($cartoesDisponiveis->isEmpty()) {
            continue;
        }

        $qtdCartoes = min(2, $cartoesDisponiveis->count());
        $cartoesSelecionados = $cartoesDisponiveis->take($qtdCartoes);

        $dados[] = [
            'email_nike' => $contaNike->email_nike,
            'email_atmos' => $contaNike->email_atmos,
            'x_client_token' => $contaNike->x_client_token,
            'accessToken' => $contaNike->accessToken,
            'accessTokenExpires' => $contaNike->accessTokenExpires,
            'parcelas' => $monitoramento->parcelas,
            'cartoes' => $cartoesSelecionados
        ];

        Cache::put($cacheKey, true, $accountDelay);
    }

    if (empty($dados)) {
        return response()->json([]);
    }

    shuffle($dados);

    return response()->json($dados);
}






}
