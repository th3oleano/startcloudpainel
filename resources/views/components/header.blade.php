@vite(['resources/css/app.css', 'resources/css/dashboard.css'])
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="dashboard-wrapper">
    @include('components.sidebar')
    <main class="main-content">
        <header class="main-header">
            <span class="welcome">@yield('titulo', 'Bem-vindo!')</span>
            <a href="{{ route('logout') }}" class="logout-btn">Sair</a>
        </header>
        <section class="dashboard-content">
            @yield('conteudo')
        </section>
    </main>
</div>
