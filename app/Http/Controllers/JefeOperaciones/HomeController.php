<?php

namespace App\Http\Controllers\jefeOperaciones;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;




class HomeController extends Controller
{
  /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    	//
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    /*
    public function index()
    {
        return view('home');
    }*/
   public function index(Request $request)
    {   
         $rol = "desconocido";
        
  		 //getMenuRol();

        $fullName = $request->user()->name." ".$request->user()->ap_paterno;
        $cuentaUsuario = $request->user()->cuenta_usuario;
    
	//return view('home')->with(compact('menu','fullName','cuentaUsuario'));
    //return view('jefe_operaciones.index')->with(compact('menu','fullName','cuentaUsuario'));
    return view('jefe_operaciones.index')->with(compact('fullName','cuentaUsuario'));    

    }

    public function importantTasks(){

    	$requerimientos = DB::table('tb_requerimiento')
		 
		->join('users','tb_requerimiento.id_operador', '=' , 'users.id')
		->select('id_requerimiento', 'tipo_tarea', 'fecha_solicitud', 'tb_requerimiento.tipo', 'hora_solicitud', 'prioridad', 'descripcion', 'resultado', 'obs', 'accesible', 'motivo_cambio', 'name', 'ap_paterno')
		->paginate(5);
		dd($requerimientos);
		return view('jefe_operaciones.important_tasks',  array(
							'requerimientos' => $requerimientos
						));
    }
}
