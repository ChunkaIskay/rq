<?php

namespace App\Http\Controllers\Verificador;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;


use App\DepurarReq;
use App\Requerimiento;
use App\AprobacionRq;
use App\AsignacionReq;
use App\SolucionRq;
use App\CertificacionRq;
use App\AsigInstalReq;
use App\Instalacion;
use App\CertOnLine;



class ImportantTasksController extends Controller
{
    public function rqDepurarReq(){
	
		// Datos de la Asignacion del requerimiento
		$seguimientoRq = DB::table('tb_requerimiento')
			->join('users','tb_requerimiento.id_operador', '=' , 'users.id')
			->select('id_requerimiento', 'tipo_tarea', 'fecha_solicitud', 'tb_requerimiento.tipo', 'hora_solicitud',  'descripcion', 'name', 'ap_paterno', 'accesible')
			->where('tb_requerimiento.accesible','<>','Rm')
			->orderBy('fecha_solicitud', 'DESC')
			->get();

		return view('verificador.rq_depurar')->with(compact('seguimientoRq'));
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
		return view('verificador.rq_depurar_detalle')->with(compact('detalle','adjuntos','nombreFuncion','arrayFases','arraycodFase','aprobacion','asignacion','desarrollo','prueba','certificacion','instalacionAsig','instalacionRqSeg','instalacion','certOnLine','adjuntosDesa','adjuntosPrue','adjuntosCert','adjuntosInsAsig','adjuntosInsta','adjuntosCeOnLine','adjuntosAcepCliente','controlSvn','aceptacionCliente'));

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
        	
        	return redirect()->route('depuradorDetalleVeri',$request->input('idr'))->with(array(
    		'error' => 'Error, No se pudo depurar este requerimiento.!!'
    		));
        }

        return redirect()->route('rqDepurarReqVeri')->with(array(
    		'message' => 'El requerimiento fue depurado exitosamente.!!'
    	));

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


public function rqSeguimiento(){
	
		// Datos de la Asignacion del requerimiento
		$seguimientoRq = DB::table('tb_requerimiento')
		->join('users','tb_requerimiento.id_operador', '=' , 'users.id')
		
		->select('id_requerimiento', 'tipo_tarea', 'fecha_solicitud', 'tb_requerimiento.tipo', 'hora_solicitud',  'descripcion', 'name', 'ap_paterno', 'accesible')
		->orderBy('fecha_solicitud', 'DESC')
		->get();

		return view('verificador.rq_seguimiento')->with(compact('seguimientoRq'));
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
		return view('verificador.rq_seguimiento_detalle')->with(compact('detalle','adjuntos','nombreFuncion','arrayFases','arraycodFase','aprobacion','asignacion','desarrollo','prueba','certificacion','instalacionAsig','instalacionRqSeg','instalacion','certOnLine','adjuntosDesa','adjuntosPrue','adjuntosCert','adjuntosInsAsig','adjuntosInsta','adjuntosCeOnLine','adjuntosAcepCliente','controlSvn','aceptacionCliente'));

	}



}