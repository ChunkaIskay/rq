<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Adjunto extends Model
{
    protected $table = 'tb_adjuntos';
	protected $primaryKey = 'id_adjunto';
	public $timestamps = false;	
}
