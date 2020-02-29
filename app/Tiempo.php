<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tiempo extends Model
{
    protected $table = 'tb_tiempos';
	protected $primaryKey = 'id_tiempo';
	public $timestamps = false;	
}
