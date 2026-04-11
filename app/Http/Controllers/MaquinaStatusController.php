<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MaquinaStatus;

class MaquinaStatusController extends Controller
{
    // Retorna status de todas as máquinas
    public function index()
    {
        return MaquinaStatus::all();
    }

    // Atualiza status de uma máquina
    public function update(Request $request, $vmid)
    {
        $status = $request->input('status');
        $maquina = MaquinaStatus::updateOrCreate(
            ['vmid' => $vmid],
            ['status' => $status]
        );
        return response()->json($maquina);
    }
}
