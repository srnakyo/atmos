<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
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

public function getContasStatus($id)
{
    DB::statement("
        UPDATE nike_accounts 
        SET sincronizado = 0 
        WHERE last_sync IS NOT NULL 
        AND TIMESTAMPDIFF(SECOND, last_sync, NOW()) > 60
        AND user_id = ?
    ", [$id]);

    $contas = DB::table('nike_accounts')
        ->select('id', 'email_nike', DB::raw('sincronizado + 0 as sincronizado'), 'last_sync', 'created_at', 'updated_at')
        ->where('user_id', $id)
        ->get();

    return response()->json(
        $contas->map(function ($conta) {
            return [
                'id' => (int) $conta->id,
                'email_nike' => $conta->email_nike,
                'sincronizado' => (int) $conta->sincronizado,
                'last_sync' => $conta->last_sync,
                'created_at' => $conta->created_at,
                'updated_at' => $conta->updated_at
            ];
        })
    );
}




}