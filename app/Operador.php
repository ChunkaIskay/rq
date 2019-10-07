<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Operador extends Model
{
    protected $table = 'users';
	protected $primaryKey = 'id';
	public $timestamps = false;	


	public static $messages =[
            'nombre_ope.required' => 'Ingrese el nombre del operador.',
            'paterno.required' => 'Ingrese el apellido paterno por favor.',
            'paterno.between' => 'El apellido paterno debe tener entre 3 y 30 caracteres.',
           // 'telefono.between' => paterno'El número de teléfono debe tener entre 7 y 8 digitos.',
            //'celular.between' => 'El número de celular debe tener entre 7 y 8 digitos.',
            'email.required' => 'Ingrese nuevamente su correo.',
            
    ];

    public static $rules = [
            'nombre_ope' => 'required|between:3,30',
            'paterno' => 'required|between:3,30',
          //  'telefono' => 'required|between:7,8',
           // 'celular' => 'required|between:7,8',
            'email' => 'required|string|email|max:255'
           // 'cargo' => 'required|between:3,45'

    ];



}