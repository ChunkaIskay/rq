<?php

namespace App\Http\Controllers\JefeSistemas;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

use App\AprobacionRq;
use App\Requerimiento;
use App\Adjunto;
use App\AsignacionReq;
use App\FechaReq;
use App\CertificacionRq;
use App\AsigInstalReq;
use App\Operador;
use App\RoleUser;
use App\AsigReqSolucion;
use File;

use Illuminate\Support\Facades\Auth;


class ImportantTasksController extends Controller
{
    public function index(){   

    	$listAprob = DB::table('tb_aprobacion_requerimiento')
		->where('accesible', '=' , 'Si')
		->select('nro_aprobacion','id_requerimiento','fecha_aprobacion','hora_aprobacion','accesible')
		->orderBy('id_requerimiento','ASC')
		->get();

		    	
		return view('jefe_sistemas.rq_aprobados')->with(compact('listAprob'));    
	}


	public function rqDetalleAprob($id){

		if (!Auth::check()) {
		   return view('auth.login');	
		}

		$user = \Auth::user();

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

		// rol de usuario jefe de sistemas = 3
		$gestor = $this->listUserRol(3,$user->id);
		
		// rol de usuario desarrollador = 2
		$desarrollador = $this->listUserRol(2);

		$fecha_plan = DB::table('tb_req_fecha')
			->where('id_requerimiento','=', $id)
			->get();
		//agregar nick del mètodo para subir y borrar archivos
		$nombreFuncion = 'detalleAprob';
		return view('jefe_sistemas.rq_detalle_aprob')->with(compact('detalle','adjuntos','nombreFuncion','gestor','desarrollador','fecha_plan','id'));

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

      //  dd(storage_path()."/app/files/");
        
        if(Storage::exists('files/'.$adjunto[0]->nombre)){
			if(!$this->downloadFile(storage_path()."/app/files/".$adjunto[0]->nombre)){

            	return redirect()->back();
        	}
        }
		//Storage::exists('files/'.$nombreArchivo)
        
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


	public function rqGuadarAprob(Request $request, $id){
		
	    date_default_timezone_set('America/La_Paz');
	    
	    $this->validate($request, AsignacionReq::$rules, AsignacionReq::$messages);
   
		// ingresar registros en asignación de requerimientos..
		$asignacionReq = new AsignacionReq();

		$fecha = date('Y-m-d');
		$hora = date('H:i:s');
	 	
		$asignacionReq->nro_asignacion = $id;
	    $asignacionReq->id_requerimiento = $id;
	    $asignacionReq->id_gestor = $request->gestor;
	    $asignacionReq->id_programador = $request->desarrollador;

	    $asignacionReq->fecha_asignacion = $fecha;
	    $asignacionReq->hora_asignacion = $hora;
		$asignacionReq->accesible = 'Si';
    	
    	if($asignacionReq->save()){  // save
	        //actualizar el campo accesible de la tabla aprobacion_requerimiento
			$rqAprob = AprobacionRq::find($id);
	    	$rqAprob->accesible = 'No';
	        $rqAprob->save();
	        //actualizar el campo fecha plan y hora
	        $fecha_antes = date('Y-m-d', strtotime($request->dateFrom));
	        //$hora1 = date('H:i:s',$request->t_des);
	        $rqFechaRq = FechaReq::find($id);
	    	$rqFechaRq->fecha_plan_de = $request->fch_planif; 
	    	$rqFechaRq->t_desa = $request->t_des;
	        $rqFechaRq->save();
	
        }else{

        	return redirect()->route('detalleAprob')->with(array(
    		'error' => 'Error, no se puedo asignar el requemirimiento.!!'
    		));
        }

        return redirect()->route('rqAprob')->with(array(
    		'message' => 'El requerimiento fue asignado exitosamente.!!'
    	)); 
    	
	}


	public function rqCertificados(){   
		
        $listCert = DB::table('tb_requerimiento as r')
        ->join('tb_asignacion_requerimiento as ar', 'r.id_requerimiento', '=','ar.id_requerimiento')
        ->join('tb_solucion_requerimiento as sr' ,'ar.Nro_asignacion','=','sr.id_asignacion')
        ->join('tb_certificacion', 'sr.id_solucion','=','tb_certificacion.id_solucion')
    	->join( 'users','tb_certificacion.id_operador', '=', 'users.id')
		->where('tb_certificacion.accesible', '=' , 'Si')
        
        ->select('tb_certificacion.id_certificacion','tb_certificacion.id_solucion','tb_certificacion.fecha_certificacion','tb_certificacion.hora_certificacion','tb_certificacion.accesible','r.id_requerimiento','users.name','users.ap_paterno')
		
		->orderBy('tb_certificacion.id_certificacion','ASC')
		->get();
	  

		return view('jefe_sistemas.rq_certificados')->with(compact('listCert'));    
	    
	}


	public function rqDetalleCert($idrq){

    	$adjuntos = "";
    	$adjuntoSol = "";

    	if (!Auth::check()) {
		   return view('auth.login');	
		}

		$user = \Auth::user();

		$detalle = DB::select('SELECT air.id_certificacion,air.id_solucion, air.id_operador
			,air.fecha_certificacion,air.hora_certificacion,air.accesible cert_acces, air.detalle_certificacion, air.detalle_funcionalidades,
			sr.id_asignacion,sr.secuencia,sr.fecha_inicio,sr.hora_inicio,sr.fecha_fin,sr.hora_fin,
			sr.descripcion sol_desc, sr.prog_clientes, sr.prog_servidores,sr.accesible sol_req_acces,ar.Nro_asignacion, 
			ar.fecha_asignacion, ar.hora_asignacion, ar.accesible asig_req_acces, 
			r.tipo, r.tipo_tarea, r.fecha_solicitud, r.hora_solicitud, r.id_cliente,r.id_operador,
			r.prioridad,r.descripcion rq_desc, r.id_requerimiento, r.resultado, r.accesible req_acces, u.name, u.ap_paterno, u.ap_materno
			FROM tb_certificacion air
			JOIN tb_solucion_requerimiento sr on air.id_solucion=sr.id_solucion 
			JOIN tb_asignacion_requerimiento ar on sr.id_asignacion=ar.Nro_asignacion 
			JOIN tb_requerimiento r on ar.id_requerimiento=r.id_requerimiento 
			JOIN users u on r.id_operador=u.id
			WHERE air.accesible = "Si"
			AND air.id_certificacion = :id', ['id' => $idrq]);

			//$idAsigInstal = $detalle[0]->id_asig_instal;#id de la asignacion a 
			
			$idCertificacion = $detalle[0]->id_certificacion;#id de la certificacion
            $idSolucion = $detalle[0]->id_solucion;#id de la solucion
            $idAsignacion = $detalle[0]->Nro_asignacion; #id de la asignacion
            $idRqto = $detalle[0]->id_requerimiento; # 29, id requerimiento
            $descSolicitud = $detalle[0]->rq_desc;#descripcion de la solicitud
            $descSolucion = $detalle[0]->sol_desc;#descripcion de la solucion, NOTA: se cabio de 15 a 14 en Mantenimiento
            $fechaSolu = $detalle[0]->fecha_fin;#fecha solucion
            $horaSolu = $detalle[0]->hora_fin;#hora solucion
          //  $idDesa = $detalle[0]->id_programador;#id del desarrollador
            $secuencial = $detalle[0]->secuencia;

            $adjuntos = DB::table('tb_adjuntos')
			->where('id_requerimiento', '=' , $idrq)
			->where('id_etapa', '=' , '1')
			->select('id_adjunto', 'id_requerimiento', 
					 'id_etapa', 'nombre', 
					 'fecha', 'hora')
			->get();
		
			$responsableSolu = DB::table('users')
			->where('id', '=' , $detalle[0]->id_operador)
			->select('id','name', 'ap_paterno')
			->get();


			//agregar nick del mètodo para subir y borrar archivos
			$nombreFuncion = 'detalleCert';

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

//dd($adjuntoSol);

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

			$nombreCarpeta = 'arch_adjuntos';
			$nombreController = 'JefeSistemas';

			// rol de usuario jefe de sistemas = 3
			$gestor = $this->listUserRol(3,$user->id);	
			// rol de usuario desarrollador = 2
			$desarrollador = $this->listUserRol(2);
			$id = $idrq;

			return view('jefe_sistemas.rq_detalle_cert')->with(compact('detalle','adjuntos','nombreFuncion','aproRq','asigRq','responsableSolu','adjuntoCertiPreInst','adjuntoSol','certiPreInst','adjuntoCertiPreInst','asigInstRq','nombreCarpeta','nombreController','gestor','desarrollador','id')); 	

	}

	public function listUserRol($id,$iduse=0){

		if($iduse!=0){
			$listado = DB::table('users')
			->join('role_user', 'users.id','=','role_user.user_id')
			->join('roles', 'role_user.role_id','=','roles.id')
			->where('users.id', '=' , $iduse)
			->where('roles.id', '=' , $id)
			->where('users.activo', '=' , 'Si')
			->select('users.id','users.name','users.ap_paterno','roles.name as tipo','activo')
			->get();
		}else{
				$listado = DB::table('users')
				->join('role_user', 'users.id','=','role_user.user_id')
				->join('roles', 'role_user.role_id','=','roles.id')
				->where('roles.id', '=' , $id)
				->where('users.activo', '=' , 'Si')
				->select('users.id','users.name','users.ap_paterno','roles.name as tipo','activo')
				->get();
		}
		
		return $listado;

	}

	public function rqGuadarCert(Request $request, $id){
		
		date_default_timezone_set('America/La_Paz');
	    
	    $this->validate($request, CertificacionRq::$rules, CertificacionRq::$messages);
	    
		// ingresar registros en asignación de requerimientos..
		$asignacionInstRq = new AsigInstalReq();
		
		$fecha = date('Y-m-d');
		$hora = date('H:i:s');
	 	
		$asignacionInstRq->id_asig_instal = $request->id_solucion;
	    $asignacionInstRq->id_solucion = $request->id_solucion;
	    $asignacionInstRq->id_gestor = $request->gestor;
	    $asignacionInstRq->id_programador = $request->desarrollador;
	    
	    $asignacionInstRq->fecha_asig_instal = $fecha;
	    $asignacionInstRq->hora_asig_instal = $hora;
		$asignacionInstRq->accesible = 'Au';
    	
    	if($asignacionInstRq->save()){  // save
	        //actualizar el campo accesible de la tabla aprobacion_requerimiento
			$rqCert = CertificacionRq::find($id);
	    	$rqCert->accesible = 'No';
	        $rqCert->save();
        }else{
        	return redirect()->route('detalleCert')->with(array(
    		'error' => 'Error, no se puedo asignar el requemirimiento.!!'
    		));
        }

        return redirect()->route('rqCertificados')->with(array(
    		'message' => 'El requerimiento fue asignado exitosamente.!!'
    	));

	}

	// Entrega de planificaciones
	public function entregaPlanificada(){
		
	   // $listaReq = '';

		return view('jefe_sistemas.entrega_plani')->with(compact('listaReq'));

    }

	//searchlistaRq
    public function searchlistaRq(Request $request){

		if(!empty($request->input('idReq'))){
			
			$listaReq = Requerimiento::find($request->input('idReq'));

	    //    dd($listaReq);		
		}
 	
       	return view('jefe_sistemas.entrega_plani')->with(compact('listaReq'));

    }

    public function detalleEntregaDesarrollo($id){

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

		
		$rqFecha = DB::table('tb_req_fecha')
				->where('id_requerimiento', '=' , $id)
				->select('id_requerimiento', 'fecha_plan_op', 
						 'fecha_plan_de', 't_desa')
				->get();

		//dd($detalle);
		//agregar nick del mètodo para subir y borrar archivos
		$nombreFuncion = 'pendDetalle';
		return view('jefe_sistemas.rq_detalle_entrega')->with(compact('detalle','rqFecha','id'));

    }

    public function rqGuadarEntrega(Request $request, $id){
		
	    date_default_timezone_set('America/La_Paz');
	    
 		//actualizar el campo fecha plan y hora
        $fecha_planif = date('Y-m-d', strtotime($request->fch_planif));
        //$hora1 = date('H:i:s',$request->t_des);
        $rqFechaRq = FechaReq::find($id);
        
    	$rqFechaRq->fecha_plan_de = $fecha_planif; 
    	$rqFechaRq->t_desa = $request->t_des;
       
    	if($rqFechaRq->save()){  // save

 			return redirect()->route('entregaPlanificada')->with(array(
    			'message' => 'El requerimiento fue asignado exitosamente.!!'
    		));

        }else{
	        	return redirect()->route('entregaDesarrollo')->with(array(
	    		'error' => 'Error, no se puedo asignar el requemirimiento.!!'
	    		));
        }
	}

    // Revisar rq pendientes.

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
		
		return view('jefe_sistemas.rq_estado')->with(compact('rqAprobadosRecien','rqAprobadosHisto','pagTitulo','activo'));

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

	
		return view('jefe_sistemas.rq_estado')->with(compact('rqAsignados','rqAsignadosHisto','pagTitulo','activo'));

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

		return view('jefe_sistemas.rq_estado')->with(compact('rqDesarrollo','rqDesarrolloHisto','pagTitulo','activo'));

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

	return view('jefe_sistemas.rq_estado')->with(compact('rqPruebas','rqPruebasHisto','pagTitulo','activo'));

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

		return view('jefe_sistemas.rq_estado')->with(compact('rqInstalacion','rqInstalacionHisto','pagTitulo','activo'));

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

		return view('jefe_sistemas.rq_estado')->with(compact('rqCertificado','pagTitulo','activo'));

	}

	public function paginacionManual($sqlResult){

		$perPage=100000;
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

		return view('jefe_sistemas.rq_seguimiento')->with(compact('seguimientoRq'));
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

		$adjuntos = $this->adjuntoArchivos($id, 1);
		
		// 1 si se ejecuto correctamente la consulta. 0 si no trae datos (datos vacios)
		$arrayEstado = array('inicio_req'=>100,
							 'aprobacion'=>100,
							 'asignacion'=>100,
							 'desarrollo'=>100,
							 'prueba'=>100,
							 'certificacion'=>100,
							 'instalacion_asig'=>100,
							 'instalacion'=>100,
							 'cert_on_line'=>100,
							 'control_svn'=>100,
							 'aceptacion_cliente'=>100
							);
		// face actual = 1 ,  no llego a esa fase =0, ya paso por esa fase = 2,'Rm' Rq depurado = 3, Ob Rq rechazado por el auditor = 4
		$faseActual = array('inicio_req'=> 0,
							 'aprobacion'=> 0,
							 'asignacion'=> 0,
							 'desarrollo'=> 0,
							 'prueba'=> 0,
							 'certificacion'=> 0,
							 'instalacion_asig'=> 0,
							 'instalacion'=> 0,
							 'cert_on_line'=> 0,
							 'control_svn'=> 0,
							 'aceptacion_cliente'=> 0
							);

		// fase aprobacion
		if($detalle[0]->accesible == 'No'){

			$arrayEstado['inicio_req'] = 1;
			$faseActual['inicio_req'] = 2;

			$aprobacion = $this->aprobacionSeg($id);

			if($aprobacion){
				$arrayEstado['aprobacion'] = 1;
			}else{
				$arrayEstado['aprobacion'] = 0;
			}

			// fase de asignación
			if($aprobacion->accesible == 'No'){
				$faseActual['aprobacion'] = 2;
				$asignacion = $this->asignacionSeg($id);

				if($asignacion){
					$arrayEstado['asignacion'] = 1;
				}else{
					$arrayEstado['asignacion'] = 0;
				}

				// fase de desarrollo
				if($asignacion[0]->accesible == 'No' && !empty($asignacion[0]->Nro_asignacion)){
					$faseActual['asignacion'] = 2;
					$desarrollo = $this->desarrolloSeg($asignacion[0]->Nro_asignacion);

					if($desarrollo){
						$adjuntosDesa = $this->adjuntoArchivos($id, 4);
						$arrayEstado['desarrollo'] = 1;
					}else{
						$arrayEstado['desarrollo'] = 0;
					}

					// fase de prueba OPR
				    $fechaFin = $desarrollo[0]->fecha_fin;
	                $horaFin = $desarrollo[0]->hora_fin;
	                $idSolu = $desarrollo[0]->id_solucion;
	             
					if($desarrollo[0]->accesible == 'No'){
						$faseActual['desarrollo'] = 2;
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
							$faseActual['prueba'] = 2;
							$instalacionAsig = $this->instalacionAsigSeg($idSolu);
							$instalacionRqSeg = $this->instalacionRqSeg($idSolu);
						
							if($instalacionAsig){
								$adjuntosInsAsig = $this->adjuntoArchivos($id, 0);
								$arrayEstado['instalacion_asig'] = 1;
								$faseActual['instalacion'] = 1;
							}else{
								$arrayEstado['instalacion_asig'] = 0;
							}

							// si instalacionAsig[0]->accesible es NO significa que ya ha sido instalado luego de la asignacion a instalacion

							if($instalacionAsig[0]->accesible == 'No'){ 
								//$faseActual['instalacion_asig'] = 2;
								$instalacion = $this->instalacionSeg($instalacionAsig[0]->id_asig_instal);

								if($instalacion){ 
									$adjuntosInsta = $this->adjuntoArchivos($id, 7);
									$arrayEstado['instalacion'] = 1;
									$faseActual['instalacion'] = 1;
								}else{
									$arrayEstado['instalacion'] = 0;
								}

								// fase de certificacion On Line
								$idInstalacion = $instalacion[0]->id_instalacion;
								$certOnLine = $this->certOnLineSeg($idInstalacion);

								if($certOnLine){
									$adjuntosCeOnLine = $this->adjuntoArchivos($id, 8);
									$arrayEstado['cert_on_line'] = 1;
									$faseActual['cert_on_line'] = 1;  
									if(!empty($instalacion[0]->fecha_instal)){
										$faseActual['instalacion'] = 2;
									}
								}else{
									$arrayEstado['cert_on_line'] = 0;
								}

								// fase de control de SVN
								$controlSvn = $this->controlSvnSeg($idInstalacion);

								if($controlSvn){
									//$adjuntosCSvn = $this->adjuntoArchivos($id, 8);
									$arrayEstado['control_svn'] = 1;
									$faseActual['control_svn'] = 1;

									if(!empty($controlSvn[0]->fecha_subversion)){
										$faseActual['cert_on_line'] = 2;
									}
									// fase de aceptacion del cliente
									$aceptacionCliente = $this->aceptacionCliSeg($controlSvn[0]->id_control_svn);

									if($aceptacionCliente){
										$arrayEstado['aceptacion_cliente'] = 1;
										$faseActual['aceptacion_cliente'] = 1;

										if(!empty($aceptacionCliente[0]->fecha_aceptacion)){
											$faseActual['control_svn'] = 2;
										}
										if(!empty($aceptacionCliente[0]->id_aceptacion)){
											$faseActual['aceptacion_cliente'] = 2;
										}
									}else{
										$arrayEstado['aceptacion_cliente'] = 0;
									}

								}else{
									$arrayEstado['control_svn'] = 0;
								}

							}else{
									if($instalacionAsig[0]->accesible == 'Si')
										$faseActual['instalacion'] = 1;
									//$arrayEstado['instalacion'] = 0;
							}

						}else{  // certificacion y prueba
								if($certificacion[0]->accesible == 'Si')
									$faseActual['certificacion'] = 1;
								//$arrayEstado['instalacion_asig'] = 0;
								$arrayEstado['instalacion'] = 0;
						}

					}else{
							if($desarrollo[0]->accesible == 'Si')
								$faseActual['desarrollo'] = 1;

							$arrayEstado['prueba'] = 0;
					}

				}else{
						if($asignacion[0]->accesible == 'Si')
							$faseActual['asignacion'] = 1;

						$arrayEstado['desarrollo'] = 0;
				}
	
			}else{
					if($aprobacion->accesible == 'Si')
						$faseActual['aprobacion'] = 1;

					$arrayEstado['asignacion'] = 0;
			}

		}else{
				if($detalle[0]->accesible == 'Si')
					$faseActual['inicio_req'] = 1;

				$arrayEstado['aprobacion'] = 0;
				$arrayEstado['inicio_req'] = 0;
		}


		//	$estado = (rechazaso, depurado, aprobado, en curso)
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
		
		$arrayFases = array('inicio_req' => 
									array('nom_fase' => 'Inicio Requerimiento',
										  'desc_fase' => 'Inicia Requerimiento', 
										  'id_requerimiento' => $id,
										  'estado' => $arrayEstado['inicio_req'],
										  'id_fase' => $arraycodFase['id_fase1'],
										  'fase_actual' => $faseActual['inicio_req']
										),
							'aprobacion' => 
									array('nom_fase' => 'Fase de Aprobación',
										  'desc_fase' => 'Aprobación Opr.',
										  'id_requerimiento' => $id,
										  'estado' => $arrayEstado['aprobacion'],
										  'id_fase' => $arraycodFase['id_fase2'],
										  'fase_actual' => $faseActual['aprobacion']
										),
							'asignacion' => 
									array('nom_fase' => 'Fase de Asignación',
										  'desc_fase' => 'Asignación en Des.',
										  'id_requerimiento' => $id,
										  'estado' => $arrayEstado['asignacion'],
										  'id_fase' => $arraycodFase['id_fase3'],
										  'fase_actual' => $faseActual['asignacion']
										),
							'desarrollo' => 
									array('nom_fase' => 'Fase de Desarrollo',
										  'desc_fase' => 'Desarrollo Req',
										  'id_requerimiento' => $id,
										  'estado' => $arrayEstado['desarrollo'],
										  'id_fase' => $arraycodFase['id_fase4'],
										  'fase_actual' => $faseActual['desarrollo']

										),
							'prueba' => 
									array('nom_fase' => 'Fase de Prueba Opr.',
										  'desc_fase' => 'Prueba Opr',
										  'id_requerimiento' => $id,
										  'estado' => $arrayEstado['prueba'],
										  'id_fase' => $arraycodFase['id_fase5'],
										  'fase_actual' => $faseActual['prueba']
										),
							'certificacion' => 
									array('nom_fase' => 'Fase de Certificación',
										  'desc_fase' => 'Desarrollo Asignación Instalación.',
										  'id_requerimiento' => $id,
										  'estado' => $arrayEstado['certificacion'],
										  'id_fase' => $arraycodFase['id_fase6'],
										  'fase_actual' => $faseActual['prueba']
										),
							'instalacion' => 
									array('nom_fase' => 'Fase de Instalación',
										  'desc_fase' => 'Instalación por Des.',
										  'id_requerimiento' => $id,
										  'estado' => $arrayEstado['instalacion'],
										  'id_fase' => $arraycodFase['id_fase7'],
										  'fase_actual' => $faseActual['instalacion']
										),
							'cert_online' => 
									array('nom_fase' => 'Fase de Certificación Online',
										  'desc_fase' => 'Certificación Online Opr',
										  'id_requerimiento' => $id,
										  'estado' => $arrayEstado['cert_on_line'],
										  'id_fase' => $arraycodFase['id_fase8'],
										  'fase_actual' => $faseActual['cert_on_line']
										),
							'control_version' => 
									array('nom_fase' => 'Fase de Control de Svn',
										  'desc_fase' => 'Control de Versión Des.',
										  'id_requerimiento' => $id,
										  'estado' => $arrayEstado['control_svn'],
										  'id_fase' => $arraycodFase['id_fase9'],
										  'fase_actual' => $faseActual['control_svn']
										),
							'aceptacion_cliente' => 
									array('nom_fase' => 'Fase de Aceptación del Cliente',
										  'desc_fase' => 'Aceptación cliente.',
										  'id_requerimiento' => $id,
										  'estado' => $arrayEstado['aceptacion_cliente'],
										  'id_fase' => $arraycodFase['id_fase10'],
										  'fase_actual' => $faseActual['aceptacion_cliente']
										)
							);
		//$pendInstalar = collect($pendInstalar1)->get();
		//agregar nick del mètodo para subir y borrar archivos

		$nombreFuncion = 'pendDetalle';
		return view('jefe_sistemas.rq_seguimiento_detalle')->with(compact('detalle','adjuntos','nombreFuncion','arrayFases','arraycodFase','aprobacion','asignacion','desarrollo','prueba','certificacion','instalacionAsig','instalacionRqSeg','instalacion','certOnLine','adjuntosDesa','adjuntosPrue','adjuntosCert','adjuntosInsAsig','adjuntosInsta','adjuntosCeOnLine','adjuntosAcepCliente','controlSvn','aceptacionCliente'));	
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

	public function pruebaSeg($idSolu,$fechaFin,$horaFin){

		$pruExiste = DB::select('SELECT id_certificacion, id_solucion, 
			fecha_certificacion,hora_certificacion,detalle_certificacion,detalle_funcionalidades,
			accesible, "'.$fechaFin.'" as fecha_fin, "'.$horaFin.'" as hora_fin 
			FROM tb_certificacion WHERE id_solucion= :id', ['id' => $idSolu]);

		return $pruExiste;
	}

	public function certificacionSeg($id){

		$certExiste = DB::select('SELECT tb_certificacion.id_certificacion,tb_certificacion.id_solucion,tb_certificacion.fecha_certificacion,tb_certificacion.hora_certificacion, tb_certificacion.detalle_certificacion, tb_certificacion.detalle_funcionalidades, tb_certificacion.accesible, users.name, users.ap_paterno 
			FROM tb_certificacion 
			JOIN users on tb_certificacion.id_operador=users.id 
			WHERE tb_certificacion.id_solucion=:id', ['id' => $id]);

		return $certExiste;

	}

	public function instalacionAsigSeg($id){

		$instAsigExiste = DB::select('SELECT ag.id_asig_instal, ag.id_gestor, ag.id_solucion, ag.id_programador, ag.fecha_asig_instal, ag.hora_asig_instal, ag.accesible, CONCAT(ag.name, " ", ag.ap_paterno) as asignado_por , CONCAT(us.name, " ", us.ap_paterno) as asignado_a
			 	FROM (
			 	 SELECT * FROM tb_asignacion_instal_req sr 
			 	 JOIN users u on sr.id_gestor = u.id 
			 	 WHERE sr.id_solucion = :id 
			 	) ag 
			 	JOIN users us on ag.id_programador = us.id', ['id' => $id]);

		return $instAsigExiste;

	}

	public function instalacionRqSeg($id){

		$instRqExiste = DB::select('SELECT u.name, u.ap_paterno 
			FROM tb_requerimiento r
			JOIN users u on r.id_operador = u.id
			WHERE  id_requerimiento = :id', ['id' => $id]);

		return $instRqExiste;

	}

	public function instalacionSeg($id){

		$instExiste = DB::select('SELECT id_instalacion, id_asig_instal, backup, fecha_instal, hora_instal, comentario, accesible
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

	
	public function rqPrioridadListado(){

   		$rqexaminar = DB::select('SELECT * FROM tb_requerimiento 
   			JOIN tb_cliente ON tb_requerimiento.id_cliente=tb_cliente.id_cliente 
   			JOIN tb_usuario ON tb_requerimiento.id_operador=tb_usuario.id_usuario');
   		$pagTitulo = "Cambiar prioridad a requerimiento";
   		$pag = "prioridad";

		return view('jefe_sistemas.rq_examinar_listado')->with(compact('rqexaminar','pagTitulo','pag'));

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

		return view('jefe_sistemas.rq_prioridad_editar')->with(compact('detalle','fechasOpDe','arrayPrioridad'));
    }

    public function rqPrioridadActualizar(Request $request, $id){
      //  $this->validate($request, Contract::$rules, Contract::$messages);
     	
        $requerimiento = Requerimiento::find($id);
        $requerimiento->prioridad = $request->prioridad;
        $requerimiento->motivo_cambio = $request->desc_obs;
       
    	$requerimiento->save();  // update 
		
		return redirect()->route('rqPrioridadList1')->with(array(
    		'message' => 'Cambio con exito la prioridad del requerimiento '. $id.'.!!'
    	));
    }

    // Desarrollador

    public function listaDesarrolladores(){

    	$listaDesa = DB::table('users')
    	->join('role_user','users.id', '=' , 'role_user.user_id')
		->join('roles','role_user.role_id', '=' , 'roles.id')
		->join('tb_region','users.id_region', '=' , 'tb_region.id_region')
		->where('roles.id', '=', 2)
		->select('users.id', 'users.name','users.ap_paterno','users.ap_materno','users.direccion','users.telefono','users.email','users.password','users.activo','tb_region.nombre','roles.name as name_rol')
		
		->orderBy('name', 'ASC')
		->get();

		//dd($listaDesa);

		return view('jefe_sistemas.lista_desarrollador')->with(compact('listaDesa'));

    }

    public function nuevoDesarrollador(){

    	$departamentos = DB::table('tb_region')
		->select('id_region', 'nombre')
		->orderBy('nombre', 'ASC')
		->get();
		
		return view('jefe_sistemas.nuevo_desarrollador')->with(compact('operador','departamentos'));

    }
 
    public function guardarDesarrollador(Request $request){

    	date_default_timezone_set('America/La_Paz');

		$this->validate($request, Operador::$rules, Operador::$messages);

		$desarrollador = new Operador();
		$role_user = new RoleUser();
		$id_user = Auth::user();
		
		$desarrollador->name = $request->input('nombre_ope');
		$desarrollador->ap_paterno = $request->input('paterno');
		$desarrollador->ap_materno = $request->input('materno');
		$desarrollador->direccion = $request->input('direccion');
		$desarrollador->telefono = $request->input('telefono');
		$desarrollador->email = $request->email;
		$desarrollador->activo = 'Si';
	//	$desarrollador->tipo = 'Operador';
		$desarrollador->password = '$2y$10$3uvtSUS.QUrm0m4Kuqk5TODrd06Kd9nWf2fuGT1od9UNYy7F7eMT2';
		$desarrollador->id_region = $request->departamento;

		
		if (!$desarrollador->save()){ 

			return redirect()->route('listaDesa')->with(array(
				'error' => 'Error: Al guardar el nuevo Desarrollador!. Por favor intente nuevamente.'));
		}else{
				$role_user->role_id = 2; // 2 id del rol desarrollador 
				$role_user->user_id = $desarrollador->id; 
				$role_user->save();
			 
			 return redirect()->route('listaDesa')->with(array(
				'message' => 'El nuevo Desarrollador se guardo correctamente.'));
		}

		return view('jefe_sistemas.lista_desarrollador')->with(compact('listaDesa'));

    }


    public function modificarDesarrollador($id){

		$desarrollador = Operador::find($id); // operador llama  la tabla user

		$departamentos = DB::table('tb_region')
		->select('id_region', 'nombre')
		->orderBy('nombre', 'ASC')
		->get();
		
		if(!$desarrollador){
			return redirect()->route('listaDesa')->with(array(
						'error' => 'Error: No existe el Desarrollador!. Por favor intente nuevamente.'));
		}

		return view('jefe_sistemas.modificar_desarrollador')->with(compact('desarrollador','departamentos'));

    }

 	
    public function editarDesarrollador(Request $request, $id){

    	$this->validate($request, Operador::$rules, Operador::$messages);

		if($request->ido == $id){
			$desarrollador = Operador::find($id);

			$desarrollador->name = $request->input('nombre_ope');
			$desarrollador->ap_paterno = $request->input('paterno');
			$desarrollador->ap_materno = $request->input('materno');
			$desarrollador->direccion = $request->input('direccion');
			$desarrollador->telefono = $request->input('telefono');
			$desarrollador->email = $request->email;
			$desarrollador->activo = $request->estado;
			$desarrollador->id_region = $request->departamento;
			$desarrollador->password = $desarrollador->password;
	
			if (!$desarrollador->save()){ 
				
				return redirect()->route('listaDesa')->with(array(
					'error' => 'Error: no se pudo modificar los datos del desarrollador {{ $desarrollador->nombre }} !. Por favor intente nuevamente.'));
					
			}else{ 
						return redirect()->route('listaDesa')->with(array(
							'message' => 'Los datos del Desarrollador se modificó correctamente.'));
			}
		}else{
			return redirect()->route('listaDesa')->with(array(
					'error' => 'Error: no se pudo modificar el Desarrollador {{ $desarrollador->nombre }} !. Por favor intente nuevamente.'));
		}

		return view('jefe_sistemas.lista_desarrollador')->with(compact('listaDesa'));
    }

    public function listarAsigInstalar(){


		date_default_timezone_set('America/La_Paz');

		$dateTo = date('Y-m-d');
		$calcularFecha = strtotime('-90 day',strtotime($dateTo));
	    $dateFrom = date('Y-m-d',$calcularFecha);
	  
		$rqAsigIstalar = $this->sqlAsigInstalar($dateFrom, $dateTo);
		
		
		return view('jefe_sistemas.rq_asignar_instalar')->with(compact('rqAsigIstalar','dateFrom','dateTo'));

    }

    public function searchAsigInstalar(Request $request){
	
		if(!empty($request->input('dateFrom')) && !empty($request->input('dateTo')) ){

			$rqAsigIstalar = $this->sqlAsigInstalar($request->input('dateFrom'), $request->input('dateTo'));
		}
	
		$dateFrom = $request->input('dateFrom');
		$dateTo = $request->input('dateTo');


		$perPage=20;
        $currentPage = 0;
    	$pagedData = array_slice($rqAsigIstalar, $currentPage * $perPage, $perPage);
    	
    	$rqAsigIstalar = new \Illuminate\Pagination\LengthAwarePaginator($pagedData, count($rqAsigIstalar), $perPage);
		

       	return view('jefe_sistemas.rq_asignar_instalar')->with(compact('rqAsigIstalar','dateFrom','dateTo'));

	}


    public function asigInstalar(){   

    	$listAprob = DB::table('tb_aprobacion_requerimiento')
		->where('accesible', '=' , 'Si')
		->select('nro_aprobacion','id_requerimiento','fecha_aprobacion','hora_aprobacion','accesible')
		->orderBy('id_requerimiento','ASC')
		->get();

		    	
		return view('jefe_sistemas.rq_asignar_instalar')->with(compact('listAprob'));    
	}

	public function rqDetalleAsigInstalar($id){


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
		$gestor = $this->listUserRol(3);
		
		// rol de usuario desarrollador = 2
		$desarrollador = $this->listUserRol(2);

		$fecha_plan = DB::table('tb_req_fecha')
			->where('id_requerimiento','=', $id)
			->get();

		//agregar nick del mètodo para subir y borrar archivos
		$nombreFuncion = 'detalleAprob';
		return view('jefe_sistemas.rq_detalle_asig_inst')->with(compact('detalle','adjuntos','nombreFuncion','gestor','desarrollador','fecha_plan','id'));

	}


	public function rqGuadarInstalar(Request $request, $id){
		
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


	public function sqlAsigInstalar($fecha_inicio, $fecha_fin){

		$lista = DB::select('SELECT * FROM tb_asignacion_instal_req where fecha_asig_instal BETWEEN :fecha_inicio and :fecha_fin ORDER BY id_asig_instal ASC', ['fecha_inicio' => $fecha_inicio, 'fecha_fin' => $fecha_fin]);

		return $lista;
	}

	public function listarAsigSolucionar(){

		date_default_timezone_set('America/La_Paz');

		$dateTo = date('Y-m-d');
		$calcularFecha = strtotime('-90 day',strtotime($dateTo));
	    $dateFrom = date('Y-m-d',$calcularFecha);
	  
		$rqAsigIstalar = $this->sqlAsigSolucionar($dateFrom, $dateTo);

		
		return view('jefe_sistemas.rq_asignar_solucionar')->with(compact('rqAsigIstalar','dateFrom','dateTo'));

    }

    public function searchAsigSolucionar(Request $request){
	
		if(!empty($request->input('dateFrom')) && !empty($request->input('dateTo')) ){

			$rqAsigIstalar = $this->sqlAsigSolucionar($request->input('dateFrom'), $request->input('dateTo'));
		}
	
		$dateFrom = $request->input('dateFrom');
		$dateTo = $request->input('dateTo');


		$perPage=20;
        $currentPage = 0;
    	$pagedData = array_slice($rqAsigIstalar, $currentPage * $perPage, $perPage);
    	
    	$rqAsigIstalar = new \Illuminate\Pagination\LengthAwarePaginator($pagedData, count($rqAsigIstalar), $perPage);
		

       	return view('jefe_sistemas.rq_asignar_solucionar')->with(compact('rqAsigIstalar','dateFrom','dateTo'));

	}


    public function asigSolucionar(){   

    	$listAprob = DB::table('tb_aprobacion_requerimiento')
		->where('accesible', '=' , 'Si')
		->select('nro_aprobacion','id_requerimiento','fecha_aprobacion','hora_aprobacion','accesible')
		->orderBy('id_requerimiento','ASC')
		->get();

		    	
		return view('jefe_sistemas.rq_asignar_solucionar')->with(compact('listAprob'));    
	}

	public function rqDetalleAsigSolucionar($id){

		if (!Auth::check()) {
		   return view('auth.login');	
		}

		$user = \Auth::user();

		$detalle = DB::select("
			SELECT li.*, (SELECT concat(users.name , ' ', users.ap_paterno) as nombre_completo FROM users 
			WHERE users.id = id_gestor ) asig_por, (SELECT concat(users.name , ' ', users.ap_paterno) as nombre_completo FROM users 
			WHERE users.id = id_programador ) asig_a   FROM
			(
				SELECT 
				tb_asignacion_requerimiento.Nro_asignacion,
				tb_asignacion_requerimiento.id_requerimiento,  
				tb_asignacion_requerimiento.id_gestor, 
				tb_asignacion_requerimiento.id_programador,
				tb_asignacion_requerimiento.fecha_asignacion,
				tb_asignacion_requerimiento.hora_asignacion,
				tb_solucion_requerimiento.descripcion desc_rq,
	            tb_solucion_requerimiento.accesible,
				tb_requerimiento.descripcion desc_solu,
				tb_requerimiento.id_operador 
				FROM tb_asignacion_requerimiento
				JOIN tb_solucion_requerimiento ON tb_asignacion_requerimiento.Nro_asignacion=tb_solucion_requerimiento.id_solucion
				
				JOIN tb_requerimiento ON tb_asignacion_requerimiento.id_requerimiento=tb_requerimiento.id_requerimiento 
				WHERE Nro_asignacion = :ida 
			) as li
			JOIN users ON li.id_operador = users.id", ['ida' => $id]);

		$adjuntos = DB::table('tb_adjuntos')
			->where('id_requerimiento', '=' , $id)
			->where('id_etapa', '=' , '1')
			->select('id_adjunto', 'id_requerimiento', 
					 'id_etapa', 'nombre', 
					 'fecha', 'hora')
			->get();
		// rol de usuario jefe de sistemas = 3
		$gestor = $this->listUserRol(3,$user->id);
		
		// rol de usuario desarrollador = 2
		$desarrollador = $this->listUserRol(2);

		$fecha_plan = DB::table('tb_req_fecha')
			->where('id_requerimiento','=', $id)
			->get();

		//agregar nick del mètodo para subir y borrar archivos
		$nombreFuncion = 'detalleAprob';
		
		if($detalle[0]->accesible == 'Si'){
	
			return view('jefe_sistemas.rq_detalle_asig_solucionar')->with(compact('detalle','adjuntos','nombreFuncion','gestor','desarrollador','fecha_plan','id'));
		}else{
				return redirect()->route('rqListarAsigSolu')->with(array(
					'error' => 'Este requerimiento no puede ser reasignado debido que ya fue desarrollado !. Por favor verifique su req.'));
		}

	}


	public function rqGuadarSolucionar(Request $request, $id){
		
		/*		"UPDATE tb_asignacion_requerimiento SET id_gestor=".$CODG.", ".
                     # "id_gestor=".$_POST[ 'idg' ].", ".
                      "id_programador=".$CODP.", ".
                      "fecha_asignacion='".$FECHA."', ".
                      "hora_asignacion='".$HORA."'".
                      " WHERE Nro_asignacion=".$_POST[ 'nro' ], $link);
		*/

	    date_default_timezone_set('America/La_Paz');
	    
	    $this->validate($request, AsigReqSolucion::$rules, AsigReqSolucion::$messages);
		
		// ingresar registros en asignación de requerimientos..
		$asignacionSolu = AsigReqSolucion::find($id);

		$fecha = date('Y-m-d');
		$hora = date('H:i:s');
	    $asignacionSolu->id_gestor = $request->gestor;
	    $asignacionSolu->id_programador = $request->desarrollador;

	    $asignacionSolu->fecha_asignacion = $fecha;
	    $asignacionSolu->hora_asignacion = $hora;
		//$asignacionSolu->accesible = 'Si';
    	
    	if(!$asignacionSolu->save()){  // save
	       
			return redirect()->route('detalleAsigSolu')->with(array(
		    		'error' => 'Error, el requerimiento no pudo ser reasignado. !!'
		    		));
		}

		return redirect()->route('rqListarAsigSolu')->with(array(
		    		'message' => 'El requerimiento fue asignado exitosamente.!!'
		    	)); 
	} 


	public function sqlAsigSolucionar($fecha_inicio, $fecha_fin){

		$lista = DB::select('SELECT * FROM tb_asignacion_requerimiento where fecha_asignacion BETWEEN :fecha_inicio and :fecha_fin ORDER BY nro_asignacion ASC', ['fecha_inicio' => $fecha_inicio, 'fecha_fin' => $fecha_fin]);

		return $lista;
	}
	


		
}
