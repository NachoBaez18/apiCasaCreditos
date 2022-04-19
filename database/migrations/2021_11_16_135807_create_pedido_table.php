<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePedidoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pedidos', function (Blueprint $table) {
            $table->engine="InnoDB";
            $table->increments('id');
            $table->integer('id_cliente')->unsigned();
            $table->date('fecha_entrega');
            $table->integer('monto');
            $table->integer('n_cuota');
            $table->string('entregado',2);
            $table->string('cancelado',2);
            
            $table->timestamps();

            $table->foreign('id_cliente')->references('id')->on('cliente')->onDelete("cascade");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pedido');
    }
}
