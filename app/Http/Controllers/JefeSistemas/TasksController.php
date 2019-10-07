<?php

namespace App\Http\Controllers\JefeSistemas;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
     
class TasksController extends Controller
{
    public function index(){   
		
		return view('jefe_sistemas.index')->with(compact('hola'));    
	}
}
