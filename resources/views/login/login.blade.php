<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Login</title>
	@vite(['resources/css/app.css', 'resources/css/login.css'])
</head>
<body>
	<div class="login-container">
		<h2>Login</h2>
		@if(session('error'))
			<div class="alert alert-danger">
				{{ session('error') }}
			</div>
		@endif
		@if ($errors->any())
			<div class="alert alert-danger">
				<ul>
					@foreach ($errors->all() as $error)
						<li>{{ $error }}</li>
					@endforeach
				</ul>
			</div>
		@endif
		<form method="POST" action="{{ route('login.attempt') }}">
			@csrf
			<div class="form-group">
				<label for="email">E-mail</label>
				<input type="email" name="email" id="email" required autofocus value="{{ old('email') }}">
			</div>
			<div class="form-group">
				<label for="password">Senha</label>
				<input type="password" name="password" id="password" required>
			</div>
			<button type="submit">Entrar</button>
		</form>
	</div>
</body>
</html>
