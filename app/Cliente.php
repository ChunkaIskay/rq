<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $table = 'tb_cliente';
	protected $primaryKey = 'id_cliente';
	public $timestamps = false;	
}
