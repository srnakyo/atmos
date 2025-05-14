<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

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

public function listarComprasPorSkuRestock(Request $request)
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

        $cartao = DB::table('credit_cards')
        ->where('user_id', $userId)
        ->inRandomOrder()
        ->first([
            'nome_titular',
            'numero',
            'validade_mes',
            'validade_ano',
            'bandeira',
            'cvv'
        ]);

        if (!$cartao) {
            continue;
        }

        DB::table('tasks_atmos')->insert([
            'user_id' => $userId,
            'mail_sender' => $contaNike->email_atmos,
            'link' => '',
            'sku' => $skuBuscado,
            'tamanho' => '',
            'email_nike' => $contaNike->email_nike,
            'x_client_token' => $contaNike->x_client_token,
            'access_token' => $contaNike->accessToken,
            'nome_titular' => $cartao->nome_titular,
            'bandeira' => $cartao->bandeira,
            'numero_cartao' => $cartao->numero,
            'validade_cartao' => str_pad($cartao->validade_mes, 2, '0', STR_PAD_LEFT) . '/' . $cartao->validade_ano,
            'cvv_cartao' => $cartao->cvv,
            'parcelas' => $monitoramento->parcelas,
            'modo' => 'restock',
            'created_at' => now()
        ]);

        Cache::put($cacheKey, true, $accountDelay);
    }

    return response()->json(['status' => 'ok']);
}

public function distribuirCookie(Request $request, $quantidade = 1)
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

    $limite = $email === 'antonielcormanich@gmail.com' ? 20 : 3;
    $quantidade = min((int) $quantidade, $limite);

    $cookies = DB::table('cookies')
    ->where('usado', 0)
    ->orderByDesc('created_at')
    ->limit($quantidade)
    ->get();

    if ($cookies->isEmpty()) {
        return response()->json(['erro' => 'Nenhum cookie disponível'], 404);
    }

    $ids = $cookies->pluck('id');

    DB::table('cookies')
    ->whereIn('id', $ids)
    ->update([
        'usado' => 1,
        'usado_por' => $email,
        'usado_em' => now()
    ]);

    return response()->json($cookies);
}


public function inserirCookie(Request $request)
{
    $request->validate([
        'abck' => 'required|string'
    ]);

    DB::table('cookies')->insert([
        'abck' => $request->input('abck'),
        'bm_s' => $request->input('bm_s'),
        'bm_so' => $request->input('bm_so'),
        'bm_ss' => $request->input('bm_ss'),
        'bm_sz' => $request->input('bm_sz'),
        'telemetry' => $request->input('telemetry'),
        'useragent_ck' => $request->input('useragent-ck'),
        'useragent_tl' => $request->input('useragent-tl'),
        'inserido_em' => $request->input('inserido_em'),
        'usado' => 0,
        'created_at' => now()
    ]);

    return response()->json(['success' => true]);
}

public function listarCookiesUsados(Request $request)
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

    $cookies = DB::table('cookies')
    ->where('usado', 1)
    ->where('usado_por', $email)
    ->orderByDesc('usado_em')
    ->get();

    return response()->json($cookies);
}


public function quickTask(Request $request)
{
    $request->validate([
        'sku' => 'required|string',
        'email_nike' => 'required|email',
    ]);

    $userId = Auth::id();
    $skuBuscado = str_replace(' ', '', $request->input('sku'));
    $emailNike = $request->input('email_nike');
    $accountDelay = (int) env('ACCOUNT_DELAY', 90);

    $contaNike = DB::table('nike_accounts')
    ->join('users', 'users.id', '=', 'nike_accounts.user_id')
    ->where('nike_accounts.user_id', $userId)
    ->where('nike_accounts.email_nike', $emailNike)
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
        return response()->json(['erro' => 'Conta não sincronizada ou não encontrada'], 404);
    }

    $cartao = DB::table('credit_cards')
    ->where('user_id', $userId)
    ->inRandomOrder()
    ->first([
        'nome_titular',
        'numero',
        'validade_mes',
        'validade_ano',
        'bandeira',
        'cvv'
    ]);

    if (!$cartao) {
        return response()->json(['erro' => 'Nenhum cartão encontrado'], 404);
    }

    $cacheKey = 'lock_account_' . $contaNike->email_nike;

    if (Cache::has($cacheKey)) {
        return response()->json(['erro' => 'Conta em uso'], 429);
    }

    DB::table('tasks_atmos')->insert([
        'user_id' => $userId,
        'mail_sender' => $contaNike->email_atmos,
        'link' => '',
        'sku' => $skuBuscado,
        'tamanho' => '',
        'email_nike' => $contaNike->email_nike,
        'x_client_token' => $contaNike->x_client_token,
        'access_token' => $contaNike->accessToken,
        'nome_titular' => $cartao->nome_titular,
        'bandeira' => $cartao->bandeira,
        'numero_cartao' => $cartao->numero,
        'validade_cartao' => str_pad($cartao->validade_mes, 2, '0', STR_PAD_LEFT) . '/' . $cartao->validade_ano,
        'cvv_cartao' => $cartao->cvv,
        'parcelas' => 1,
        'modo' => 'restock',
        'created_at' => now()
    ]);

    Cache::put($cacheKey, true, $accountDelay);

    return response()->json(['status' => 'ok']);
}


}
