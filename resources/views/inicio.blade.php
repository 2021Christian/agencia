{{-- incorporo/heredo la vista plantilla --}}
@extends('layouts.plantilla') 

{{-- inserto la vista dentro del campo dinamico definido en la plantilla, llamado "contenido" d--}}
@section('contenido')
    <h1>Aca va mi contenido!!</h1>
@endsection