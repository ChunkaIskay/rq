<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FechaReq extends Model
{
    protected $table = 'tb_req_fecha';
	protected $primaryKey = 'id_requerimiento';
	public $timestamps = false;	
}
