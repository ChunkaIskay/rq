<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rquerimiento extends Model
{
    protected $table = 'tb_requerimiento';
	protected $primaryKey = 'id_requerimiento';
	public $timestamps = false;	
	
	public static $messages =[
            'fecha_soli.required' => 'Ingrese el campo Año Desde por favor.',
            'hora_soli.required' => 'Ingrese el campo Año Hasta por favor.',
    		'id_req.required' => 'No se genero el id de requerimiento.',
    		'id_req.numeric' => 'No se genero el id de requerimiento.',
			'tipo.required' => 'Seleccione el tipo.',
			'tipotarea.required' => 'Seleccione el tipo tarea.',
			
			'cliente.required' => 'Seleccione un cliente.',
			'cliente.numeric' => 'Seleccione un cliente.',
			'cliente.min' => 'Seleccione un cliente.',
            'cliente.max' => 'Seleccione un cliente.',

			'operadorr.required' => 'Seleccione un operador.',
			'operadorr.numeric' => 'Seleccione un cliente.',

			'prioridad.required' => 'Seleccione una prioridad.',
			'resul_dese.required' => 'Ingrese el resultado.',
			'desc.required' => 'Ingrese la descripcion.'

    ];

    public static $rules = [
            'fecha_soli' => 'required|min:8',
            'hora_soli' => 'required|min:8',
            'id_req' => 'required|numeric|min:1|max:100000',
			'tipo' => 'required|between:3,50',
			'tipotarea' => 'required|between:3,60',
	        'cliente' => 'required|numeric|min:0|max:100000',
	        'operadorr' => 'required|numeric|min:1|max:100000',
	        'prioridad' => 'required|between:3,60',
	        'resul_dese' => 'required|min:3|max:200',
	        'desc' => 'required|min:3|max:200'
    ];


}
