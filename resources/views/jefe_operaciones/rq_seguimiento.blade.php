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
          <h1 class="h3 mb-2 text-gray-800">Seguimiento de Requerimentos.</h1>
          <p class="mb-4">Seleccione un requerimiento, para ver la fase en el cual se encuetra. </p>

          <!-- DataTales Example -->
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Seleccione el requermiento y luego presione el boton seguimiento</h6>
            </div>

            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered"  class="display" id="dataTable" width="100%" cellspacing="0">

                <thead>
                    <tr>
                        <th class="text-center">Id Requerimiento</th>
                        <th class="text-left">Tipo</th>
                        <th class="text-left">Fecha Solicitud</th>
                        <th class="text-left">Hora Solicitud</th>
                       <th class="text-left">Accesible</th>
                        <th class="text-left">Operador</th>
                        <th class="text-left">Seguimiento</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($seguimientoRq as $key => $value) 
                    <tr class="table">
                        <td class="text-center">{{ $value->id_requerimiento }}</td>
                        <td>{{ $value->tipo }}</td>
                        <td>{{ $value->fecha_solicitud }}</td>
                        <td>{{ $value->hora_solicitud }}</td>
                        @if($value->accesible == 'Rm')
                          <td>Depurado</td>
                        @elseif($value->accesible == 'Ob')
                             <td>Rechazado</td>
                          @else
                            <td>{{ $value->accesible }}</td>
                          @endif
                        <td>{{ $value->name }}</td>
                        <td class="td-actions text-right">
                                  <a href="{{ url('/JefeOperaciones/'.$value->id_requerimiento.'/rq-seguimiento') }}" type="button" rel="tooltip" title="Seguimiento a este requerimiento" class="btn btn-info btn-simple btn-xs">
                                   <i class="fa fa-object-group"></i>
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