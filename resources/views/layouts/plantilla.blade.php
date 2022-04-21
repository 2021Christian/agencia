@include('layouts.header') {{-- las rutas van siempre desde la raiz resource/views, no importa donde esta el arrchivo que hace la llamada --}}
@include('layouts.nav')

    <main class="container py-4">

        @yield('contenido') {{-- campo dinamico llamado "contenido" --}}
    
    </main>

@include('layouts.footer')