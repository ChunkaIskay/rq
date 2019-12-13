<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Instalacion extends Model
{
	protected $table = 'tb_instalacion';
	protected $primaryKey = 'id_instalacion';
	public $timestamps = false;
}
