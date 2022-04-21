@extends('layouts.plantilla')
@section('contenido')

    <h1>Modificación de un destino</h1>

    <div class="alert bg-light border border-white shadow round col-8 mx-auto p-4">

        <form action="/destino/update" method="post">
            @csrf

            <div class="form-group mb-2">
                <label for="destNombre">Nombre del Destino:</label>
                <input type="text" name="destNombre"
                       id="destNombre" class="form-control"
                       value="{{$destino->destNombre}}"
                       required>
            </div>

            <div class="form-group mb-2">
                <label for="idRegion">Región</label>
                <select name="idRegion" id="idRegion" class="form-control" required>
                    @foreach ($regiones as $region)
                        <option value="{{$region->idRegion}}"
                            @if ($region->idRegion == $destino->idRegion)
                                {{"selected"}}
                            @endif>{{$region->regNombre}}</option>
                            {{-- {{ ($region->idRegion == $destino->idRegion)?'selected':'' }} --}}
                    @endforeach

                </select>
            </div>

            <div class="form-group  mb-2">
                <div class="input-group mb-2">
                    <div class="input-group-prepend">
                        <div class="input-group-text">$</div>
                    </div>
                    <input type="number" name="destPrecio"
                           class="form-control" value="{{$destino->destPrecio}}" required>
                </div>
            </div>

            <div class="form-group">
                <div class="input-group mb-2">
                    <div class="input-group-prepend">
                        <div class="input-group-text">#</div>
                    </div>
                    <input type="number" name="destAsientos"
                           class="form-control" value="{{$destino->destAsientos}}" required>
                </div>
            </div>

            <div class="form-group mb-2">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text">#</div>
                    </div>
                    <input type="number" name="destDisponibles"
                           class="form-control" value="{{$destino->destDisponibles}}" required>
                </div>
            </div>

             {{-- inserto campos ocultos para guardar idDestino y destActivo a modificar de la BD --}}
             <input type="hidden" name="idDestino"
             value="{{$destino->idDestino}}">
             <input type="hidden" name="destActivo"
                 value="{{$destino->destActivo}}">
            {{-- --------------------------------------------------------------------------------- --}}

            <button class="btn btn-dark">Modificar destino</button>
            <a href="/destinos" class="btn btn-outline-secondary">
                Volver a panel de destinos
            </a>

        </form>

    </div>


@endsection
