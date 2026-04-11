<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProxmoxController;
use App\Http\Controllers\MaquinaAlugadaController;
use App\Models\MaquinaCredencial;
use Illuminate\Http\Request;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MaquinaStatusController;
use App\Http\Controllers\MaquinaApiController;

// Exibir o formulário de login (GET)
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login.login');

// Processar o login (POST)
Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');

// Rota de logout
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

// Rota de portal
Route::get('/portal', function () {
    return view('portal');
})->name('portal');

// Redirecionamento para a página de login
Route::get('/', function () {
    return redirect()->route('login');
});

// Rota de dashboard
Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

// API para listar máquinas online
Route::get('/api/maquinas-online', [ProxmoxController::class, 'maquinasOnline']);

// Rota para visualizar máquinas disponíveis
Route::get('/maquinas-disponiveis', function () {
    return view('paginas.maquinas-disponiveis');
})->name('maquinas.disponiveis');

// Middleware de autenticação para rotas protegidas
Route::middleware(['auth'])->group(function () {
    // API para alugar máquinas
    Route::post('/api/alugar-maquina', [MaquinaAlugadaController::class, 'alugar']);
    // API para listar as máquinas do usuário
    Route::get('/api/minhas-maquinas', [MaquinaAlugadaController::class, 'minhasMaquinas']);
});

// API para status das máquinas
Route::get('/api/status-maquinas', [MaquinaStatusController::class, 'index']);
Route::post('/api/status-maquinas/{vmid}', [MaquinaStatusController::class, 'update']);

// Rota de administração para cadastrar credenciais
Route::get('/admin/cadastrar-credencial', function () {
    return view('admin.cadastra-credencial');
})->middleware('auth');

// Processar cadastro de credenciais
Route::post('/admin/cadastrar-credencial', function (Request $request) {
    $request->validate([
        'vmid' => 'required|integer|unique:maquinas_credenciais,vmid',
        'login' => 'required',
        'senha' => 'required',
        'chave_key' => 'required',
    ]);
    MaquinaCredencial::create([
        'vmid' => $request->vmid,
        'login' => $request->login,
        'senha' => $request->senha,
        'chave_key' => $request->chave_key,
    ]);
    return redirect('/admin/cadastrar-credencial')->with('success', 'Credencial cadastrada com sucesso!');
})->middleware('auth');

// API para gerar um novo token para uma máquina virtual
Route::post('/api/gerar-novo-token', function(Request $request) {
    $request->validate([
        'vmid' => 'required|integer|exists:maquinas_credenciais,vmid',
    ]);
    $credencial = MaquinaCredencial::where('vmid', $request->vmid)->first();
    if (!$credencial) {
        return response()->json(['error' => 'Credencial não encontrada.'], 404);
    }
    // Só permite gerar novo token se já tiver expirado
    if (!is_null($credencial->chave_key)) {
        return response()->json(['error' => 'Token ainda não expirou.'], 400);
    }
    // Gera nova chave aleatória
    $novoToken = bin2hex(random_bytes(16));
    $credencial->chave_key = $novoToken;
    $credencial->created_at = now(); // reinicia a contagem
    $credencial->save();
    return response()->json(['chave_key' => $novoToken]);
})->middleware('auth');

// API para checar credencial de VMID
Route::get('/api/checar-credencial-vmid/{vmid}', function($vmid) {
    $credencial = \App\Models\MaquinaCredencial::where('vmid', $vmid)->first();
    if (!$credencial) {
        return response()->json(['existe' => false]);
    }
    return response()->json([
        'existe' => true,
        'chave_key' => $credencial->chave_key
    ]);
});

// API para listar todas as credenciais
Route::get('/api/listar-credenciais', function () {
    return response()->json(\App\Models\MaquinaCredencial::all());
})->middleware('auth');

// API para atualizar credenciais
Route::post('/api/atualizar-credencial', function (Request $request) {
    $request->validate([
        'id' => 'required|integer|exists:maquinas_credenciais,id',
        'login' => 'required',
        'senha' => 'required',
        'chave_key' => 'nullable',
    ]);
    $cred = \App\Models\MaquinaCredencial::find($request->id);
    $cred->login = $request->login;
    $cred->senha = $request->senha;
    $cred->chave_key = $request->chave_key;
    $cred->save();
    return response()->json(['success' => true]);
})->middleware('auth');

// Rota de login (também usada por LoginController)
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login.login');

// API para interagir com máquinas
Route::get('/api/maquinas-online', [MaquinaApiController::class, 'maquinasOnline']);
Route::get('/api/status-maquinas', [MaquinaApiController::class, 'statusMaquinas']);
Route::post('/api/status-maquinas/{vmid}', [MaquinaApiController::class, 'atualizarStatus']);
Route::post('/api/alugar-maquina', [MaquinaApiController::class, 'alugarMaquina']);