<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Cliente;

class Pedido extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    protected $table = 'pedido';
    protected $perPage = 5;
    

    protected $fillable =[
        'id_cliente','fecha_entrega', 'monto','n_cuota','entregado','cancelado'	
    ];

    public function cliente(){
        return $this->belongsTo(Cliente::class,'id_cliente','id');
    }
    public function detalles(){
        return $this->hasMany(DetallePedido::class,'id_pedido','id');
    }



}
