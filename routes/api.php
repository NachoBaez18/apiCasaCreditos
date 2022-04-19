<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\DetallePedidoController;
use App\Http\Controllers\ArqueoController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'user'], function () {
    Route::post('login',[UserController::class,'login']);
    Route::post('signup',[UserController::class,'signUp']);
    
});

//add this middleware to ensure that every request is authenticated
Route::middleware('auth:api')->group(function(){
    Route::post('user', [UserController::class,'user']);
    Route::get('logout',[UserController::class,'logout']);
});

Route::resource('cliente',ClienteController::class);
//Route::resource('pedido',PedidoController::class);

Route::group(['prefix' => 'pedido'], function () {
   Route::resource('pedidos',PedidoController::class);
   Route::post('entregado/{id}',[PedidoController::class,'entregado']);
   Route::get('getCobrarHoy',[PedidoController::class,'getCobrarhoy']);
   Route::get('getCobrar',[PedidoController::class,'getCobrar']);
   Route::get('ultimo',[PedidoController::class,'ultimoArqueo']);
   Route::resource('detalle', DetallePedidoController::class);
   Route::put('pagadoTotal/{id}',[DetallePedidoController::class,'pagadoTotal']);
   Route::put('pagadoParcial/{id}',[DetallePedidoController::class,'pagadoParcial']);
   Route::get('imprimir',[PedidoController::class,'imprimir']);
   Route::get('aCobrarPdf',[PedidoController::class,'aCobrarPdf']);
   Route::get('aEntregarPdf',[PedidoController::class,'aEntregarPdf']);
   Route::resource('mora', MoraController::class);
   Route::get('reporte/{id}',[PedidoController::class,'reporte']);
   Route::post('orden',[PedidoController::class,'orden']);
});

Route::group(['prefix' => 'arqueo'], function () {
    Route::resource('arqueos',ArqueoController::class);
    Route::get('ultimo',[ArqueoController::class,'ultimoArqueo']);
    Route::put('cerrarCaja/{id}',[ArqueoController::class,'cerrarCaja']);
 });