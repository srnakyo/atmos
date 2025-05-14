<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\ContaNikeController;
use App\Http\Controllers\CartaoController;
use App\Http\Controllers\MonitoramentoController;
use App\Http\Controllers\RegistroComprasController;
use App\Http\Controllers\DropController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\Auth\LoginController;



Route::get('/extensao/download', function () {
    $path = public_path('extensoes/Se Liga - Nike Bot.zip');

    if (!file_exists($path)) {
        abort(404);
    }

    return Response::download($path, 'Se Liga - Nike Bot.zip', [
        'Content-Type' => 'application/zip',
    ]);
})->name('baixar.extensao');


// Login


Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login'); // Página de Login
Route::post('/login', [LoginController::class, 'login']); // Logar

Route::post('/logout', function () {
    Auth::logout();
    return redirect()->route('login');
})->name('logout')->middleware('auth');

// Fim Login

Route::get('/', function () {
    return redirect()->route('contas');
});

Route::middleware('auth')->group(function () {

    // User
    Route::get('/meus-dados', [UserController::class, 'index'])->name('meus-dados');

    Route::get('/usuario/verificar-nickname', [UserController::class, 'verificarNickname']);
    Route::post('/usuario/alterar-senha', [UserController::class, 'alterarSenha']);
    Route::post('/usuario/alterar-perfil', [UserController::class, 'alterarPerfil']);

    // CONTAS NIKE
    Route::get('/contas', [ContaNikeController::class, 'index'])->name('contas');
    Route::post('/contas', [ContaNikeController::class, 'store'])->name('contas.store');

    Route::get('/contas/{id}/editar', [ContaNikeController::class, 'edit'])->name('contas.edit');
    Route::put('/contas/atualizar/{id}', [ContaNikeController::class, 'update'])->name('contas.update');
    Route::delete('/contas/{id}/cartao/{cartaoId}', [ContaNikeController::class, 'removerCartao'])->name('contas.removerCartao');
    Route::delete('/contas/remover/{id}', [ContaNikeController::class, 'destroy'])->name('contas.destroy');

    Route::post('/contas/{id}/vincular-cartoes', [ContaNikeController::class, 'vincularCartoes'])->name('contas.vincularCartoes');

    Route::get('/contas', [ContaNikeController::class, 'index'])->name('contas');
    Route::post('/contas', [ContaNikeController::class, 'store']);
    Route::post('/contas/verificar', [ContaNikeController::class, 'verificar']);

    Route::post('/quick-task', [CompraController::class, 'quickTask']);

    // CARTÕES DE CRÉDITO
    Route::get('/cartoes', [CartaoController::class, 'index'])->name('cartoes');
    Route::post('/cartoes', [CartaoController::class, 'store'])->name('cartoes.store');
    Route::post('/cartoes/multiplos', [CartaoController::class, 'storeMultiplos'])->name('cartoes.multiplos');
    Route::delete('/cartoes/{id}', [CartaoController::class, 'destroy'])->name('cartoes.destroy');
    Route::get('/cartoes/marcas', [CartaoController::class, 'getBrands'])->name('cartoes.brands');
    Route::put('/cartoes/{id}', [CartaoController::class, 'update'])->name('cartoes.update');

    // MONITORAMENTO
    Route::get('/monitoramento', [MonitoramentoController::class, 'index'])->name('monitoramento');
    Route::post('/monitoramento', [MonitoramentoController::class, 'store'])->name('monitoramento.store');
    Route::delete('/monitoramento/{id}', [MonitoramentoController::class, 'destroy'])->name('monitoramento.destroy');
    Route::put('/monitoramento/{id}', [MonitoramentoController::class, 'update'])->name('monitoramento.update');
    Route::get('/monitoramento/{id}', [MonitoramentoController::class, 'show'])->name('monitoramento.show');


    // HISTÓRICO DE COMPRAS
    Route::get('/registro-compras', [RegistroComprasController::class, 'index'])->name('registro_compras');

    // DROP
    Route::get('/drop', [CartaoController::class, 'drop'])->name('drop');

    // DROP_PRO
    Route::get('/drop_pro', [CartaoController::class, 'drop_pro'])->name('drop_pro');


    // Redirects
    Route::post('/produto/monitorar', [MonitoramentoController::class, 'monitorar']); 

    Route::post('/request/drop', [DropController::class, 'drop']); 
    Route::post('/request/drop-pro', [DropController::class, 'dropPro']); 


});
