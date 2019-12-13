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
          <h1 class="h3 mb-2 text-gray-800">{{ $pagTitulo }}</h1>
          <p class="mb-4">Tiene el listado completo de los requerimientos.</p>
          <!-- progress bar -->
          <div class="progress">
              <div class="progress-bar progress-bar-striped" style="min-width: 20px;"></div>
          </div>
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
                        <th class="text-left">Fecha Solicitud</th>
                        <th class="text-left">Hora Solicitud</th>
                        <th class="text-left">Prioridad</th>
                        <th class="text-left">Accesible</th>
                        <th class="text-left">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($rqexaminar as $key => $value) 
                  <tr class="table">
                      <td class="text-center">{{ $value->id_requerimiento }}</td>
                      <td>{{ $value->tipo }}</td>
                      <td>{{ $value->fecha_solicitud }}</td>
                      <td>{{ $value->hora_solicitud }}</td>
                      <td>{{ $value->prioridad }}</td>
                      <td>{{ $value->accesible }}</td>
                      <td class="td-actions text-left">
                      @if($pag == 'prioridad')
                          <a href="{{ url('/Operador/'.$value->id_requerimiento.'/rq-prioridad-editar') }}" 
                                type="button" rel="tooltip" title="Editar Req." class="btn btn-info btn-simple btn-xs">
                          <i class="far fa-edit"></i>
                          </a>
                            
                      @else
                          <a href="{{ url('/Operador/'.$value->id_requerimiento.'/req-detalle') }}" type="button" rel="tooltip" title="Ver detalle" class="btn btn-success btn-simple btn-xs">
                          <i class="fa fa-eye"></i></a>
                          <a href="{{ url('/Operador/'.$value->id_requerimiento.'/rq-modificar') }}" 
                                type="button" rel="tooltip" title="Editar Req." class="btn btn-info btn-simple btn-xs">
                           <i class="far fa-edit"></i>
                           </a>
                      @endif
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