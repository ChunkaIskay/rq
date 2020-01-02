@extends('layouts.app')

@section('content')
<style type="text/css">

  

   .boton-subir{
      text-decoration:none;
      font-weight: 100;
      font-size: 14px;
      color:#fff;
      padding-top:5px;
      padding-bottom:5px;
      padding-left:15px;
      padding-right:15px;
      background-color:#1cc88a;
      border-color: #78dcb8;
      border-width: 3px;
      border-style: solid;
      border-radius:10px;
  }
  .boton-buscar{
      text-decoration:none;
      font-weight: 200;
      font-size: 14px;
      color:#fff;
      padding-top:5px;
      padding-bottom:5px;
      padding-left:25px;
      padding-right:25px;
      background-color:#36b9cc;
      border-color: #4e73df;
      border-width: 1px;
      border-style: solid;
      border-radius:5px;
  }

$custom-file-text: (
en: "Browses",
es: "Elegir"
);
</style>
 <!-- Begin Page Content -->
<div class="container-fluid">
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
  <p class="mb-4">Detalle del requerimiento. </p>

  <!-- DataTales Example -->
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary">Datos del Requerimiento</h6>
    </div>
    <div class="card-body">
      <form  class="md-form" id="logout-form2" action="{{ url('/Operador/'.$detalle[0]->id_requerimiento .'/rq-actualizar') }}" method="post" enctype="multipart/form-data">
         {{ csrf_field() }}
          <div class="row">
                <div class="col-lg-3">  
                   Id Requerimiento
                </div>
                <div class="col-lg-3 text-left">  
                   <strong>{{ $detalle[0]->id_requerimiento }}</strong>
                </div>
                <div class="col-lg-3">  
                    Accesible
                </div>
                <div class="col-lg-3 text-left">  
                   <strong>{{ $detalle[0]->accesible }}</strong>
                </div>
          </div>
          <div class="row">
                <div class="col-lg-3">  
                   Tipo
                </div>
                <div class="col-lg-3 text-left">  
                   <strong>{{ $detalle[0]->tipo }}</strong>
                </div>
                <div class="col-lg-3">  
                    Prioridad
                </div>

                 <div class="col-lg-3 text-left">
                  <select class="form-control"  name="prioridad" required="">
                     @foreach($arrayPrioridad as $keyp => $vprioridad) 
                              <option value="{{ $keyp }}" @if( $keyp == old('prioridad', $detalle[0]->prioridad )) selected @endif>{{ $vprioridad }}</option>
                     @endforeach
                  </select>
                </div>
          </div>
          <div class="row">
                <div class="col-lg-3">  
                   Tipo tarea
                </div>
                <div class="col-lg-3 text-left">  
                   <strong>{{ $detalle[0]->tipo_tarea }}</strong>
                </div>
                <div class="col-lg-3">  
                   &nbsp;
                </div>
                <div class="col-lg-3 text-left">  
                   <strong>&nbsp;</strong>
                </div>
          </div>
          <div class="row">
                <div class="col-lg-3">  
                   Fecha Certificación
                </div>
                <div class="col-lg-3 text-left">  
                   <strong>{{ $detalle[0]->fecha_solicitud }}</strong>
                </div>
                <div class="col-lg-3">  
                    Hora Certifiacación
                </div>
                <div class="col-lg-3 text-left">  
                   <strong>{{ $detalle[0]->hora_solicitud }}</strong>
                </div>
          </div>
          <div class="row">
                <div class="col-lg-3">  
                   Fecha limite
                </div>
                <div class="col-lg-3 text-left">
                 @if(!empty($fechasOpDe[0]->fecha_plan_op))  
                   <strong>{{ $fechasOpDe[0]->fecha_plan_op }}</strong>
                 @else
                   <strong>Sin fecha limite</strong>
                 @endif
                </div>
                <div class="col-lg-3">  
                    Fecha planificada
                </div>
                <div class="col-lg-3 text-left">
                 @if(!empty($fechasOpDe[0]->fecha_plan_de))  
                   <strong>{{ $fechasOpDe[0]->fecha_plan_de }}</strong>
                 @else
                  <strong>Sin fecha planificada</strong>
                 @endif  
                </div>
          </div>
          <div class="row">
                <div class="col-lg-3">  
                   Solicitado Por
                </div>
                <div class="col-lg-3 text-left">  
                   <strong>{{ $detalle[0]->name.' '.$detalle[0]->ap_paterno }}</strong>
                </div>
                <div class="col-lg-3">  
                   Para el Cliente
                </div>
                <div class="col-lg-3 text-left">  
                   <strong>{{ $detalle[0]->nombre }}</strong>
                </div>
          </div>
            <br>
          <div class="row">
                <div class="col-lg-3">  
                  Descripción
                </div>
                <div class="col-lg-9 text-left">
                <textarea class="form-control" rows="3" id="descripcion" placeholder="Modifique la descripcion" name="descripcion">{{ $detalle[0]->descripcion }}</textarea>  
                   <strong></strong>
                </div>
          </div> <br>
          <div class="row">
                <div class="col-lg-3">  
                   Resultado Deseado
                </div>
                <div class="col-lg-9 text-left">
                 <textarea class="form-control" rows="3" id="desc_deseado" placeholder="Modifique el resultado deseado" name="desc_deseado">{{ $detalle[0]->resultado }}</textarea>  
                   <strong></strong>
                </div>
          </div>
          <br>
          @include('operador.listado_adjunto')
          <br>
          <div class="row">
            <div class="col-lg-6 text-right">  
              <div class="my-2"></div>
              <a href="#" class="btn btn-success btn-icon-split" data-toggle="modal" data-target="#modalActualizar">
                <span class="icon text-white-50">
                  <i class="fas fa-check"></i>
                </span>
                <span class="text">Guardar cambios</span>
              </a>
            </div>
            <div class="col-lg-6"> 
               <div class="my-2"></div>
               <a class="btn btn-primary" href="{{ route('rqList') }}">
                <span class="icon text-white-50">
                  <i class="fas fa-exclamation-triangle"></i>
                </span>
                <span class="text">Volver</span>
              </a>
            </div>
          </div>
          </form>
      </div>
    </div>
    </div>
  </div>
</div>
  <!-- /.container-fluid -->
  <!-- Logout Modal-->
  <div class="modal fade" id="modalActualizar" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Esta seguro de modificar el requerimiento.</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">x</span>
          </button>
        </div>
        <div class="modal-body">Usted modificará el req. {{ $detalle[0]->id_requerimiento }}.</div>
        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
          <!--<a class="btn btn-primary" href="login.html">Logout</a>-->
              <a class="btn btn-primary" href="{{ route('logout') }}"
                  onclick="event.preventDefault();
                           document.getElementById('logout-form2').submit();">
                  Modificar Requerimiento
              </a>
        </div>
      </div>
    </div>
  </div>
  <!-- Logout Modal-->
  <div class="modal fade" id="logoutModalRech" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Esta seguro de rechazar el requerimiento !!!</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">x</span>
          </button>
        </div>
        <div class="form-group">
         <div class="modal-body">Observación del rq {{ $detalle[0]->id_requerimiento }}.</div>
          <textarea class="form-control" rows="3" id="comment" placeholder="Esta observación se enviara al correo del Operador" name="text"></textarea>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
          <!--<a class="btn btn-primary" href="login.html">Logout</a>-->
              <a class="btn btn-danger" href="{{ route('logout') }}"
                  onclick="event.preventDefault();
                           document.getElementById('logout-form').submit();">
                  Rechazar Requerimiento
              </a>
              <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                  {{ csrf_field() }}
              </form>
        </div>
      </div>
    </div>
  </div>

<!-- Logout Modal delete file-->
  <div class="modal fade" id="deletemodal" tabindex="-1" aria-labelledby="exampleModalLabel1" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel1">Esta seguro de borrar el archivo?.</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">x</span>
          </button>
        </div>
        <div class="modal-body">El archivo se eliminara definitivamete.</div>
        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
          <!--<a class="btn btn-primary" href="login.html">Logout</a>-->
              <a class="btn btn-primary" href="{{ route('logout') }}"
                    onclick="event.preventDefault();
                    document.getElementById('logout-form1').submit();">
                    Eliminar Archivo
              </a>
              <div class="modal-body">
              <form id="logout-form1" action="{{ route('deleteFile') }}" method="POST" style="display: none;">
                  {{ csrf_field() }}
                  <input type="hidden" name="id" value="{{ $detalle[0]->id_requerimiento }}">
               
                  <input type="text" name="idAdjunto" id="idAdjunto" value=""/>
                </form>
            </div>
        </div>
      </div>
    </div>
  </div>


@endsection