@extends('components.header')

@section('titulo', 'Cadastrar Credencial de VM')

@section('conteudo')
    <div class="btn-cadastrar-wrapper">
        <button id="abrirModal" class="btn-cadastrar-modal">Cadastrar Credencial</button>
    </div>

    <div id="modalCredencial" class="credencial-modal" style="display:none;">
        <div class="credencial-card credencial-modal-card">
            <h2 class="credencial-title" style="text-align:center;">Cadastrar Credencial de VM</h2>
            <form method="POST" action="{{ url('/admin/cadastrar-credencial') }}" class="credencial-form">
                @csrf
                <div class="input-group">
                    <label for="vmid">VMID</label>
                    <input type="number" name="vmid" id="vmid" required>
                </div>
                <div class="input-group">
                    <label for="login">Login</label>
                    <input type="text" name="login" id="login" required>
                </div>
                <div class="input-group">
                    <label for="senha">Senha</label>
                    <input type="text" name="senha" id="senha" required>
                </div>
                <div class="input-group">
                    <label for="chave_key">Chave Key</label>
                    <input type="text" name="chave_key" id="chave_key" required>
                </div>
                <div class="botoes-modal">
                    <button type="submit" class="btn-cadastrar">Cadastrar</button>
                    <button type="button" id="fecharModal" class="btn-fechar-modal">Cancelar</button>
                </div>
            </form>
            @if(session('success'))
                <div class="success-msg">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="error-msg">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Tabela de Listagem de Credenciais -->
    <div style="max-width:900px;margin:40px auto 0 auto;padding:32px 28px 28px 28px;background:#fff;border-radius:14px;box-shadow:0 2px 16px #0001;">
        <h2 style="margin-bottom:24px;text-align:center;color:#2d8cff;">Credenciais de VMs</h2>
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="background:#f5f5f5;">
                    <th style="padding:8px; border:1px solid #eee;text-align:center;">VMID</th>
                    <th style="padding:8px; border:1px solid #eee;text-align:center;">Login</th>
                    <th style="padding:8px; border:1px solid #eee;text-align:center;">Senha</th>
                    <th style="padding:8px; border:1px solid #eee;text-align:center;">Chave Key</th>
                    <th style="padding:8px; border:1px solid #eee;text-align:center;">Ações</th>
                </tr>
            </thead>
            <tbody id="credenciais-list">
                <tr><td colspan="5" style="text-align:center;">Carregando...</td></tr>
            </tbody>
        </table>
    </div>
    <script>
    function renderCredenciais() {
        fetch('/api/listar-credenciais')
            .then(r => r.json())
            .then(credenciais => {
                const tbody = document.getElementById('credenciais-list');
                if (!Array.isArray(credenciais) || credenciais.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="5">Nenhuma credencial cadastrada.</td></tr>';
                    return;
                }
                tbody.innerHTML = credenciais.map(c => `
                    <tr>
                        <td style="text-align:center;">${c.vmid}</td>
                        <td style="text-align:center;"><input type="text" value="${c.login}" id="login_${c.id}" style="width:100px;text-align:center;"></td>
                        <td style="text-align:center;"><input type="text" value="${c.senha}" id="senha_${c.id}" style="width:100px;text-align:center;"></td>
                        <td style="text-align:center;"><input type="text" value="${c.chave_key ?? ''}" id="chave_${c.id}" style="width:140px;text-align:center;"></td>
                        <td style="text-align:center;">
                            <button onclick="atualizarCredencial(${c.id})" style="background:#43e97b;color:#fff;border:none;border-radius:6px;padding:2px 8px;font-weight:500;cursor:pointer;display:inline-block;font-size:0.92em;min-width:54px;margin-top:6px;">Salvar</button>
                        </td>
                    </tr>
                `).join('');
            });
    }
    function atualizarCredencial(id) {
        const login = document.getElementById('login_' + id).value;
        const senha = document.getElementById('senha_' + id).value;
        const chave_key = document.getElementById('chave_' + id).value;
        fetch('/api/atualizar-credencial', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ id, login, senha, chave_key })
        })
        .then(r => r.json())
        .then(resp => {
            if (resp.success) {
                alert('Credencial atualizada!');
                renderCredenciais();
            } else {
                alert('Erro: ' + (resp.error || 'Falha ao atualizar.'));
            }
        });
    }
    document.addEventListener('DOMContentLoaded', renderCredenciais);
    </script>

    <style>
    .btn-cadastrar-wrapper {
        width: 100%;
        display: flex;
        justify-content: flex-start;
        margin: 32px 0 0 0;
        padding-left: 32px;
        box-sizing: border-box;
    }
    .btn-cadastrar-modal {
        background: linear-gradient(90deg,#43e97b,#38f9d7);
        color: #fff;
        border: none;
        border-radius: 10px;
        padding: 8px 14px;
        font-size: 1em;
        font-weight: 600;
        cursor: pointer;
        min-width: 170px;
        box-shadow: 0 2px 8px #43e97b22;
        transition: background 0.2s, box-shadow 0.2s;
    }
    .btn-cadastrar-modal:hover {
        background: linear-gradient(90deg,#38f9d7,#43e97b);
        box-shadow: 0 4px 16px #43e97b33;
    }
    .credencial-modal {
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(40, 50, 70, 0.22);
        z-index: 1000;
        display: flex;
        align-items: center;
        justify-content: center;
        animation: fadeIn 0.3s;
        backdrop-filter: blur(2px);
    }
    .credencial-modal-card {
        min-width: 420px;
        max-width: 98vw;
        border-radius: 18px;
        box-shadow: 0 8px 40px #0003;
        padding: 32px 32px 28px 32px;
        background: #fff;
        margin: 0;
    }
    .credencial-modal-card .credencial-title {
        margin-bottom: 18px;
        font-size: 1.3em;
        color: #2d8cff;
    }
    .credencial-modal-card .credencial-form {
        gap: 14px;
    }
    .credencial-modal-card .input-group input {
        font-size: 1.08em;
        padding: 12px 14px;
    }
    .botoes-modal {
        display: flex;
        gap: 18px;
        justify-content: center;
        margin-top: 12px;
    }
    .btn-cadastrar {
        min-width: 110px;
    }
    .btn-fechar-modal {
        min-width: 110px;
        background: #ececec;
        color: #222;
        border: none;
        border-radius: 10px;
        padding: 10px 0;
        font-size: 1.08em;
        font-weight: 500;
        cursor: pointer;
        box-shadow: 0 2px 8px #aaa2;
        transition: background 0.2s, box-shadow 0.2s;
    }
    .btn-fechar-modal:hover {
        background: #e0e7ef;
        box-shadow: 0 4px 16px #aaa3;
    }
    .btn-fechar-modal {
        background: #eee;
        color: #333;
        border: none;
        border-radius: 8px;
        padding: 10px 18px;
        font-size: 1em;
        font-weight: 500;
        cursor: pointer;
        transition: background 0.2s;
    }
    .btn-fechar-modal:hover {
        background: #e0e7ef;
    }
    </style>

    <script>
    document.getElementById('abrirModal').onclick = function() {
        document.getElementById('modalCredencial').style.display = 'flex';
    };
    document.getElementById('fecharModal').onclick = function() {
        document.getElementById('modalCredencial').style.display = 'none';
    };
    </script>

    <style>
    .credencial-card {
        max-width: 340px;
        margin: 24px auto;
        padding: 12px 12px 10px 12px;
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 4px 24px #0002;
        display: flex;
        flex-direction: column;
        align-items: center;
        animation: fadeIn 0.7s;
    }
    .credencial-title {
        margin-bottom: 16px;
        font-size: 1.35em;
        font-weight: bold;
        color: #2d8cff;
        letter-spacing: 0.5px;
    }
    .credencial-form {
        width: 100%;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .input-group {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }
    .input-group label {
        font-weight: 500;
        color: #333;
        margin-bottom: 2px;
    }
    .input-group input {
        padding: 10px 12px;
        border: 1.5px solid #e0e7ef;
        border-radius: 8px;
        font-size: 1em;
        background: #f8fafc;
        transition: border 0.2s;
    }
    .input-group input:focus {
        border: 1.5px solid #2d8cff;
        outline: none;
        background: #fff;
    }
    .btn-cadastrar {
        margin-top: 8px;
        background: linear-gradient(90deg,#43e97b,#38f9d7);
        color: #fff;
        border: none;
        border-radius: 10px;
        padding: 10px 0;
        font-size: 1.08em;
        font-weight: bold;
        cursor: pointer;
        box-shadow: 0 2px 8px #43e97b22;
        transition: background 0.2s, box-shadow 0.2s;
    }
    .btn-cadastrar:hover {
        background: linear-gradient(90deg,#38f9d7,#43e97b);
        box-shadow: 0 4px 16px #43e97b33;
    }
    .success-msg {
        margin-top: 18px;
        color: #43e97b;
        font-weight: bold;
        text-align: center;
    }
    .error-msg {
        margin-top: 18px;
        color: #ff3b3b;
        font-weight: 500;
        text-align: center;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @media (max-width: 600px) {
        .credencial-card {
            padding: 18px 6vw;
        }
        .credencial-title {
            font-size: 1.1em;
        }
    }
    </style>
@endsection
