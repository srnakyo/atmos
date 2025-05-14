@extends('layouts.main')

@section('title', 'Meus Dados')

@push('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')

<div class="flex justify-between items-center mb-6">
  <h1 class="text-2xl font-bold text-white">Meus Dados</h1>
</div>
<div class="grid gap-6">

  <div class="bg-card text-white p-6 rounded-xl border-l-4 border-greenlight shadow">
    <h2 class="text-lg font-semibold mb-4">Alterar Nickname e Webhook</h2>
    <form id="form-nickname" class="space-y-4">
      @csrf
      <div>
        <label for="nickname" class="block text-sm text-white mb-1">Nickname</label>
        <input type="text" id="nickname" name="nickname" value="{{ $user->nickname }}" class="w-full bg-bg border border-gray-600 rounded p-2 text-white">
        <div id="nickname-feedback" class="text-red-500 text-sm mt-1"></div>
      </div>
      <div>
        <label for="webhook" class="block text-sm text-white mb-1">Webhook</label>
        <input type="text" id="webhook" name="webhook" value="{{ $user->webhook }}" class="w-full bg-bg border border-gray-600 rounded p-2 text-white">
        <div id="webhook-feedback" class="text-red-500 text-sm mt-1"></div>
      </div>
      <button type="submit" class="btn btn-primary">Salvar Dados</button>
    </form>
  </div>

  <div class="bg-card text-white p-6 rounded-xl border-l-4 border-yellow-500 shadow">
    <h2 class="text-lg font-semibold mb-4">Alterar Senha</h2>
    <form id="form-senha" class="space-y-4">
      @csrf
      <div>
        <label for="senha_atual" class="block text-sm text-white mb-1">Senha Atual</label>
        <input type="password" id="senha_atual" name="senha_atual" class="w-full bg-bg border border-gray-600 rounded p-2 text-white">
        <div id="erro-senha-atual" class="text-red-500 text-sm mt-1"></div>
      </div>
      <div>
        <label for="nova_senha" class="block text-sm text-white mb-1">Nova Senha</label>
        <input type="password" id="nova_senha" name="nova_senha" class="w-full bg-bg border border-gray-600 rounded p-2 text-white">
        <div id="erro-nova-senha" class="text-red-500 text-sm mt-1"></div>
      </div>
      <div>
        <label for="nova_senha_confirmation" class="block text-sm text-white mb-1">Confirmar Nova Senha</label>
        <input type="password" id="nova_senha_confirmation" name="nova_senha_confirmation" class="w-full bg-bg border border-gray-600 rounded p-2 text-white">
        <div id="erro-confirmacao" class="text-red-500 text-sm mt-1"></div>
      </div>
      <button type="submit" class="btn btn-primary">Alterar Senha</button>
    </form>
  </div>

</div>

@vite('resources/js/pages/meus-dados.js')

@endsection
