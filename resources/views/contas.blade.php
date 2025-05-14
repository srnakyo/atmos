@extends('layouts.main')

@section('title', 'Contas Nike')

@push('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')

<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-white">Contas <span class="text-greenlight"> [{{ count($contas) }}]</span></h1>
    <div class="flex justify-end items-center gap-4">
        <a href="javascript:void(0)" class="text-greenlight flex items-center gap-2 btn btn-primary" id="nova-conta">
            <span class="text-l leading-none">+ Adicionar</span>
        </a>
        <a href="{{ route('baixar.extensao') }}" class="text-greenlight flex items-center gap-2 btn btn-outline">
            <span class="text-l leading-none">⬇ Baixar Extensão</span>
        </a>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
   @foreach ($contas as $conta)
   <div class="bg-card rounded-xl px-6 py-8 shadow-lg transition-all duration-300 text-gray-800 transform hover:scale-[1.02] hover:shadow-2xl border-l-4 border-greenlight flex flex-col relative" data-email="{{ $conta->email_nike }}">

    <div class="absolute top-4 right-4 flex flex-col items-end gap-2">
        <div class="flex gap-2">
            <div class="text-sm font-semibold sync-status {{ $conta->sincronizado ? 'text-green-400' : 'text-red-400' }}">
                <span class="piscar">{{ $conta->sincronizado ? '🟢' : '🔴' }}</span> {{ $conta->sincronizado ? 'Sincronizado' : 'Desconectado' }}
            </div>

            <button class="quick-task text-greenlight hover:text-red-400 cursor-pointer" data-id="{{ $conta->email_nike }}">
               <svg
               xmlns="http://www.w3.org/2000/svg"
               width="24"
               height="24"
               viewBox="0 0 24 24"
               fill="none"
               stroke="currentColor"
               stroke-width="2"
               stroke-linecap="round"
               stroke-linejoin="round"
               >
               <path d="M13 3l0 7l6 0l-8 11l0 -7l-6 0l8 -11" />
           </svg>
       </button>

       <a href="{{ route('contas.edit', $conta->id) }}" class="text-greenlight hover:text-green-400 cursor-pointer">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 hover:brightness-110" viewBox="0 0 24 24" stroke="#78c676" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M15 6l3 3L7 20H4v-3L15 6z" />
            <path d="M18 3l3 3" />
        </svg>
    </a>

    <button class="remover-conta text-greenlight hover:text-red-400 cursor-pointer" data-id="{{ $conta->id }}">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 hover:brightness-110" viewBox="0 0 24 24" stroke="#78c676" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M4 7h16"/>
            <path d="M10 11v6"/>
            <path d="M14 11v6"/>
            <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2l1-12"/>
            <path d="M9 7V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v3"/>
        </svg>
    </button>
</div>
</div>

<div class="mb-4">
    <div class="text-sm text-gray-900">E-mail</div>
    <div class="text-lg font-semibold truncate">{{ $conta->email_nike }}</div>
    <div class="text-sm font-semibold truncate {{ $conta->cartoes_count > 0 ? 'text-greenlight' : 'text-red-400' }}">
        Cartões vinculados: {{ $conta->cartoes_count }}
    </div>
</div>

<div class="text-xs text-gray-400 mt-auto sync-time text-gray-800">
    Última sincronização: {{ $conta->last_sync ? \Carbon\Carbon::parse($conta->last_sync)->format('d/m/Y H:i') : '—' }}
</div>
</div>
@endforeach

</div>


<div id="modal-quick-task" class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center hidden">
    <div class="bg-card text-white w-full max-w-md rounded-xl p-6 space-y-6 relative border-l-4 border-greenlight">
        <h2 class="text-xl font-bold text-gray-800">Executar Quick Task</h2>
        <form id="form-quick-task" class="space-y-4">
            @csrf
            <input type="hidden" name="email_nike" id="quick-email-nike">
            <div>
                <label for="quick-sku" class="text-sm text-gray-800 block mb-1">SKU</label>
                <input type="text" name="sku" id="quick-sku" class="w-full bg-bg text-gray-800 p-2 rounded border border-gray-300 focus:outline-none focus:ring-2 focus:ring-greenlight" placeholder="Digite o SKU">
                <p id="quick-sku-error" class="text-red-500 text-sm mt-1 hidden">Campo obrigatório.</p>
            </div>
            <div class="flex justify-end gap-4 pt-2">
                <button type="button" id="cancelar-quick-task" class="btn btn-secondary">Cancelar</button>
                <button type="submit" class="btn btn-primary">Enviar</button>
            </div>
        </form>
    </div>
</div>



<div id="modal-nova-conta" class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center hidden">
    <div class="bg-card text-white w-full max-w-4xl max-h-[90vh] overflow-y-auto rounded-xl p-8 space-y-6 relative border-l-4 border-greenlight">

        <h2 class="text-2xl font-bold">Adicionar Conta</h2>

        <form id="form-nova-conta" class="space-y-10">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4 border-b border-gray-700 pb-6">
                <div class="col-span-2">
                    <label class="text-sm text-gray-800 block mb-1">E-mail</label>
                    <input type="email" name="email_nike" id="email_nike" required class="w-full bg-bg text-gray-800 p-2 rounded" placeholder="email@exemplo.com">
                </div>
            </div>
        </form>

        <div class="flex justify-end items-center pt-2 gap-4">
            <button type="button" id="cancelar-modal" class="btn btn-secondary">
                Cancelar
            </button>
            <button type="submit" form="form-nova-conta" class="btn-primary btn cursor-pointer">
                Salvar
            </button>
        </div>

    </div>
</div>
<input type="hidden" id="user-id" value="{{ auth()->id() }}">


@vite('resources/js/pages/nova_conta.js')
@vite('resources/js/pages/remover_conta.js')
@vite('resources/js/pages/quick_task.js')
@vite('resources/js/pages/sync_contas.js')


@endsection
