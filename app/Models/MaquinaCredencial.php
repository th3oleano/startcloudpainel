<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaquinaCredencial extends Model
{
    use HasFactory;
    protected $table = 'maquinas_credenciais';
    protected $fillable = [
        'vmid', 'login', 'senha', 'chave_key'
    ];

    // Permite chave_key ser nulo após expiração
    protected $attributes = [
        'chave_key' => null,
    ];
}
