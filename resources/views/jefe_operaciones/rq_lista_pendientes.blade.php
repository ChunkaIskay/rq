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
          <h1 class="h3 mb-2 text-gray-800">Requerimentos Pendientes</h1>
          <p class="mb-4">Lista de requerimientos pendientes. </p>



      
      
          <!-- DataTales Example -->
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Revisar requerimientos pendietes</h6>
            </div>

            
    <div class="card-body">
            <div class="container">
                <form action="{{ route('searchRqPendientes') }}"  method="post"  enctype="multipart/form-data" class="navbar-form navbar-left" >
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
          </div>
            <br>
              <div class="table-responsive">
                <table class="table table-bordered"  class="display" id="dataTableAsigHist" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th class="text-center">Id Requerimiento</th>
                        <th class="text-left">Nombre Fase</th>
                        <th class="text-left">fechaSolicitud</th>
                        <th class="text-left">Desarrollador</th>
                        <th class="text-left">Operador</th>
                        <th class="text-left">Cliente</th>
                        <th class="text-left">Estado</th>
                    </tr>
                </thead>
                <tbody>
                  @if(!empty($rqPendientes))
                  @foreach($rqPendientes as $key => $value) 
                      <tr class="table">
                          <td class="text-center">{{ $value['id_requerimiento'] }}</td>
                          <td>{{ $value['nombre_fase'] }}</td>
                          <td>{{ $value['fechaSolicitud'] }}</td>
                          <td>{{ $value['desarrollador'] }}</td>
                          <td>{{ $value['operador'] }}</td>
                          <td>{{ $value['nombreCliente'] }}</td>
                          <td>{{ $value['estado'] }}</td>
                          
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