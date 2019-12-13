<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DepurarReq extends Model
{
   protected $table = 'tb_req_depurado';
	protected $primaryKey = 'id_requerimiento';
	public $timestamps = false;

	public static $messages =[
    		'detalle_depurar.required' => 'Ingrese el detalle por favor!'
    		

    ];

    public static $rules = [
	
    	    'detalle_depurar' => 'required|min:3|max:200'
    ];
}
