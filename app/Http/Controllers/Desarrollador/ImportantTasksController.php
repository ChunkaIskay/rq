<?php

namespace App\Http\Controllers\Desarrollador;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;


class ImportantTasksController extends Controller
{
    
    public function revListarAsigInstInstalar(){

		$dateFrom = "";
		$dateTo = "";
		$rqAsigIstalar = "";

		return view('desarrollador.rq_asignar_instalar')->with(compact('rqAsigIstalar','dateFrom','dateTo'));

    }

    public function searchRevAsigInst(Request $request){
		
		$user = \Auth::user();

		if(!empty($request->input('dateFrom')) && !empty($request->input('dateTo')) ){

			$rqAsigIstalar = $this->sqlAsigInstalar($request->input('dateFrom'), $request->input('dateTo'), $user->id);
		}
	
		$dateFrom = $request->input('dateFrom');
		$dateTo = $request->input('dateTo');


		$perPage=20;
        $currentPage = 0;
    	$pagedData = array_slice($rqAsigIstalar, $currentPage * $perPage, $perPage);
    	
    	$rqAsigIstalar = new \Illuminate\Pagination\LengthAwarePaginator($pagedData, count($rqAsigIstalar), $perPage);
		

       	return view('desarrollador.rq_asignar_instalar')->with(compact('rqAsigIstalar','dateFrom','dateTo'));

	}


    public function asigInstalar(){   

    	$listAprob = DB::table('tb_aprobacion_requerimiento')
		->where('accesible', '=' , 'Si')
		->select('nro_aprobacion','id_requerimiento','fecha_aprobacion','hora_aprobacion','accesible')
		->orderBy('id_requerimiento','ASC')
		->get();

		    	
		return view('desarrollador.rq_asignar_instalar')->with(compact('listAprob'));    
	}

	public function revDetalleAsigInst($id){


		$detalle = DB::select("
			SELECT li.*, (SELECT concat(users.name , ' ', users.ap_paterno) as nombre_completo FROM users 
			WHERE users.id = id_gestor ) asig_por, (SELECT concat(users.name , ' ', users.ap_paterno) as nombre_completo FROM users 
			WHERE users.id = id_programador ) asig_a   FROM
			(
			SELECT 
			tb_asignacion_instal_req.id_asig_instal, 
			tb_asignacion_instal_req.id_gestor, 
			tb_asignacion_instal_req.id_solucion, 
			tb_asignacion_instal_req.id_programador,
			tb_asignacion_instal_req.fecha_asig_instal,
			tb_asignacion_instal_req.hora_asig_instal,
			tb_solucion_requerimiento.descripcion desc_rq,
			tb_requerimiento.descripcion desc_solu,
			tb_requerimiento.id_operador 
			FROM tb_asignacion_instal_req
			JOIN tb_solucion_requerimiento ON tb_asignacion_instal_req.id_solucion=tb_solucion_requerimiento.id_solucion
			JOIN tb_asignacion_requerimiento ON tb_solucion_requerimiento.id_asignacion=tb_asignacion_requerimiento.Nro_asignacion 
			JOIN tb_requerimiento ON tb_asignacion_requerimiento.id_requerimiento=tb_requerimiento.id_requerimiento 
			WHERE id_asig_instal = :ida ) as li
			JOIN users ON li.id_operador = users.id", ['ida' => $id]);

		$adjuntos = DB::table('tb_adjuntos')
			->where('id_requerimiento', '=' , $id)
			->where('id_etapa', '=' , '1')
			->select('id_adjunto', 'id_requerimiento', 
					 'id_etapa', 'nombre', 
					 'fecha', 'hora')
			->get();

		// rol de usuario jefe de sistemas = 3
		//$gestor = $this->listUserRol(3);
			$gestor=array();
			$desarrollador=array();
		
		// rol de usuario desarrollador = 2
		//$desarrollador = $this->listUserRol(2);

		$fecha_plan = DB::table('tb_req_fecha')
			->where('id_requerimiento','=', $id)
			->get();

		//agregar nick del mètodo para subir y borrar archivos
		$nombreFuncion = 'detalleAprob';
		return view('desarrollador.rq_detalle_asig_inst')->with(compact('detalle','adjuntos','nombreFuncion','gestor','desarrollador','fecha_plan','id'));

	}


	public function revGuadarInstalar(Request $request, $id){
		
	    date_default_timezone_set('America/La_Paz');
	    
	    $this->validate($request, AsigInstalReq::$rules, AsigInstalReq::$messages);

		// ingresar registros en asignación de requerimientos..
		$asignacionReq = AsigInstalReq::find($id);

		$fecha = date('Y-m-d');
		$hora = date('H:i:s');
	    $asignacionReq->id_gestor = $request->gestor;
	    $asignacionReq->id_programador = $request->desarrollador;

	    $asignacionReq->fecha_asig_instal = $fecha;
	    $asignacionReq->hora_asig_instal = $hora;
		//$asignacionReq->accesible = 'Si';
    	
    	if(!$asignacionReq->save()){  // save
	        //actualizar el campo accesible de la tabla aprobacion_requerimiento
			$rqAprob = AprobacionRq::find($id);
	    	$rqAprob->accesible = 'No';
	        $rqAprob->save();
	  
			return redirect()->route('detalleAsigInst')->with(array(
		    		'error' => 'Error, no se puedo asignar el requemirimiento.!!'
		    		));
		}

		    return redirect()->route('rqListarAsigInst')->with(array(
		    		'message' => 'El requerimiento fue asignado exitosamente.!!'
		    	)); 
    	
	}


	public function sqlAsigInstalar($fecha_inicio, $fecha_fin, $user_id){
		// accesible es SI se puso no para prueba.
		$lista = DB::select("SELECT * FROM tb_asignacion_instal_req WHERE accesible='No' and id_programador =:user_id and fecha_asig_instal BETWEEN :fecha_inicio and :fecha_fin  ORDER BY id_asig_instal ASC", ['fecha_inicio' => $fecha_inicio, 'fecha_fin' => $fecha_fin, 'user_id' => $user_id ]);

		
		return $lista;

	}


}
