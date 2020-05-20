<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AsigInstalDesc extends Model
{
    protected $table = 'tb_asignacion_instal_desc';
	protected $primaryKey = 'idSol';
	public $timestamps = false;
}
