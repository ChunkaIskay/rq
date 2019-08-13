<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CertificacionRq extends Model
{
    protected $table = 'tb_certificacion';
	protected $primaryKey = 'id_certificacion';
	public $timestamps = false;	
}
