<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AprobacionRq extends Model
{
    protected $table = 'tb_aprobacion_requerimiento';
	protected $primaryKey = 'id_requerimiento';
	public $timestamps = false;	
	
}
