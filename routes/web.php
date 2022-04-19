<?php

use Illuminate\Support\Facades\Route;
use PHPJasper\PHPJasper; 

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get('/reporte', function () {
    $input = base_path() .
    '/vendor/geekcom/phpjasper/examples/hello_world.jasper';
    $output = base_path() .
    '/vendor/geekcom/phpjasper/examples';
    $options = [
        'format' => ['pdf']
    ];

    $jasper = new PHPJasper;

    $jasper->process(
        $input,
        $output,
        $options
    )->output();

    $pathToFile = base_path() .
    '/vendor/geekcom/phpjasper/examples/hello_world.pdf';
    return response()->file($pathToFile);
});

Route::get('/pdf', function () {
    $input = base_path() .
    '\app\Reportes\pagare1.jasper';
     $output = base_path() .
    '\app\Reportes';
    $options = [
        'format' => ['pdf']
    ];

    $jasper = new PHPJasper;

          $jasper->process(
          $input,
          $output,
          $options
            )->output();

            return response()->json([
                'status' => 'ok',
                'msj' => 'pdf'
            ]);
});

