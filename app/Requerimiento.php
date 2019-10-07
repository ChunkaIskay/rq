<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Requerimiento extends Model
{
    protected $table = 'tb_requerimiento';
	protected $primaryKey = 'id_requerimiento';
	public $timestamps = false;	
	
	public static $messages =[
            'dateFrom.required' => 'Ingrese el campo Año Desde por favor.',
            'dateTo.required' => 'Ingrese el campo Año Hasta por favor.',
    ];

    public static $rules = [
            'dateFrom' => 'required|min:8',
            'dateTo' => 'required|min:8',
    ];

}
