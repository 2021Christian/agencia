@extends('layouts.plantilla')

@section('contenido')
    <h1>Modificación de una región</h1>

    <div class="alert bg-light p-4 col-8 mx-auto shadow">
        <form action="/region/update" method="post">
            @csrf
        
            <div class="form-group">
                <label for="regNombre">Nombre de la región</label>
                <input type="text" name="regNombre"
                    value="{{$region->regNombre}}" 
                    class="form-control" id="regNombre" required>
            </div>
            {{-- inserto un campo oculto para guardar el ID a modificar de la BD --}}
            <input type="hidden" name="idRegion"
                value="{{$region->idRegion}}">

            <button class="btn btn-dark my-3 px-4">Modificar Región</button>
            <a href="/regiones" class="btn btn-outline-secondary">Volver al panel de regiones</a>
    
        </form>

    </div>

@endsection