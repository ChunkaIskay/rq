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
          <h1 class="h3 mb-2 text-gray-800">Reasignar Requerimientos a otro operador</h1>
          <p class="mb-4">Listado completo de los rq a asignar.</p>

          <!-- DataTales Example -->
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Listado de Requerimientos</h6>
            </div>

            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered"  class="display" id="dataTable" width="100%" cellspacing="0">

                <thead>
                    <tr>
                        <th class="text-center">Id Requerimiento</th>
                        <th class="text-left">Tipo</th>
                        <th class="text-left">Fecha aprobación</th>
                        <th class="text-left">Hora aprobación</th>
                        <th class="text-left">Prioridad</th>
                        <th class="text-left">Accesible</th>
                        <th class="text-left">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($rqReasignar as $key => $value) 
                  <tr class="table">
                      <td class="text-center">{{ $value->id_requerimiento }}</td>
                      <td>{{ $value->tipo }}</td>
                      <td>{{ $value->fecha_aprobacion }}</td>
                      <td>{{ $value->hora_aprobacion }}</td>
                      <td>{{ $value->prioridad }}</td>
                      <td>{{ $value->accesible }}</td>
                      <td class="td-actions text-left">
                        
                                <a href="{{ url('/JefeOperaciones/'.$value->id_requerimiento.'/rq-reasignar-editar') }}" type="button" rel="tooltip" title="Editar Req." class="btn btn-info btn-simple btn-xs">
                                <i class="far fa-edit"></i>
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