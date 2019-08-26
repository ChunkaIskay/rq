@extends('layouts.app')

@section('content')
 <!-- Begin Page Content -->
  
          @if(session('message'))
                <div class="alert alert-success">
                    {{ session('message') }}
                </div>
            @endif  

          @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif
          <!-- Page Heading -->
          <h1 class="h3 mb-2 text-gray-800">Lista completa de clientes.</h1>
          <p class="mb-4">Estan todos los clientes Activo e Inactivos. </p>

          <!-- DataTales Example -->
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Lista de clientes.</h6>
            </div>

            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered"  class="display" id="dataTable" width="100%" cellspacing="0">

                <thead>
                  <tr><td colspan="4">Nuevo cliente &nbsp;&nbsp;&nbsp;<a href="{{ url('/JefeOperaciones/nuevo-cliente') }}" type="button" rel="tooltip" title="Nuevo cliente" class="btn btn-info btn-simple btn-xs"><i class="fa fa-plus" aria-hidden="true"></i>
                    </a>
                    </td>
                  </tr>
                  <tr>
                      <th class="text-center">Id cliente</th>
                      <th class="text-left">Nombre</th>
                      <th class="text-left">Estado</th>
                      <th class="text-left">Acciones</th>
                  </tr>
                </thead>
                <tbody>
                @foreach($listaClientes as $key => $value) 
                    <tr class="table">
                        <td class="text-center">{{ $value->id_cliente }}</td>
                        <td>{{ $value->nombre }}</td>
                        <td>
                            @if($value->activo == 'Si' )
                              Activo
                            @endif
                            @if($value->activo == 'No' )
                             Inactivo
                            @endif

                        </td>
                 
                        <td class="td-actions text-right">
                          
                                <a href="{{ url('/JefeOperaciones/'.$value->id_cliente.'/modificar-cliente') }}" type="button" rel="tooltip" title="Modificar cliente" class="btn btn-info btn-simple btn-xs">
                                   <i class="fa fa-edit" aria-hidden="true"></i>
                                </a>
                        </td>
                    </tr> 
                @endforeach
                </tbody>
            </table>
               
              </div>
            </div>

              <div class="row">
                  <div class="col-md-4"></div>
                  <div class="col-md-4 text-left"></div>
                  <div class="col-md-3"></div>
                </div>  
          </div>
 

@endsection