<?php

namespace App\Http\Controllers\Desarrollador;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

use App\Tiempo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;

use App\Adjunto;
use App\SolucionRq;
use App\AsignacionReq;
use File;

class ImportantTasksController extends Controller
{
    
    public function revListarAsigInstInstalar(){

		$dateFrom = "";
		$dateTo = "";
		$rqAsigIstalar = "";

		return view('desarrollador.rq_asignar_instalar')->with(compact('rqAsigIstalar','dateFrom','dateTo'));

    }

    public function searchRevAsigInst(Request $request){
		
		if (!Auth::check()) {
		   return view('auth.login');	
		}

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

	// Revisar req asignados

	public function revListarReqAsig(Request $request){
       
       // print_r($_GET);
        $req_id = 0;
        if(!empty($_GET)){
	        foreach ($_GET as $key => $value) {
	        	 $req_id = base64_decode($key);
	        	// base64_decode
	        }
	    }
        
		$user = \Auth::user();

		if (!Auth::check()) {
		   return view('auth.login');	
		}

		$rqAsig = DB::select('SELECT a.Nro_asignacion,
		    a.id_requerimiento,a.id_gestor,a.id_programador,a.fecha_asignacion,a.hora_asignacion,
		    a.accesible as accesible_asig,ap.nro_aprobacion,ap.fecha_aprobacion,ap.hora_aprobacion, 
		    rq.prioridad, rq.tipo, rq.fecha_solicitud, rq.hora_solicitud, rq.accesible,
		    rq.descripcion, rq.resultado,
		    (SELECT CONCAT(name ," ",ap_paterno) FROM users WHERE id= a.id_gestor ) asig_por,
		    (SELECT CONCAT(name ," ",ap_paterno) FROM users WHERE id= a.id_programador ) asig_a,
            (SELECT CONCAT(name ," ",ap_paterno) FROM users WHERE id= rq.id_operador ) solicitado_por
		    FROM tb_asignacion_requerimiento a 
		    JOIN tb_aprobacion_requerimiento ap ON a.id_requerimiento=ap.id_requerimiento
		    JOIN tb_requerimiento rq ON a.id_requerimiento=rq.id_requerimiento 
		    WHERE a.accesible="Si" AND a.id_programador = :id', ['id' => $user->id]);

		$pagTitulo = 'Revisar estado de requerimientos';

		/*listdo de rq en desarrollo*/
		$rqDesarrollo = DB:: select("SELECT  req.Nro_asignacion, req.id_requerimiento, req.accesible, req.nro_aprobacion,  req.prioridad , t.fase 
			FROM ( 
			    SELECT  a.Nro_asignacion, a.id_requerimiento, a.accesible, ap.nro_aprobacion, rq.prioridad 
			    FROM tb_asignacion_requerimiento a 
			    JOIN tb_aprobacion_requerimiento ap ON a.id_requerimiento=ap.id_requerimiento 
			    JOIN tb_requerimiento rq ON a.id_requerimiento=rq.id_requerimiento 
			    WHERE a.accesible='Si' AND a.id_programador = :id1
			    GROUP BY a.Nro_asignacion, a.id_requerimiento, ap.nro_aprobacion, rq.prioridad  ) req 
			    JOIN tb_tiempos t ON (req.Nro_asignacion = t.id_requerimiento and t.fase = 'desarrollo')
			    GROUP BY req.Nro_asignacion, req.id_requerimiento, req.nro_aprobacion,req.prioridad ", ['id1' => $user->id]);

		/*listdo de rq en pruebas*/
		$rqPrueba = DB::select("
			SELECT * FROM tb_solucion_requerimiento 
			JOIN tb_asignacion_requerimiento a ON tb_solucion_requerimiento.id_asignacion=a.Nro_asignacion 
			JOIN tb_requerimiento ON a.id_requerimiento=tb_requerimiento.id_requerimiento 
			WHERE tb_solucion_requerimiento.accesible='Si' AND a.id_programador = :id1
			ORDER BY tb_requerimiento.id_requerimiento ASC", ['id1' => $user->id]);
		
		foreach ($rqAsig as $keya => $valuea){

			$tiempo1 = DB::select('SELECT * FROM tb_tiempos WHERE id_requerimiento = :id', ['id' => $valuea->Nro_asignacion]);
			
				if(!$tiempo1){
					$rqAsignados[]= array('Nro_asignacion' => $valuea->Nro_asignacion,
							'id_requerimiento' => $valuea->id_requerimiento,
							'id_gestor' => $valuea->id_gestor,
							'id_programador' => $valuea->id_programador,
							'fecha_asignacion' => $valuea->fecha_asignacion, 
							'hora_asignacion' => $valuea->hora_asignacion,
							'accesible' => $valuea->accesible,
							'nro_aprobacion' => $valuea->nro_aprobacion,
							'fecha_aprobacion' => $valuea->fecha_aprobacion,
							'hora_aprobacion' => $valuea->hora_aprobacion,
							'prioridad' => $valuea->prioridad
							);
				}
	    	}
		

		//dd($rqAsignados);

		$activo = array(
			'aprobado' => array('active' => '' , 'show_active' => '' ),
			'asignado' => array('active' => 'active' , 'show_active' => 'show active' ),  
			'desarrollo' => array('active' => '' , 'show_active' => '' ),
			'pruebas' => array('active' => '' , 'show_active' => '' ),
			'instalacion' => array('active' => '' , 'show_active' => '' ),
			'certificado' => array('active' => '' , 'show_active' => '' )
		);

		$arraycodFase = array(  'id_fase1' => 1, 
								'id_fase2' => 2,
								'id_fase3' => 3,
								'id_fase4' => 4,
								'id_fase5' => 5,
								'id_fase6' => 6,
								'id_fase7' => 7,
								'id_fase8' => 8,
								'id_fase9' => 9,
								'id_fase10' => 10 );

		$arrayTiempoFin = array();

		foreach ($rqAsig as $keyt => $valuet){

			$tiempo = DB::select('SELECT * FROM tb_tiempos WHERE id_requerimiento = :id', ['id' => $valuet->id_requerimiento]);
			
			if($tiempo){
				foreach ($tiempo as $keytt => $valuett){
					if($tiempo[$keytt]->fase == 'desarrollo' and $tiempo[$keytt]->estado == 'I'){
			    		$arrayTiempoFin[] =  array(
			    									'id_tp'=> $tiempo[$keytt]->id_tiempo,
			    									'id_rq'=> $tiempo[$keytt]->id_requerimiento
			    									);
		    		}	
				}
	    	}
		}

		$arrayAdjunto = array();
		$arrayAdj = array();
		$arrayAdjuntos = array();

		foreach ($rqAsig as $keyad => $valuead){
			$arrayAdj = array();
			
			$adjuntos = DB::table('tb_adjuntos')
			->where('id_requerimiento', '=' , $valuead->id_requerimiento)
			//->where('id_requerimiento', '=' , 3784)
			->where('id_etapa', '=' , 4)
			//->where('id_etapa', '=' , '1')
			->select('id_adjunto', 'id_requerimiento', 
					 'id_etapa', 'nombre', 
					 'fecha', 'hora')
			->get();

			if($adjuntos->isEmpty()){
				$adjVacio = array(
									'id_adjunto' => 0,
									'id_requerimiento' => $valuead->id_requerimiento,
									'id_etapa' => 4,
									'nombre' => '',
									'fecha' => '',
									'hora' => ''
								 );
				$arrayAdj[$valuead->id_requerimiento]= $adjVacio;

			}else{
				$arrayAdj[$valuead->id_requerimiento]= $adjuntos;
	
			}

			
			array_push($arrayAdjunto, $arrayAdj);
		}

		$nombreFuncion = 'revListarReqAsig';

		$rqAsignadosHisto = array();
		$arrayAdjuntos = json_decode(json_encode($arrayAdjunto));
		$rqAsignados = json_decode(json_encode($rqAsignados));

		return view('desarrollador.rq_desa')->with(compact('rqAsignados','rqAsignadosHisto','pagTitulo','activo','arraycodFase','arrayTiempoFin','rqDesarrollo','rqAsig','rqPrueba','arrayAdjuntos','nombreFuncion','req_id'));

	}

	public function revGuadarReqAsig(Request $request)
	{
		//print_r($request->name);

		echo "im in AjaxController index";//simplemente haremos que devuelva esto
 		return response()->json([
			    'success'   => true,
			    'message'   => 'Los datos se han guardado correctamente.' //Se recibe en la seccion "success", data.message
			    ], 200);

 		return response()->json([
            'exception' => false,
            'success'   => false,
            'message'   => $errors //Se recibe en la sección "error" de tu código JavaScript, y se almacena en la variable "info"
        ], 422);
	}

	public function revAsigTiempoReq(Request $request)
	{
		date_default_timezone_set('America/La_Paz');
		$ulitmoTiempo = $request->tiempo_id;
		$hora_calculada = '00:00:00';

		if($request->accion == 'insert'){
			
				$last = DB::table('tb_tiempos')->orderBy('id_tiempo','DESC')->first();
 				$ulitmoTiempo = $last->id_tiempo + 1;

 				$fecha = date('Y')."-".date('m')."-".date('d');
				$hora = date('H').":".date('i').":".date('s');
				
				$tiempo = new Tiempo();
				$tiempo->id_tiempo = $ulitmoTiempo;
				$tiempo->id_requerimiento = $request->name;
				$tiempo->fecha_ini = $fecha;
				$tiempo->hora_ini = $hora;
				$tiempo->fase = 'desarrollo';
				$tiempo->estado = 'I';

				if (!$tiempo->save()){
				 		return response()->json([
				            'exception' => false,
				            'success'   => false,
				            'message'   => 'No se pudo cargar los datos, intente nuevamente por favor!' //Se recibe en la sección "error" de tu código JavaScript, y se almacena en la variable "info"
				        ], 422);
				}else{
						//
				}
		}

		if($request->accion == 'update'){
			
			$fecha = date('Y')."-".date('m')."-".date('d');
			$hora = date('H').":".date('i').":".date('s');
			
			$tiempoU = Tiempo::find($request->tiempo_id);
			$tiempoU->fecha_fin = $fecha;
			$tiempoU->hora_fin = $hora;
			$tiempoU->estado = 'F';
			
			if (!$tiempoU->save()){
			 		return response()->json([
			            'exception' => false,
			            'success'   => false,
			            'message'   => 'No se pudo cargar los datos, intente nuevamente por favor!' //Se recibe en la sección "error" de tu código JavaScript, y se almacena en la variable "info"
			        ], 422);
			}
		}

		if($request->accion == 'updateI'){
			
			$fecha = date('Y')."-".date('m')."-".date('d');
			$hora = date('H').":".date('i').":".date('s');
			$ulitmoTiempo=0;
			$tiempoU = Tiempo::find($request->tiempo_id);
			$tiempoU->fecha_fin = $fecha;
			$tiempoU->hora_fin = $hora;
			$tiempoU->estado = 'F';
			
			if (!$tiempoU->save()){
			 		return response()->json([
			            'exception' => false,
			            'success'   => false,
			            'message'   => 'No se pudo cargar los datos, intente nuevamente por favor!' //Se recibe en la sección "error" de tu código JavaScript, y se almacena en la variable "info"
			        ], 422);
			}

		}
/*
		$arrayTiempoFin = array();

		foreach ($rqAsignados as $keyt => $valuet){

			$tiempo = DB::select('SELECT * FROM tb_tiempos WHERE id_requerimiento = :id', ['id' => $valuet->id_requerimiento]);

			if($tiempo){
		    	if($tiempo[0]->fase == 'desarrollo' and $tiempo[0]->estado == 'I'){
	             	
		    		$arrayTiempoFin[] =  array(
		    									'id_tp'=> $tiempo[0]->id_tiempo,
		    									'id_rq'=> $tiempo[0]->id_requerimiento,
		    									'f_ini'=> $tiempo[0]->fecha_ini,
		    									'h_ini'=> $tiempo[0]->hora_ini
		    									);
		    	}
	    	}
		}*/
	$rqTiempo = DB::table('tb_tiempos')
		->where('id_requerimiento', '=' , $request->name)
		->where('estado', '=' , 'F')
		->select('id_tiempo', 'id_requerimiento', 
				 'fecha_ini', 'hora_ini',
				 'fecha_fin', 'hora_fin', 
				 'fase', 'estado')
		->get();

	if(!$rqTiempo->isEmpty()){
		$hora_calculada = $this->calculoTiempo($rqTiempo);
	}else{
			if($request->tiempo_id != 0){
				$tiempoU = Tiempo::find($request->tiempo_id);
				if($tiempoU->estado == 'I' ){
					$ulitmoTiempo = $request->tiempo_id;
				}
			}
		 }

	//echo "im in AjaxController index";//simplemente haremos que devuelva esto
		return response()->json([
		    'success'   => true,
		    'hora_calculada' => $hora_calculada,
		    'tiempo_id' => $ulitmoTiempo,
		    'message'   => 'Los datos se han guardado correctamente.' //Se recibe en la seccion "success", data.message
		    ], 200);

		return response()->json([
		    'exception' => false,
		    'success'   => false,
		    'message'   => $errors //Se recibe en la sección "error" de tu código JavaScript, y se almacena en la variable "info"
			], 422);
	}

	public function calculoTiempo($arrayTiempo){

		foreach ($arrayTiempo as $key => $value) {
			
			$horaini = explode(":", $value->hora_ini);
			$horafin = explode(":", $value->hora_fin);
			$fechaini = explode("-", $value->fecha_ini);
			$fechafin = explode("-", $value->fecha_fin);

			$hora_ini = $horaini[0].','.$horaini[1].','.$horaini[2];
			$hora_fin = $horafin[0].','.$horafin[1].','.$horafin[2];

			$fecha_ini = $fechaini[1].','.$fechaini[2].','.$fechaini[0];
			$fecha_fin = $fechafin[1].','.$fechafin[2].','.$fechafin[0];

			$reqTiempo [] = array(   'hora_ini_0'=>$horaini[0],
									 'hora_ini_1'=>$horaini[1],
									 'hora_ini_2'=>$horaini[2],
									 'hora_fin_0'=>$horafin[0],
									 'hora_fin_1'=>$horafin[1],
									 'hora_fin_2'=>$horafin[2],
									 'fecha_ini_0'=>$fechaini[0],
									 'fecha_ini_1'=>$fechaini[1],
									 'fecha_ini_2'=>$fechaini[2],
									 'fecha_fin_0'=>$fechafin[0],
									 'fecha_fin_1'=>$fechafin[1],
									 'fecha_fin_2'=>$fechafin[2]
							  	  );
	
		}

		foreach ($reqTiempo as $key1 => $value1) {
			
			$timesIni = mktime($value1['hora_ini_0'],$value1['hora_ini_1'],$value1['hora_ini_2'],  $value1['fecha_ini_1'], $value1['fecha_ini_0'], $value1['fecha_ini_2']);
			$timesFin = mktime($value1['hora_fin_0'],$value1['hora_fin_1'],$value1['hora_fin_2'],  $value1['fecha_fin_1'], $value1['fecha_fin_0'], $value1['fecha_fin_2']);

			$calculoTime = $timesIni - $timesFin;
			$cal_hora = $calculoTime / (60 * 60);

			$ca_h[] = abs($calculoTime);

			//obtengo el valor absoulto
			$cal_hora = abs($cal_hora);
			//quito los decimales
			$cal_hora = round($cal_hora);
			$calculo_hora[]=$cal_hora;
		}

		$sumaCalculo=0;
		foreach ($ca_h as $key2 => $value2) {
			$sumaCalculo += $value2;
		}

		$hora_calculada = date('H:i:s', $sumaCalculo);

		return $hora_calculada;

	}

	
	public function revValidarRq(Request $request){

		$rqTiempo = DB::table('tb_tiempos')
			->where('id_requerimiento', '=' , $request->name)
			
			->select('id_tiempo', 'id_requerimiento', 
					 'fecha_ini', 'hora_ini',
					 'fecha_fin', 'hora_fin', 
					 'fase', 'estado')
			->get();

		$adjuntos = DB::table('tb_adjuntos')
			->where('id_requerimiento', '=' , $request->name)
			->where('id_etapa', '=' , '4')
			->select('id_adjunto', 'id_requerimiento', 
					 'id_etapa', 'nombre', 
					 'fecha', 'hora')
			->get();

		if($adjuntos->isEmpty()){
			return response()->json([
			            'exception' => false,
			            'success'   => false,
			            'message'   =>'Error: Por favor suba la ducumentacion del requerimiento!' 
			        ], 420);
		}
	
		if(!$rqTiempo->isEmpty()){
			foreach ($rqTiempo as $key => $value) {
			 	
			 	if($value->estado =='I'){
			 		return response()->json([
			            'exception' => false,
			            'success'   => false,
			            'message'   =>'Error: El requerimiento esta en desarrollo, para terminar presione el boton DETENER TAREA!' 
			        ], 422);
			 	}
			
			}
			return response()->json([
			    'success'   => true,
			    'message'   => 'El requerimiento .' //Se recibe en la seccion "success", data.message
			    ], 200);

		}else{
			    return response()->json([
			            'exception' => false,
			            'success'   => false,
			            'message'   =>'Error: Requerimiento no tiene horas trabajadas!' 
			        ], 421);
			 }
	}
	
	/**
		Download
	**/

	public function deleteFile(Request $request){
	
	  try {
	  		$request['id'] = 0;
            $adjunto = Adjunto::findOrFail($request['idAdjunto']);
            $request['id'] = base64_encode($adjunto->id_requerimiento);

		    $archivo_path = storage_path("app/files/{$adjunto->nombre}");

		    if (File::exists($archivo_path)) {

		       	$dfile = File::delete($archivo_path);

				if($dfile == true){

					if (!$adjunto->delete()){
						return redirect()->route($request['nombreFuncion'], $request['id'])->with(array(
						'error' => 'Error: no se pudo eliminar el archivo completamente!. Por favor comuniquese con administrador.'));
					}else{

						return redirect()->route($request['nombreFuncion'], $request['id'])->with(array(
						'message' => 'El archivo se elimino con exito!.'));
					}

				}else{
					return redirect()->route($request['nombreFuncion'], $request['id'])->with(array(
						'error' => 'Error: no se pudo eliminar el archivo!. Por favor intente nuevamente.'));
				}
		        unlink($archivo_path);
			}else{

				return redirect()->route($request['nombreFuncion'], $request['id'])->with(array(
						'error' => 'Error: No existe el archivo. Por favor comuniquese con el administrador'));
			}
		    
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            abort(404);
        }

    }																		


    public function uploadFile(Request $request){

    	date_default_timezone_set('America/La_Paz');
		
		if(request()->file('rqdoc')){

			$last = DB::table('tb_adjuntos')->orderBy('id_adjunto','DESC')->first();
 			$ulitmoAdjunto = $last->id_adjunto + 1;

        	$file = request()->file('rqdoc');
			$nombreArchivo = $request->idrq.'_'.$request->etapa.'_'.$ulitmoAdjunto.'-'.$file->getClientOriginalName();

	        $subirArchivo = $request->rqdoc->storeAs('files', $nombreArchivo);

	        $existeArchivo = Storage::exists('files/'.$nombreArchivo);

	        if(Storage::exists('files/'.$nombreArchivo) == true ){

				$adjunto = new Adjunto();
				$fecha = date('Y')."-".date('m')."-".date('d');
				$hora = date('H').":".date('i').":".date('s');
				$adjunto->id_adjunto = $ulitmoAdjunto;
				$adjunto->id_requerimiento = $request->idrq;
				$adjunto->id_etapa = $request->etapa;
				$adjunto->nombre = $nombreArchivo;
				$adjunto->fecha = $fecha;
				$adjunto->hora = $hora;
				
				if (!$adjunto->save()){
					return redirect()->route($request->nombreFuncion, base64_encode($request->idrq))->with(array(
						'error' => 'Error: Al subir el archivo!. Por favor intente nuevamente.'));
				
				}else{

					return redirect()->route($request->nombreFuncion, base64_encode($request->idrq))->with(array(
						'message' => 'El archivo fue subido con exito!.'));
				}

			}else{

				 return redirect()->route($request->nombreFuncion, base64_encode($request->idrq))->with(array(
						'error' => 'Error: Al subir el archivo!.Por favor intente nuevamente.'));
			}
		}else{

			 return redirect()->route($request->nombreFuncion, base64_encode($request->idrq))->with(array(
						'error' => 'Error: Al subir el archivo!.Por favor intente nuevamente.'));
		}
    }

    public function revSolucionTarea(Request $request){

/*    	$last = DB::table('tb_solucion_requerimiento')->orderBy('id_solucion','DESC')->first();
 		$ulitmaSolucion = $last->id_tiempo + 1;*/
 		$rqTiempo = DB::table('tb_tiempos')
			->where('id_requerimiento', '=' , $request->idRq)
			->select('id_tiempo', 'id_requerimiento', 
					 'fecha_ini', 'hora_ini',
					 'fecha_fin', 'hora_fin', 
					 'fase', 'estado')
			->orderBy('id_tiempo','ASC')
			->get();

 		$SolucionRq = new SolucionRq();
		$fecha = date('Y')."-".date('m')."-".date('d');
		$hora = date('H').":".date('i').":".date('s');
		
		$SolucionRq->id_solucion = $request->idRq;
		$SolucionRq->id_asignacion = $request->idRq;
		$SolucionRq->secuencia = 1;
		$SolucionRq->fecha_inicio = $rqTiempo[0]->fecha_ini;
		$SolucionRq->hora_inicio = $rqTiempo[0]->hora_ini;
		$SolucionRq->fecha_fin = $fecha;
		$SolucionRq->hora_fin = $hora;
		$SolucionRq->descripcion = $request->texto_desc; 
		$SolucionRq->prog_clientes = $request->texto_cliente;
		$SolucionRq->prog_servidores = $request->texto_servidores; 
		$SolucionRq->tablas_mod = $request->texto_tabla;
		$SolucionRq->accesible = 'Si';
		//dd($SolucionRq);
		
		if($SolucionRq->save()){
			$rqAsignacion = AsignacionReq::find($request->idRq);
			$rqAsignacion->accesible = 'No';
			$rqAsignacion->save();
			
			return response()->json([
			    'success'   => true,
			    'message'   => 'El requerimiento ya se encuentra en la fase de Asignación de pruebas.' //Se recibe en la seccion "success", data.message
			    ], 200);

		}else{
			    return response()->json([
			            'exception' => false,
			            'success'   => false,
			            'message'   =>'Error: Requerimiento no tiene horas trabajadas!' 
			        ], 421);
			 }
    }

}
