<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AsignacionReq extends Model
{
    protected $table = 'tb_asignacion_requerimiento';
	protected $primaryKey = 'Nro_asignacion';
	public $timestamps = false;

	public static $messages =[
    		'gestor.required' => 'Seleccione un gestor por favor.',
    		'gestor.numeric' => 'Seleccione un gestor por favor.',
            'gestor.between' => 'Seleccione un gestor por favor.',
            'gestor.min' => 'Seleccione un gestor por favor.',
            'gestor.max' => 'Seleccione un gestor por favor.',

            'desarrollador.required' => 'Seleccione un desarrollador por favor.',
            'desarrollador.numeric' => 'Seleccione un desarrollador por favor.',
            'desarrollador.between' => 'Seleccione un desarrollador por favor.',
            'desarrollador.min' => 'Seleccione un desarrollador por favor.',
            'desarrollador.max' => 'Seleccione un desarrollador por favor.',

            'fch_planif.required' => 'Ingrese el campo Año Desde por favor.',

			't_des.required' => 'Ingrese las horas de desarrollo.',
            't_des.numeric' => 'INgrese datos numericos por favor.',
            't_des.between' => 'Ingrese las horas de desarrollo.',
            't_des.min' => 'Las horas tienen que ser mayor a cero.',
            't_des.max' => 'Ingrese las horas de desarrollo.',



    ];

    public static $rules = [
	
    	    'gestor' => 'required|numeric|min:1|max:1000',
    	    'desarrollador' => 'required|numeric|min:1|max:1000',
            'fch_planif' => 'required|min:8',
            't_des' => 'required|numeric|min:1|max:1440',
    ];

	
}
