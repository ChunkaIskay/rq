<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ImportantTask extends Model
{
	protected $table = 'contracts';
	protected $primaryKey = 'contract_id';

	public function user(){

	 return $this->belongsTo('App\User');
	}
}
