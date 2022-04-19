<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMorasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('moras', function (Blueprint $table) {
            $table->engine="InnoDB";
            $table->increments('id');
            $table->integer('id_detallePedido')->unsigned();
            $table->integer('monto');
            $table->integer('dias');
            $table->string('cancelado',2);
            
            $table->timestamps();

            $table->foreign('id_detallepedido')->references('id')->on('detalle_pedidos')->onDelete("cascade");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('moras');
    }
}
