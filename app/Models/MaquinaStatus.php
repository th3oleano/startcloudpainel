<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaquinaStatus extends Model
{
    protected $table = 'maquinas_status';
    protected $fillable = ['vmid', 'status'];
}