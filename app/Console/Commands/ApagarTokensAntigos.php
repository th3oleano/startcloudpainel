<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MaquinaCredencial;
use App\Models\MaquinaStatus;

class ApagarTokensAntigos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tokens:apagar-antigos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Apaga o campo chave_key de credenciais com mais de 30 dias';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $expirados = MaquinaCredencial::where('created_at', '<', now()->subDays(30))->get();
        foreach ($expirados as $credencial) {
            $credencial->chave_key = null;
            $credencial->save();
            // Torna a máquina disponível automaticamente
            $status = MaquinaStatus::where('vmid', $credencial->vmid)->first();
            if ($status) {
                $status->status = 'livre';
                $status->save();
            }
        }
        $this->info(count($expirados) . ' tokens apagados e máquinas liberadas.');
    }
}