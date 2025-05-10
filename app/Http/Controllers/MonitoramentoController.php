<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class MonitoramentoController extends Controller
{

    public function index()
    {
        $monitoramentos = DB::table('monitorings')
        ->where('user_id', auth()->id())
        ->get();

        return view('monitoramento', compact('monitoramentos'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string',
            'foto' => 'nullable|string',
            'tamanhos' => 'nullable|string',
            'sku' => 'nullable|string',
            'link' => 'nullable|string',
            'codigo_estilo' => 'required|string',
            'parcelas' => 'required|integer',
            'tamanhos_disponiveis' => 'nullable|string',
            'all_sku' => 'nullable|string',
            'parcelas_max' => 'nullable|integer',
        ]);

        $link = $request->input('link');

        if ($link) {
            $exists = DB::table('monitorings')
            ->where('user_id', auth()->id())
            ->where('link', $link)
            ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este link já está sendo monitorado.'
                ], 409);
            }
        }

        DB::table('monitorings')->insert([
            'user_id' => auth()->id(),
            'nome' => $request->input('nome'),
            'foto' => $request->input('foto'),
            'tamanho' => $request->input('tamanhos'),
            'sku' => $request->input('sku'),
            'link' => $link,
            'codigo_estilo' => $request->input('codigo_estilo'),
            'parcelas' => $request->input('parcelas'),
            'tamanhos_disponiveis' => $request->input('tamanhos_disponiveis'),
            'all_sku' => $request->input('all_sku'),
            'parcelas_max' => $request->input('parcelas_max'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'tamanhos' => 'nullable|string',
            'parcelas' => 'required|integer',
            'sku' => 'nullable|string',
        ]);

        DB::table('monitorings')
        ->where('id', $id)
        ->where('user_id', auth()->id())
        ->update([
            'tamanho' => $request->input('tamanhos'),
            'parcelas' => $request->input('parcelas'),
            'sku' => $request->input('sku'),
            'updated_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }


    public function show($id)
    {
        $monitoramento = DB::table('monitorings')
        ->where('id', $id)
        ->where('user_id', auth()->id())
        ->first();

        if (!$monitoramento) {
            return response()->json(['error' => 'Monitoramento não encontrado.'], 404);
        }

        $monitoramento->tamanhos_disponiveis = $monitoramento->tamanhos_disponiveis;

        return response()->json($monitoramento);
    }

    public function destroy($id)
    {
        DB::table('monitorings')
        ->where('id', $id)
        ->where('user_id', auth()->id())
        ->delete();

        return response()->json(['success' => true]);
    }

    public function monitorar(Request $request)
    {
        $response = Http::post(env('URL_MONITORAMENTO'), $request->all());

        return response($response->body(), $response->status());
    }

}
