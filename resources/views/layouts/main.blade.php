<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>@yield('title','Dashboard')</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>

<body class="flex h-screen">
    @include('templates.loading')
    {{-- Sidebar --}}
    <aside class="w-64 bg-gray-800 text-white p-4">
        <h2 class="text-lg font-bold mb-4">Mi App</h2>
        <nav>
            <ul>
                <li><a href="{{ route('home') }}" class="block py-2 px-3 hover:bg-gray-700 rounded">🏠 Home</a></li>
            </ul>
        </nav>
    </aside>

    {{-- Contenido principal --}}
    <div class="flex-1 flex flex-col">
        {{-- Navbar --}}
        <header class="bg-white shadow p-4 flex justify-between items-center">
            <h1>@yield('title')</h1>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="bg-red-500 text-white px-3 py-1 rounded">Salir</button>
            </form>
        </header>

        <main class="p-6">
            @yield('content')
        </main>
    </div>

</body>

</html>