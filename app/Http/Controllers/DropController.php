<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
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

        $response = Http::post(env('URL_DROP'), $payload);

        return response($response->body(), $response->status());
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
