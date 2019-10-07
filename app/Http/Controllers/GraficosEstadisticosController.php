<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

use App\Requerimiento;


class GraficosEstadisticosController extends Controller
{
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
	
    	//dd($anio_actual);
		return view('graficos.gestion_actual')->with(compact('anio_actual','anio'));

    }


     public function reporteAntAct(){


     	$fecha_actual = date('Y-m-d'); 
     	$anio_actual = date('Y',strtotime($fecha_actual));
  
    	$fecha_antes = date('Y-m-d', strtotime('-1 year'));
    	$anio_antes = date('Y',strtotime($fecha_antes));

    	$rq_anio_actual = $this->sqlRequerimientoMeses($fecha_actual,$anio_actual);
    	$rq_anio_antes = $this->sqlRequerimientoMeses($fecha_antes,$anio_antes);
	
    	//dd($anio_actual);
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

    	$fecha_actual = date('Y-m-d'); 
    	$dateTo = $fecha_actual;
     	$anio_actual = date('Y',strtotime($fecha_actual));
  
    	$fecha_antes = date('Y-m-d', strtotime('-1 year'));
    	$dateFrom = $fecha_antes;
    	$anio_antes = date('Y',strtotime($fecha_antes));

    	$rq_anio_actual = $this->sqlRequerimientoMeses($fecha_actual,$anio_actual);
    	$rq_anio_antes = $this->sqlRequerimientoMeses($fecha_antes,$anio_antes);

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


}
