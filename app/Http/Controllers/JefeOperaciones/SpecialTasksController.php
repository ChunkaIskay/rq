<?php

namespace App\Http\Controllers\JefeOperaciones;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

use App\Cliente;
use App\Operador;
use Illuminate\Support\Facades\Auth;


class SpecialTasksController extends Controller
{
    
    public function listaClientes(){

    	$listaClientes = DB::table('tb_cliente')
		->select('id_cliente', 'nombre', 'activo')
		->orderBy('nombre', 'ASC')
		->get();

		return view('jefe_operaciones.lista_clientes')->with(compact('listaClientes'));

    }

    public function nuevoCliente(){

		return view('jefe_operaciones.nuevo_cliente')->with(compact('nuevoCliente'));

    }
 
    public function guardarCliente(Request $request){

		
		$last = DB::table('tb_cliente')->orderBy('id_cliente', 'DESC')->first();
 		$idUltimo = $last->id_cliente +1; 
		
		$cliente = new Cliente();

		$cliente->nombre = $request->input('nombre_cli');
		$cliente->activo = $request->estado;
		$cliente->id_cliente = $idUltimo;

		if (!$cliente->save()){ 
			
					return redirect()->route('listaClientes')->with(array(
						'error' => 'Error: Al guardar el nueco cliente!. Por favor intente nuevamente.'));
				
		}else{ 
					return redirect()->route('listaClientes')->with(array(
						'message' => 'El nuevo cliente se guardo correctamente.'));
		}

		return view('jefe_operaciones.lista_clientes')->with(compact('listaClientes'));

    }

    public function modificarCliente($id){


		$cliente = Cliente::find($id);

		if(!$cliente){
			return redirect()->route('listaClientes')->with(array(
						'error' => 'Error: No existe el cliente!. Por favor intente nuevamente.'));
		}

		return view('jefe_operaciones.modificar_cliente')->with(compact('cliente'));

    }
 	
    public function editarCliente(Request $request){

		$cliente = Cliente::find($request->idc);

		$cliente->nombre = $request->input('nombre_cli');
		$cliente->activo = $request->estado;
	
		if (!$cliente->save()){ 
			
			return redirect()->route('listaClientes')->with(array(
				'error' => 'Error: no se pudo modificar el cliente {{ $cliente->nombre }} !. Por favor intente nuevamente.'));
				
		}else{ 
					return redirect()->route('listaClientes')->with(array(
						'message' => 'El cliente se modificó correctamente.'));
		}

		return view('jefe_operaciones.lista_clientes')->with(compact('listaClientes'));

    }


    public function listaOperadores(){

    	$listaOpe = DB::table('users')
    	->join('tb_region','users.id_region', '=' , 'tb_region.id_region')
		->select('users.id', 'users.name','users.ap_paterno','users.ap_materno','users.direccion','users.telefono','users.email','users.password','users.activo','tb_region.nombre')
		->orderBy('name', 'ASC')
		->get();

		return view('jefe_operaciones.lista_operadores')->with(compact('listaOpe'));

    }

    public function nuevoOperador(){

    	$departamentos = DB::table('tb_region')
		->select('id_region', 'nombre')
		->orderBy('nombre', 'ASC')
		->get();
		
		return view('jefe_operaciones.nuevo_operador')->with(compact('operador','departamentos'));

    }
 
    public function guardarOperador(Request $request){
		
		$this->validate($request, Operador::$rules, Operador::$messages);

		$operador = new Operador();
		$role_user = new RoleUser();
		$id_user = Auth::user();
		

		$operador->name = $request->input('nombre_ope');
		$operador->ap_paterno = $request->input('paterno');
		$operador->ap_materno = $request->input('materno');
		$operador->direccion = $request->input('direccion');
		$operador->telefono = $request->input('telefono');
		$operador->email = $request->email;
		$operador->activo = 'Si';
		$operador->tipo = 'Operador';
		$operador->password = '$2y$10$3uvtSUS.QUrm0m4Kuqk5TODrd06Kd9nWf2fuGT1od9UNYy7F7eMT2';
		$operador->id_region = $request->departamento;

		if (!$operador->save()){ 
			return redirect()->route('listaOperadores')->with(array(
				'error' => 'Error: Al guardar el nuevo Operador!. Por favor intente nuevamente.'));
				
		}else{ 

			    $role_user->role_id = 5; // 5 id del rol operaddor 
				$role_user->user_id = $operador->id; 
				$role_user->save();

			return redirect()->route('listaOperadores')->with(array(
				'message' => 'El nuevo Operador se guardo correctamente.'));
		}

		return view('jefe_operaciones.lista_operadores')->with(compact('listaOperadores'));

    }

    public function modificarOperador($id){

		$operador = Operador::find($id);

		$departamentos = DB::table('tb_region')
		->select('id_region', 'nombre')
		->orderBy('nombre', 'ASC')
		->get();
		
		if(!$operador){
			return redirect()->route('listaOperadores')->with(array(
						'error' => 'Error: No existe el Operador!. Por favor intente nuevamente.'));
		}

		return view('jefe_operaciones.modificar_operador')->with(compact('operador','departamentos'));

    }
 	
    public function editarOperador(Request $request, $id){

    	$this->validate($request, Operador::$rules, Operador::$messages);

		if($request->ido == $id){
			$operador = Operador::find($id);

			$operador->name = $request->input('nombre_ope');
			$operador->ap_paterno = $request->input('paterno');
			$operador->ap_materno = $request->input('materno');
			$operador->direccion = $request->input('direccion');
			$operador->telefono = $request->input('telefono');
			$operador->email = $request->email;
			$operador->activo = $request->estado;
			$operador->id_region = $request->departamento;
			$operador->password = $operador->password;
	
			if (!$operador->save()){ 
				
				return redirect()->route('listaOperadores')->with(array(
					'error' => 'Error: no se pudo modificar los datos del Operador {{ $Operador->nombre }} !. Por favor intente nuevamente.'));
					
			}else{ 
						return redirect()->route('listaOperadores')->with(array(
							'message' => 'Los datos del Operador se modificó correctamente.'));
			}
		}else{
			return redirect()->route('listaOperadores')->with(array(
					'error' => 'Error: no se pudo modificar el Operador {{ $Operador->nombre }} !. Por favor intente nuevamente.'));
		}

		return view('jefe_operaciones.lista_operadores')->with(compact('listaOperadores'));
    }


    public function listaCertificaciones(){

		$fecha = date('Y-m-j');
	    
	    $calcularFecha = strtotime('-1 month',strtotime($fecha));
	    $dateFrom = date('Y-m-d',$calcularFecha);
	    $dateTo = date('Y-m-d',strtotime($fecha));

	    	$listaCert = DB::table('tb_certificacion')
	    	->select('id_certificacion', 'id_solucion','id_operador','fecha_certificacion','hora_certificacion','detalle_certificacion','detalle_funcionalidades')
	    	->whereBetween('fecha_certificacion', [$dateFrom, $dateTo])
			->orderBy('fecha_certificacion' , 'hora_certificacion', 'DESC')
			->get();

		return view('jefe_operaciones.lista_certificaciones')->with(compact('listaCert','dateFrom','dateTo'));

    }

    public function searchCertificaciones(Request $request){
	
		    $dateFrom = $request->dateFrom;
		    $dateTo = $request->dateTo;

	    	$listaCert = DB::table('tb_certificacion')
	    	->select('id_certificacion', 'id_solucion','id_operador','fecha_certificacion','hora_certificacion','detalle_certificacion','detalle_funcionalidades')
	    	->whereBetween('fecha_certificacion', [$dateFrom, $dateTo])
			->orderBy('fecha_certificacion' , 'hora_certificacion', 'DESC')
			->get();

		return view('jefe_operaciones.lista_certificaciones')->with(compact('listaCert','dateFrom','dateTo'));

    }

    public function listaCertificacionesOnline(){

		$fecha = date('Y-m-j');
	    $fecha = date('Y-m-j');
	    
	    $calcularFecha = strtotime('-5 month',strtotime($fecha));
	    $dateFrom = date('Y-m-d',$calcularFecha);
	    $dateTo = date('Y-m-d',strtotime($fecha));

    	$listaCertOnline= DB::table('tb_certificacion_online')
    	->join('users','tb_certificacion_online.id_operador', '=' , 'users.id')
    	->select('tb_certificacion_online.id_certificacion_online', 'tb_certificacion_online.id_instalacion','users.name','users.ap_paterno','tb_certificacion_online.fecha_certificacion','tb_certificacion_online.hora_certificacion','tb_certificacion_online.conformidad')
    	->whereBetween('fecha_certificacion', [$dateFrom, $dateTo])
		->orderBy('fecha_certificacion' , 'hora_certificacion', 'DESC')
		->get();

		return view('jefe_operaciones.lista_cert_online')->with(compact('listaCertOnline','dateFrom','dateTo'));

    }


    public function searchCertificacionesOnline(Request $request){
	
		    $dateFrom = $request->dateFrom;
		    $dateTo = $request->dateTo;

	    	$listaCertOnline= DB::table('tb_certificacion_online')
	    	->join('users','tb_certificacion_online.id_operador', '=' , 'users.id')
	    	->select('tb_certificacion_online.id_certificacion_online', 'tb_certificacion_online.id_instalacion','users.name','users.ap_paterno','tb_certificacion_online.fecha_certificacion','tb_certificacion_online.hora_certificacion','tb_certificacion_online.conformidad')
	    	->whereBetween('fecha_certificacion', [$dateFrom, $dateTo])
			->orderBy('fecha_certificacion' , 'hora_certificacion', 'DESC')
			->get();

		return view('jefe_operaciones.lista_cert_online')->with(compact('listaCertOnline','dateFrom','dateTo'));

    }
    
    //Métodos del graficos estadisticos.

    public function reportes(){

    	$fecha_actual = date('Y-m-d'); 
    	//dd($fecha_actual);
    	$anio = date('Y');

    	$anio_actual = DB::select('SELECT
								(
								SELECT count(*)
								FROM tb_requerimiento 
								WHERE MONTH(fecha_solicitud) = 1 and YEAR(fecha_solicitud) = "'.$anio.'"
								) as enero,
								(
								SELECT count(*)
								FROM tb_requerimiento 
								WHERE MONTH(fecha_solicitud) = 2 and YEAR(fecha_solicitud) = "'.$anio.'"
								) as febrero,
								(
								SELECT count(*)
								FROM tb_requerimiento 
								WHERE MONTH(fecha_solicitud) = 3 and YEAR(fecha_solicitud) = "'.$anio.'"
								) as marzo,
								(
								SELECT count(*)
								FROM tb_requerimiento 
								WHERE MONTH(fecha_solicitud) = 4 and YEAR(fecha_solicitud) = "'.$anio.'"
								) as abril,
								(
								SELECT count(*)
								FROM tb_requerimiento 
								WHERE MONTH(fecha_solicitud) = 5 and YEAR(fecha_solicitud) = "'.$anio.'"
								) as mayo,
								(
								SELECT count(*)
								FROM tb_requerimiento 
								WHERE MONTH(fecha_solicitud) = 6 and YEAR(fecha_solicitud) = "'.$anio.'"
								) as junio,
								(
								SELECT count(*)
								FROM tb_requerimiento 
								WHERE MONTH(fecha_solicitud) = 7 and YEAR(fecha_solicitud) = "'.$anio.'"
								) as julio,
								(
								SELECT count(*)
								FROM tb_requerimiento 
								WHERE MONTH(fecha_solicitud) = 8 and YEAR(fecha_solicitud) = "'.$anio.'"
								) as agosto,
								(
								SELECT count(*)
								FROM tb_requerimiento 
								WHERE MONTH(fecha_solicitud) = 9 and YEAR(fecha_solicitud) = "'.$anio.'"
								) as septiembre,
								(
								SELECT count(*)
								FROM tb_requerimiento 
								WHERE MONTH(fecha_solicitud) = 10 and YEAR(fecha_solicitud) = "'.$anio.'"
								) as octubre, 
								(
								SELECT count(*)
								FROM tb_requerimiento 
								WHERE MONTH(fecha_solicitud) = 11 and YEAR(fecha_solicitud) = "'.$anio.'"
								) as noviembre, 
								(
								SELECT count(*)
								FROM tb_requerimiento 
								WHERE MONTH(fecha_solicitud) = 12 and YEAR(fecha_solicitud) =  "'.$anio.'"
								) as diciembre 
								FROM (
										SELECT DISTINCT YEAR(fecha_solicitud) FROM tb_requerimiento WHERE YEAR(fecha_solicitud) = :id
						    		 ) rq', ['id' => $fecha_actual]);
	
		return view('graficos.gestion_actual')->with(compact('anio_actual','anio'));

    }


     public function reporteAntAct(){


     	$fecha_actual = date('Y-m-d'); 
     	$anio_actual = date('Y',strtotime($fecha_actual));
  
    	$fecha_antes = date('Y-m-d', strtotime('-1 year'));
    	$anio_antes = date('Y',strtotime($fecha_antes));

    	$rq_anio_actual = $this->sqlRequerimientoMeses($fecha_actual,$anio_actual);
    	$rq_anio_antes = $this->sqlRequerimientoMeses($fecha_antes,$anio_antes);

		return view('graficos.gestion_ant_act')->with(compact('rq_anio_actual','anio_actual','rq_anio_antes','anio_antes'));

    }

    private function sqlRequerimientoMeses($fecha_anio,$anio){

    	$rq_meses = DB::select('SELECT
								(
								SELECT count(*)
								FROM tb_requerimiento 
								WHERE MONTH(fecha_solicitud) = 1 and YEAR(fecha_solicitud) = "'.$anio.'"
								) as enero,
								(
								SELECT count(*)
								FROM tb_requerimiento 
								WHERE MONTH(fecha_solicitud) = 2 and YEAR(fecha_solicitud) = "'.$anio.'"
								) as febrero,
								(
								SELECT count(*)
								FROM tb_requerimiento 
								WHERE MONTH(fecha_solicitud) = 3 and YEAR(fecha_solicitud) = "'.$anio.'"
								) as marzo,
								(
								SELECT count(*)
								FROM tb_requerimiento 
								WHERE MONTH(fecha_solicitud) = 4 and YEAR(fecha_solicitud) = "'.$anio.'"
								) as abril,
								(
								SELECT count(*)
								FROM tb_requerimiento 
								WHERE MONTH(fecha_solicitud) = 5 and YEAR(fecha_solicitud) = "'.$anio.'"
								) as mayo,
								(
								SELECT count(*)
								FROM tb_requerimiento 
								WHERE MONTH(fecha_solicitud) = 6 and YEAR(fecha_solicitud) = "'.$anio.'"
								) as junio,
								(
								SELECT count(*)
								FROM tb_requerimiento 
								WHERE MONTH(fecha_solicitud) = 7 and YEAR(fecha_solicitud) = "'.$anio.'"
								) as julio,
								(
								SELECT count(*)
								FROM tb_requerimiento 
								WHERE MONTH(fecha_solicitud) = 8 and YEAR(fecha_solicitud) = "'.$anio.'"
								) as agosto,
								(
								SELECT count(*)
								FROM tb_requerimiento 
								WHERE MONTH(fecha_solicitud) = 9 and YEAR(fecha_solicitud) = "'.$anio.'"
								) as septiembre,
								(
								SELECT count(*)
								FROM tb_requerimiento 
								WHERE MONTH(fecha_solicitud) = 10 and YEAR(fecha_solicitud) = "'.$anio.'"
								) as octubre, 
								(
								SELECT count(*)
								FROM tb_requerimiento 
								WHERE MONTH(fecha_solicitud) = 11 and YEAR(fecha_solicitud) = "'.$anio.'"
								) as noviembre, 
								(
								SELECT count(*)
								FROM tb_requerimiento 
								WHERE MONTH(fecha_solicitud) = 12 and YEAR(fecha_solicitud) =  "'.$anio.'"
								) as diciembre 
								FROM (
										SELECT DISTINCT YEAR(fecha_solicitud) FROM tb_requerimiento WHERE YEAR(fecha_solicitud) = :id
						    		 ) rq', ['id' => $fecha_anio]);
    	return $rq_meses;
    }

    public function reporteAnyDate(){

    	$rq_anio_actual = "";
    	$rq_anio_antes = "";
    	$rq_anio_actua = "";
    	$rq_anio_antes = "";
    	$anio_actual = "";
    	$anio_antes = "";

		return view('graficos.gestion_any_date')->with(compact('rq_anio_actual','anio_actual','rq_anio_antes','anio_antes'));
    } 

     public function searchGraficos(Request $request){

     	$this->validate($request, Requerimiento::$rules, Requerimiento::$messages);

        $fecha_antes = date('Y-m-d', strtotime($request->dateFrom));
 		$anio_antes = date('Y',strtotime($request->dateFrom));
 		$dateFrom = $fecha_antes;
 		
 	    $fecha_actual = date('Y-m-d', strtotime($request->dateTo));		
 		$anio_actual = date('Y',strtotime($request->dateTo));
 		$dateTo = $fecha_actual;

 		$rq_anio_actual = $this->sqlRequerimientoMeses($fecha_actual,$anio_actual);
	    $rq_anio_antes = $this->sqlRequerimientoMeses($fecha_antes,$anio_antes);

		return view('graficos.gestion_any_date')->with(compact('rq_anio_actual','anio_actual','rq_anio_antes','anio_antes','dateFrom','dateTo'));

     }

     public function rqFaseTiempo(){
			
		    $dateFrom = '';
		    $dateTo = '';
		    $rq_fases_tiempo = '';

		return view('jefe_operaciones.rq_fase_tiempo')->with(compact('rq_fases_tiempo','dateFrom','dateTo'));

     }


     public function rqSearchFaseTiempo(Request $request){

//$this->validate($request, Operador::$rules, Operador::$messages);
     	$dateFrom = $request->dateFrom;
		$dateTo = $request->dateTo;


     	$rq_fases_tiempo = DB::select("SELECT 
   					   ca.id_req,
				       ca.nombre_ope,
				       ca.fecha_ingreso,
				       ca.fecha_certificacion,
                       ca.aprobb,
                       ca.asig_desa,
                       ca.solu, 
                       ca.cert_prueb,
                       ca.asig_inst,
                       ca.inst,
                       ca.certi,
                       (ca.aprobb + ca.asig_desa + ca.solu + ca.asig_inst + ca.inst + ca.certi + ca.cert_prueba1) total
       				    
   FROM (
               SELECT cao.id_req,
				       cao.nombre_ope,
				       cao.fecha_soli fecha_ingreso,
				       cao.fecha_certificacion fecha_certificacion,
				       ROUND(((unix_timestamp(cao.fecha_aprob) - unix_timestamp(cao.fecha_soli) ) / 3600 ) ,2) aprobb ,
				       ROUND(((unix_timestamp(cao.fecha_asig) - unix_timestamp(cao.fecha_aprob) ) / 3600 ),2) asig_desa,
				       ROUND(((unix_timestamp(cao.fecha_sol) - unix_timestamp(cao.fecha_asig) ) / 3600 ),2) solu,
				       CONCAT(CONCAT(ROUND ( ((unix_timestamp(cao.fecha_cert) - unix_timestamp(cao.fecha_sol) ) / 3600 ),2 ),' (',cao.secuen_sol),' ','It)' ) cert_prueb ,
				       ROUND(((unix_timestamp(cao.fecha_asig_inst) - unix_timestamp(cao.fecha_cert) ) / 3600 ),2) asig_inst,
				       ROUND(((unix_timestamp(cao.fecha_inst) - unix_timestamp(cao.fecha_asig_inst) ) / 3600 ),2) inst,
				       ROUND(((unix_timestamp(cao.fecha_cert_on) - unix_timestamp(cao.fecha_inst) ) / 3600 ),2) certi,
                       ROUND(((unix_timestamp(cao.fecha_cert) - unix_timestamp(cao.fecha_sol) ) / 3600 ),2 ) cert_prueba1
                                           
				   FROM(
				SELECT co.id_certificacion_online id_req,

				       (SELECT CONCAT(u.name,' ',u.ap_paterno) nombre_ope FROM users u WHERE co.id_operador = u.id) nombre_ope,
				       ( SELECT CONCAT(r.fecha_solicitud,' ', r.hora_solicitud) fecha_soli FROM tb_requerimiento r WHERE  co.id_certificacion_online = r.id_requerimiento) fecha_soli ,
				       co.fecha_certificacion,
				       ( SELECT CONCAT(ar.fecha_aprobacion,' ', ar.hora_aprobacion) fecha_apro FROM tb_aprobacion_requerimiento ar WHERE co.id_certificacion_online = ar.nro_aprobacion ) fecha_aprob,
				       ( SELECT CONCAT(asr.fecha_asignacion,' ', asr.hora_asignacion) fecha_asig FROM tb_asignacion_requerimiento asr WHERE co.id_certificacion_online = asr.Nro_asignacion) fecha_asig,
				       ( SELECT CONCAT(sr.fecha_inicio,' ', sr.hora_inicio) fecha_sol FROM tb_solucion_requerimiento sr WHERE co.id_certificacion_online = sr.id_solucion) fecha_sol,
				       ( SELECT sr.secuencia FROM tb_solucion_requerimiento sr WHERE co.id_certificacion_online = sr.id_solucion) secuen_sol,
				       ( SELECT CONCAT(cert.fecha_certificacion,' ', cert.hora_certificacion) fecha_cert FROM tb_certificacion cert WHERE co.id_certificacion_online = cert.id_certificacion) fecha_cert,
				       ( SELECT CONCAT(asir.fecha_asig_instal,' ', asir.hora_asig_instal) fecha_asig_inst FROM tb_asignacion_instal_req asir WHERE co.id_certificacion_online = asir.id_asig_instal) fecha_asig_inst ,
				       ( SELECT CONCAT(inst.fecha_instal,' ', inst.hora_instal) fecha_inst FROM tb_instalacion inst WHERE co.id_certificacion_online = inst.id_instalacion) fecha_inst,
				       ( SELECT CONCAT(certo.fecha_certificacion,' ', certo.hora_certificacion) fecha_cet FROM tb_certificacion_online certo WHERE co.id_certificacion_online = certo.id_certificacion_online) fecha_cert_on
				       
						
				FROM (
						    SELECT id_certificacion_online,id_operador, fecha_certificacion FROM tb_certificacion_online 
						    WHERE fecha_certificacion BETWEEN :id AND :id1                    
					 ) co
				    
				 ) cao
       
       )ca", ['id' => $dateFrom,'id1' => $dateTo]);

     		return view('jefe_operaciones.rq_fase_tiempo')->with(compact('rq_fases_tiempo','dateTo','dateFrom'));

     }


}
