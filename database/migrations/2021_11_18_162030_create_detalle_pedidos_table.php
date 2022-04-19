<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetallePedidosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalle_pedidos', function (Blueprint $table) {
            $table->engine="InnoDB";
            $table->increments('id');
            $table->integer('id_pedido')->unsigned();
            $table->date('fecha_vencimiento');
            $table->integer('monto');
            $table->integer('cuota_numero');
            $table->string('cancelado',2);
            
            $table->timestamps();

            $table->foreign('id_pedido')->references('id')->on('pedido')->onDelete("cascade");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('detalle_pedidos');
    }
}
