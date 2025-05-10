@extends('layouts.base')

@section('title', 'Login')

@push('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

<style>
 .page-bg {
    background-image: url('assets/media/bg-login.png');
}
</style>
<div class="flex items-center justify-center grow bg-center bg-no-repeat page-bg">
 <div class="card max-w-[370px] w-full">
    <form action="#" class="card-body flex flex-col gap-5 p-10" id="sign_in_form" method="get">
       @csrf
       <div class="text-center mb-2.5">
          <img src="{{ asset('assets/media/app/mini-logo.png') }}" alt="Logo" class="mx-auto mb-2">
          <h3 class="text-lg font-medium text-gray-900 leading-none mb-2.5">
            Login
        </h3>
    </div>

    <div class="flex flex-col gap-1">
      <label class="form-label font-normal text-gray-900">
         Email
     </label>
     <input class="input" placeholder="contato@atmos.com" name="email" id="email" type="text" value=""/>
 </div>
 <div class="flex flex-col gap-1">
  <div class="flex items-center justify-between gap-1">
     <label class="form-label font-normal text-gray-900">
        Senha
    </label>
</div>
<div class="input" data-toggle-password="true">
 <input name="user_password" placeholder="**********" type="password" name="password" id="password" value=""/>
 <button class="btn btn-icon" data-toggle-password-trigger="true" type="button">
    <i class="ki-filled ki-eye text-gray-500 toggle-password-active:hidden">
    </i>
    <i class="ki-filled ki-eye-slash text-gray-500 hidden toggle-password-active:block">
    </i>
</button>
</div>
</div>
<button class="btn btn-primary flex justify-center grow">
  Entrar
</button>
</form>
</div>
</div>

@vite('resources/js/pages/login.js')