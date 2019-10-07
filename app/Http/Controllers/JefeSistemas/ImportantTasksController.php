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
use File;


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

		$gestor = DB::table('users')
			->join('role_user', 'users.id','=','role_user.user_id')
			->join('roles', 'role_user.role_id','=','roles.id')
			->where('roles.id', '=' , '3')
			->where('users.activo', '=' , 'Si')
			->select('users.id','users.name','users.ap_paterno','roles.name as tipo','activo')
			->get();
			
		$desarrollador = DB::table('users')
			->join('role_user', 'users.id','=','role_user.user_id')
			->join('roles', 'role_user.role_id','=','roles.id')
			->where('roles.id', '=' , '2')
			->where('users.activo', '=' , 'Si')
			->select('users.id','users.name','users.ap_paterno','roles.name as tipo','activo')
			->get();

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
}
