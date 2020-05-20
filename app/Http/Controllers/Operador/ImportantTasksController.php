<?php

namespace App\Http\Controllers\Operador;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

use App\Requerimiento;
use App\Rquerimiento;
use App\Adjunto;
use App\Operador;
use App\AprobacionRq;
use App\DepurarReq;
use App\AsignacionReq;
use App\CertificacionRq;
use App\AsigInstalReq;
use App\SolucionRq;
use App\Instalacion;
use App\CertOnLine;
use App\FechaReq;
use App\Tiempo;
use App\Traits\RechazarFase;
use File;

class ImportantTasksController extends Controller
{
    use RechazarFase;

	public function rqDetalleAprob($id){

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
		$gestor = $this->listUserRol(3);
		
		// rol de usuario desarrollador = 2
		$desarrollador = $this->listUserRol(2);

		$fecha_plan = DB::table('tb_req_fecha')
			->where('id_requerimiento','=', $id)
			->get();
		//agregar nick del mètodo para subir y borrar archivos
		$nombreFuncion = 'detalleAprob';
		return view('operador.rq_detalle_aprob')->with(compact('detalle','adjuntos','nombreFuncion','gestor','desarrollador','fecha_plan','id'));

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
		//->where('id_etapa', '=' , '1')
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

		return view('operador.rq_seguimiento')->with(compact('seguimientoRq'));
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
		return view('operador.rq_seguimiento_detalle')->with(compact('detalle','adjuntos','nombreFuncion','arrayFases','arraycodFase','aprobacion','asignacion','desarrollo','prueba','certificacion','instalacionAsig','instalacionRqSeg','instalacion','certOnLine','adjuntosDesa','adjuntosPrue','adjuntosCert','adjuntosInsAsig','adjuntosInsta','adjuntosCeOnLine','adjuntosAcepCliente','controlSvn','aceptacionCliente'));

	}

	public function adjuntoArchivos($id, $etapa){

		if($etapa == 1000){
			$adjuntos = DB::table('tb_adjuntos')
			->where('id_requerimiento', '=' , $id)
			->select('id_adjunto', 'id_requerimiento', 
					 'id_etapa', 'nombre', 
					 'fecha', 'hora')
			->orderBy('id_adjunto', 'ASC')
			->get();
			
		}else{
			$adjuntos = DB::table('tb_adjuntos')
			->where('id_requerimiento', '=' , $id)
			->where('id_etapa', '=' , $etapa)
			->select('id_adjunto', 'id_requerimiento', 
					 'id_etapa', 'nombre', 
					 'fecha', 'hora')
			->get();
		}

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


	public function rqDepurarReq(){
	
		// Datos de la Asignacion del requerimiento
		$seguimientoRq = DB::table('tb_requerimiento')
			->join('users','tb_requerimiento.id_operador', '=' , 'users.id')
			
				
			->select('id_requerimiento', 'tipo_tarea', 'fecha_solicitud', 'tb_requerimiento.tipo', 'hora_solicitud',  'descripcion', 'name', 'ap_paterno', 'accesible')
			->where('tb_requerimiento.accesible','<>','Rm')
			->orderBy('fecha_solicitud', 'DESC')
			->get();

		return view('operador.rq_depurar')->with(compact('seguimientoRq'));
	}

	public function rqDepurarDetalle($id){
		
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
		return view('operador.rq_depurar_detalle')->with(compact('detalle','adjuntos','nombreFuncion','arrayFases','arraycodFase','aprobacion','asignacion','desarrollo','prueba','certificacion','instalacionAsig','instalacionRqSeg','instalacion','certOnLine','adjuntosDesa','adjuntosPrue','adjuntosCert','adjuntosInsAsig','adjuntosInsta','adjuntosCeOnLine','adjuntosAcepCliente','controlSvn','aceptacionCliente'));

	}

	public function rqGuardarDepurar(Request $request,$id){
	   		
			date_default_timezone_set('America/La_Paz');

		    $this->validate($request, DepurarReq::$rules, DepurarReq::$messages);
			
			$fecha = date('Y-m-d');
			$hora = date('H:i:s');

		    $depurar = new DepurarReq();
			$depurar->id_requerimiento = $request->input('idr');
			$depurar->fecha_dep = $fecha;
			$depurar->hora_dep = $hora;
			$depurar->motivo = $request->detalle_depurar;

			if ($depurar->save())
			{

				$rqUpdate = Requerimiento::find($request->input('idr'));

				if($rqUpdate){
					$rqUpdate->accesible = 'Rm';
					$rqUpdate->obs = $request->detalle_depurar;
					$rqUpdate->save();	
				}
				
				$aproUpdate = AprobacionRq::find($request->input('idr'));
				
				if($aproUpdate){
					$aproUpdate->accesible = 'Rm';
					$aproUpdate->save();	
				}

				$asigUpdate = AsignacionReq::find($request->input('idr'));
				
				if($asigUpdate){
					$asigUpdate->accesible = 'Rm';
					$asigUpdate->save();	
				}
				

				$soluUpdate = SolucionRq::find($request->input('idr'));
				
				if($soluUpdate){
					$soluUpdate->accesible = 'Rm';
					$soluUpdate->save();
				}
						
				$certUpdate = CertificacionRq::find($request->input('idr'));
				
				if($certUpdate){
					$certUpdate->accesible = 'Rm';
					$certUpdate->save();
				}

				$asigInsUpdate = AsigInstalReq::find($request->input('idr'));
				
				if($asigInsUpdate){
					$asigInsUpdate->accesible = 'Rm';
					$asigInsUpdate->save();	
				}
				
				$instUpdate = Instalacion::find($request->input('idr'));

				if($instUpdate){
					$instUpdate->accesible = 'Rm';
					$instUpdate->save();	
				}
				
				$certOnUpdate = CertOnLine::find($request->input('idr'));
				
				if($certOnUpdate){
					$certOnUpdate->accesible = 'Rm';
					$certOnUpdate->save();	
				}

			}else{
        	
        	return redirect()->route('depuradorDetalle',$request->input('idr'))->with(array(
    		'error' => 'Error, No se pudo depurar este requerimiento.!!'
    		));
        }

        return redirect()->route('rqDepurarRequeriemto')->with(array(
    		'message' => 'El requerimiento fue depurado exitosamente.!!'
    	));

	}


	public function rqListaPendientes(){
		
	
		$dateFrom = "";
		$dateTo = "";
		$rqPendientes = "";

		return view('operador.rq_lista_pendientes')->with(compact('rqPendientes','dateFrom','dateTo'));

	}

	public function searchReqPendientes(Request $request){ 
	
		if(!empty($request->input('dateFrom')) && !empty($request->input('dateTo')) ){
			$listarqPen = $this->listaPendDetalle($request->input('dateFrom'), $request->input('dateTo'));
			
			if($listarqPen){

				$arrayListRqPen = $this->requerimientosPendientes($listarqPen);

			}else{


			}

		}else{
			//dd("las fechas son nullas");
		}
	

		$dateFrom = $request->input('dateFrom');
		$dateTo = $request->input('dateTo');
   
       	$rqPendientes = $arrayListRqPen;
	
       	return view('operador.rq_lista_pendientes')->with(compact('rqPendientes','dateFrom','dateTo'));
	}

	public function requerimientosPendientes($listaRq){ 	
		
		
		$arrayP = array();
		$nombre_fase = '';
		$desc_fase = '';
		$fase_actual = '';
		$estado = '';

		foreach ($listaRq as $key => $valuerq){
		
				$id = $valuerq->id_requerimiento;
				// fase inicial 
				$detalle = DB::table('tb_requerimiento')
				->join('tb_cliente','tb_requerimiento.id_cliente', '=' , 'tb_cliente.id_cliente')
				->join('users','tb_requerimiento.id_operador', '=' , 'users.id')
				->where('tb_requerimiento.id_requerimiento', '=' , $id)
				->select('id_requerimiento', 'tb_requerimiento.accesible')
				->get();

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
									$arrayEstado['prueba'] = 1;
								}else{ 	
									$arrayEstado['prueba'] = 0;
								}

								// fase de certificaión
								$certificacion = $this->certificacionSeg($idSolu);

								if($certificacion){
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
											$arrayEstado['instalacion'] = 1;
											$faseActual['instalacion'] = 1;
										}else{
											$arrayEstado['instalacion'] = 0;
										}

										// fase de certificacion On Line
										$idInstalacion = $instalacion[0]->id_instalacion;
										$certOnLine = $this->certOnLineSeg($idInstalacion);

										if($certOnLine){
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

				foreach($arrayFases as $keyf => $valuef){
				
						if($valuef['fase_actual'] == 1){
							$nombre_fase = $valuef['nom_fase'];
							$desc_fase = $valuef['desc_fase'];
							$fase_actual = $valuef['fase_actual'];
							$estado = "pendiente";
						}else{
								if($keyf == 'aceptacion_cliente' ){
									if($valuef['fase_actual'] == 2){
										$nombre_fase = $valuef['nom_fase'];
										$desc_fase = $valuef['desc_fase'];
										$fase_actual = $valuef['fase_actual'];
										$estado = "solucion_exitosa";
									}
								}
						}
				}

				$arrayPendientes = array(  'id_requerimiento' => $id,
												'idProgramador' => $valuerq->id_programador,
												'fechaSolicitud' => $valuerq->fecha_solicitud,
												'desarrollador' => $valuerq->desarro,
												'operador' => $valuerq->operador,
												'nombreCliente' => $valuerq->nombre,
												'nombre_fase' => $nombre_fase,
												'desc_fase' => $desc_fase,
												'fase_actual' => $fase_actual,
												'estado' => $estado
				  							);
					array_push($arrayP, $arrayPendientes);
		}//end foreach	


		$objPendietes = $arrayP;

	//	dd($objPendietes);
		return $objPendietes;	
	}

	public function listaPendDetalle($fecha_inicio, $fecha_fin){

		$lista = DB::select('SELECT ap.Nro_asignacion, ap.id_requerimiento, ap.id_gestor, ap.id_programador, ap.fecha_asignacion, ap.hora_asignacion, ap.operador, CONCAT(us.name ," ",us.ap_paterno) as desarro, cl.nombre, ap.fecha_solicitud
			FROM (
					SELECT ar.Nro_asignacion, ar.id_requerimiento, ar.id_gestor, ar.id_programador, ar.fecha_asignacion, ar.hora_asignacion, CONCAT(u.name, " ", u.ap_paterno) operador,r.id_cliente, r.fecha_solicitud
					FROM tb_requerimiento r 
		    		JOIN tb_asignacion_requerimiento ar on r.id_requerimiento=ar.id_requerimiento
					JOIN users u on r.id_operador=u.id 
					WHERE r.fecha_solicitud BETWEEN :fecha_inicio and :fecha_fin
			)ap
			JOIN users us on ap.id_programador = us.id
			JOIN tb_cliente cl on ap.id_cliente = cl.id_cliente
        	ORDER BY ap.id_requerimiento DESC', ['fecha_inicio' => $fecha_inicio, 'fecha_fin' => $fecha_fin]);

		return $lista;
	}

	/** requerimientos */
	public function rqExaminarList(){

   		$rqexaminar = DB::select('SELECT DISTINCT tb_requerimiento.id_requerimiento, tb_requerimiento.tipo, tb_requerimiento.tipo_tarea, tb_requerimiento.fecha_solicitud, tb_requerimiento.hora_solicitud, tb_requerimiento.prioridad, tb_requerimiento.accesible FROM tb_requerimiento 
   			JOIN tb_cliente ON tb_requerimiento.id_cliente=tb_cliente.id_cliente 
   			JOIN users ON tb_requerimiento.id_operador=users.id 
            ORDER BY tb_requerimiento.id_requerimiento DESC');

   		$pagTitulo = "Examinar requerimiento";
   		$pag = "examinar";

		return view('operador.rq_examinar_listado')->with(compact('rqexaminar','pagTitulo','pag'));

	
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

			return view('operador.rq_examinar_detalle')->with(compact('detalle','adjuntos','nombreFuncion'));
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

		$adjuntos = DB::table('tb_adjuntos')
		->where('id_requerimiento', '=' , $id)
		->select('id_adjunto', 'id_requerimiento', 
				 'id_etapa', 'nombre', 
				 'fecha', 'hora')
		->get();
	
		//agregar nick del mètodo para subir y borrar archivos
		$nombreFuncion = 'pendDetalle';

		return view('operador.rq_editar')->with(compact('nombreFuncion','detalle','fechasOpDe','arrayPrioridad','adjuntos'));
    }

	public function rqActualizar(Request $request, $id){
    	
      //  $this->validate($request, Contract::$rules, Contract::$messages);
        $requerimiento = Requerimiento::find($id);
        $requerimiento->prioridad = $request->prioridad;
        $requerimiento->resultado = $request->desc_deseado;
        $requerimiento->descripcion = $request->descripcion;

    	$requerimiento->save();  // update 

    	return redirect()->route('rqList')->with(array(
    		'message' => 'El requerimiento se modifico exitosamente.!!'
    	));
    }

    public function nuevoRequerimiento(){

    	if (!Auth::check()) {
		   return view('auth.login');	
		}

		$user = \Auth::user();
 		
    	date_default_timezone_set('America/La_Paz');
	    
	//    $this->validate($request, AsigInstalReq::$rules, AsigInstalReq::$messages);
		
	    $last = DB::table('tb_requerimiento')->orderBy('id_requerimiento','DESC')->first();
 		$idReqUltimo = $last->id_requerimiento + 1; 

		$fecha = date('Y-m-d');
		$hora = date('H:i:s');

		$listaClientes = DB::table('tb_cliente')
		->select('id_cliente', 'nombre', 'activo')
		->orderBy('nombre', 'ASC')
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

		$arrayTipo = array(
						'Evolutivo' => 'Evolutivo', 
						'Correctivo' => 'Correctivo', 
						'Gestion' => 'Gestión'
		 );

		$arrayTipoTarea = array(
						'mantenimiento' => 'mantenimiento', 
						'Creacion_de_aplicacion' => 'Creacion de aplicacion' 
						
		 );
/*
		$fechasOpDe = DB::table('tb_req_fecha')
		->where('id_requerimiento', '=' , $id)
		->select('fecha_plan_op', 'fecha_plan_de')
		->get();*/

		$adjuntos = DB::table('tb_adjuntos')
			->where('id_requerimiento', '=' , $idReqUltimo)
			->where('id_etapa', '=' , '1')
			->select('id_adjunto', 'id_requerimiento', 
					 'id_etapa', 'nombre', 
					 'fecha', 'hora')
			->get();

		$operador = DB::table('users')
			->join('role_user', 'users.id','=','role_user.user_id')
			->join('roles', 'role_user.role_id','=','roles.id')
			->where('users.id', '=' , $user->id)
			->where('roles.id', '=' , '5')
			->where('users.activo', '=' , 'Si')
			->select('users.id','users.name','users.ap_paterno','roles.name as tipo','activo')
			->get();

		$nombreFuncion = 'nuevoReq';

		return view('operador.rq_nuevo')->with(compact('operador','listaClientes','arrayPrioridad','arrayTipo','arrayTipoTarea','operador','fecha','hora','idReqUltimo','adjuntos','nombreFuncion'));


    }
 
    public function guardarRequerimiento(Request $request){

		date_default_timezone_set('America/La_Paz');
		$this->validate($request, Rquerimiento::$rules, Rquerimiento::$messages);
		$req = new Rquerimiento();
		//$desarrollador = new Operador();
		//$role_user = new RoleUser();
		//$id_user = Auth::user();
		$req->id_requerimiento = $request->input('id_req');
		$req->tipo = $request->input('tipo');
		$req->tipo_tarea = $request->input('tipotarea');
		$req->fecha_solicitud = $request->input('fecha_soli');
		$req->hora_solicitud = $request->input('hora_soli');
		$req->id_cliente = $request->cliente;
		$req->id_operador = $request->operadorr;
		$req->prioridad = $request->prioridad;
		$req->resultado = $request->resul_dese;
		$req->descripcion = $request->desc;
		$req->accesible = 'Si';
		
		if (!$req->save()){ 

			return redirect()->route('nuevoRequerimiento')->with(array(
				'error' => 'Error: Al guardar el nuevo requerimento!. Por favor intente nuevamente.'));
		}else{
				$rqFecha = new FechaReq();

				$rqFecha->id_requerimiento = $request->input('id_req');
				$rqFecha->fecha_plan_op = $request->fechalim;
				$rqFecha->fecha_plan_de = $request->fechalim;

				$rqFecha->save();
						 	
			 	return redirect()->route('rqList')->with(array(
				'message' => 'El nuevo Requerimiento se guardo correctamente.'));
		}

		return view('jefe_sistemas.lista_desarrollador')->with(compact('listaDesa'));

    }

    public function deleteFile(Request $request){

		if($request->id_requerimiento){
			$request['id'] = $request['id_requerimiento'];
		}

	  	try {
         
            $adjunto = Adjunto::findOrFail($request['idAdjunto']);

		    $archivo_path = storage_path("app/files/{$adjunto->nombre}");

		    if (File::exists($archivo_path)) {

		       	$dfile = File::delete($archivo_path);

				if($dfile){

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

    /***Certificaciones****/

	public function reqListarCertificaciones(Request $request){
       
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
            (SELECT CONCAT(name ," ",ap_paterno) FROM users WHERE id= rq.id_operador ) solicitado_por, sl.id_solucion, sl.id_asignacion, sl.secuencia, sl.fecha_inicio, sl.hora_inicio, sl.fecha_fin, sl.hora_fin, sl.descripcion,sl.accesible
		    FROM tb_asignacion_requerimiento a 
		    JOIN tb_aprobacion_requerimiento ap ON a.id_requerimiento=ap.id_requerimiento
            JOIN tb_solucion_requerimiento sl on ap.id_requerimiento=sl.id_solucion
		    JOIN tb_requerimiento rq ON a.id_requerimiento=rq.id_requerimiento 
		    WHERE sl.accesible="Si" AND rq.id_operador = :id' , ['id' => $user->id]);

		$pagTitulo = 'Certificaciones Pre-Instalación';
		$rqAsignados = array();

		/*listdo de rq en desarrollo*/
		$rqDesarrollo = DB:: select("SELECT  req.Nro_asignacion, req.id_requerimiento, req.accesible, req.nro_aprobacion,  req.prioridad , t.fase , req.id_solucion
			FROM ( 
			    SELECT  a.Nro_asignacion, a.id_requerimiento, sl.accesible, ap.nro_aprobacion, rq.prioridad, sl.id_solucion
			    FROM tb_asignacion_requerimiento a 
			    JOIN tb_aprobacion_requerimiento ap ON a.id_requerimiento=ap.id_requerimiento
			    JOIN tb_solucion_requerimiento sl on ap.id_requerimiento=sl.id_solucion 
			    JOIN tb_requerimiento rq ON a.id_requerimiento=rq.id_requerimiento 
			    WHERE sl.accesible='Si' AND rq.id_operador = :id1
			    GROUP BY a.Nro_asignacion, a.id_requerimiento, a.accesible, ap.nro_aprobacion, rq.prioridad,sl.id_solucion  ) req 
			    JOIN tb_tiempos t ON (req.id_requerimiento = t.id_requerimiento and t.fase = 'certipru')
			    GROUP BY req.Nro_asignacion, req.id_requerimiento, req.accesible, req.nro_aprobacion,req.prioridad, req.id_solucion ", ['id1' => $user->id]);

		/*listdo de rq en pruebas*/
		$rqPrueba = DB::select("
			SELECT *, rq.id_requerimiento, rq.prioridad FROM tb_certificacion 
			JOIN tb_requerimiento rq ON  tb_certificacion.id_certificacion = rq.id_requerimiento
			WHERE tb_certificacion.accesible='Si' AND tb_certificacion.id_operador = :id1
			ORDER BY tb_certificacion.id_certificacion ASC", ['id1' => $user->id]);
	
		foreach ($rqAsig as $keya => $valuea){

			$tiempo1 = DB::select('SELECT * FROM tb_tiempos WHERE fase="certipru" AND id_requerimiento = :id', ['id' => $valuea->Nro_asignacion]);
		
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

			$tiempo = DB::select('SELECT * FROM tb_tiempos WHERE fase="certipru" and id_requerimiento = :id', ['id' => $valuet->id_requerimiento]);
			
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
		$arrayAdjTodos = array();

		foreach ($rqAsig as $keyad => $valuead){
			$arrayAdj = array();
			$adjTodos = array();

			$adjuntos = DB::table('tb_adjuntos')
			->where('id_requerimiento', '=' , $valuead->id_requerimiento)
			//->where('id_requerimiento', '=' , 3784)
			->where('id_etapa', '=' , 5)
			//->where('id_etapa', '=' , '1')
			->select('id_adjunto', 'id_requerimiento', 
					 'id_etapa', 'nombre', 
					 'fecha', 'hora')
			->get();

			if($adjuntos->isEmpty()){
				$adjVacio = array(
									'id_adjunto' => 0,
									'id_requerimiento' => $valuead->id_requerimiento,
									'id_etapa' => 5,
									'nombre' => '',
									'fecha' => '',
									'hora' => ''
								 );
				$arrayAdj[$valuead->id_requerimiento] = $adjVacio;

			}else{
				$arrayAdj[$valuead->id_requerimiento] = $adjuntos;
	
			}

			$adjTodos[$valuead->id_requerimiento] = $this->adjuntoArchivos($valuead->id_requerimiento,1000);

			array_push($arrayAdjTodos, $adjTodos);
			array_push($arrayAdjunto, $arrayAdj);
		}

		$nombreFuncion = 'reqListarCert';

		$rqAsignadosHisto = array();
		$arrayAdjuntos = json_decode(json_encode($arrayAdjunto));
		$rqAsignados = json_decode(json_encode($rqAsignados));
		$arrayAdjTodos = json_decode(json_encode($arrayAdjTodos));
	
		return view('operador.rq_certi')->with(compact('rqAsignados','rqAsignadosHisto','pagTitulo','activo','arraycodFase','arrayTiempoFin','rqDesarrollo','rqAsig','rqPrueba','arrayAdjuntos','nombreFuncion','req_id','arrayAdjTodos'));
	}

	public function revGuadarReqAsig(Request $request)
	{
		return response()->json([
			    'success'   => true,
			    'message'   => 'Los datos se han guardado correctamente.' 
			    ], 200);

 		return response()->json([
            'exception' => false,
            'success'   => false,
            'message'   => $errors 
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

			if($request->nom_fase != 'no_fase')
				$tiempo->fase = $request->nom_fase;
			else
				$tiempo->fase = 'certipru';

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
	$rqTiempo = DB::table('tb_tiempos')
		->where('id_requerimiento', '=' , $request->name)
		->where('fase', '=' , 'certipru')
		->where('estado', '=' , 'F')
		->select('id_tiempo', 'id_requerimiento', 
				 'fecha_ini', 'hora_ini',
				 'fecha_fin', 'hora_fin', 
				 'fase', 'estado')
		->get();
*/
		//dd($request);
	if($request->nom_fase != 'no_fase')
		$rqTiempo = $this->selectReqTiempo($request->nom_fase, $request->name);
	else
		$rqTiempo = $this->selectReqTiempo('certipru',$request->name);

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
		
		date_default_timezone_set('America/La_Paz');
		
		foreach ($arrayTiempo as $key => $value) {
			
			$horaini = explode(":", $value->hora_ini);
			$horafin = explode(":", $value->hora_fin);
			$fechaini = explode("-", $value->fecha_ini);
			$fechafin = explode("-", $value->fecha_fin);

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
			// mktime(horas, minutos, segundo, Mes,dia, Año);
			$timesIni = mktime($value1['hora_ini_0'],$value1['hora_ini_1'],$value1['hora_ini_2'],  $value1['fecha_ini_1'], $value1['fecha_ini_2'], $value1['fecha_ini_0']);
			$timesFin = mktime($value1['hora_fin_0'],$value1['hora_fin_1'],$value1['hora_fin_2'],  $value1['fecha_fin_1'],$value1['fecha_fin_2'],$value1['fecha_fin_0']);
			
	    	$calculoTime[] = abs($timesFin - $timesIni);
			
		}
	
		$horas = array();
		$hora_calculada = array();
		$suma_h = array();
		
		foreach($calculoTime as $key => $segs){
			$convertir_smh =  $this->convertir_seg_min_horas($segs);
			$suma_h = $this->suma_horas($convertir_smh,$suma_h);
		}

		return $suma_h;
	}

	
	public function revValidarRq(Request $request){

		$rqTiempo = DB::table('tb_tiempos')
			->where('id_requerimiento', '=' , $request->name)
			->where('fase', '=' , 'certipru')
			->select('id_tiempo', 'id_requerimiento', 
					 'fecha_ini', 'hora_ini',
					 'fecha_fin', 'hora_fin', 
					 'fase', 'estado')
			->get();

		$adjuntos = DB::table('tb_adjuntos')
			->where('id_requerimiento', '=' , $request->name)
			->where('id_etapa', '=' , '5')
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
			            'message'   =>'Error: La tarea esta en ejecución para terminar presione el boton DETENER TAREA!' 
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


	 public function revSolucionTarea(Request $request){

	 	if (!Auth::check()) {
		   return view('auth.login');	
		}

		$user = \Auth::user();
/*
 		$rqTiempo = DB::table('tb_tiempos')
			->where('id_requerimiento', '=' , $request->idRq)
			->where('fase', '=' , 'certipru')
			->select('id_tiempo', 'id_requerimiento', 
					 'fecha_ini', 'hora_ini',
					 'fecha_fin', 'hora_fin', 
					 'fase', 'estado')
			->orderBy('id_tiempo','ASC')
			->get();*/

		$certificacionRq = new CertificacionRq();
 		$fecha = date('Y')."-".date('m')."-".date('d');
		$hora = date('H').":".date('i').":".date('s');
		
		$certificacionRq->id_certificacion = $request->idRq;
		$certificacionRq->id_solucion = $request->idRq;
		$certificacionRq->id_operador = $user->id;
		/*$certificacionRq->fecha_inicio = $rqTiempo[0]->fecha_ini;
		$certificacionRq->hora_inicio = $rqTiempo[0]->hora_ini;*/
		$certificacionRq->fecha_certificacion = $fecha;
		$certificacionRq->hora_certificacion = $hora;

		$certificacionRq->detalle_certificacion = $request->texto_desc; 
		$certificacionRq->detalle_funcionalidades = $request->texto_2;
	
		$certificacionRq->accesible = 'Si';
		
		if($certificacionRq->save()){ 
			$rqSolucion = SolucionRq::find($request->idRq);
			$rqSolucion->accesible = 'No';
			$rqSolucion->save();
			
			return response()->json([
			    'success'   => true,
			    'message'   => 'El requerimiento ya se encuentra en la siguiente fase.' //Se recibe en la seccion "success", data.message
			    ], 200);

		}else{
			    return response()->json([
			            'exception' => false,
			            'success'   => false,
			            'message'   =>'Error: Requerimiento no tiene horas trabajadas!' 
			        ], 421);
			 }
    }


    private function suma_horas($hrs1,$hrs2){

    	$hora1=explode(":",$hrs1);

    	if(!empty($hrs2)){
    		$hora2=explode(":",$hrs2);
			$calculo_hms = $this->sumar_hra_min_seg($hora1[2],$hora1[1],$hora1[0],$hora2[2],$hora2[1],$hora2[0]); 
	    }else{
  			$calculo_hms = $this->sumar_hra_min_seg($hora1[2],$hora1[1],$hora1[0],0,0,0); 
	    }

	    return $calculo_hms;

	}

	public function contador_seg_min($seg_min,$temp){
	    
	    while($seg_min>=60){
	        $seg_min=$seg_min-60;
	        $temp++;
	    }
	    $hms = array('temp' => $temp, 'seg_min'=> $seg_min);
	    return $hms;
	
	}

	public function sumar_hra_min_seg($seg1,$min1,$hrs1,$seg2,$min2,$hrs2){

		$temp=0;
		$hms = array();
		$hms['temp']=0;
		$segundos=(int)$seg1+(int)$seg2;
		$hms= $this->contador_seg_min($segundos,$hms['temp']);
		$segundos =$hms['seg_min'];
		//sumo minutos 
		$minutos=(int)$min1+(int)$min2+$hms['temp'];
		$temp=0;
		$hms['temp']=0;
		$hms= $this->contador_seg_min($minutos,$hms['temp']);
		$minutos =$hms['seg_min'];
		//sumo horas 
		$horas=(int)$hrs1+(int)$hrs2+$hms['temp'];

		if($horas<10)
			$horas= '0'.$horas;

		if($minutos<10)
			$minutos= '0'.$minutos;

		if($segundos<10)
			$segundos= '0'.$segundos;

		$sumar_hms = $horas.':'.$minutos.':'.$segundos;

		return $sumar_hms;

	}

	public function convertir_seg_min_horas($seds) {

		$horas = floor($seds / 3600);
		$minutos = floor(($seds - ($horas * 3600)) / 60);
		$segundos = $seds - ($horas * 3600) - ($minutos * 60);
		
		return $horas . ':' . $minutos . ":" . $segundos;
	}

	/**
		Certificación online!
	**/

	public function revListarCertOnline(){

    	if (!Auth::check()) {
		   return view('auth.login');	
		}

		$user = \Auth::user();
		
		$rqAsigIstalar = DB::select("
		
		SELECT i.id_instalacion, i.id_asig_instal,
				(SELECT CONCAT(name,' ',ap_paterno) FROM users WHERE id=inst_req.id_gestor ) nom_gestor , 
				(SELECT CONCAT(name,' ',ap_paterno) FROM users WHERE id=inst_req.id_programador ) nom_prog , 
				i.fecha_instal, i.hora_instal, i.accesible 
		FROM tb_instalacion i 
		JOIN tb_asignacion_instal_req inst_req on i.id_instalacion = inst_req.id_asig_instal 
		join tb_usuario on inst_req.id_programador=tb_usuario.id_usuario
		JOIN tb_requerimiento r on inst_req.id_asig_instal = r.id_requerimiento 
		JOIN tb_asignacion_requerimiento A on R.id_requerimiento=A.id_requerimiento 
		JOIN tb_solucion_requerimiento S on A.Nro_asignacion=S.id_asignacion 
		WHERE i.accesible='Si' AND r.id_operador =:idi
		ORDER BY inst_req.id_asig_instal ASC ", ['idi' => $user->id]);


		return view('operador.rq_list_cert_online')->with(compact('rqAsigIstalar'));

    }

	public function asigInstalar(){   

    	$listAprob = DB::table('tb_aprobacion_requerimiento')
		->where('accesible', '=' , 'Si')
		->select('nro_aprobacion','id_requerimiento','fecha_aprobacion','hora_aprobacion','accesible')
		->orderBy('id_requerimiento','ASC')
		->get();

		    	
		return view('Operador.rq_asignar_instalar')->with(compact('listAprob'));    
	}

	public function revDetalleCertOnline(Request $request,$id){

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
	
		$rqAsignados = array();
		$nombre_fase = 'certonline';

		$tiempo_vacio = 0;

		$rqAsig = DB::select('SELECT i.id_instalacion, i.id_asig_instal, i.fecha_instal, i.hora_instal, a.Nro_asignacion,
		    a.id_requerimiento,a.id_gestor,a.id_programador,a.fecha_asignacion,a.hora_asignacion,
		    a.accesible as accesible_asig,ap.nro_aprobacion,ap.fecha_aprobacion,ap.hora_aprobacion, 
		    rq.prioridad, rq.tipo, rq.fecha_solicitud, rq.hora_solicitud, rq.accesible,
		    rq.descripcion, rq.resultado,
		    (SELECT CONCAT(name ," ",ap_paterno) FROM users WHERE id= ai.id_gestor ) asig_por,
		    (SELECT CONCAT(name ," ",ap_paterno) FROM users WHERE id= ai.id_programador ) asig_a,
            (SELECT CONCAT(name ," ",ap_paterno) FROM users WHERE id= rq.id_operador ) solicitado_por,
            (SELECT CONCAT(name ," ",ap_paterno) FROM users WHERE id= ce.id_operador ) certificado_por,
            (SELECT CONCAT(name ," ",ap_paterno) FROM users WHERE id= ai.id_programador ) instal_por,
            s.fecha_inicio, s.hora_inicio, s.fecha_fin, s.hora_fin, s.secuencia, ce.id_certificacion, ce.fecha_certificacion, ce.hora_certificacion, ce.detalle_certificacion, ce.detalle_funcionalidades
		    FROM tb_instalacion i
		    JOIN tb_asignacion_requerimiento a ON i.id_instalacion = a.id_requerimiento
		    JOIN tb_aprobacion_requerimiento ap ON a.id_requerimiento=ap.id_requerimiento
		    JOIN tb_requerimiento rq ON a.id_requerimiento=rq.id_requerimiento
            JOIN tb_solucion_requerimiento s on a.Nro_asignacion=s.id_solucion 
            JOIN tb_asignacion_instal_req ai on s.id_solucion=ai.id_solucion
            JOIN tb_certificacion ce ON s.id_solucion = ce.id_solucion
            WHERE i.accesible="Si" AND i.id_instalacion=:id 
            ORDER BY i.fecha_instal ASC', ['id' => $id]);

	//	dd($rqAsig);

		$pagTitulo = 'Detalle del requerimiento Certicaión Online';

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
								'id_fase10' => 10);

		$arrayTiempoFin = array();
		
		foreach ($rqAsig as $keyt => $valuet){

			$tiempo = DB::select('SELECT * FROM tb_tiempos WHERE fase=:f AND id_requerimiento = :id', ['id' => $valuet->id_requerimiento, 'f'=>$nombre_fase]);
		
			if($tiempo){
				foreach ($tiempo as $keytt => $valuett){
					if($tiempo[$keytt]->fase == $nombre_fase and $tiempo[$keytt]->estado == 'I'){
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
		$arrayAdjTodos = array();

		foreach ($rqAsig as $keyad => $valuead){
			$arrayAdj = array();
			$adjTodos = array();

			$adjuntos = DB::table('tb_adjuntos')
			->where('id_requerimiento', '=' , $valuead->id_requerimiento)
			//->where('id_requerimiento', '=' , 3784)
			->where('id_etapa', '=' , 8)
			//->where('id_etapa', '=' , '1')
			->select('id_adjunto', 'id_requerimiento', 
					 'id_etapa', 'nombre', 
					 'fecha', 'hora')
			->get();

			if($adjuntos->isEmpty()){
				$adjVacio = array ( 0 => array(
									'id_adjunto' => 0,
									'id_requerimiento' => $valuead->id_requerimiento,
									'id_etapa' => 8,
									'nombre' => '',
									'fecha' => '',
									'hora' => ''
								 ));
				$arrayAdj[$valuead->id_requerimiento] = $adjVacio;
				$adj_vacio=0;

			}else{ $adj_vacio=1;
				$arrayAdj[$valuead->id_requerimiento] = $adjuntos;
	
			}

			$adjTodos[$valuead->id_requerimiento] = $this->adjuntoArchivos($valuead->id_requerimiento,1000);

			array_push($arrayAdjTodos, $adjTodos);
			array_push($arrayAdjunto, $arrayAdj);
		}
 
		$nombreFuncion = 'detalleCertificacionOnLine';
		

		$rqAsignadosHisto = array();
		$arrayAdjuntos = json_decode(json_encode($arrayAdjunto));
		$rqAsignados = json_decode(json_encode($rqAsignados));
		$arrayAdjTodos = json_decode(json_encode($arrayAdjTodos));
	
		return view('operador.rq_det_cert_online')->with(compact('detalle','rqAsignados','rqAsignadosHisto','pagTitulo','activo','arraycodFase','arrayTiempoFin','rqDesarrollo','rqAsig','rqPrueba','arrayAdjuntos','nombreFuncion','req_id','arrayAdjTodos','nombre_fase','adj_vacio'));
		

	}

	public function revGuadarCertOnline(Request $request, $id){
				
	    date_default_timezone_set('America/La_Paz');
	    $tiempo_out = array();
		
		$user = \Auth::user();

		if (!Auth::check()) {
		   return view('auth.login');	
		}

		//$this->validate($request, instalacion::$rules, instalacion::$messages);
	    $tiempo = DB::select('SELECT * FROM tb_tiempos WHERE fase="certonline" AND id_requerimiento = :id 
	    	ORDER BY id_tiempo DESC', ['id' => $id]);
	    
	    $tiempo_out[0] = 0;
	    $tiempo_out[1] = 0;

	    if($tiempo){
		  	foreach($tiempo as $key => $value1){
		    	if($value1->estado == 'I'){
		    		$tiempo_out[0]= 1;
		    	}
		    	if($value1->estado == 'F'){
		    		$tiempo_out[1] = 1;
		    	}
		    }
        }

        if($tiempo_out[0] == 1){
        	return redirect()->route('detalleCertificacionOnLine', $id)->with(array(
			    		'error' => 'Error!. Detenga las tareas de certificación online por favor!.'
			    		));
        }
      // print_r($tiempo);
	   // dd($request);

        if($tiempo_out[1] == 1){
			if(!empty($request->textDesc)){
				// ingresar registros en asignación de requerimientos..
				$certOnlinerq = new CertOnline();

				$fecha = date('Y-m-d');
				$hora = date('H:i:s');

			    $certOnlinerq->id_certificacion_online = $request->id_reqqq;
			    $certOnlinerq->id_instalacion = $request->id_reqqq;
				$certOnlinerq->id_operador = $user->id;
			    $certOnlinerq->conformidad = $request->textDesc;
			    $certOnlinerq->fecha_certificacion = $fecha;
			    $certOnlinerq->hora_certificacion = $hora;
				$certOnlinerq->accesible = 'Si';
			
		    	if($certOnlinerq->save()){// save
			        //actualizar el campo accesible de la tabla 

			        $rqInstalacion = Instalacion::find($id);
			    	$rqInstalacion->accesible = 'No';
			        $rqInstalacion->save();
			  		
			  		return redirect()->route('revCertificacionOnLine')->with(array(
				    		'message' => 'El requerimiento fue certificado(online)! exitosamente!.'
				    	));
				}
				return redirect()->route('detalleCertificacionOnLine', $id)->with(array(
			    		'error' => 'Error, vuelva a intentar otra vez!.'
			    		));
			}else{
				return redirect()->route('detalleCertificacionOnLine', $id)->with(array(
			    		'error' => 'Error, El campo conformidad es obligatorio!.'
			    		));
			    }
		}else{
			return redirect()->route('detalleCertificacionOnLine', $id)->with(array(
			    		'error' => 'Error!. Inicie las tareas de certificación Online por favor!.'
			    		));
		}
    	
	}

	private function selectReqTiempo($req_fase,$id_req){
		
		$reqTiempo = DB::table('tb_tiempos')
		->where('id_requerimiento', '=' , $id_req)
		->where('estado', '=' , 'F')
		->where('fase', '=' , $req_fase)
		->select('id_tiempo', 'id_requerimiento', 
				 'fecha_ini', 'hora_ini',
				 'fecha_fin', 'hora_fin', 
				 'fase', 'estado')
		->get();

		return $reqTiempo;
	}

	public function revListarCertSvn(){

    	if (!Auth::check()) {
		   return view('auth.login');	
		}

		$user = \Auth::user();
		
		$rqControVer = DB::select("SELECT * FROM tb_control_svn cv 
			JOIN tb_certificacion_online co ON cv.id_certificacion_online=co.id_certificacion_online 
			WHERE cv.accesible='Si' AND co.id_operador= :idi ORDER BY fecha_subversion ASC ", ['idi' => $user->id]);


		return view('operador.rq_list_control_svn')->with(compact('rqControVer'));

    }

    public function revDetalleCertSvn(Request $request,$id){

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

		$rqAsignados = array();
		$nombre_fase = 'instlinea';

		$tiempo_vacio = 0;

		$rqAsig = DB::select('SELECT i.id_instalacion, i.id_asig_instal, i.fecha_instal, i.hora_instal, a.Nro_asignacion,
		    a.id_requerimiento,a.id_gestor,a.id_programador,a.fecha_asignacion,a.hora_asignacion,
		    a.accesible as accesible_asig,ap.nro_aprobacion,ap.fecha_aprobacion,ap.hora_aprobacion, 
		    rq.prioridad, rq.tipo, rq.fecha_solicitud, rq.hora_solicitud, rq.accesible,
		    rq.descripcion, rq.resultado,
		    (SELECT CONCAT(name ," ",ap_paterno) FROM users WHERE id= ai.id_gestor ) asig_por,
		    (SELECT CONCAT(name ," ",ap_paterno) FROM users WHERE id= ai.id_programador ) asig_a,
            (SELECT CONCAT(name ," ",ap_paterno) FROM users WHERE id= rq.id_operador ) solicitado_por,
            (SELECT CONCAT(name ," ",ap_paterno) FROM users WHERE id= ce.id_operador ) certificado_por,
            (SELECT CONCAT(name ," ",ap_paterno) FROM users WHERE id= ai.id_programador ) instal_por,
            (SELECT CONCAT(name ," ",ap_paterno) FROM users WHERE id= co.id_operador ) cert_online_por,
            s.fecha_inicio, s.hora_inicio, s.fecha_fin, s.hora_fin, s.secuencia, ce.id_certificacion, ce.fecha_certificacion, ce.hora_certificacion, ce.detalle_certificacion, ce.detalle_funcionalidades, co.id_certificacion_online, co.fecha_certificacion fecha_certificacion_online, co.hora_certificacion hora_certificacion_online, co.conformidad, co.accesible, 
            	svn.id_control_svn, svn.id_certificacion_online, svn.fecha_subversion, svn.hora_subversion, svn.fecha_cert, svn.hora_cert, svn.comentarios
		    FROM 
            ( 
            	SELECT id_control_svn, id_certificacion_online, id_operador, fecha_subversion, hora_subversion, fecha_cert, hora_cert, comentarios
            	FROM tb_control_svn
            	WHERE id_control_svn =:id ) svn

		    JOIN tb_certificacion_online co on svn.id_control_svn=co.id_certificacion_online
		    JOIN tb_instalacion i on co.id_instalacion= i.id_instalacion
		    JOIN tb_asignacion_requerimiento a ON i.id_instalacion = a.id_requerimiento
		    JOIN tb_aprobacion_requerimiento ap ON a.id_requerimiento=ap.id_requerimiento
		    JOIN tb_requerimiento rq ON a.id_requerimiento=rq.id_requerimiento
            JOIN tb_solucion_requerimiento s on a.Nro_asignacion=s.id_solucion 
            JOIN tb_asignacion_instal_req ai on s.id_solucion=ai.id_solucion
            JOIN tb_certificacion ce ON s.id_solucion = ce.id_solucion
            
            ORDER BY co.fecha_certificacion ASC', ['id' => $id]);

		    //WHERE a.accesible="Si" AND a.id_programador = :id', ['id' => $user->id]);

		$pagTitulo = 'Detalle del requerimiento Control de versiones pendientes';

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
								'id_fase10' => 10);

		$arrayTiempoFin = array();
		
		foreach ($rqAsig as $keyt => $valuet){

			$tiempo = DB::select('SELECT * FROM tb_tiempos WHERE fase="instlinea" AND id_requerimiento = :id', ['id' => $valuet->id_requerimiento]);
		
			if($tiempo){
				foreach ($tiempo as $keytt => $valuett){
					if($tiempo[$keytt]->fase == $nombre_fase and $tiempo[$keytt]->estado == 'I'){
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
		$arrayAdjTodos = array();

		foreach ($rqAsig as $keyad => $valuead){
			$arrayAdj = array();
			$adjTodos = array();

			$adjuntos = DB::table('tb_adjuntos')
			->where('id_requerimiento', '=' , $valuead->id_requerimiento)
			//->where('id_requerimiento', '=' , 3784)
			->where('id_etapa', '=' , 8)
			//->where('id_etapa', '=' , '1')
			->select('id_adjunto', 'id_requerimiento', 
					 'id_etapa', 'nombre', 
					 'fecha', 'hora')
			->get();

			if($adjuntos->isEmpty()){
				$adjVacio = array ( 0 => array(
									'id_adjunto' => 0,
									'id_requerimiento' => $valuead->id_requerimiento,
									'id_etapa' => 8,
									'nombre' => '',
									'fecha' => '',
									'hora' => ''
								 ));
				$arrayAdj[$valuead->id_requerimiento] = $adjVacio;
				$adj_vacio=0;

			}else{ $adj_vacio=1;
				$arrayAdj[$valuead->id_requerimiento] = $adjuntos;
			}

			$adjTodos[$valuead->id_requerimiento] = $this->adjuntoArchivos($valuead->id_requerimiento,1000);

			array_push($arrayAdjTodos, $adjTodos);
			array_push($arrayAdjunto, $arrayAdj);
		}
 
		$nombreFuncion = 'detalleControlVerPendientes';
		

		$rqAsignadosHisto = array();
		$arrayAdjuntos = json_decode(json_encode($arrayAdjunto));
		$rqAsignados = json_decode(json_encode($rqAsignados));
		$arrayAdjTodos = json_decode(json_encode($arrayAdjTodos));
								
		return view('operador.rq_detalle_control_svn')->with(compact('detalle','rqAsignados','rqAsignadosHisto','pagTitulo','activo','arraycodFase','arrayTiempoFin','rqDesarrollo','rqAsig','rqPrueba','arrayAdjuntos','nombreFuncion','req_id','arrayAdjTodos','nombre_fase','adj_vacio'));
	}

	public function revGuadarCertSvn(Request $request, $id){
		
	    date_default_timezone_set('America/La_Paz');
	    
	    $user = \Auth::user();

		if (!Auth::check()) {
		   return view('auth.login');	
		}
	    if(!empty($request->textDesc)){

				// ingresar registros en asignación de requerimientos..
				$rqCertsVN = ControlSvn::find($id);
				$fecha = date('Y-m-d');
				$hora = date('H:i:s');
				$rqCertsVN->fecha_cert = $fecha;
				$rqCertsVN->hora_cert = $hora;
				$rqCertsVN->id_operador = $user->id;
				$rqCertsVN->accesible = 'No';

		    	if($rqCertsVN->save()){// save
			        
			        return redirect()->route('revCertificacionSvn')->with(array(
				    		'message' => 'El requerimiento fue registrado exitosamente!.'
				    	));
					
				}
				return redirect()->route('detalleCertificacionSvn', $id)->with(array(
			    		'error' => 'Error, vuelva a intentar otra vez!.'
			    		));

			}else{
				return redirect()->route('detalleCertificacionSvn', $id)->with(array(
			    		'error' => 'Error, El campo Comentario es obligatorio!.'
			    		));
			    }
	}

	public function revRechazarCertSvn(Request $request, $id){
		
	    date_default_timezone_set('America/La_Paz');
	    
	    $test = $this->pruebatrait(1000);
dd($test);
	    $user = \Auth::user();

		if (!Auth::check()) {
		   return view('auth.login');	
		}
	    if(!empty($request->textDesc)){

				// ingresar registros en asignación de requerimientos..
				$rqCertsVN = ControlSvn::find($id);
				$fecha = date('Y-m-d');
				$hora = date('H:i:s');
				$rqCertsVN->fecha_cert = $fecha;
				$rqCertsVN->hora_cert = $hora;
				$rqCertsVN->id_operador = $user->id;
				$rqCertsVN->accesible = 'No';

		    	if($rqCertsVN->save()){// save
			        
			        return redirect()->route('revCertificacionSvn')->with(array(
				    		'message' => 'El requerimiento fue registrado exitosamente!.'
				    	));
					
				}
				return redirect()->route('detalleCertificacionSvn', $id)->with(array(
			    		'error' => 'Error, vuelva a intentar otra vez!.'
			    		));

			}else{
				return redirect()->route('detalleCertificacionSvn', $id)->with(array(
			    		'error' => 'Error, El campo Comentario es obligatorio!.'
			    		));
			    }
	}




}
