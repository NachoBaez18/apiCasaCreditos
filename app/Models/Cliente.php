<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Pedido;

class Cliente extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'cliente';
    protected $perPage = 5;

    protected $fillable =[
        'nombre','telefono', 'ciudad','cedula','activo'	
    ];

    public function pedidios(){
        return $this->hasMany(Pedido::class,'id');
    }
}
