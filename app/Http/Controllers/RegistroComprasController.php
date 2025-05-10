<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegistroComprasController extends Controller
{

   public function index()
   {
    $compras = DB::table('buyings_register')->orderByDesc('id')->get();
    return view('registro_compras', compact('compras'));
}

public function storeApi(Request $request)
{
    $request->validate([
        'email' => 'required|string|email',
        'email_nike' => 'required|string|email',
        'link_imagem' => 'required|string',
        'sku' => 'required|string',
        'tamanho' => 'required|string',
        'nome_produto' => 'required|string'
    ]);

    $conta_nike = DB::table('nike_accounts')
    ->where('email_nike', $request->input('email_nike'))
    ->first();

    if (!$conta_nike) {
        return response()->json([
            'success' => false,
            'erro' => 'E-mail Nike não encontrado na tabela nike_accounts'
        ], 404);
    }

    $conta = DB::table('user')
    ->where('email', $request->input('email'))
    ->first();

    if (!$conta) {
        return response()->json([
            'success' => false,
            'erro' => 'E-mail não encontrado na base de dados.'
        ], 404);
    }

    DB::table('buyings_register')->insert([
        'user_id' => $conta->id,
        'mail' => $request->input('email'),
        'mail_nike' => $request->input('email_nike'),
        'img_link' => $request->input('link_imagem'),
        'sku' => $request->input('sku'),
        'tamanho' => $request->input('tamanho'),
        'product_name' => $request->input('nome_produto'),
        'created_at' => now(),
        'updated_at' => now()
    ]);

    return response()->json(['success' => true], 200);
}
}
