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
          <h1 class="h3 mb-2 text-gray-800">Lista de nuevos Requerimientos certificados.</h1>
          <p class="mb-4">Lista de requerimientos certificados. </p>

          <!-- DataTales Example -->
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Lista de requerimientos certificados.</h6>
            </div>

            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered"  class="display" id="dataTable" width="100%" cellspacing="0">

                <thead>
                    <tr>
                        <th class="text-left">Id Certificación</th>
                        <th class="text-center">Id Solución</th>
                        <th class="text-left">Id Req</th>
                        <th class="text-left">Fecha Certif.</th>
                        <th class="text-left">Hora Certif.</th>
                        <th class="text-left">Certificado Por</th>
                        <th class="text-left">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($listCert as $key => $value) 
                    <tr class="table">
                        <td class="text-center">{{ $value->id_certificacion }}</td>
                        <td>{{ $value->id_solucion }}</td>
                        <td>{{ $value->id_requerimiento }}</td>
                        <td>{{ $value->fecha_certificacion }}</td>
                        <td>{{ $value->hora_certificacion }}</td>
                        <td>{{ $value->name.' '.$value->ap_paterno }}</td>
                        <td class="td-actions text-right">
                                <a href="{{ url('/JefeSistemas/'.$value->id_certificacion.'/rq-detalle-cert') }}" type="button" rel="tooltip" title="Ver detalle" class="btn btn-success btn-simple btn-xs">
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
                  <div class="col-md-4"></div>
                  <div class="col-md-4 text-left"></div>
                  <div class="col-md-3"></div>
                </div>  
          </div>
 
@endsection