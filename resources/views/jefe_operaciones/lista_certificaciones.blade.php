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
          <h1 class="h3 mb-2 text-gray-800">Lista de certificaciones.</h1>
          <p class="mb-4">Lista de certificaciones desde la fecha {{ $dateFrom }} hasta {{ $dateTo }}. </p>

          <!-- DataTales Example -->
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Lista de certificaciones.</h6>
            </div>

            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered"  class="display" id="dataTable" width="100%" cellspacing="0">

                <thead>
                  <tr>
                    <td colspan="7">
                        <form action="{{ route('searchCert') }}"  method="post"  enctype="multipart/form-data" class="navbar-form navbar-left" >
                           {{ csrf_field() }}
                           <div class="row text-left">
                            <div class="col-2 text-right"> <label>Fecha desde</label></div>
                            <div class="col-3 text-left">
                              <input type="date" class="form-control " id="dateTimeFrom" name="dateFrom" value="{{ $dateFrom }}" autocomplete="off" placeholder="Fecha desde">
                            </div>
                            <div class="col-2 text-right"> <label>Fecha hasta</label></div>
                            <div class="col-3 text-left">
                              <input type="date" class="form-control" id="dateTimeUntil" name="dateTo" value="{{ $dateTo }}"  autocomplete="off"  placeholder="Fecha hasta">
                            </div>
                             <div class="col-2 text-left">
                              <button type="submit" class="btn btn-info">
                                <span >Buscar por fechas</span>
                              </button>
                            </div>
                            
                          </div>
                        </form>
                    </td>
                  </tr>
                  <tr>
                
                     <td class="text-center">ID certificación</td>
                      <th class="text-left">ID solución</th>
                      <th class="text-left">ID operador</th>
                      <th class="text-left">Fecha certificación</th>
                      <th class="text-left">Hora certificación</th>
                      <th class="text-left">Detalle certificación</th>
                      <th class="text-left">Detalle funcionalidad</th>
                      
                  </tr>
                </thead>
                <tbody>
                @foreach($listaCert as $key => $value) 
                    <tr class="table">
                        <td class="text-center">{{ $value->id_certificacion }}</td>
                        <td>{{ $value->id_solucion }}</td>
                        <td>{{ $value->id_operador }}</td>
                        <td>{{ $value->fecha_certificacion }}</td>
                        <td>{{ $value->hora_certificacion }}</td>
                        <td>{{ $value->detalle_certificacion }}</td>
                        <td>{{ $value->detalle_funcionalidades }}</td>
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