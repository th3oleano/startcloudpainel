<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\MaquinaAlugada;
use App\Models\MaquinaStatus;

class MaquinaApiController extends Controller
{
    // Retorna lista de máquinas online (exemplo)
    public function maquinasOnline()
    {
        // Exemplo: buscar todas as máquinas alugadas
        $maquinas = MaquinaAlugada::all();
        return response()->json($maquinas);
    }

    // Retorna status de todas as máquinas
    public function statusMaquinas()
    {
        $status = MaquinaStatus::all();
        return response()->json($status);
    }

    // Atualiza status de uma máquina
    public function atualizarStatus($vmid, Request $request)
    {
        $status = $request->input('status');
        $maquina = MaquinaStatus::where('vmid', $vmid)->first();
        if ($maquina) {
            $maquina->status = $status;
            $maquina->save();
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false, 'message' => 'Máquina não encontrada'], 404);
    }

    // Alugar máquina
    public function alugarMaquina(Request $request)
    {
        $data = $request->only(['vmid', 'chave_key']);
        $validator = Validator::make($data, [
            'vmid' => 'required',
            'chave_key' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }
        // Exemplo: marcar máquina como alugada
        $maquina = MaquinaAlugada::where('vmid', $data['vmid'])->first();
        if ($maquina) {
            $maquina->chave_key = $data['chave_key'];
            $maquina->save();
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false, 'message' => 'Máquina não encontrada'], 404);
    }
}
