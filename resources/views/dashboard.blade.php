@extends('components.header')

@section('titulo', 'Dashboard')

@section('conteudo')

    <div class="dashboard-info">
        <div id="dias-restantes">Carregando dias restantes...</div>
        <div>Última sessão:</div>
        <div>Horas totais:</div>
        <div>Assinatura atual:</div>
    </div>

    <script>
    function carregarDiasRestantes() {
        fetch('/api/minhas-maquinas')
            .then(r => r.json())
            .then(maquinas => {
                if (!Array.isArray(maquinas) || maquinas.length === 0) {
                    document.getElementById('dias-restantes').innerText = 'Nenhuma máquina alugada.';
                    return;
                }
                // Busca o menor dias_restantes entre as máquinas alugadas
                let diasRestantes = maquinas.map(m => m.dias_restantes ?? 0);
                if (diasRestantes.length === 0) {
                    document.getElementById('dias-restantes').innerText = 'Não foi possível obter dias restantes.';
                } else {
                    const menor = Math.min(...diasRestantes);
                    document.getElementById('dias-restantes').innerText = `Dias restantes do token: ${menor}`;
                }
            })
            .catch(() => {
                document.getElementById('dias-restantes').innerText = 'Erro ao carregar dias restantes.';
            });
    }
    document.addEventListener('DOMContentLoaded', carregarDiasRestantes);
    </script>

    <h2 style="margin-top:32px;">Minhas Máquinas Alugadas</h2>
    <div id="minhas-maquinas">
        Carregando...
    </div>

    <script>
    function renderMinhasMaquinas() {
        fetch('/api/minhas-maquinas')
            .then(r => r.json())
            .then(maquinas => {
                const container = document.getElementById('minhas-maquinas');
                if (!Array.isArray(maquinas) || maquinas.length === 0) {
                    container.innerHTML = '<p>Nenhuma máquina alugada.</p>';
                } else {
                    container.innerHTML = `<table style="width:100%; max-width:700px; margin-top:18px; border-collapse:collapse;">
                        <thead>
                            <tr style='background:#f5f5f5;'>
                                <th style='padding:8px; border:1px solid #eee;'>VMID</th>
                                <th style='padding:8px; border:1px solid #eee;'>IP</th>
                                <th style='padding:8px; border:1px solid #eee;'>Porta</th>
                                <th style='padding:8px; border:1px solid #eee;'>Login</th>
                                <th style='padding:8px; border:1px solid #eee;'>Senha</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${maquinas.map(m => `
                                <tr>
                                    <td style='padding:8px; border:1px solid #eee;'>${m.vmid}</td>
                                    <td style='padding:8px; border:1px solid #eee;'>${m.ip}</td>
                                    <td style='padding:8px; border:1px solid #eee;'>${m.porta}</td>
                                    <td style='padding:8px; border:1px solid #eee;'>${m.login}</td>
                                    <td style='padding:8px; border:1px solid #eee;'>${m.senha}</td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>`;
                }
            })
            .catch(() => {
                document.getElementById('minhas-maquinas').innerHTML = '<p>Erro ao carregar as máquinas alugadas.</p>';
            });
    }
    document.addEventListener('DOMContentLoaded', renderMinhasMaquinas);
    </script>
@endsection
