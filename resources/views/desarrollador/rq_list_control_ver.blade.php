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
          <h1 class="h3 mb-2 text-gray-800">Control de Versiones Pendientes.</h1>
          <p class="mb-4">Listado de requerimientos control de versiones pendientes.</p>
          <!-- DataTales Example -->
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Lista control de versiones pendientes.</h6>
            </div>
            <div class="card-body">
               
             <br>
             <div class="table-responsive">
                <table class="table table-bordered"  class="display" id="dataTableAsigHist" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th class="text-center">Id Inst.</th>
                        <th class="text-left">Fecha Ins.</th>
                        <th class="text-left">Comentario</th>
                        <th class="text-left">Fecha CerOnline</th>
                        <th class="text-left">Hora CerOnline</th>
                        <th class="text-left">CerOnline Por</th>
                        <th class="text-left">Acción</th>
                    </tr>
                </thead>
                <tbody>
                  @if(!empty($rqControVer))
                  @foreach($rqControVer as $key => $value) 
                      <tr class="table">
                          <td class="text-center">{{ $value->id_instalacion }}</td>
                          <td>{{ $value->fecha_instal }}</td>
                          <td>{{ $value->comentario }}</td>
                          <td>{{ $value->fecha_certificacion }}</td>
                          <td>{{ $value->hora_certificacion }}</td>
                          <td>{{ $value->nom_ope }}</td>
                          
                          <td class="td-actions text-right">
                                <a href="{{ url('/Desarrollador/'.$value->id_instalacion.'/rev-detalle-cont-ver-pend') }}" type="button" rel="tooltip" title="Asignar req. a instalar" class="btn btn-info btn-simple btn-xs">
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