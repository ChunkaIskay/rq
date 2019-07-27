<?php

namespace App\Http\Controllers\JefeOperaciones;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Tasks extends Controller
{
     public function index(Request $request){
		dd('holaa');
		$requerimientos = DB::table('tb_requerimiento')
		 
		->join('users','tb_requerimiento.id_operador', '=' , 'users.id')
		->select('id_requerimiento', 'tipo_tarea', 'fecha_solicitud', 'tb_requerimiento.tipo', 'hora_solicitud', 'prioridad', 'descripcion', 'resultado', 'obs', 'accesible', 'motivo_cambio', 'name', 'ap_paterno')
		->paginate(5);
		return view('jefe_operaciones.index',  array(
							'requerimientos' => $requerimientos
						));

      /*  $categorizations = DB::table('categorizations')->get();
        $typeContracts = DB::table('type_contracts')->get();
		return view('contract.index',  array(
							'contracts' => $contracts,
                            'categorizations' => $categorizations,
                            'typeContracts' => $typeContracts
						));*/
	}
}
