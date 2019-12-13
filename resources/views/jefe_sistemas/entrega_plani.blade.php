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
          <h1 class="h3 mb-2 text-gray-800">Fecha de entrega y tiempo de desarrollo.</h1>
          <p class="mb-4">Seleccione un id de requerimiento para cambiar la fecha de entrega y tiempo de desarrollo. </p>

          <!-- DataTales Example -->
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Requerimiento seleccionado.</h6>
            </div>

            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered"  class="display" id="dataTable" width="100%" cellspacing="0">

                <thead>
                  <tr>
                    <td colspan="7">
                        <form action="{{ route('searchlistRq') }}"  method="post"  enctype="multipart/form-data" class="navbar-form navbar-left" >
                           {{ csrf_field() }}
                           <div class="row text-left">
                            <div class="col-2 text-right"> <label>Id Requerimiento</label></div>
                            <div class="col-3 text-left">
                              <input type="number" class="form-control " id="idReq" name="idReq" value="" autocomplete="off" placeholder="0">
                            </div>
                            <div class="col-4 text-left">
                              <button type="submit" class="btn btn-info">
                                <span >Buscar requerimiento</span>
                              </button>
                            </div>
                          </div>
                        </form>
                    </td>
                  </tr>
                  <tr>
                     <td class="text-center">ID req</td>
                      <th class="text-left">Tipo tarea</th>
                      <th class="text-left">Prioridad</th>
                      <th class="text-left">Accesible</th>
                      <th class="td-actions text-right">CAMBIAR</th>
                  </tr>
                </thead>
                <tbody>
                    @if(!empty($listaReq))
                          <tr class="table">
                              <td class="text-left">{{ $listaReq->id_requerimiento }}</td>
                              <td>{{ $listaReq->tipo_tarea }}</td>
                              <td>{{ $listaReq->prioridad }}</td>
                              <td>{{ $listaReq->accesible }}</td>
                              <td class="td-actions text-right">
                                    <a href="{{ url('/JefeSistemas/'.$listaReq->id_requerimiento.'/rq-detalle-prioridad') }}" type="button" rel="tooltip" title="Ver detalle" class="btn btn-success btn-simple btn-xs">Prioridad</a>
                                    <a href="{{ url('/JefeSistemas/'.$listaReq->id_requerimiento.'/rq-detalle-entrega') }}" type="button" rel="tooltip" title="Ver detalle" class="btn btn-info btn-simple btn-xs">Fecha entrega y tiempo desarrollo
                                    </a>
                              </td>
                          </tr> 
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