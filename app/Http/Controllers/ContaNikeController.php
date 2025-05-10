<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ContaNikeController extends Controller
{

    public function index()
    {
        $contas = DB::table('nike_accounts')
        ->where('user_id', Auth::id())
        ->get();

        $idsParaAtualizar = [];

        foreach ($contas as $conta) {
            if ($conta->last_sync) {
                $ultimaSync = new \DateTime($conta->last_sync);
                $agora = new \DateTime();
                $diff = $agora->getTimestamp() - $ultimaSync->getTimestamp();

                if ($diff > 60 && $conta->sincronizado) {
                    $conta->sincronizado = 0;
                    $idsParaAtualizar[] = $conta->id;
                }
            }
        }

        if (!empty($idsParaAtualizar)) {
            DB::table('nike_accounts')
            ->whereIn('id', $idsParaAtualizar)
            ->update(['sincronizado' => 0]);
        }

        foreach ($contas as $conta) {
            $conta->cartoes_count = DB::table('conta_nike_credit_card')
            ->where('nike_account_id', $conta->id)
            ->count();
        }

        return view('contas', compact('contas'));
    }



    public function store(Request $request)
    {
        $request->validate([
            'email_nike' => 'required|email',
        ]);

        $email = strtolower($request->email_nike);

        $existe = DB::table('nike_accounts')->where('email_nike', $email)->exists();

        if ($existe) {
            return response()->json(['message' => 'E-mail já cadastrado.'], 409);
        }

        DB::table('nike_accounts')->insert([
            'email_nike' => $email,
            'user_id' => auth()->id(),
            'sincronizado' => false,
            'last_sync' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Conta criada com sucesso.']);
    }



    public function edit($id)
    {
        $conta = DB::table('nike_accounts')
        ->where('id', $id)
        ->where('user_id', Auth::id())
        ->first();

        if (!$conta) {
            abort(403);
        }

        $cartoes = DB::table('credit_cards')
        ->join('conta_nike_credit_card', 'credit_cards.id', '=', 'conta_nike_credit_card.credit_card_id')
        ->where('conta_nike_credit_card.nike_account_id', $id)
        ->where('credit_cards.user_id', Auth::id())
        ->select('credit_cards.*')
        ->get();

        $disponiveis = DB::table('credit_cards')
        ->where('user_id', Auth::id())
        ->whereNotIn('id', function ($query) use ($id) {
            $query->select('credit_card_id')
            ->from('conta_nike_credit_card')
            ->where('nike_account_id', $id);
        })
        ->get();

        return view('contas.editar', compact('conta', 'cartoes', 'disponiveis'));
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'email_nike' => 'required|email',
            'sincronizado' => 'nullable|boolean',
        ]);

        $email = strtolower($request->email_nike);

        $conta = DB::table('nike_accounts')
        ->where('id', $id)
        ->where('user_id', auth()->id())
        ->first();

        if (!$conta) {
            abort(403);
        }

        $existe = DB::table('nike_accounts')
        ->where('email_nike', $email)
        ->where('id', '!=', $id)
        ->exists();

        if ($existe) {
            return response()->json(['message' => 'E-mail já cadastrado.'], 409);
        }

        DB::table('nike_accounts')->where('id', $id)->update([
            'email_nike' => $email,
            'sincronizado' => $request->sincronizado ? 1 : 0,
            'updated_at' => now()
        ]);

        return response()->json(['message' => 'Conta atualizada com sucesso.']);
    }



    public function removerCartao($id, $cartaoId)
    {
        $conta = DB::table('nike_accounts')
        ->where('id', $id)
        ->where('user_id', auth()->id())
        ->first();

        if (!$conta) {
            return response()->json(['message' => 'Acesso não autorizado.'], 403);
        }

        DB::table('conta_nike_credit_card')
        ->where('nike_account_id', $id)
        ->where('credit_card_id', $cartaoId)
        ->delete();

        return response()->json(['message' => 'Cartão removido com sucesso.']);
    }


    public function vincularCartoes(Request $request, $id)
    {
        $conta = DB::table('nike_accounts')
        ->where('id', $id)
        ->where('user_id', Auth::id())
        ->first();

        if (!$conta) {
            abort(403);
        }

        $cartoes = $request->input('cartoes', []);

        if (empty($cartoes)) {
            return back()->withErrors(['cartoes' => 'Selecione ao menos um cartão para vincular.']);
        }

        foreach ($cartoes as $cartaoId) {
            DB::table('conta_nike_credit_card')->updateOrInsert([
                'nike_account_id' => $id,
                'credit_card_id' => $cartaoId
            ]);
        }

        return redirect()->route('contas.edit', $id);
    }

    public function verificar(Request $request)
    {
        $email = strtolower($request->email_nike);
        $id = $request->id;

        $existe = DB::table('nike_accounts')
        ->where('email_nike', $email)
        ->when($id, fn($query) => $query->where('id', '!=', $id))
        ->exists();

        return response()->json(['exists' => $existe]);
    }


    public function destroy($id)
    {
        $conta = DB::table('nike_accounts')
        ->where('id', $id)
        ->where('user_id', auth()->id())
        ->first();

        if (!$conta) {
            abort(403);
        }

        DB::table('nike_accounts')->where('id', $id)->delete();

        return response()->json(['message' => 'Conta removida com sucesso.']);
    }



}
