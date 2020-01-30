<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AjaxController extends Controller
{
   /**
      * Display a listing of the resource.
      *
      * @return \Illuminate\Http\Response
      */
     public function index()
     {
  echo "im in AjaxController index";//simplemente haremos que devuelva esto
 		return response()->json([
			    'success'   => true,
			    'message'   => 'Los datos se han guardado correctamente.' //Se recibe en la seccion "success", data.message
			    ], 200);

       
 

     }
}
