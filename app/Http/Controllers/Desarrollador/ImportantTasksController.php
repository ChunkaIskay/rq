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

	// Revisar req asignados

	public function revListarReqAsig(Request $request){
	
		// Datos de la Asignacion del requerimiento
		
		$rqAsignados = DB::select('SELECT * FROM tb_asignacion_requerimiento JOIN tb_aprobacion_requerimiento ON tb_asignacion_requerimiento.id_requerimiento=tb_aprobacion_requerimiento.id_requerimiento WHERE tb_asignacion_requerimiento.accesible="Si"');


		$pagTitulo = 'Revisar estado de requerimientos';
	
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
		//$rqAsignadosHisto	ados = $this->paginacionManual($rqAsignados);
		//$rqAsignadosHisto = $this->paginacionManual($rqAsignadosHisto);
		$rqAsignadosHisto = array();
	
		return view('desarrollador.rq_estado')->with(compact('rqAsignados','rqAsignadosHisto','pagTitulo','activo','arraycodFase'));

	}

	public function revGuadarReqAsig(Request $request)
	{
		print_r($request->name);

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
	//	print_r($request->name);

		$rqTiempo = DB::table('tb_tiempos')
			//->where('id_requerimiento', '=' , $request->name)
			->where('id_requerimiento', '=' , 324)
	
			->select('id_tiempo', 'id_requerimiento', 
					 'fecha_ini', 'hora_ini',
					 'fecha_fin', 'hora_fin', 
					 'fase', 'estado')
			->get();


		foreach ($rqTiempo as $key => $value) {
			
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
	
		//echo "im in AjaxController index";//simplemente haremos que devuelva esto
 		return response()->json([
			    'success'   => true,
			    'hora_calculada' => $hora_calculada,
			    'message'   => 'Los datos se han guardado correctamente.' //Se recibe en la seccion "success", data.message
			    ], 200);

 		return response()->json([
            'exception' => false,
            'success'   => false,
            'message'   => $errors //Se recibe en la sección "error" de tu código JavaScript, y se almacena en la variable "info"
        ], 422);
	}
	

}
