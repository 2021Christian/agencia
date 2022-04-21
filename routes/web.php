<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Route::get('peticion', accion );

Route::get('/saludo', function () {
    return 'Hola mundo desde Laravel';
});

Route::get('/prueba', function () {
    return view('test');
});

//plantilla
Route::get('/inicio', function () {
    return view('inicio');
});

/************* CRUD Regiones ********************/
//MUESTRO LAS REGIONES
Route::get('/regiones', function () {
    // obtengo el listado de regiones
    // $regiones = DB::select('SELECT idRegion, regNombre FROM regiones'); //esto es RAW SQL
    $regiones = DB::table('regiones')->get(); //esto es lo mismo, pero Fluent Query Builder
    
    // print_r($regiones);
    // dd($regiones); //dump & die (muestra los datos con dump e interrumpe la app con die)
    //hay que recordar comentarlo porque interrumple  el flujo de la app

    //retorno la vista y en el array le paso la variable con los resultados de la busqueda
    return view('regiones', ['regiones'=> $regiones]);
});

//CREO una region
//1ro - Muestro form donde ingresar los datos
Route::get('/region/create', function () {
    return view('regionCreate');
});
//2do - Creo la region en la BD (submit del form anterior)
Route::post('/region/store', function (){
    // recibo los datos del form
    // $regNombre = $_POST['regNombre']; //opcion original
    $regNombre = request()->regNombre; //esta relacionado con $_REQUEST[];

    //inserto en regiones
    
    //RawSQL
    //EJEMPLO // DB::insert('insert into users (id, name) values (?, ?)', [1, 'Marc']);
    // DB::insert('INSERT INTO regiones (regNombre) VALUE (:regNombre)', [$regNombre]);
    
    //query Builder
    DB::table('regiones')
        ->insert(['regNombre' => $regNombre]);
    
    return redirect('/regiones')
        ->with(['mensaje' => 'Región "' .$regNombre .'" agregada correctamente']);
});

//MODIFICAR REGION
    //1ro - uso get, para traer el form con los datos cargados
    //con {} en la ruta incluyo el dato dinamico (id del elemento a modificar) qeu viene por la uri.
    //la variable recibida por la uri, la incluyo como parametro de la funcion.
    //si tengo mas de una, la relacion de {} con () se hace por la posicion y no por el nombre.
Route::get('/region/edit/{id}', function ($id) {
    //Traigo los datos de la region en base al ID recibido
    //RawSQL
    /*$region = DB::select('SELECT idRegion, regNombre 
        FROM regiones 
        WHERE idRegion = :idRegion',[$id]); */

    //query Builder
    $region = DB::table('regiones')
        ->where('idRegion', $id)
        ->first(); //fetch

    // dd($region);

    //retorno la vista modificar con los datos solicitados
    return view('regionEdit', ['region'=>$region]);
});
//2do - Registro la modificacon de la region en la BD (submit del form anterior)
Route::post('/region/update', function () {
    $idRegion = request()->idRegion;
    $regNombre = request()->regNombre;

    //RawSQL
    /* DB::update('UPDATE regiones 
            SET regNombre = :regNombre 
            WHERE idRegion = :idRegion', [$regNombre, $idRegion]);*/
    
    //Query Builder
    DB::table('regiones')
        ->where('idRegion', $idRegion)
        ->update(['regNombre' => $regNombre]);

    //retorno la peticion a la pagina original de regiones
    return redirect('/regiones')
        ->with(['mensaje'=>'Región "' .$regNombre .'" modificada correctamente']);
});


// Eliminar Region
//1ro - Muestro los datos de la region a eliminar
Route::get('/region/delete/{id}', function ($id) {
    // Reviso Destinos vs. Regiones
    $regionEnDestino = DB::table('destinos')
                            ->where('idRegion',$id)
                            ->get();
    
    if(count($regionEnDestino) == 0)
    {   
        // si no hay destinos vinculados a esa región
        $region = DB::table('regiones')
            ->where('idRegion',$id)
            ->first();
        // vista previa a la eliminación
        return view('regionDelete', ['region' => $region]);
    }
    else
    {
        // si hay destinos en esa region NO permite eliminar
        // vista regiones con mensaje de advertencia
        return redirect('/regiones')
            ->with([
                'mensaje'=>'No se puede eliminar - Hay destinos que dependen de la región que desea borrar'
                ]);
    }

});
// 2do - Elimino la region
Route::post('/region/destroy', function() {
    $idRegion = request()->idRegion;
    $regNombre = request()->regNombre;

    DB::table('regiones')
        ->where('idRegion', $idRegion)
        ->delete();
        
    return redirect('/regiones')
        ->with(['mensaje'=>'Región "' .$regNombre. '" eliminada correctamente.']);
});
        
/********************************************** */
        
/************* CRUD Destinos ********************/
//MUESTRO TODOS LOS DESTINOS
Route::get('/destinos', function () {
    
    // RawSQL
    /* $destinos = DB::select('
        SELECT destinos.*, regiones.regNombre 
        FROM regiones, destinos 
        WHERE regiones.idRegion=destinos.idRegion'); */

    // queryBuilder
    $destinos = DB::table('destinos')
            ->select('destinos.*', 'regiones.regNombre')
            ->join('regiones', 'destinos.idRegion', '=', 'regiones.idRegion')
            ->get();

    // dd($destinos);

    return view('destinos', ['destinos' => $destinos]);
});

//CREAR destinos
//1ero -Muestro el formulario de creacion y le cargo las regiones
Route::get('/destino/create', function () {
    //Traigo los datos de las regiones, para incluir en el formulario
    //RawSQL
    /*$regiones = DB::select('SELECT idRegion, regNombre
        FROM regiones');*/
    //queryBuilder
    $regiones = DB::table('regiones')
        ->get();
    // dd($regiones);

    return view('destinoCreate', ['regiones' => $regiones]);
});

//2do - Creo el destino en la BD
Route::post('/destino/store', function () {
    //capto los datos que vienen del form
    $destNombre = request('destNombre');
    $idRegion = request('idRegion');
    $destPrecio = request('destPrecio');
    $destAsientos = request('destAsientos');
    $destDisponibles = request('destDisponibles');
    $destActivo = 1;

    //creo el registro en la BD
    //rawSQL
    /*DB::insert('INSERT INTO destinos
        (destNombre, destPrecio, destAsientos, destDisponibles, destActivo, idRegion) 
        VALUE (:destNombre, :destPrecio, :destAsientos, :destDisponibles, :destActivo, :idRegion)',
        [$destNombre, $destPrecio, $destAsientos, $destDisponibles, $destActivo, $idRegion]);*/
    
    DB::table('destinos')
        ->insert([  'destNombre'=>$destNombre,
                    'destPrecio'=>$destPrecio,
                    'destAsientos'=>$destAsientos,
                    'destDisponibles'=>$destDisponibles,
                    'destActivo'=>$destActivo,
                    'idRegion'=>$idRegion]
        );

    return redirect('/destinos')
        ->with(['mensaje'=>'Destino "' .$destNombre .'" creada correctamente']);
    });

    //MODIFICAR Destino
    //1ero - Muestro el Form con los datos del destino a modificar
    Route::get('/destino/edit/{id}', function ($id) {
        //traigo las regiones
        $regiones = DB::table('regiones')
        ->get();
        //traigo el destino
        $destino = DB::table('destinos')
        ->where('idDestino', $id)
        ->first();

        //muestro la vista editar destino
        return view('destinoEdit', ['regiones' => $regiones, 'destino'=>$destino]);
    });
    
    //2do - Modifico el destino en la BD (retorno del form)
    Route::post('/destino/update', function () {
        $idDestino = request('idDestino');
        $destNombre = request('destNombre');
        $idRegion = request('idRegion');
        $destPrecio = request('destPrecio');
        $destAsientos = request('destAsientos');
        $destDisponibles = request('destDisponibles');
        $destActivo = request('destActivo');

        DB::table('destinos')
            ->where('idDestino', $idDestino)
            ->update(['destNombre'=>$destNombre,
                    'destPrecio'=>$destPrecio,
                    'destAsientos'=>$destAsientos,
                    'destDisponibles'=>$destDisponibles,
                    'destActivo'=>$destActivo,
                    'idRegion'=>$idRegion
            ]);

        return redirect('/destinos')
            ->with(['mensaje'=>'Destino "' .$destNombre .'" modificado correctamente']);
    });

    // ELIMINAR Destino
    //1ro - Muestro el destino a eliminar
    Route::get('/destino/delete/{id}', function ($id) {
        
        $destino = DB::table('destinos as d')
                ->join('regiones as r', 'd.idRegion', '=', 'r.idRegion')
                ->select('d.*', 'r.regNombre')
                ->where('d.idDestino', $id)
                ->first();

        return view('destinoDelete', ['destino' => $destino]);    
        
    });
    
    Route::post('/destino/destroy', function(){
        $idDestino = request()->idDestino;
        $destNombre = request()->destNombre;
        
        DB::table('destinos')
            ->where('idDestino', $idDestino)
            ->delete();
        
        return redirect('/destinos')
        ->with(['mensaje'=>'Destino "' .$destNombre. '" eliminado correctamente.']);
    });

/********************************************** */
