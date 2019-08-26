<?php

namespace App\Http\Controllers\JefeOperaciones;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

use App\Cliente;


class SpecialTasksController extends Controller
{
    
    public function listaClientes(){

    	$listaClientes = DB::table('tb_cliente')
		->select('id_cliente', 'nombre', 'activo')
		->orderBy('nombre', 'ASC')
		->get();

		return view('jefe_operaciones.lista_clientes')->with(compact('listaClientes'));

    }

    public function nuevoCliente(){

		return view('jefe_operaciones.nuevo_cliente')->with(compact('nuevoCliente'));

    }
 
     public function guardarCliente(Request $request){

		
		$last = DB::table('tb_cliente')->orderBy('id_cliente', 'DESC')->first();
 		$idUltimo = $last->id_cliente +1; 
		
		$cliente = new Cliente();

		$cliente->nombre = $request->input('nombre_cli');
		$cliente->activo = $request->estado;
		$cliente->id_cliente = $idUltimo;

		if (!$cliente->save()){ 
			
					return redirect()->route('listaClientes')->with(array(
						'error' => 'Error: Al guardar el nueco cliente!. Por favor intente nuevamente.'));
				
		}else{ 
					return redirect()->route('listaClientes')->with(array(
						'message' => 'El nuevo cliente se guardo correctamente.'));
		}

		return view('jefe_operaciones.lista_clientes')->with(compact('listaClientes'));

    }

    public function modificarCliente($id){


		$cliente = Cliente::find($id);

		if(!$cliente){
			return redirect()->route('listaClientes')->with(array(
						'error' => 'Error: No existe el cliente!. Por favor intente nuevamente.'));
		}

		return view('jefe_operaciones.modificar_cliente')->with(compact('cliente'));

    }
 	
    public function editarCliente(Request $request){

		$cliente = Cliente::find($request->idc);

		$cliente->nombre = $request->input('nombre_cli');
		$cliente->activo = $request->estado;
	
		if (!$cliente->save()){ 
			
			return redirect()->route('listaClientes')->with(array(
				'error' => 'Error: no se pudo modificar el cliente {{ $cliente->nombre }} !. Por favor intente nuevamente.'));
				
		}else{ 
					return redirect()->route('listaClientes')->with(array(
						'message' => 'El cliente se modificó correctamente.'));
		}

		return view('jefe_operaciones.lista_clientes')->with(compact('listaClientes'));

    }



}
