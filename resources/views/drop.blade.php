@extends('layouts.main')

@section('title', 'Drop')

@push('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush


@section('content')

<div class="max-w-4xl mx-auto bg-card text-white p-8 rounded-xl shadow-lg">
    <h1 class="text-2xl font-bold mb-6">Drop</h1>

    <form id="form-enviar-compra">
        <div id="step-1" class="space-y-6">
            <div>
                <label class="block mb-1">Link do Produto</label>
                <input type="text" id="input-link" name="link" class="w-full p-2 rounded bg-bg text-white border border-gray-600" placeholder="https://www.nike.com.br/..." required>
                <button type="button" id="btn-buscar-produto" class="mt-2 btn btn-primary">Buscar Produto</button>
            </div>

            <div id="div-tamanhos" class="hidden">
                <div class="flex flex-col md:flex-row items-start gap-6">
                    <div class="w-full md:w-1/3 flex flex-col items-center md:items-start text-center md:text-left">
                        <div id="produto-nome" class="text-lg font-bold mb-2"></div>
                        <img id="produto-foto" src="" alt="Imagem Produto" class="w-32 h-32 object-cover bg-white rounded shadow" />
                    </div>

                    <div class="w-full md:w-2/3">
                        <label class="block mb-1">Selecione um Tamanho</label>
                        <div id="grade-tamanhos" class="grid grid-cols-4 sm:grid-cols-6 gap-2"></div>
                    </div>
                </div>
            </div>

            <div>
                <label class="block mb-1">API Session</label>
                <div class="mb-3 text-sm">Deslogue e logue na Nike, acesse: <a class="link" href="https://www.nike.com.br/api/auth/session" target="_blank">https://www.nike.com.br/api/auth/session</a> copie tudo e coloque embaixo. (É importante deslogar e logar e preencher apenas 15 minutos antes do lançamento)</div>
                <textarea name="api_session" id="api-session" rows="8" class="w-full p-2 rounded bg-bg text-white border border-gray-600" required></textarea>
            </div>


            <button type="button" onclick="nextStep()" class="btn btn-primary w-full">Próximo</button>
        </div>

        <div id="step-2" class="space-y-4 hidden">

         <div>
            <label class="block mb-1">Nome do Titular</label>
            <input type="text" id="nome-titular" class="w-full p-2 rounded bg-bg text-white border border-gray-600" required>
        </div>

        <div>
            <label class="block mb-1">Bandeira</label>
            <select id="bandeira-cartao" class="w-full p-2 rounded bg-bg text-white border border-gray-600" required>
                <option value="">Selecione</option>
                @foreach ($bandeiras as $bandeira)
                <option value="{{ $bandeira->name }}">{{ $bandeira->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block mb-1">Número do Cartão</label>
            <input type="text" id="numero-cartao" class="w-full p-2 rounded bg-bg text-white border border-gray-600" placeholder="0000 0000 0000 0000" required>
        </div>

        <div>
            <label class="block mb-1">Expiração</label>
            <input type="text" id="expiracao-cartao" class="w-full p-2 rounded bg-bg text-white border border-gray-600" placeholder="00/0000" required>
        </div>

        <div>
            <label class="block mb-1">CVV</label>
            <input type="text" id="cvv-cartao" class="w-full p-2 rounded bg-bg text-white border border-gray-600" placeholder="000" required>
        </div>

        <div>
            <label class="block mb-1">Parcelas</label>
            <select id="parcelas-cartao" class="w-full p-2 rounded bg-bg text-white border border-gray-600" required>
                <option value="">Selecione</option>
            </select>
        </div>

        <button type="button" onclick="backStep()" class="btn btn-secondary w-full">Voltar</button>
        <button type="submit" class="btn btn-primary w-full">Enviar Compra</button>
    </div>
</form>
</div>



@vite('resources/js/pages/drop.js')

@endsection
