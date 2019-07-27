<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Requerimiento extends Model
{
    protected $table = 'tb_requerimiento';
	protected $primaryKey = 'id_requerimiento';
	public $timestamps = false;	

}
