<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\DetallePedido;
class Mora extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    protected $table = 'moras';
    protected $perPage = 5;
    

    protected $fillable =[
        'id_detallePedido', 'monto','dias','cancelado'	
    ];

    public function detallePedido(){
        return $this->belongsTo(DetallePedido::class,'id_detallePedido','id');
    }
}
