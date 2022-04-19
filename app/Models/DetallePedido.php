<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Pedido;

class DetallePedido extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'detalle_pedidos';
    protected $perPage = 5;
    

    protected $fillable =[
        'id_pedido','fecha_vencimiento', 'monto','cuota_numero','cancelado'	
    ];

    public function pedido(){
        return $this->belongsTo(Pedido::class,'id_pedido','id');
    }
    
    public function moras(){
        return $this->hasMany(Mora::class,'id_Detallepedido','id');
    }
    

}
