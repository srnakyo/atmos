@extends('layouts.main')

@section('title', 'Meus Cartões')

@push('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')

<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-white">Cartões <span class="text-greenlight"> [{{ count($cartoes) }}]</span></h1>
    <a href="javascript:void(0)" class="text-greenlight flex items-center gap-2 btn btn-primary" id="add-card-btn">
        <span class="text-l leading-none">+ Adicionar</span>
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    @foreach ($cartoes as $cartao)
    <div class="bg-card rounded-xl px-6 py-8 shadow-lg transition-all duration-300 transform hover:scale-[1.02] hover:shadow-2xl border-l-4 border-greenlight flex flex-col justify-between">

        <div class="flex justify-between items-start mb-6">
            <div class="text-lg font-bold text-blue-400 uppercase">{{ $cartao->bandeira ?? 'Cartão' }}</div>

            <div class="flex gap-3">
                <a href="javascript:void(0)" class="text-greenlight hover:text-green-400 btn-editar-cartao" data-id="{{ $cartao->id }}" data-nome="{{ $cartao->nome_titular }}" data-numero="{{ $cartao->numero }}" data-bandeira="{{ $cartao->bandeira }}" data-mes="{{ $cartao->validade_mes }}" data-ano="{{ $cartao->validade_ano }}" data-cvv="{{ $cartao->cvv }}">
                   <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 hover:brightness-110" viewBox="0 0 24 24" fill="none" stroke="#78c676" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <path d="M16 3l5 5L8 21H3v-5L16 3z" />
                  </svg>
              </a>
              <a href="javascript:void(0)" class="text-greenlight hover:text-green-400" onclick="deletarCartao({{ $cartao->id }})">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 hover:brightness-110" viewBox="0 0 24 24" fill="none" stroke="#78c676" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 7h16M10 11v6M14 11v6M5 7l1 12a2 2 0 002 2h8a2 2 0 002-2l1-12M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3" />
                </svg>
            </a>
        </div>
    </div>

    <div class="text-xl font-mono mb-6 text-gray-800 w-full text-left">
        {{ implode(' ', str_split($cartao->numero, 4)) }}
    </div>

    <div class="flex justify-between items-start text-sm text-gray-300 gap-4">
        <div class="flex-1 min-w-0">
            <div class="text-white text-xs">Nome</div>
            <div class="truncate text-gray-800">{{ $cartao->nome_titular }}</div>
        </div>
        <div class="text-left">
            <div class="text-white text-xs">Validade</div>
            <div class="text-gray-800">{{ $cartao->validade_mes }}/{{ $cartao->validade_ano }}</div>
        </div>
        <div class="text-left">
            <div class="text-white text-xs">Cvv</div>
            <div class="text-gray-800">{{ $cartao->cvv }}</div>
        </div>
    </div>


</div>
@endforeach

</div>

<div id="modal-cartoes" class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center hidden">
    <div class="bg-card text-white w-full max-w-4xl max-h-[90vh] overflow-y-auto rounded-xl p-8 space-y-6 relative border-l-4 border-greenlight">

        <h2 class="text-2xl font-bold">Adicionar Cartões</h2>

        <form id="form-cartoes" class="space-y-10">
            @csrf
            <div class="form-cartao grid grid-cols-1 md:grid-cols-3 gap-x-6 gap-y-4 border-b border-gray-700 pb-6">
                <div class="col-span-3 md:col-span-1">
                    <label class="text-sm text-gray-800 block mb-1">Nome do Titular</label>
                    <input type="text" name="nome_titular[]" class="w-full bg-bg text-white p-2 rounded">
                </div>
                <div class="col-span-3 md:col-span-1">
                    <label class="text-sm text-gray-800 block mb-1">Número</label>
                    <input type="text" name="numero[]" maxlength="16" class="w-full bg-bg text-white p-2 rounded">
                </div>
                <div class="col-span-3 md:col-span-1">
                    <label class="text-sm text-gray-800 block mb-1">Bandeira</label>
                    <select name="bandeira[]" class="brand-select select2 w-full bg-bg text-white p-2 rounded" data-placeholder="Selecione uma bandeira">
                        <option></option>
                    </select>
                </div>
                <div>
                    <label class="text-sm text-gray-800 block mb-1">Mês</label>
                    <select name="validade_mes[]" class="w-full bg-bg text-white p-2 rounded">
                        @for ($i = 1; $i <= 12; $i++)
                        <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}">{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="text-sm text-gray-800 block mb-1">Ano</label>
                    <select name="validade_ano[]" class="w-full bg-bg text-white p-2 rounded">
                        @for ($i = date('Y'); $i <= date('Y') + 10; $i++)
                        <option value="{{ $i }}">{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="text-sm text-gray-800 block mb-1">CVV</label>
                    <input type="text" name="cvv[]" maxlength="3" class="w-20 bg-bg text-white p-2 rounded">
                </div>
            </div>
        </form>

        <div class="flex justify-between items-center pt-2">
            <button type="button" id="add-card-form" class="text-greenlight text-sm font-semibold hover:text-green-800 cursor-pointer focus:outline-none">
                + Adicionar outro
            </button>
            <div class="flex gap-4">
                <button id="fechar-modal" type="button" class="btn btn-secondary">
                    Cancelar
                </button>
                <button id="salvar-cartoes" type="button" class="btn-primary btn cursor-pointer">
                    Salvar todos
                </button>
            </div>
        </div>
    </div>
</div>


<div id="modal-editar-cartao" class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center hidden">
    <div class="bg-card text-white w-full max-w-xl rounded-xl p-8 relative border-l-4 border-greenlight">
        <h2 class="text-2xl font-bold mb-4">Editar Cartão</h2>
        <form id="form-editar-cartao" class="space-y-4">
            @csrf
            <input type="hidden" id="cartao_id">
            <div>
                <label class="text-sm block mb-1">Nome do Titular</label>
                <input type="text" id="nome_titular" class="w-full p-2 bg-bg text-white rounded">
            </div>
            <div>
                <label class="text-sm block mb-1">Número</label>
                <input type="text" id="numero" class="w-full p-2 bg-bg text-white rounded">
            </div>
            <div>
                <label class="text-sm block mb-1">Bandeira</label>
                <select id="bandeira" class="w-full p-2 bg-bg text-white rounded"></select>
            </div>
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="text-sm block mb-1">Mês</label>
                    <select id="validade_mes" class="w-full p-2 bg-bg text-white rounded"></select>
                </div>
                <div>
                    <label class="text-sm block mb-1">Ano</label>
                    <select id="validade_ano" class="w-full p-2 bg-bg text-white rounded"></select>
                </div>
                <div>
                    <label class="text-sm block mb-1">CVV</label>
                    <input type="text" id="cvv" maxlength="5" class="w-full p-2 bg-bg text-white rounded">
                </div>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" id="cancelar-edicao" class="btn btn-secondary">Cancelar</button>
                <button type="submit" class="btn-primary btn">Salvar</button>
            </div>
        </form>
    </div>
</div>



@vite('resources/js/pages/cartoes.js')
@vite('resources/js/pages/editar_cartao.js')

@endsection
