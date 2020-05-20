<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Instalacion extends Model
{
	protected $table = 'tb_instalacion';
	protected $primaryKey = 'id_instalacion';
	public $timestamps = false;

	public static $messages =[
    		'textDesc.required' => 'Escriba backup por favor!',
    		'textComentario.required' => 'Escriba el comentario por favor!',
    		
    ];

    public static $rules = [
		    'textDesc' => 'required|min:3|max:600',
		    'textComentario' => 'required|min:3|max:600',
    ];
}
