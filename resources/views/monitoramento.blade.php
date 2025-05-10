@extends('layouts.main')

@section('title', 'Monitoramento')

@push('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')

<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-white">Monitor <span class="text-greenlight"> [{{ count($monitoramentos) }}]</span></h1>
    <a href="javascript:void(0)" class="text-greenlight flex items-center gap-2 btn btn-primary" id="btn-adicionar">
        <span class="text-l leading-none">+ Adicionar</span>
    </a>
</div>

@if ($monitoramentos->isEmpty())
<div class="text-sm text-gray-500">Nenhum monitoramento encontrado.</div>
@else
<div class="grid gap-6">
   @foreach ($monitoramentos as $monitoramento)
   <div class="w-full bg-card text-white p-4 rounded-xl border-l-4 border-greenlight flex items-center justify-between shadow hover:shadow-lg transition mb-4">
    <div class="flex items-center gap-4">
        <img src="{{ $monitoramento->foto }}" alt="{{ $monitoramento->nome }}" class="w-20 h-20 object-cover rounded-md bg-white" />
        <div>
            <div class="text-base font-bold text-white uppercase">{{ $monitoramento->nome }}</div>
            <div class="flex flex-wrap gap-2 mt-2">
                @foreach (explode(';', $monitoramento->tamanho) as $tamanho)
                <span class="bg-green-600 text-white text-xs font-semibold px-2 py-1 rounded">{{ trim($tamanho) }}</span>
                @endforeach
            </div>
            <div class="text-sm text-gray-700 font-semibold mt-2">
                Parcelamento em {{ $monitoramento->parcelas }}x
            </div>
        </div>
    </div>
    <div class="flex gap-3">
        <button class="btn-editar-monitoring" data-id="{{ $monitoramento->id }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 hover:text-green-400" viewBox="0 0 24 24" fill="none" stroke="#ccc" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M15 6l3 3L7 20H4v-3L15 6z"/>
                <path d="M18 3l3 3"/>
            </svg>
        </button>
        <button class="btn-deletar-monitoring" data-id="{{ $monitoramento->id }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 hover:text-red-500" viewBox="0 0 24 24" fill="none" stroke="#f87171" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 7h16"/>
                <path d="M10 11v6"/>
                <path d="M14 11v6"/>
                <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2l1-12"/>
                <path d="M9 7V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v3"/>
            </svg>
        </button>
    </div>
</div>
@endforeach



</div>
@endif

<div id="modal-monitoramento" class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4 hidden">

    <div class="bg-card text-white w-full max-w-2xl rounded-xl p-6 border-l-4 border-greenlight mx-auto">

        <h2 class="text-xl font-bold mb-4">Adicionar monitoramento</h2>

        <div class="flex items-center gap-3 mb-4">
            <input type="text" id="input-link" class="w-full bg-bg text-white p-2 rounded border border-gray-600" placeholder="https://www.nike.com.br/sn..." />
            <button id="btn-buscar" class="btn btn-primary px-4">BUSCAR</button>
        </div>

        <div id="conteudo-produto" class="hidden space-y-4">
            <div class="flex items-center gap-4">
                <img id="produto-foto" src="" class="w-24 h-24 object-cover rounded bg-white">
                <div>
                    <div id="produto-nome" class="text-lg font-semibold text-white"></div>
                    <input type="hidden" name="foto" id="produto-foto-url">
                    <input type="hidden" name="nome" id="produto-nome-hidden">
                    <div class="flex items-center gap-2">
                        <label for="produto-parcelas" class="text-sm text-white">Parcelas:</label>
                        <select id="produto-parcelas" name="parcelas" class="bg-bg text-white text-sm p-2 rounded border border-gray-600">
                            <option value="">Selecione</option>
                        </select>
                    </div>

                    <input type="hidden" name="codigo_estilo" id="produto-codigo_estilo">
                    <input type="hidden" name="tamanhos" id="tamanhosSelecionados">


                </div>
            </div>

            <div id="grade-tamanhos" class="grid grid-cols-6 gap-2"></div>

            <div class="flex justify-between items-center text-sm pt-2">
                <button type="button" id="btn-selecionar-todos" class="btn btn-primary">SELECIONAR TODOS</button>
                <button type="button" id="btn-limpar-selecao" class="btn btn-secondary">LIMPAR SELEÇÃO</button>
                <button type="button" id="btn-salvar-monitoramento" class="btn btn-primary">SALVAR</button>
            </div>
        </div>
    </div>
</div>

<div id="modal-monitoramento-edicao" class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-card text-white w-full max-w-2xl rounded-xl p-6 border-l-4 border-yellow-500 mx-auto">
        <h2 class="text-xl font-bold mb-4">Editar Monitoramento</h2>

        <input type="hidden" id="monitoramento-id">
        <input type="hidden" id="monitoramento-tamanhosSelecionados">

        <div class="flex items-center gap-4 mb-4">
            <img id="monitoramento-foto" src="" class="w-24 h-24 object-cover rounded bg-white">
            <div>
                <div id="monitoramento-nome" class="text-lg font-semibold text-white uppercase"></div>
                <div class="flex items-center gap-2 mt-2">
                    <label for="monitoramento-parcelas" class="text-sm text-white">Parcelas:</label>
                    <select id="monitoramento-parcelas" name="parcelas" class="bg-bg text-white text-sm p-2 rounded border border-gray-600">
                        <option value="">Selecione</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="mb-4">
            <div class="text-sm font-semibold text-white mb-2">Tamanhos</div>
            <div id="monitoramento-grade-tamanhos" class="grid grid-cols-6 gap-2"></div>
        </div>

        <div class="flex justify-end items-center gap-3 pt-2 text-sm">
            <button type="button" class="btn btn-secondary" onclick="document.getElementById('modal-monitoramento-edicao').classList.add('hidden')">
                Cancelar
            </button>
            <button type="button" id="btn-monitoramento-salvar" class="btn btn-primary">
                Salvar Alterações
            </button>
        </div>
    </div>
</div>

@vite('resources/js/pages/monitoramento.js')
@vite('resources/js/pages/remover_monitoramento.js')
@vite('resources/js/pages/editar_monitoramento.js')

@endsection

