<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaquinaAlugada extends Model
{
    protected $table = 'maquinas_alugadas';
    use HasFactory;

    protected $fillable = [
        'user_id',
        'vmid',
        'ip',
        'porta',
        'login',
        'senha',
        'chave_key',
        'dias_restantes',
    ];
}
