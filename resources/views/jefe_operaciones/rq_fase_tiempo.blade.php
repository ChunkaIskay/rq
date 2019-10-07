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
          <h1 class="h3 mb-2 text-gray-800">Lista del tiempo de requerimientos por fases.</h1>
          <p class="mb-4">Analisis del tiempo empleado por fase de los requerimientos. </p>
          <p class="mb-4">El listado visualiza los tiempos empleados por fase de aquellos requerimientos que han sido <strong>certificados en linea</strong>.</p>

          <!-- DataTales Example -->
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Lista seleccionada por fechas.</h6>
            </div>

            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered"  class="display" id="dataTable" width="100%" cellspacing="0">

                <thead>
                  <tr>
                    <td colspan="13">
                        <form action="{{ route('searchFaseTiempo') }}"  method="post"  enctype="multipart/form-data" class="navbar-form navbar-left" >
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
                      <td class="text-center">Id Req</td>
                      <th class="text-left">Operador</th>
                      <th class="text-left">Fecha Ingreso Req.</th>
                      <th class="text-left">Fecha Cert Online</th>
                      <th class="text-left">Aprob</th>
                      <th class="text-left">Asig. a Desa.</th>
                      <th class="text-left">Solución</th>
                      <th class="text-left">Prueba y Cert.</th>
                      <th class="text-left">Asig. a Inst.</th>
                      <th class="text-left">Instalación</th>
                      <th class="text-left">Cert. Online</th>
                      <th class="text-left">Total</th>
                  </tr>
                </thead>
                <tbody>
                @if(!empty($rq_fases_tiempo))  
                  @foreach($rq_fases_tiempo as $key => $value) 
                      <tr class="table">
                          <td class="text-center">{{ $value->id_req }}</td>
                          <td>{{ $value->nombre_ope }}</td>
                          <td>{{ $value->fecha_ingreso }}</td>
                          <td>{{ $value->fecha_certificacion }}</td>
                          <td>{{ $value->aprobb }}</td>
                          <td>{{ $value->asig_desa }}</td>
                          <td>{{ $value->solu }}</td>
                          <td>{{ $value->cert_prueb }}</td>
                          <td>{{ $value->asig_inst }}</td>
                          <td>{{ $value->inst }}</td>
                          <td>{{ $value->certi }}</td>
                          <td>{{ $value->total }}</td>

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