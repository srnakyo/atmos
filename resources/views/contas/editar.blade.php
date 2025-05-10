@extends('layouts.main')

@section('title', 'Editar Conta')

@push('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')

<div class="flex justify-between items-center mb-6">

    <a href="{{ route('contas') }}" class="btn btn-primary">&larr; Voltar</a>
</div>

<h1 class="text-2xl font-bold text-white mb-6">Editar Conta Nike</h1>

<form id="form-editar-conta" class="space-y-6 mb-10">
    @csrf

    <div>
        <label class="text-sm text-gray-800 block mb-1">E-mail</label>
        <input type="email" name="email_nike" id="email_nike" data-id="{{ $conta->id }}" value="{{ $conta->email_nike }}" class="w-full bg-bg text-white p-2 rounded border border-gray-600">
    </div>

    <button type="submit" class="btn btn-primary">Salvar</button>
</form>


<div class="flex justify-between items-center mb-4">
    <h2 class="text-xl font-semibold text-white">Cartões Vinculados</h2>
    <button id="btn-vincular" type="button" class="btn btn-primary">+ VINCULAR</button>
</div>

@if ($cartoes->isEmpty())
<div class="text-gray-700 text-sm">Esta conta não possui cartões vinculados.</div>
@else
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @foreach ($cartoes as $cartao)

    <div class="bg-card rounded-xl px-6 py-8 shadow-lg transition-all duration-300 transform hover:scale-[1.02] hover:shadow-2xl border-l-4 border-greenlight flex flex-col justify-between">

  <button type="button" class="remover-cartao text-greenlight hover:text-red-400" style="position: absolute; top: 1rem; right: 1rem;" data-conta="{{ $conta->id }}" data-cartao="{{ $cartao->id }}">
    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 hover:brightness-110" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M4 7h16M10 11v6M14 11v6M5 7l1 12a2 2 0 002 2h8a2 2 0 002-2l1-12M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3" />
    </svg>
</button>


<div class="flex justify-between items-start mb-6">
    <div class="text-lg font-bold text-blue-400 uppercase">{{ $cartao->bandeira ?? 'Cartão' }}</div>
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
@endif

<!-- Modal de Vinculação -->
<div id="modal-vincular" class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center hidden">
    <div class="bg-card text-white w-full max-w-2xl rounded-xl p-6 relative border-l-4 border-greenlight">
        <h2 class="text-xl font-semibold mb-4 text-gray-800">Vincular Cartões</h2>

        <form method="POST" action="{{ route('contas.vincularCartoes', $conta->id) }}" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-h-64 overflow-y-auto pr-1">
                @foreach ($disponiveis as $disponivel)
                <label class="flex items-center gap-2 text-sm bg-bg p-3 rounded border border-gray-600 cursor-pointer hover:bg-[#222126]">
                    <input type="checkbox" name="cartoes[]" value="{{ $disponivel->id }}" class="form-checkbox text-greenlight focus:ring-greenlight">
                    <span>{{ $disponivel->bandeira }} — {{ chunk_split($disponivel->numero, 4, ' ') }}</span>
                </label>
                @endforeach
            </div>

            <div class="flex justify-end gap-4 pt-2"> 
               <button id="fechar-modal-vincular" type="button" class="btn btn-secondary">Cancelar</button>
               <button type="submit" class="btn-primary btn">Vincular Selecionados</button>
           </div>
       </form>
   </div>
</div>

@vite('resources/js/pages/editar_conta.js')

@endsection
