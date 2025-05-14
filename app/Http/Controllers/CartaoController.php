<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CartaoController extends Controller
{
	public function index()
	{
		$nikeAccounts = DB::table('nike_accounts')->where('user_id',  Auth::user()->id)->get();
		$cartoes = DB::table('credit_cards')->where('user_id',  Auth::user()->id)->get();
		return view('cartoes', compact('nikeAccounts', 'cartoes'));
	}

	public function store(Request $request)
	{
		$request->validate([
			'nome_titular' => 'required|string',
			'numero' => 'required|string',
			'validade_mes' => 'required|string',
			'validade_ano' => 'required|string',
			'cvv' => 'required|string',
			'bandeira' => 'nullable|string',
			'nike_account_id' => 'nullable|exists:nike_accounts,id',
		]);

		DB::table('credit_cards')->insert([
			'user_id' => Auth::id(),
			'nike_account_id' => $request->nike_account_id,
			'nome_titular' => $request->nome_titular,
			'numero' => $request->numero,
			'validade_mes' => $request->validade_mes,
			'validade_ano' => $request->validade_ano,
			'cvv' => $request->cvv,
			'bandeira' => $request->bandeira,
			'created_at' => now(),
			'updated_at' => now()
		]);

		return redirect()->route('cartoes');
	}

	public function storeMultiplos(Request $request)
	{
		$cartoes = [];

		foreach ($request->numero as $index => $numero) {
			$cartoes[] = [
				'user_id' => Auth::id(),
				'nome_titular' => $request->nome_titular[$index],
				'numero' => $numero,
				'validade_mes' => $request->validade_mes[$index],
				'validade_ano' => $request->validade_ano[$index],
				'cvv' => $request->cvv[$index],
				'bandeira' => $request->bandeira[$index],
				'created_at' => now(),
				'updated_at' => now(),
			];
		}

		DB::table('credit_cards')->insert($cartoes);

		return response()->json(['success' => true]);
	}

	public function getBrands()
	{
		$brands = DB::table('card_brands')->orderBy('name')->get();
		return response()->json($brands);
	}

	public function destroy($id)
	{
		$cartao = DB::table('credit_cards')
		->where('id', $id)
		->where('user_id', Auth::id())
		->first();

		if (!$cartao) {
			return response()->json(['message' => 'Acesso não autorizado.'], 403);
		}

		DB::table('credit_cards')->where('id', $id)->delete();

		return response()->json(['message' => 'Cartão removido com sucesso.']);
	}

	public function update(Request $request, $id)
	{
		$request->validate([
			'nome_titular' => 'required|string|max:255',
			'numero' => 'required|string|max:255',
			'bandeira' => 'nullable|string|max:50',
			'validade_mes' => 'required|string|size:2',
			'validade_ano' => 'required|string|size:4',
			'cvv' => 'required|string|max:5',
		]);

		$cartao = DB::table('credit_cards')
		->where('id', $id)
		->where('user_id', Auth::id())
		->first();

		if (!$cartao) {
			return response()->json(['message' => 'Acesso não autorizado.'], 403);
		}

		DB::table('credit_cards')
		->where('id', $id)
		->update([
			'nome_titular' => $request->nome_titular,
			'numero' => $request->numero,
			'bandeira' => $request->bandeira,
			'validade_mes' => $request->validade_mes,
			'validade_ano' => $request->validade_ano,
			'cvv' => $request->cvv,
			'updated_at' => now()
		]);

		return response()->json(['message' => 'Cartão atualizado com sucesso.']);
	}

	public function drop()
	{
		$bandeiras = DB::table('card_brands')->orderBy('name')->get();
		return view('drop', compact('bandeiras'));
	}

	public function drop_pro()
	{
		$bandeiras = DB::table('card_brands')->orderBy('name')->get();
		return view('drop_pro', compact('bandeiras'));
	}


}
