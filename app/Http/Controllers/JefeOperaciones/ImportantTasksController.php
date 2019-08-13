<?php

namespace App\Http\Controllers\jefeOperaciones;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use Symfony\Component\HttpFoundation\Response;
//use Illuminate\Pagination\Paginator;
//use App\ImportantTask;
//use App\Http\Controllers\MenuRolController;
use App\AprobacionRq;
use App\Requerimiento;
use App\Adjunto;
use File;

use Illuminate\Support\Collection as Collection;


class ImportantTasksController extends Controller
{
 
       
	public function index(){   
		
		return view('jefe_operaciones.index')->with(compact('hola'));    
	}


	public function rqPendientes(){

		/*
		select * from tb_requerimiento join tb_usuario on tb_requerimiento.id_operador=tb_usuario.id_usuario where accesible='Si' order by fecha_solicitud asc
		*/

		$requerimientos = DB::table('tb_requerimiento')
		->join('users','tb_requerimiento.id_operador', '=' , 'users.id')
		->where('accesible', '=' , 'Si')
		->select('id_requerimiento', 'tipo_tarea', 'fecha_solicitud', 'tb_requerimiento.tipo', 'hora_solicitud', 'prioridad', 'descripcion', 'resultado', 'obs', 'accesible', 'motivo_cambio', 'name', 'ap_paterno')
		->orderBy('fecha_solicitud', 'ASC')
		->paginate(10);

		return view('jefe_operaciones.rq_pendientes')->with(compact('requerimientos'));

	}

	
	public function pendienteDetalle($id){

		$detalle = DB::table('tb_requerimiento')
		->join('tb_cliente','tb_requerimiento.id_cliente', '=' , 'tb_cliente.id_cliente')
		->join('users','tb_requerimiento.id_operador', '=' , 'users.id')
		->where('tb_requerimiento.id_requerimiento', '=' , $id)
		->select('id_requerimiento', 'tb_requerimiento.tipo', 
				 'tb_requerimiento.fecha_solicitud', 'tb_requerimiento.hora_solicitud', 
				 'tb_requerimiento.prioridad', 'tb_requerimiento.accesible', 
				 'tb_requerimiento.descripcion', 'tb_requerimiento.resultado', 
				 'users.name','users.ap_paterno','tb_cliente.nombre')
		->get();
		$adjuntos = DB::table('tb_adjuntos')
			->where('id_requerimiento', '=' , $id)
			->where('id_etapa', '=' , '1')
			->select('id_adjunto', 'id_requerimiento', 
					 'id_etapa', 'nombre', 
					 'fecha', 'hora')
			->get();

		//agregar nick del mètodo para subir y borrar archivos
		$nombreFuncion = 'pendDetalle';
		return view('jefe_operaciones.rq_pendetalle')->with(compact('detalle','adjuntos','nombreFuncion'));	

			
	}

	public function mostrar($id){

		$dl = File::find($id);
		return Storage::download($dl->path, $dl->title);

	}

	 protected function downloadFile($src){

        if(is_file($src)){
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $content_type = finfo_file($finfo, $src);
            finfo_close($finfo);
            $file_name = basename($src).PHP_EOL;
            $size = filesize($src);
            header("Content-Type: $content_type");
            header("Content-Disposition: attachment; filename=$file_name");
            header("Content-Transfer-Encoding: binary");
            header("Content-Length: $size");
            readfile($src);
            return true;
        } else{
            return false;
        }
    }

    public function download($id){

    	$adjunto = DB::table('tb_adjuntos')
		->where('id_adjunto', '=' , $id)
		->where('id_etapa', '=' , '1')
		->select('id_adjunto', 'id_requerimiento', 
				 'id_etapa', 'nombre')
		->get();

        if(!$this->downloadFile(public_path()."/files/".$adjunto[0]->nombre)){

            return redirect()->back();
        }
    }

    public function aprobarRqPen(Request $request){

       // $this->validate($request, Contract::$rules, Contract::$messages);
		$aprobacionExiste = AprobacionRq::find($request->input('id'));
		
		if($aprobacionExiste){
			return redirect()->route('rqPendientes')->with(array(
    		'error' => 'Error: El querimiento ya esta aprobado.!!'
    		));
		}else{
				$aprobacion = new AprobacionRq();
				$fecha = date('Y')."-".date('m')."-".date('d');
				$hora = date('H').":".date('i').":".date('s');
				$aprobacion->id_requerimiento = $request->input('id');
				$aprobacion->nro_aprobacion = $request->input('id');
				$aprobacion->fecha_aprobacion = $fecha;
				$aprobacion->hora_aprobacion = $hora;
				$aprobacion->accesible = 'Si';

				if (!$aprobacion->save()){
					return redirect()->route('rqPendientes')->with(array(
						'error' => 'Error: El requerimiento no fue aprobado, consulte con el administrador.!!'
					));
				}else{
						$rqUpdate = Requerimiento::find($request->input('id'));
						$rqUpdate->accesible = 'No';
						$rqUpdate->save();

					return redirect()->route('rqPendientes')->with(array(
						'message' => 'Gracias! El requermiento fue aprobodo con exito.'));
				}
			}
    }

    public function deleteFile(Request $request){
	
	
	  try {
            $adjunto = Adjunto::findOrFail($request['idAdjunto']);

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


    function uploadFile(Request $request){

		if(request()->file('rqdoc')){

        	$adjuntos = Adjunto::all();
        	$ulitmoAdjunto =  $adjuntos->last()->id_adjunto + 1;

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
					return redirect()->route($request->nombreFuncion, $request->idrq)->with(array(
						'error' => 'Error: Al subir el archivo!. Por favor intente nuevamente.'));
				
				}else{
						//$rqUpdate = Requerimiento::find($request->input('id'));
						//$rqUpdate->accesible = 'No';
						//$rqUpdate->save();

					return redirect()->route($request->nombreFuncion, $request->idrq)->with(array(
						'message' => 'El archivo fue subido con exito!.'));
				}

			}else{
				 return redirect()->route($request->nombreFuncion, $request->idrq)->with(array(
						'error' => 'Error: Al subir el archivo!.Por favor intente nuevamente.'));
			}
		
		   
		}else{
			 return redirect()->route($request->nombreFuncion, $request->idrq)->with(array(
						'error' => 'Error: Al subir el archivo!.Por favor intente nuevamente.'));
		}

    }

    public function rqPendientesInstalar(){

   		$pendInstalar = DB::select('SELECT * FROM (
            SELECT t1.id_asig_instal, t1.id_solucion, t1.id_programador, t1.id_usuario as usuario_asig_por, CONCAT(t1.nombre," " ,t1.ap_paterno) as asig_por,  u.id_usuario as usuario_asig_a, CONCAT(u.nombre," " ,u.ap_paterno) as asig_a FROM (
                        SELECT * FROM tb_asignacion_instal_req
                        JOIN tb_usuario ON tb_asignacion_instal_req.id_gestor=tb_usuario.id_usuario
                        WHERE tb_asignacion_instal_req.accesible = "Au"
                        ORDER BY tb_asignacion_instal_req.id_asig_instal ASC
            ) t1
            INNER JOIN tb_usuario u on t1.id_programador = u.id_usuario
            ORDER by t1.id_asig_instal ASC
		    ) asig_t1
		    INNER JOIN 
		    (
		        SELECT r.id_requerimiento, r.tipo, r.tipo_tarea, r.prioridad, si.fecha_asig_instal, si.hora_asig_instal, s.id_solucion from tb_requerimiento r 
		        join tb_asignacion_requerimiento a on r.id_requerimiento=a.id_requerimiento 
		        join tb_solucion_requerimiento s on a.Nro_asignacion=s.id_asignacion 
		        JOIN tb_asignacion_instal_req si on (s.id_solucion = si.id_solucion and si.accesible = "Au")
		    ) req_t2 on asig_t1.id_solucion = req_t2.id_solucion');
		    
		//dd($pendInstalar);
    	//$pendInstalar = Paginator::make($pendInstalar, count($pendInstalar), $results_per_page);
    	$perPage=20;
        $currentPage = 0;
    	$pagedData = array_slice($pendInstalar, $currentPage * $perPage, $perPage);
    	
    	$pendInstalar = new \Illuminate\Pagination\LengthAwarePaginator($pagedData, count($pendInstalar), $perPage);

    	//$pendInstalar = collect($pendInstalar1)->get();

    	return view('jefe_operaciones.rq_pendientes_instalar')->with(compact('pendInstalar'));


    }

    public function rqPendInstDetalle($idrq){

    	$adjuntos = "";
    	$adjuntoSol = "";

		$detalle = DB::select('SELECT air.id_asig_instal,air.id_gestor,air.id_solucion,
			air.id_programador,air.fecha_asig_instal,air.hora_asig_instal,air.accesible asig_ins_rq_acces,
			sr.id_asignacion,sr.secuencia,sr.fecha_inicio,sr.hora_inicio,sr.fecha_fin,sr.hora_fin,
			sr.descripcion sol_desc, sr.prog_clientes, sr.prog_servidores,sr.accesible sol_req_acces,ar.Nro_asignacion, 
			ar.fecha_asignacion, ar.hora_asignacion, ar.accesible asig_req_acces, 
			r.tipo, r.tipo_tarea, r.fecha_solicitud,r.hora_solicitud, r.id_cliente,r.id_operador,
			r.prioridad,r.descripcion rq_desc, r.id_requerimiento, r.resultado, r.accesible req_acces, u.name, u.ap_paterno, u.ap_materno
			FROM tb_asignacion_instal_req air
			JOIN tb_solucion_requerimiento sr on air.id_solucion=sr.id_solucion 
			JOIN tb_asignacion_requerimiento ar on sr.id_asignacion=ar.Nro_asignacion 
			JOIN tb_requerimiento r on ar.id_requerimiento=r.id_requerimiento 
			JOIN users u on r.id_operador=u.id
			WHERE air.accesible="Au" 
			AND r.id_requerimiento = :id', ['id' => $idrq]);

	//	echo "<pre>";print_r($detalle); exit;
			$idAsigInstal = $detalle[0]->id_asig_instal;#id de la asignacion a 
			
            $idSolucion = $detalle[0]->id_solucion;#id de la solucion
            $idAsignacion = $detalle[0]->Nro_asignacion; #id de la asignacion
            $idRqto = $detalle[0]->id_requerimiento; # 29, id requerimiento
            $descSolicitud = $detalle[0]->rq_desc;#descripcion de la solicitud
            $descSolucion = $detalle[0]->sol_desc;#descripcion de la solucion, NOTA: se cabio de 15 a 14 en Mantenimiento
            $fechaSolu = $detalle[0]->fecha_fin;#fecha solucion
            $horaSolu = $detalle[0]->hora_fin;#hora solucion
            $idDesa = $detalle[0]->id_programador;#id del desarrollador
            $secuencial = $detalle[0]->secuencia;

            $adjuntos = DB::table('tb_adjuntos')
			->where('id_requerimiento', '=' , $idrq)
			->where('id_etapa', '=' , '1')
			->select('id_adjunto', 'id_requerimiento', 
					 'id_etapa', 'nombre', 
					 'fecha', 'hora')
			->get();
		
			$responsableSolu = DB::table('users')
			->where('tipo', '=' , 'Desarrollador')
			->where('id', '=' , $detalle[0]->id_programador)
			->select('id','name', 'ap_paterno')
			->get();


			//agregar nick del mètodo para subir y borrar archivos
			$nombreFuncion = 'pendInstalarDetelle';

			// Datos de la aprobación del requermiento
			$aproRq = DB::select('SELECT * FROM tb_aprobacion_requerimiento WHERE id_requerimiento = :id', ['id' => $idrq]);

			// Datos de la Asignacion del requerimiento
			$asigRq = DB::select('SELECT ap.Nro_asignacion, ap.id_requerimiento, ap.id_gestor, ap.id_programador, ap.fecha_asignacion, ap.hora_asignacion, ap.asignado_por, CONCAT(us.name ," ",us.ap_paterno) as asignado_a  FROM (
				SELECT Nro_asignacion, id_requerimiento, id_gestor, id_programador, fecha_asignacion, hora_asignacion, CONCAT(name, " ", ap_paterno) asignado_por 
				FROM tb_asignacion_requerimiento ar
    			JOIN users u on ar.id_gestor=u.id 
    			WHERE ar.id_requerimiento = :id
				)ap
				JOIN users us on ap.id_programador = us.id', ['id' => $idrq]);
			
			// Datos de la solucion y adjunto

			$adjuntoSol =  DB::select('SELECT * FROM tb_adjuntos where id_etapa = 4 and id_requerimiento = :id', ['id' => $idrq]);

			 // Datos de la certificacion Pre-instalacion
			
			
			$adjuntoCertiPreInst = DB::table('tb_adjuntos')
			->where('id_requerimiento', '=' , $idrq)
			->where('id_etapa', '=' , '5')
			->select('id_adjunto', 'id_requerimiento', 
					 'id_etapa', 'nombre', 
					 'fecha', 'hora')
			->get();

			//dd($adjuntoCertiPreInst);
			// Datos de la asignacion a instalacion

			$asigInstRq = DB::select('SELECT ag.id_asig_instal, ag.id_gestor, ag.id_solucion, ag.id_programador, ag.fecha_asig_instal, ag.hora_asig_instal, CONCAT(ag.name, " ", ag.ap_paterno) as asignado_por , CONCAT(us.name, " ", us.ap_paterno) as asignado_a
			 	FROM (
			 	 SELECT * FROM tb_asignacion_instal_req sr 
			 	 JOIN users u on sr.id_gestor = u.id 
			 	 WHERE sr.id_solucion = :id 
			 	) ag 
			 	JOIN users us on ag.id_programador = us.id', ['id' => $idrq]);
			
			// Datos de certificación  pre-instalación
			$certiPreInst = DB::select('SELECT id_certificacion, id_solucion, id_operador, fecha_certificacion, hora_certificacion, detalle_certificacion, detalle_funcionalidades, accesible, us.name, us.ap_paterno 
				FROM tb_certificacion ce
				JOIN users us on ce.id_operador=us.id where id_solucion = :id', ['id' => $idSolucion]);
			
			return view('jefe_operaciones.rq_pend_inst_detalle')->with(compact('detalle','adjuntos','nombreFuncion','aproRq','asigRq','responsableSolu','adjuntoCertiPreInst','adjuntoSol','certiPreInst','adjuntoCertiPreInst','asigInstRq')); 	

    }


     public function rqExaminarList(){

   		$rqexaminar = DB::select('SELECT * FROM tb_requerimiento 
   			JOIN tb_cliente ON tb_requerimiento.id_cliente=tb_cliente.id_cliente 
   			JOIN tb_usuario ON tb_requerimiento.id_operador=tb_usuario.id_usuario');
   		$pagTitulo = "Examinar requerimiento";
   		$pag = "examinar";

		return view('jefe_operaciones.rq_examinar_listado')->with(compact('rqexaminar','pagTitulo','pag'));

	//dd($pendInstalar);
	//$pendInstalar = Paginator::make($pendInstalar, count($pendInstalar), $results_per_page);
	//$perPage=20;
    	
    //    $currentPage = 0;
    //	$pagedData = array_slice($pendInstalar, $currentPage * $perPage, $perPage);
    //	$pendInstalar = new \Illuminate\Pagination\LengthAwarePaginator($pagedData, count($pendInstalar), $perPage);

    	//$pendInstalar = collect($pendInstalar1)->get();

    //	return view('jefe_operaciones.rq_pendientes_instalar')->with(compact('pendInstalar'));

    }

    public function urlAntes($path,$bsearch){

		//$path = $this->urlAntes(redirect()->getUrlGenerator()->previous(),'rq-examinar');

    	$urlexiste = 0;
    	$arrayUrl = explode('/', $path);
		
		foreach ($arrayUrl as $key => $value) {
			if($value == $bsearch ){
				$urlexiste = 1;
			}
		}
	
		return $urlexiste;
    }

    public function rqExaminarDetalle($id){

		$detalle = DB::table('tb_requerimiento')
		->join('tb_cliente','tb_requerimiento.id_cliente', '=' , 'tb_cliente.id_cliente')
		->join('users','tb_requerimiento.id_operador', '=' , 'users.id')
		->where('tb_requerimiento.id_requerimiento', '=' , $id)
		->select('id_requerimiento', 'tb_requerimiento.tipo', 
				 'tb_requerimiento.fecha_solicitud', 'tb_requerimiento.hora_solicitud', 
				 'tb_requerimiento.prioridad', 'tb_requerimiento.accesible', 
				 'tb_requerimiento.descripcion', 'tb_requerimiento.resultado', 
				 'users.name','users.ap_paterno','tb_cliente.nombre')
		->get();

//		$urlexiste = $this->urlAntes(redirect()->getUrlGenerator()->previous(),'rq-examinar');

		$adjuntos = DB::table('tb_adjuntos')
		->where('id_requerimiento', '=' , $id)
		->select('id_adjunto', 'id_requerimiento', 
				 'id_etapa', 'nombre', 
				 'fecha', 'hora')
		->get();

		//agregar nick del mètodo para subir y borrar archivos
		$nombreFuncion = 'pendDetalle';

			return view('jefe_operaciones.rq_examinar_detalle')->with(compact('detalle','adjuntos','nombreFuncion'));
    }


	public function rqEditar($id){

		$detalle = DB::table('tb_requerimiento')
		->join('tb_cliente','tb_requerimiento.id_cliente', '=' , 'tb_cliente.id_cliente')
		->join('users','tb_requerimiento.id_operador', '=' , 'users.id')
		->where('tb_requerimiento.id_requerimiento', '=' , $id)
		->whereOr('accesible','!=','No')
		->select('id_requerimiento', 'tb_requerimiento.tipo', 'tb_requerimiento.tipo_tarea',
				 'tb_requerimiento.fecha_solicitud', 'tb_requerimiento.hora_solicitud', 
				 'tb_requerimiento.prioridad', 'tb_requerimiento.accesible', 
				 'tb_requerimiento.descripcion', 'tb_requerimiento.resultado', 
				 'users.name','users.ap_paterno','tb_cliente.nombre')
		->get();
//		$urlexiste = $this->urlAntes(redirect()->getUrlGenerator()->previous(),'rq-examinar');

		$fechasOpDe = DB::table('tb_req_fecha')
		->where('id_requerimiento', '=' , $id)
		->select('fecha_plan_op', 'fecha_plan_de')
		->get();
		//dd($fechasOpDe);

		$arrayPrioridad = array(
								'0.Critico' => 'Critico', 
								'1.Muy Urgente' => 'Muy Urgente', 
								'2.Urgente' => 'Urgente', 
								'3.Cronograma' => 'Cronograma', 
								'3.Media' => 'Media', 
								'3.Medio' => 'Medio', 
								'3.Urgente' => 'Urgente', 
								'4.Baja' => 'Baja',
								'4.Bajo' => 'Bajo', 
								'8.Suspendido'=> 'Suspendido',
								'2.Urgente' => 'Urgente', 
								'3.Cronograma' => 'Cronograma', 
								'3.Media' => 'Media', 
								'3.Medio' => 'Medio', 
					);
		//dd($arrayPrioridad);
		$adjuntos = DB::table('tb_adjuntos')
		->where('id_requerimiento', '=' , $id)
		->where('id_etapa', '=' , '1')
		->select('id_adjunto', 'id_requerimiento', 
				 'id_etapa', 'nombre', 
				 'fecha', 'hora')
		->get();
		//dd($fechasOpDe);	

		//agregar nick del mètodo para subir y borrar archivos
		$nombreFuncion = 'pendDetalle';

		return view('jefe_operaciones.rq_editar')->with(compact('detalle','fechasOpDe','arrayPrioridad','adjuntos'));
    }

	public function rqActualizar(Request $request, $id){
    	
      //  $this->validate($request, Contract::$rules, Contract::$messages);
        $requerimiento = Requerimiento::find($id);
        $requerimiento->prioridad = $request->prioridad;
        $requerimiento->resultado = $request->desc_deseado;
        $requerimiento->descripcion = $request->descripcion;

    	$requerimiento->save();  // update 

    	return redirect()->route('examinarList')->with(array(
    		'message' => 'El requerimiento se modifico exitosamente.!!'
    	));
    }

    public function rqReasignarList(){

   		$rqReasignar = DB::select('SELECT * FROM tb_requerimiento r
		
		JOIN tb_aprobacion_requerimiento s on r.id_requerimiento = s.id_requerimiento');

		return view('jefe_operaciones.rq_reasignar_listado')->with(compact('rqReasignar'));

	}

	public function rqReasignarEditar($id){

		$detalle = DB::table('tb_requerimiento')
		->join('tb_cliente','tb_requerimiento.id_cliente', '=' , 'tb_cliente.id_cliente')
		->join('users','tb_requerimiento.id_operador', '=' , 'users.id')
		->where('tb_requerimiento.id_requerimiento', '=' , $id)
		->whereOr('accesible','!=','No')
		->select('id_requerimiento', 'tb_requerimiento.tipo', 'tb_requerimiento.tipo_tarea',
				 'tb_requerimiento.fecha_solicitud', 'tb_requerimiento.hora_solicitud', 
				 'tb_requerimiento.prioridad', 'tb_requerimiento.accesible', 
				 'tb_requerimiento.descripcion', 'tb_requerimiento.resultado', 
				 'tb_requerimiento.id_operador',
				 'users.name','users.ap_paterno','tb_cliente.nombre')
		->get();
//		$urlexiste = $this->urlAntes(redirect()->getUrlGenerator()->previous(),'rq-examinar');

		$fechasOpDe = DB::table('tb_req_fecha')
		->where('id_requerimiento', '=' , $id)
		->select('fecha_plan_op', 'fecha_plan_de')
		->get();
		
		
		$adjuntos = DB::table('tb_adjuntos')
		->where('id_requerimiento', '=' , $id)
		->where('id_etapa', '=' , '1')
		->select('id_adjunto', 'id_requerimiento', 
				 'id_etapa', 'nombre', 
				 'fecha', 'hora')
		->get();

		$operador = DB::table('users')
		->where('tipo', '=' , 'Operador')
		->select('id','name','ap_paterno','tipo','activo')
		->get();

		return view('jefe_operaciones.rq_reasignar_editar')->with(compact('detalle','fechasOpDe','operador'));
    
    }

    public function rqReasignarActualizar(Request $request, $id){
      //  $this->validate($request, Contract::$rules, Contract::$messages);
        $requerimiento = Requerimiento::find($id);
        $requerimiento->id_operador = $request->id_ope;
       
       	$operador = DB::table('users')
		->where('tipo', '=' , 'Operador')
		->where('id','=',$request->id_ope)
		->select('id','name','ap_paterno','tipo','activo')
		->get();

    	$requerimiento->save();  // update 

    	return redirect()->route('rqReasignar')->with(array(
    		'message' => 'Se reasigno con exito el requerimiento, al operador '. $operador[0]->name.' '. $operador[0]->ap_paterno .'.!!'
    	));
    }

     public function rqPrioridadListado(){

   		$rqexaminar = DB::select('SELECT * FROM tb_requerimiento 
   			JOIN tb_cliente ON tb_requerimiento.id_cliente=tb_cliente.id_cliente 
   			JOIN tb_usuario ON tb_requerimiento.id_operador=tb_usuario.id_usuario');
   		$pagTitulo = "Cambiar prioridad a requerimiento";
   		$pag = "prioridad";

		return view('jefe_operaciones.rq_examinar_listado')->with(compact('rqexaminar','pagTitulo','pag'));

	//dd($pendInstalar);
	//$pendInstalar = Paginator::make($pendInstalar, count($pendInstalar), $results_per_page);
	//$perPage=20;
    	
    //    $currentPage = 0;
    //	$pagedData = array_slice($pendInstalar, $currentPage * $perPage, $perPage);
    //	$pendInstalar = new \Illuminate\Pagination\LengthAwarePaginator($pagedData, count($pendInstalar), $perPage);

    	//$pendInstalar = collect($pendInstalar1)->get();

    //	return view('jefe_operaciones.rq_pendientes_instalar')->with(compact('pendInstalar'));

    }

    public function rqPrioridadEditar($id){

		$detalle = DB::table('tb_requerimiento')
		->join('tb_cliente','tb_requerimiento.id_cliente', '=' , 'tb_cliente.id_cliente')
		->join('users','tb_requerimiento.id_operador', '=' , 'users.id')
		->where('tb_requerimiento.id_requerimiento', '=' , $id)
		->whereOr('accesible','!=','No')
		->select('id_requerimiento', 'tb_requerimiento.tipo', 'tb_requerimiento.tipo_tarea',
				 'tb_requerimiento.fecha_solicitud', 'tb_requerimiento.hora_solicitud', 
				 'tb_requerimiento.prioridad', 'tb_requerimiento.accesible', 
				 'tb_requerimiento.descripcion', 'tb_requerimiento.resultado', 
				 'users.name','users.ap_paterno','tb_cliente.nombre')
		->get();
//		$urlexiste = $this->urlAntes(redirect()->getUrlGenerator()->previous(),'rq-examinar');

		$fechasOpDe = DB::table('tb_req_fecha')
		->where('id_requerimiento', '=' , $id)
		->select('fecha_plan_op', 'fecha_plan_de')
		->get();

		$arrayPrioridad = array(
								'0.Critico' => 'Critico', 
								'1.Muy Urgente' => 'Muy Urgente', 
								'2.Urgente' => 'Urgente', 
								'3.Cronograma' => 'Cronograma', 
								'3.Media' => 'Media', 
								'3.Medio' => 'Medio', 
								'3.Urgente' => 'Urgente', 
								'4.Baja' => 'Baja',
								'4.Bajo' => 'Bajo', 
								'8.Suspendido'=> 'Suspendido',
								'2.Urgente' => 'Urgente', 
								'3.Cronograma' => 'Cronograma', 
								'3.Media' => 'Media', 
								'3.Medio' => 'Medio', 
					);

		//agregar nick del mètodo para subir y borrar archivos
		$nombreFuncion = 'pendDetalle';

		return view('jefe_operaciones.rq_prioridad_editar')->with(compact('detalle','fechasOpDe','arrayPrioridad'));
    }

     public function rqPrioridadActualizar(Request $request, $id){
      //  $this->validate($request, Contract::$rules, Contract::$messages);
     	
        $requerimiento = Requerimiento::find($id);
        $requerimiento->prioridad = $request->prioridad;
        $requerimiento->motivo_cambio = $request->desc_obs;
       
    	$requerimiento->save();  // update 

    	return redirect()->route('rqPrioridadListado')->with(array(
    		'message' => 'Cambio con exito la prioridad del requerimiento '. $id.'.!!'
    	));
    }

    public function rqEstadoAll(){
		
		// Datos de la Asignacion del requerimiento
		
		$rqAprobadosRecien = DB::select('SELECT * FROM tb_aprobacion_requerimiento JOIN tb_requerimiento ON tb_aprobacion_requerimiento.id_requerimiento=tb_requerimiento.id_requerimiento WHERE tb_aprobacion_requerimiento.accesible="Si" ORDER BY tb_aprobacion_requerimiento.id_requerimiento ASC');

		$rqAprobadosHisto = DB::select('SELECT * FROM tb_aprobacion_requerimiento JOIN tb_requerimiento ON tb_aprobacion_requerimiento.id_requerimiento=tb_requerimiento.id_requerimiento WHERE tb_aprobacion_requerimiento.accesible="No" ORDER BY tb_aprobacion_requerimiento.id_requerimiento ASC');

		$activo = array(
			'aprobado' => array('active' => 'active' , 'show_active' => 'show active' ),
			'asignado' => array('active' => '' , 'show_active' => '' ),  
			'desarrollo' => array('active' => '' , 'show_active' => '' ),
			'pruebas' => array('active' => '' , 'show_active' => '' ),
			'instalacion' => array('active' => '' , 'show_active' => '' ),
			'certificado' => array('active' => '' , 'show_active' => '' )
		);	
		$pagTitulo = "Revisión estado requerimiento";
		//$rqAprobadosRecien = $this->paginacionManual($rqAprobadosRecien);
		
		return view('jefe_operaciones.rq_estado')->with(compact('rqAprobadosRecien','rqAprobadosHisto','pagTitulo','activo'));

	}


	 public function rqEstadoAsig(Request $request){
	
		// Datos de la Asignacion del requerimiento
		
		$rqAsignados = DB::select('SELECT * FROM tb_asignacion_requerimiento JOIN tb_aprobacion_requerimiento ON tb_asignacion_requerimiento.id_requerimiento=tb_aprobacion_requerimiento.id_requerimiento WHERE tb_asignacion_requerimiento.accesible="Si"');
		
		$rqAsignadosHisto = DB::select("SELECT * FROM tb_asignacion_requerimiento JOIN tb_aprobacion_requerimiento ON tb_asignacion_requerimiento.id_requerimiento=tb_aprobacion_requerimiento.id_requerimiento WHERE tb_asignacion_requerimiento.accesible='No'");	
		
		$pagTitulo = 'Revisar estado de requerimientos';
	
		$activo = array(
			'aprobado' => array('active' => '' , 'show_active' => '' ),
			'asignado' => array('active' => 'active' , 'show_active' => 'show active' ),  
			'desarrollo' => array('active' => '' , 'show_active' => '' ),
			'pruebas' => array('active' => '' , 'show_active' => '' ),
			'instalacion' => array('active' => '' , 'show_active' => '' ),
			'certificado' => array('active' => '' , 'show_active' => '' )
		);

		$rqAsignados = $this->paginacionManual($rqAsignados);
		$rqAsignadosHisto = $this->paginacionManual($rqAsignadosHisto);

	
		return view('jefe_operaciones.rq_estado')->with(compact('rqAsignados','rqAsignadosHisto','pagTitulo','activo'));

	}

	 public function rqEstadoDesa(Request $request){
	
		// Datos de la Asignacion del requerimiento
		
		$rqDesarrollo = DB::select("SELECT * FROM tb_solucion_requerimiento JOIN tb_asignacion_requerimiento ON tb_solucion_requerimiento.id_asignacion=tb_asignacion_requerimiento.Nro_asignacion JOIN tb_requerimiento ON tb_asignacion_requerimiento.id_requerimiento=tb_requerimiento.id_requerimiento WHERE tb_solucion_requerimiento.accesible='Si' ORDER BY tb_requerimiento.id_requerimiento ASC");	

		$rqDesarrolloHisto = DB::select("SELECT * FROM tb_solucion_requerimiento JOIN tb_asignacion_requerimiento ON tb_solucion_requerimiento.id_asignacion=tb_asignacion_requerimiento.Nro_asignacion JOIN tb_requerimiento ON tb_asignacion_requerimiento.id_requerimiento=tb_requerimiento.id_requerimiento JOIN tb_certificacion ON tb_solucion_requerimiento.id_solucion=tb_certificacion.id_solucion WHERE tb_solucion_requerimiento.accesible='No' ORDER BY tb_requerimiento.id_requerimiento ASC");
	
		
		$pagTitulo = 'Revisar estado de requerimientos';
	
		$activo = array(
			'aprobado' => array('active' => '' , 'show_active' => '' ),
			'asignado' => array('active' => '' , 'show_active' => '' ),  
			'desarrollo' => array('active' => 'active' , 'show_active' => 'show active' ),
			'pruebas' => array('active' => '' , 'show_active' => '' ),
			'instalacion' => array('active' => '' , 'show_active' => '' ),
			'certificado' => array('active' => '' , 'show_active' => '' )
		);

		return view('jefe_operaciones.rq_estado')->with(compact('rqDesarrollo','rqDesarrolloHisto','pagTitulo','activo'));

	}

	public function rqEstadoPruebas(Request $request){
	
		// Datos de la Asignacion del requerimiento
		
		$rqPruebas = DB::select("SELECT tb_requerimiento.id_requerimiento, tb_solucion_requerimiento.fecha_fin, tb_solucion_requerimiento.hora_fin 
			FROM tb_solucion_requerimiento 
			JOIN tb_asignacion_requerimiento ON tb_solucion_requerimiento.id_asignacion=tb_asignacion_requerimiento.Nro_asignacion 
			JOIN tb_requerimiento ON tb_asignacion_requerimiento.id_requerimiento=tb_requerimiento.id_requerimiento 
			WHERE tb_solucion_requerimiento.accesible='Si' 
			ORDER BY tb_requerimiento.id_requerimiento ASC");

		$rqPruebasHisto = DB::select("SELECT  s.id_solucion,s.id_asignacion, s.fecha_inicio, s.hora_inicio,s.fecha_fin, s.hora_fin, s.id_requerimiento, c.fecha_certificacion, c.hora_certificacion FROM (
			    SELECT sr.id_solucion,sr.id_asignacion, sr.fecha_inicio, sr.hora_inicio, tb_requerimiento.id_requerimiento, sr.fecha_fin, sr.hora_fin FROM (
			     	SELECT * FROM tb_solucion_requerimiento  WHERE accesible = 'No'
			     ) sr 
			    JOIN tb_asignacion_requerimiento ON sr.id_asignacion=tb_asignacion_requerimiento.Nro_asignacion 
			    JOIN tb_requerimiento ON tb_asignacion_requerimiento.id_requerimiento=tb_requerimiento.id_requerimiento
			) s
			JOIN tb_certificacion as c ON s.id_solucion=c.id_solucion
			ORDER BY s.id_requerimiento ASC");
	
		
		$pagTitulo = 'Revisar estado de requerimientos';
	
		$activo = array(
			'aprobado' => array('active' => '' , 'show_active' => '' ),
			'asignado' => array('active' => '' , 'show_active' => '' ),  
			'desarrollo' => array('active' => '' , 'show_active' => '' ),
			'pruebas' => array('active' => 'active' , 'show_active' => 'show active' ),
			'instalacion' => array('active' => '' , 'show_active' => '' ),
			'certificado' => array('active' => '' , 'show_active' => '' )
		);

	return view('jefe_operaciones.rq_estado')->with(compact('rqPruebas','rqPruebasHisto','pagTitulo','activo'));

	}

	public function rqEstadoInst(Request $request){
	
		// Datos de la Asignacion del requerimiento
		
		$rqInstalacion = DB::select("SELECT tb_requerimiento.id_requerimiento, tb_asignacion_instal_req.fecha_asig_instal, tb_asignacion_instal_req.hora_asig_instal, tb_instalacion.fecha_instal, tb_instalacion.hora_instal   FROM tb_instalacion 
			JOIN tb_asignacion_instal_req ON tb_instalacion.id_asig_instal=tb_asignacion_instal_req.id_asig_instal 
			JOIN tb_solucion_requerimiento ON tb_asignacion_instal_req.id_solucion=tb_solucion_requerimiento.id_solucion 
			JOIN tb_asignacion_requerimiento ON tb_solucion_requerimiento.id_asignacion=tb_asignacion_requerimiento.Nro_asignacion 
			JOIN tb_requerimiento ON tb_asignacion_requerimiento.id_requerimiento=tb_requerimiento.id_requerimiento 
			WHERE tb_instalacion.accesible='Si' ORDER BY tb_requerimiento.id_requerimiento ASC");

		$rqInstalacionHisto = DB::select("SELECT  asig_instal.id_requerimiento, asig_instal.fecha_asig_instal, asig_instal.hora_asig_instal , asig_instal.id_instalacion, tb_certificacion_online.fecha_certificacion, tb_certificacion_online.hora_certificacion FROM (
			SELECT tb_requerimiento.id_requerimiento, tb_asignacion_instal_req.fecha_asig_instal, tb_asignacion_instal_req.hora_asig_instal , tb_instalacion.id_instalacion
			FROM tb_instalacion join tb_asignacion_instal_req on tb_instalacion.id_asig_instal=tb_asignacion_instal_req.id_asig_instal 
			join tb_solucion_requerimiento on tb_asignacion_instal_req.id_solucion=tb_solucion_requerimiento.id_solucion 
			JOIN tb_asignacion_requerimiento ON tb_solucion_requerimiento.id_asignacion=tb_asignacion_requerimiento.Nro_asignacion 
			JOIN tb_requerimiento ON tb_asignacion_requerimiento.id_requerimiento=tb_requerimiento.id_requerimiento 
			JOIN tb_certificacion ON tb_solucion_requerimiento.id_solucion=tb_certificacion.id_solucion
			WHERE tb_instalacion.accesible='No' 
			ORDER BY tb_requerimiento.id_requerimiento ASC
			    ) asig_instal
    		join tb_certificacion_online on asig_instal.id_instalacion = tb_certificacion_online.id_instalacion");

		$pagTitulo = 'Revisar estado de requerimientos';
	
		$activo = array(
			'aprobado' => array('active' => '' , 'show_active' => '' ),
			'asignado' => array('active' => '' , 'show_active' => '' ),  
			'desarrollo' => array('active' => '' , 'show_active' => '' ),
			'pruebas' => array('active' => '' , 'show_active' => '' ),
			'instalacion' => array('active' => 'active' , 'show_active' => 'show active' ),
			'certificado' => array('active' => '' , 'show_active' => '' )
		);

		return view('jefe_operaciones.rq_estado')->with(compact('rqInstalacion','rqInstalacionHisto','pagTitulo','activo'));

	}


	public function rqEstadoCert(Request $request){
	
		// Datos de la Asignacion del requerimiento

		$rqCertificado = DB::select("SELECT tb_requerimiento.id_requerimiento, tb_instalacion.fecha_instal, tb_instalacion.hora_instal, tb_certificacion_online.fecha_certificacion, tb_certificacion_online.hora_certificacion 
			FROM tb_certificacion_online join tb_instalacion on tb_certificacion_online.id_instalacion=tb_instalacion.id_instalacion 
			JOIN tb_asignacion_instal_req ON tb_instalacion.id_asig_instal=tb_asignacion_instal_req.id_asig_instal 
			JOIN tb_solucion_requerimiento ON tb_asignacion_instal_req.id_solucion=tb_solucion_requerimiento.id_solucion 
			JOIN tb_asignacion_requerimiento ON tb_solucion_requerimiento.id_asignacion=tb_asignacion_requerimiento.Nro_asignacion 
			JOIN tb_requerimiento ON tb_asignacion_requerimiento.id_requerimiento=tb_requerimiento.id_requerimiento 
			ORDER BY tb_requerimiento.id_requerimiento ASC");

		$pagTitulo = 'Revisar estado de requerimientos';
	
		$activo = array(
			'aprobado' => array('active' => '' , 'show_active' => '' ),
			'asignado' => array('active' => '' , 'show_active' => '' ),  
			'desarrollo' => array('active' => '' , 'show_active' => '' ),
			'pruebas' => array('active' => '' , 'show_active' => '' ),
			'instalacion' => array('active' => '' , 'show_active' => '' ),
			'certificado' => array('active' => 'active' , 'show_active' => 'show active' )
		);

		return view('jefe_operaciones.rq_estado')->with(compact('rqCertificado','pagTitulo','activo'));

	}


	public function paginacionManual($sqlResult){

		$perPage=20;
        $currentPage = 0;
    	$pagedData = array_slice($sqlResult, $currentPage * $perPage, $perPage);
    	
    	$sqlResult = new \Illuminate\Pagination\LengthAwarePaginator($pagedData, count($sqlResult), $perPage);

		return $sqlResult;
	}


	public function rqSeguimiento(){
	
		// Datos de la Asignacion del requerimiento
		$seguimientoRq = DB::table('tb_requerimiento')
		->join('users','tb_requerimiento.id_operador', '=' , 'users.id')
		
		->select('id_requerimiento', 'tipo_tarea', 'fecha_solicitud', 'tb_requerimiento.tipo', 'hora_solicitud',  'descripcion', 'name', 'ap_paterno', 'accesible')
		->orderBy('fecha_solicitud', 'DESC')
		->get();

		return view('jefe_operaciones.rq_seguimiento')->with(compact('seguimientoRq'));
	}
	
	public function rqSegtoDetalle($id){
		
		// fase inicial 
		$detalle = DB::table('tb_requerimiento')
		->join('tb_cliente','tb_requerimiento.id_cliente', '=' , 'tb_cliente.id_cliente')
		->join('users','tb_requerimiento.id_operador', '=' , 'users.id')
		->where('tb_requerimiento.id_requerimiento', '=' , $id)
		->select('id_requerimiento', 'tb_requerimiento.tipo', 
				 'tb_requerimiento.fecha_solicitud', 'tb_requerimiento.hora_solicitud', 
				 'tb_requerimiento.prioridad', 'tb_requerimiento.accesible', 
				 'tb_requerimiento.descripcion', 'tb_requerimiento.resultado', 
				 'users.name','users.ap_paterno','tb_cliente.nombre')
		->get();

		$adjuntosIni= $this->adjuntoArchivos($id, 1);
		
		$arrayEstado = array();

		// fase aprobacion
		if($detalle[0]->accesible == 'No'){
			
			$aprobacion = $this->aprobacionSeg($id);

			if($aprobacion){
				$arrayEstado['aprobacion'] = 1;
			}else{
				$arrayEstado['aprobacion'] = 0;
			}

			// fase de asignación
			if($aprobacion->accesible == 'No'){
				$asignacion = $this->asignacionSeg($id);

				if($asignacion){
					$arrayEstado['asignacion'] = 1;
				}else{
					$arrayEstado['asignacion'] = 0;
				}

				// fase de desarrollo
				if($asignacion[0]->accesible == 'No' && !empty($asignacion[0]->Nro_asignacion)){
					$desarrollo = $this->desarrolloSeg($asignacion[0]->Nro_asignacion);

					if($desarrollo){
						$adjuntosDesa= $this->adjuntoArchivos($id, 4);
						$arrayEstado['desarrollo'] = 1;
					}else{
						$arrayEstado['desarrollo'] = 0;
					}

					// fase de prueba OPR
				    $fechaFin = $desarrollo[0]->fecha_fin;
	                $horaFin = $desarrollo[0]->hora_fin;
	                $idSolu = $desarrollo[0]->id_solucion;
	             
					if($desarrollo[0]->accesible == 'No'){
						$prueba = $this->pruebaSeg($idSolu,$fechaFin,$horaFin);

						if($prueba){

							$adjuntosPrue= $this->adjuntoArchivos($id, 0);
							$arrayEstado['prueba'] = 1;
						}else{
							$arrayEstado['prueba'] = 0;
						}

						// fase de certificaión
						$certificacion = $this->certificacionSeg($idSolu);

						if($certificacion){

							$adjuntosCert= $this->adjuntoArchivos($id, 5);
							$arrayEstado['certificacion'] = 1;
						}else{
							$arrayEstado['certificacion'] = 0;
						}

						// fase de instalacion
						$idSolu = $certificacion[0]->id_solucion;
						if($certificacion[0]->accesible == 'No'){

							$instalacionAsig = $this->instalacionAsigSeg($idSolu);

							if($instalacionAsig){

								$adjuntosInsAsig= $this->adjuntoArchivos($id, 0);
								$arrayEstado['instalacionAsig'] = 1;
							}else{
								$arrayEstado['instalacionAsig'] = 0;
							}

							// Significa que ya ha sido instalado luego de la asignacion a instalacion

							if($instalacionAsig[0]->accesible == 'No'){

								$instalacion = $this->instalacionSeg($instalacionAsig[0]->id_asig_instal);

								if($instalacion){

									$adjuntosInsta = $this->adjuntoArchivos($id, 7);
									$arrayEstado['instalacion'] = 1;
								}else{
									$arrayEstado['instalacion'] = 0;
								}

								// fase de certificacion On Line
								$idInstalacion = $instalacion[0]->id_instalacion;
								$certOnLine = $this->certOnLineSeg($idInstalacion);

								if($certOnLine){
									$adjuntosCeOnLine = $this->adjuntoArchivos($id, 8);
									$arrayEstado['certOnLine'] = 1;
								}else{
									$arrayEstado['certOnLine'] = 0;
								}

								// fase de control de SVN
								$controlSvn = $this->controlSvnSeg($idInstalacion);

								if($controlSvn){
									//$adjuntosCSvn = $this->adjuntoArchivos($id, 8);
									$arrayEstado['controlSvn'] = 1;
								}else{
									$arrayEstado['controlSvn'] = 0;
								}

								// fase de aceptacion del cliente
								$aceptacionCliente = $this->aceptacionCliSeg($idInstalacion);

								if($aceptacionCliente){
									$adjuntosAcepCliente = $this->adjuntoArchivos($id, 10);
									$arrayEstado['aceptacionCliente'] = 1;
								}else{
									$arrayEstado['aceptacionCliente'] = 0;
								}

							}else{
								$arrayEstado['instalacion'] = 0;
			
							}

						}else{
							$arrayEstado['instalacionAsig'] = 0;
						}

					}else{
						$arrayEstado['prueba'] = 0;
					}

				}else{
					$arrayEstado['desarrollo'] = 0;
				}
	
			}else{
				$arrayEstado['asignacion'] = 0;
			}

		}else{
			$arrayEstado['aprobacion'] = 0;
		}
		

		//	$estado = (rechazaso, depurado, aprobado, en curso)
		$arraycodFase = array(  'id_fase1' => 1, 
								'id_fase2' => 2 ,
								'id_fase3' => 3 ,
								'id_fase4' => 4 ,
								'id_fase5' => 5 ,
								'id_fase6' => 6 ,
								'id_fase7' => 7 ,
								'id_fase8' => 8 );
		
		$arratFases = array('inicio_req' => 
									array('nom_fase' => 'Inicio Requerimiento',
										  'id_requerimiento' => $id,
										  'estado' => $arrayEstado['inicio_req'],
										  'id_fase' => $arraycodFase['id_fase1']
										),
							'aprobacion' => 
									array('nom_fase' => 'Fase de aprobación',
										  'id_requerimiento' => $id,
										  'estado' => $arrayEstado['aprobacion'],
										  'id_fase' => $arraycodFase['id_fase2']
										),
							'asignacion' => 
									array('nom_fase' => 'Fase de asignación',
										  'id_requerimiento' => $id,
										  'estado' => $arrayEstado['asignacion'],
										  'id_fase' => $arraycodFase['id_fase3']
										),
							'desarrollo' => 
									array('nom_fase' => 'Fase de desarrollo',
										  'id_requerimiento' => $id,
										  'estado' => $arrayEstado['desarrollo'],
										  'id_fase' => $arraycodFase['id_fase4']
										),
							'prueba' => 
									array('nom_fase' => 'Fase de prueba operador',
										  'id_requerimiento' => $id,
										  'estado' => $arrayEstado['prueba'],
										  'id_fase' => $arraycodFase['id_fase5']
										),
							'certificacion' => 
									array('nom_fase' => 'Fase de certificación',
										  'id_requerimiento' => $id,
										  'estado' => $arrayEstado['certificacion'],
										  'id_fase' => $arraycodFase['id_fase6']
										),
							'instalacion' => 
									array('nom_fase' => 'Fase de instalación',
										  'id_requerimiento' => $id,
										  'estado' => $arrayEstado['instalacion'],
										  'id_fase' => $arraycodFase['id_fase7']
										),
							'cert_online' => 
									array('nom_fase' => 'Fase de certificación on line',
										  'id_requerimiento' => $id,
										  'estado' => $arrayEstado['certOnLine'],
										  'id_fase' => $arraycodFase['id_fase8']
										)
							);
		 

		

		//$pendInstalar = collect($pendInstalar1)->get();

		//agregar nick del mètodo para subir y borrar archivos
		$nombreFuncion = 'pendDetalle';
		return view('jefe_operaciones.rq_seguimiento_detalle')->with(compact('detalle','adjuntos','nombreFuncion','arratFases','arraycodFase'));	

			
	}

	public function downloadSeg(Request $request,$id){

    	$adjunto = DB::table('tb_adjuntos')
		->where('id_adjunto', '=' , $id )
		->where('id_etapa', '=' , $request->ide)
		->select('id_adjunto', 'id_requerimiento', 
				 'id_etapa', 'nombre')
		->get();

        if(!$this->downloadFile(public_path()."/files/".$adjunto[0]->nombre)){
     
            return redirect()->back();
        }
    }	

    public function aprobacionSeg($id){
		
		$aproExiste = AprobacionRq::find($id);
		
		return $aproExiste;
    	
    }

    public function asignacionSeg($id){

    	// Datos de la Asignacion del requerimiento
		$asigExiste = DB::select('SELECT ap.Nro_asignacion, ap.id_requerimiento, ap.id_gestor, ap.id_programador, ap.fecha_asignacion, ap.hora_asignacion, ap.asignado_por, CONCAT(us.name ," ",us.ap_paterno) as asignado_a, accesible  FROM (
			SELECT Nro_asignacion, id_requerimiento, id_gestor, id_programador, fecha_asignacion, hora_asignacion, CONCAT(name, " ", ap_paterno) asignado_por, accesible 
			FROM tb_asignacion_requerimiento ar
			JOIN users u on ar.id_gestor=u.id 
			WHERE ar.id_requerimiento = :id
			)ap
			JOIN users us on ap.id_programador = us.id', ['id' => $id]);

		return $asigExiste;
	}

	/**
	*Nota: la fechafin y la hora_fin se refiere a las fecha/hora en que se entrego la primera solucion
	*para luego ser probado, una vez es probado y certificado entonces se tiene una solucion
	*definitiva.
	*
	*
	**/
	public function desarrolloSeg($id){

		
		$desaExiste = DB::table('tb_solucion_requerimiento')
		->where('id_asignacion', '=' , $id)
		->select('id_solucion', 'id_asignacion','fecha_inicio','hora_inicio','fecha_fin','hora_fin','descripcion','accesible')
		->get();


		return $desaExiste;
	
	}

	public function adjuntoArchivos($id, $etapa){

		$adjuntos = DB::table('tb_adjuntos')
			->where('id_requerimiento', '=' , $id)
			->where('id_etapa', '=' , $etapa)
			->select('id_adjunto', 'id_requerimiento', 
					 'id_etapa', 'nombre', 
					 'fecha', 'hora')
			->get();

		return $adjuntos;
	}

	public function pruebaSeg($idSolu,$fechaFin,$horaFin){

		$pruExiste = DB::select('SELECT id_certificacion, id_solucion, 
			fecha_certificacion,hora_certificacion,detalle_certificacion,detalle_funcionalidades,
			accesible, "'.$fechaFin.'" as fecha_fin, "'.$horaFin.'" as hora_fin 
			FROM tb_certificacion WHERE id_solucion= :id', ['id' => $idSolu]);

		return $pruExiste;
	}

	public function certificacionSeg($id){

		$certExiste = DB::select('SELECT tb_certificacion.id_certificacion,tb_certificacion.id_solucion,tb_certificacion.fecha_certificacion,tb_certificacion.hora_certificacion, tb_certificacion.detalle_certificacion, tb_certificacion.detalle_funcionalidades, users.name, users.ap_paterno 
			FROM tb_certificacion 
			JOIN users on tb_certificacion.id_operador=users.id 
			WHERE tb_certificacion.id_solucion=:id', ['id' => $idSolu]);

		return $certExiste;

	}

	public function instalacionAsigSeg($id){

		$instAsigExiste = DB::select('SELECT ag.id_asig_instal, ag.id_gestor, ag.id_solucion, ag.id_programador, ag.fecha_asig_instal, ag.hora_asig_instal, CONCAT(ag.name, " ", ag.ap_paterno) as asignado_por , CONCAT(us.name, " ", us.ap_paterno) as asignado_a
			 	FROM (
			 	 SELECT * FROM tb_asignacion_instal_req sr 
			 	 JOIN users u on sr.id_gestor = u.id 
			 	 WHERE sr.id_solucion = :id 
			 	) ag 
			 	JOIN users us on ag.id_programador = us.id', ['id' => $id]);

		return $instAsigExiste;

	}


	public function instalacionSeg($id){

		$instExiste = DB::select('SELECT id_instalcion, id_asig_instal, backup, fecha_instal, hora_instal, comentario, accesible
			FROM tb_instalacion 
			WHERE id_asig_instal= :id', ['id' => $id]);

		return $instExiste;

	}


	public function certOnLineSeg($id){

		$certOnLineExiste = DB::select('SELECT co.id_certificacion_online, co.id_instalacion, co.id_operador, co.fecha_certificacion, co.hora_certificacion, co.conformidad, co.accesible, u.name, u.ap_paterno, u.ap_materno  
			FROM tb_certificacion_online co
			JOIN users u on co.id_operador=u.id and u.tipo = "Operador"
			WHERE co.id_instalacion = :id', ['id' => $id]);

		return $certOnLineExiste;

	}


	public function controlSvnSeg($id){

		$certOnLineExiste = DB::select('SELECT id_control_svn, id_certificacion_online, fecha_subversion, hora_subversion, comentarios, accesible, fecha_cert, hora_cert, id_operador
			FROM tb_control_svn 
			WHERE id_certificacion_online = :id', ['id' => $id]);

		return $certOnLineExiste;

	}


	public function aceptacionCliSeg($id){

		$aceptacionExiste = DB::select('SELECT a.id_aceptacion, a.id_control_svn, a.fecha_aceptacion, a.hora_aceptacion, a.comentarios, u.name, u.ap_paterno, u.ap_materno 
			FROM tb_aceptacion a
			JOIN users u ON a.id_operador=u.id  AND u.tipo="Operador"
			WHERE a.id_aceptacion = :id', ['id' => $id]);

		return $aceptacionExiste;

	}
	


	






	



}