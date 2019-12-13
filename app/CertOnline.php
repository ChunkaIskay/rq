<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CertOnline extends Model
{
    protected $table = 'tb_certificacion_online';
	protected $primaryKey = 'id_certificacion_online';
	public $timestamps = false;	
}
