
@extends('components.header')

@section('titulo', 'Máquinas Disponíveis')

@section('conteudo')
    <style>
        .maquinas-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 18px;
            margin-top: 32px;
        }
        .maquina-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 170px;
            min-width: 170px;
            border-radius: 18px;
            font-size: 1.2em;
            font-weight: bold;
            color: #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
            margin: 0 auto;
        }
        .maquina-livre {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        }
        .maquina-ocupada {
            background: linear-gradient(135deg, #ff5858 0%, #f09819 100%);
        }
        .maquina-indisponivel {
            background: linear-gradient(135deg, #ff5858 0%, #f09819 100%);
        }
        .maquina-card .icon {
            font-size: 2.5em;
            margin-bottom: 0.5em;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
        }
        .maquina-card {
            text-align: center;
        }
    </style>

    <div id="maquinas-list">
        Carregando máquinas...
    </div>


    <!-- Modal -->
    <div id="modal-alugar" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.35); z-index:1000; align-items:center; justify-content:center;">
        <div style="background:#fff; border-radius:12px; padding:32px 24px; min-width:320px; max-width:90vw; box-shadow:0 4px 24px rgba(0,0,0,0.15); position:relative; text-align:center;">
            <button onclick="fecharModal()" style="position:absolute; top:12px; right:16px; background:none; border:none; font-size:1.5em; cursor:pointer;">&times;</button>
            <h2 id="modal-titulo" style="margin-bottom:18px; color:#222;">Alugar Máquina</h2>
            <div id="modal-info"></div>
            <div style="margin:18px 0;">
                <label for="chave-key" style="display:block;margin-bottom:6px;">Chave Key</label>
                <input type="text" id="chave-key" style="width:80%;padding:8px;border-radius:6px;border:1px solid #ccc;">
            </div>
            <button id="btn-confirmar-aluguel" style="margin-top:8px; background:#43e97b; color:#fff; border:none; border-radius:8px; padding:10px 28px; font-size:1.1em; font-weight:bold; cursor:pointer;">Confirmar Aluguel</button>
        </div>
    </div>

    <script>
        let statusMaquinas = {};

        function carregarStatusMaquinas(callback) {
            fetch('/api/status-maquinas')
                .then(response => response.json())
                .then(statusList => {
                    statusMaquinas = {};
                    statusList.forEach(item => {
                        statusMaquinas[item.vmid] = item.status;
                    });
                    callback();
                });
        }

        function renderMaquinas() {
            fetch('/api/maquinas-online')
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById('maquinas-list');
                    if (!Array.isArray(data) || data.length === 0) {
                        container.innerHTML = '<p>Nenhuma máquina encontrada.</p>';
                    } else {
                        container.innerHTML = '<div class="maquinas-grid">' +
                            data.map(vm => {
                                let sistema = '';
                                if (vm.name && vm.name.toLowerCase().includes('win')) {
                                    sistema = 'Windows';
                                } else if (vm.name && vm.name.toLowerCase().includes('linux')) {
                                    sistema = 'Linux';
                                } else {
                                    sistema = 'Outro';
                                }
                                const status = statusMaquinas[vm.vmid] === 'indisponivel' ? 'indisponivel' : 'livre';
                                const cardClass = status === 'indisponivel' ? 'maquina-card maquina-indisponivel' : 'maquina-card maquina-livre';
                                // Só permite clicar se estiver livre
                                // Corrige aspas simples e duplas no nome para evitar erro de sintaxe
                                let nomeSafe = vm.name ? String(vm.name).replace(/'/g, "\\'").replace(/\"/g, '\\"') : '';
                                const clickHandler = status === 'livre' ? `onclick=\"abrirModalAlugar('${vm.vmid}','${nomeSafe}','${sistema}')\" style=\"cursor:pointer;\"` : '';
                                return `
                                    <div class=\"${cardClass}\" ${clickHandler}>
                                        <span class=\"icon\">&#128187;</span>
                                        <div style=\"font-size:1.1em;font-weight:bold;\">${status === 'livre' ? 'Livre' : 'Indisponível'}</div>
                                        <div style=\"font-size:1em;\">${vm.name && vm.name.toLowerCase() !== sistema.toLowerCase() ? vm.name : ''}</div>
                                        <div style=\"font-size:0.95em;\">Sistema: ${sistema}</div>
                                    </div>
                                `;
                            }).join('') +
                        '</div>';
                    }
                })
                .catch(() => {
                    document.getElementById('maquinas-list').innerHTML = '<p>Erro ao carregar as máquinas.</p>';
                });
        }
        // Função para abrir o modal de aluguel
        let maquinaSelecionada = null;
        function abrirModalAlugar(vmid, nome, sistema) {
            maquinaSelecionada = { vmid, nome, sistema };
            document.getElementById('modal-info').innerHTML = `
                <p><strong>Nome:</strong> ${nome || '(sem nome)'}</p>
                <p><strong>Sistema:</strong> ${sistema}</p>
            `;
            document.getElementById('modal-alugar').style.display = 'flex';
        }

        function fecharModal() {
            document.getElementById('modal-alugar').style.display = 'none';
            maquinaSelecionada = null;
        }



        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('btn-confirmar-aluguel').onclick = function() {
                if (!maquinaSelecionada) return;
                const chave = document.getElementById('chave-key').value.trim();
                if (!chave) {
                    alert('Digite a chave key para alugar a máquina.');
                    return;
                }
                // Aluga a máquina e salva a chave_key
                fetch(window.location.origin + '/api/alugar-maquina', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ vmid: maquinaSelecionada.vmid, chave_key: chave })
                })
                .then(r => {
                    if (!r.ok) {
                        return r.text().then(text => { throw new Error(text); });
                    }
                    return r.json();
                })
                .then(resp => {
                    // Atualiza status para ocupada
                    fetch(window.location.origin + '/api/status-maquinas/' + maquinaSelecionada.vmid, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ status: 'indisponivel' })
                    })
                    .then(() => {
                        alert('Máquina marcada como ocupada!');
                        fecharModal();
                        atualizarMaquinas();
                    });
                })
                .catch((err) => {
                    alert('Erro ao alugar máquina: ' + err.message);
                });
            };
        });
    // Garante que o token CSRF está presente no head
    if (!document.querySelector('meta[name="csrf-token"]')) {
        const meta = document.createElement('meta');
        meta.name = 'csrf-token';
        meta.content = '{{ csrf_token() }}';
        document.head.appendChild(meta);
    }

        function atualizarMaquinas() {
            carregarStatusMaquinas(renderMaquinas);
        }

        atualizarMaquinas();
    </script>
@endsection
