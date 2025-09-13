@extends('layouts.auth')

@section('title', 'Registro')

@section('content')
<form method="POST" action="{{ route('register') }}">
    @csrf
    <div class="mb-3">
        <label>Nombre</label>
        <input type="text" name="name" class="w-full border px-2 py-1" required>
    </div>
    <div class="mb-3">
        <label>Email</label>
        <input type="email" name="email" class="w-full border px-2 py-1" required>
        @error('email') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
    </div>
    <div class="mb-3">
        <label>Contraseña</label>
        <input type="password" name="password" class="w-full border px-2 py-1" required>
    </div>
    <div class="mb-3">
        <label>Confirmar Contraseña</label>
        <input type="password" name="password_confirmation" class="w-full border px-2 py-1" required>
    </div>
    <button class="w-full bg-green-500 text-white py-2 rounded">Registrarse</button>
</form>
<p class="text-center mt-4">
    ¿Ya tienes cuenta? <a href="{{ route('login') }}" class="text-blue-500">Inicia sesión</a>
</p>
@endsection
