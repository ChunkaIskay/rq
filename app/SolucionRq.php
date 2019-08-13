<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SolucionRq extends Model
{
    protected $table = 'tb_solucion_requerimiento';
	protected $primaryKey = 'id_solucion';
	public $timestamps = false;	
}
