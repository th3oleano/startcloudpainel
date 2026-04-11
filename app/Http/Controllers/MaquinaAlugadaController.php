<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MaquinaAlugada;
use App\Models\MaquinaCredencial;
use Illuminate\Support\Facades\Auth;

class MaquinaAlugadaController extends Controller
{
    public function alugar(Request $request)
    {
        $request->validate([
            'vmid' => 'required',
        ]);

        // Busca credenciais reais
        $credencial = MaquinaCredencial::where('vmid', $request->vmid)->first();
        if (!$credencial) {
            return response()->json(['error' => 'Credenciais não encontradas para esta máquina.'], 404);
        }

        // Exemplo: Defina IP e porta fixos ou busque de outra tabela se necessário
        $ip = '192.168.0.' . $request->vmid;
        $porta = '22';

        $maquina = MaquinaAlugada::create([
            'user_id' => Auth::id(),
            'vmid' => $request->vmid,
            'ip' => $ip,
            'porta' => $porta,
            'login' => $credencial->login,
            'senha' => $credencial->senha,
            'chave_key' => $request->chave_key,
            'dias_restantes' => 30,
        ]);

        return response()->json($maquina);
    }

    public function minhasMaquinas()
    {
        $maquinas = MaquinaAlugada::where('user_id', Auth::id())->get();
        return response()->json($maquinas);
    }
}
