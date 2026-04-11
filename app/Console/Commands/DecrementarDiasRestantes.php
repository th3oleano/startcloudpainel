<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MaquinaAlugada;

class DecrementarDiasRestantes extends Command
{
    protected $signature = 'maquinas:decrementar-dias';
    protected $description = 'Decrementa o campo dias_restantes de todas as máquinas alugadas em 1 por dia';

    public function handle()
    {
        $maquinas = MaquinaAlugada::where('dias_restantes', '>', 0)->get();
        foreach ($maquinas as $maquina) {
            $maquina->dias_restantes -= 1;
            $maquina->save();
        }
        $this->info('Dias restantes decrementados para todas as máquinas alugadas.');
    }
}
