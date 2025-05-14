<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DropController extends Controller {

    public function drop(Request $request)
    {
        $user = Auth::user();
        $cacheKey = 'drop_send_' . $user->id;
        $sendAmount = (int) env('DROP_SEND_AMOUNT', 4);
        $sendDelay = (int) env('DROP_SEND_DELAY', 300);
        $sendCount = Cache::get($cacheKey, 0);

        if ($sendCount >= $sendAmount) {
            return response()->json([
                'message' => 'Limite de envios atingido. Aguarde para tentar novamente.'
            ], 429);
        }

        Cache::put($cacheKey, $sendCount + 1, $sendDelay);

        $payload = array_merge($request->all(), [
            'mail_sender' => $user->email,
        ]);

        DB::table('tasks_atmos')->insert([
            'user_id' => $user->id,
            'link' => $payload['link'],
            'sku' => $payload['sku'],
            'tamanho' => $payload['tamanho'],
            'email_nike' => $payload['email_nike'],
            'x_client_token' => $payload['x_client_token'],
            'access_token' => $payload['access_token'],
            'nome_titular' => $payload['cartao']['nome_titular'],
            'bandeira' => $payload['cartao']['bandeira'],
            'numero_cartao' => $payload['cartao']['numero'],
            'validade_cartao' => $payload['cartao']['validade'],
            'cvv_cartao' => $payload['cartao']['cvv'],
            'parcelas' => $payload['cartao']['parcelas'],
            'mail_sender' => $payload['mail_sender'],
            'modo' => 'drop',
            'created_at' => now()
        ]);

        return response()->json([
            'message' => 'Drop registrado com sucesso.'
        ], 200);
    }

    public function dropPro(Request $request)
    {
        $user = Auth::user();
        $cacheKey = 'drop_pro_send_' . $user->id;
        $sendAmount = (int) env('DROP_PRO_SEND_AMOUNT', 4);
        $sendDelay = (int) env('DROP_PRO_SEND_DELAY', 300);
        $sendCount = Cache::get($cacheKey, 0);

        if ($sendCount >= $sendAmount) {
            return response()->json([
                'message' => 'Limite de envios atingido. Aguarde para tentar novamente.'
            ], 429);
        }

        Cache::put($cacheKey, $sendCount + 1, $sendDelay);

        $payload = array_merge($request->all(), [
            'mail_sender' => $user->email,
        ]);

        $response = Http::post(env('URL_DROP_PRO'), $payload);

        return response($response->body(), $response->status());
    }

}
