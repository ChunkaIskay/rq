<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CertificacionRq extends Model
{
    protected $table = 'tb_certificacion';
	protected $primaryKey = 'id_certificacion';
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
            'desarrollador.max' => 'Seleccione un desarrollador por favor.'

    ];

    public static $rules = [
	
    	    'gestor' => 'required|numeric|min:1|max:1000',
    	    'desarrollador' => 'required|numeric|min:1|max:1000'
    ];

}
