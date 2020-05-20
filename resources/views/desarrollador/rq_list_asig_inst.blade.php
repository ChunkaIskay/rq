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
          <h1 class="h3 mb-2 text-gray-800">Revisar Asignación de Requerimientos a Instalar.</h1>
          <p class="mb-4">Listado de nuevas asignaciones.</p>
          <!-- DataTales Example -->
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Lista nueva de asignaciones.</h6>
            </div>
            <div class="card-body">
               
             <br>
             <div class="table-responsive">
                <table class="table table-bordered"  class="display" id="dataTableAsigHist" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th class="text-center">Nro Asignación</th>
                        <th class="text-left">Id Solución</th>
                        <th class="text-left">Gestor</th>
                        <th class="text-left">Programador</th>
                        <th class="text-left">Fecha Asignación</th>
                        <th class="text-left">Hora Asignación</th>
                        <th class="text-left">Acción</th>
                    </tr>
                </thead>
                <tbody>
                  @if(!empty($rqAsigIstalar))
                  @foreach($rqAsigIstalar as $key => $value) 
                      <tr class="table">
                          <td class="text-center">{{ $value->id_asig_instal }}</td>
                          <td>{{ $value->id_solucion }}</td>
                          <td>{{ $value->nom_gestor }}</td>
                          <td>{{ $value->nom_prog }}</td>
                          <td>{{ $value->fecha_asig_instal }}</td>
                          <td>{{ $value->hora_asig_instal }}</td>
                          
                          <td class="td-actions text-right">
                                <a href="{{ url('/Desarrollador/'.$value->id_asig_instal.'/rev-detalle-req-inst') }}" type="button" rel="tooltip" title="Asignar req. a instalar" class="btn btn-info btn-simple btn-xs">
                                   <i class="fa fa-edit" aria-hidden="true"></i>
                                </a>
                        </td>
                      </tr> 
                  @endforeach
                  @endif
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