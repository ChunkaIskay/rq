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
          <h1 class="h3 mb-2 text-gray-800">Certificaciones SVN.</h1>
          <p class="mb-4">Listado de Certificaciones SVN .</p>
          <!-- DataTales Example -->
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Lista de Certificaciones SVN.</h6>
            </div>
            <div class="card-body">
             <br>
             <div class="table-responsive">
                <table class="table table-bordered"  class="display" id="dataTableAsigHist" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th class="text-center">Id Control de Versiones</th>
                        <th class="text-left">Fecha de Control.</th>
                        <th class="text-left">Hora de Control.</th>
                        <th class="text-left">Ver Detalle</th>
                    </tr>
                </thead>
                <tbody>
                  @if(!empty($rqControVer))
                  @foreach($rqControVer as $key => $value) 
                      <tr class="table">
                          <td class="text-center">{{ $value->id_control_svn }}</td>
                          <td>{{ $value->fecha_subversion }}</td>
                          <td>{{ $value->hora_subversion }}</td>
                          <td class="td-actions text-right">
                                <a href="{{ url('/Operador/'.$value->id_control_svn.'/rev-detalle-cert-svn') }}" type="button" rel="tooltip" title="Control svn" class="btn btn-info btn-simple btn-xs">
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