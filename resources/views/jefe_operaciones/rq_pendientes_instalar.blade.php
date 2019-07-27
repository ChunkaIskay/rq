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
          <h1 class="h3 mb-2 text-gray-800">Revisar requerimientos pendientes a instalar</h1>
          <p class="mb-4">Listado de rq a instalar. </p>

          <!-- DataTales Example -->
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Revisar requerimientos pendietes</h6>
            </div>

            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered"  class="display" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th class="text-center">Id Req</th>
                        <th class="text-left">Tipo</th>
                        <th class="text-left">Tipo tarea</th>
                        <th class="text-left">Prioridad</th>
                        <th class="text-left">Fecha asignación</th>
                        <th class="text-left">Hora asignación</th>
                        <th class="text-left">Asignado Por</th>
                        <th class="text-left">Asignado A.</th>
                        <th class="text-left">Ver detalle</th>
                    </tr>
                </thead>
                <tbody>
                  @foreach($pendInstalar as $key => $value) 
                      <tr class="table">
                          <td class="text-center">{{ $value->id_requerimiento }}</td>
                          <td>{{ $value->tipo }}</td>
                          <td>{{ $value->tipo_tarea }}</td>
                          <td>{{ $value->prioridad }}</td>
                          <td>{{ $value->fecha_asig_instal }}</td>
                          <td>{{ $value->hora_asig_instal }}</td>
                          <td>{{ $value->asig_por }}</td>
                          <td>{{ $value->asig_a }}</td>
                          <td class="td-actions text-right">
                                    <a href="{{ url('/JefeOperaciones/'.$value->id_requerimiento.'/rq-pend-inst-detalle') }}" type="button" rel="tooltip" title="Ver detalle" class="btn btn-success btn-simple btn-xs">
                                      <i class="fa fa-eye"></i>
                                  </a>
                          </td>
                      </tr> 
                  @endforeach
                </tbody>
            </table>
              </div>
            </div>

              <div class="row">
                  <div class="col-md-4">{{ $pendInstalar->links() }}</div>
                  <div class="col-md-4 text-left"></div>
                  <div class="col-md-3"></div>
                </div>  
          </div>
      



@endsection