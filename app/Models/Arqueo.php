<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Arqueo extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    protected $table = 'arqueos';
    protected $perPage = 5;
    

    protected $fillable =[
        'caja','cobrado', 'entregado','arqueoDia','cerrado'
    ];
}
