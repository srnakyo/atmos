<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SyncController extends Controller
{
   public function update(Request $request)
   {
    $payload = $request->all();

    if (!isset($payload['email'])) {
        return response()->json(['error' => 'E-mail não informado'], 400);
    }

    $conta = DB::table('nike_accounts')
    ->where('email_nike', $payload['email'])
    ->first();

    if (!$conta) {
        return response()->json(['error' => 'Conta não encontrada'], 404);
    }

    DB::table('nike_accounts')
    ->where('email_nike', $payload['email'])
    ->update([
        'sincronizado' => 1,
        'last_sync' => now(),
        'updated_at' => now(),
        'x_client_token' => $payload['clientToken'] ?? null,
        'accessToken' => $payload['token'] ?? null,
        'accessTokenExpires' => $payload['accessTokenExpires'] ?? null,
    ]);

    return response()->json(['status' => 'ok', 'message' => 'Conta sincronizada com sucesso']);
}


public function getContasStatus()
{

    DB::statement("
        UPDATE nike_accounts 
        SET sincronizado = 0 
        WHERE last_sync IS NOT NULL 
        AND TIMESTAMPDIFF(SECOND, last_sync, NOW()) > 60
        ");


    $contas = DB::table('nike_accounts')
    ->select('id', 'email_nike', 'sincronizado', 'last_sync', 'created_at', 'updated_at')
    ->get();

    return response()->json($contas);
}
}