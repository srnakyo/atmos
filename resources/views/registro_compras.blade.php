@extends('layouts.main')

@section('title', 'Registro de Compras')

@push('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')

<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-white">Compras <span class="text-greenlight"> [{{ count($compras) }}]</span></h1>
</div>

@if (empty($compras) || count($compras) === 0)
<div class="text-sm text-gray-500">Nenhuma compra registrada.</div>
@else
<div class="grid gap-6">

  @foreach ($compras as $compra)
  <div class="w-full bg-card text-white p-4 rounded-xl border-l-4 border-greenlight flex items-center justify-between shadow hover:shadow-lg transition mb-4">
    <div class="flex items-center gap-4 w-full">
        @if ($compra->img_link)
        <img src="{{ $compra->img_link }}" alt="Imagem" class="w-20 h-20 object-cover rounded-md bg-white" />
        @endif
        <div class="flex flex-col w-full">
            <div class="text-base font-bold text-white uppercase">{{ $compra->product_name }}</div>
            <div class="text-sm text-gray-700 font-semibold">{{ $compra->mail }}</div>

            <div class="flex flex-wrap items-center gap-3 mt-2">
                <div class="text-sm text-gray-700">SKU: <span class="font-semibold text-white">{{ $compra->sku }}</span></div>
                <span class="bg-green-600 text-white text-xs font-semibold px-2 py-1 rounded">Tamanho: {{ $compra->tamanho }}</span>
                <div class="text-xs text-gray-700 ml-auto">Registrado em {{ \Carbon\Carbon::parse($compra->created_at)->format('d/m/Y H:i') }}</div>
            </div>
        </div>
    </div>
</div>
@endforeach

</div>
@endif

@endsection
