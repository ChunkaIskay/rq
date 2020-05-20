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
      @if($errors->any())
                      <div class="alert alert-danger">
                        <ul>
                          @foreach($errors->all() as $error)
                            <li>{{$error}}</li>
                          @endforeach
                        </ul>
                      </div>
      @endif

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
  <h1 class="h3 mb-2 text-gray-800">Asignación de Requerimiento a Solucionar</h1>
  <p class="mb-4">Detalle del Requerimiento a Solucionar. </p>

  <!-- DataTales Example -->
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary">Datos de la Asignación de Requerimiento a Solucionar</h6>
    </div>
    <div class="card-body">
          <div class="row">
                <div class="col-lg-2">  
                   Nro Asignación
                </div>
                <div class="col-lg-4 text-left">  
                   <strong>{{ $detalle[0]->Nro_asignacion }}</strong>
                </div>
                <div class="col-lg-2">  
                    Id Requerimiento
                </div>
                <div class="col-lg-4 text-left">  
                   <strong>{{ $detalle[0]->id_requerimiento }}</strong>
                </div>
          </div>
          <div class="row">
                  <div class="col-lg-2">  
                     Fecha Asignación
                  </div>
                  <div class="col-lg-4 text-left">  
                     <strong>{{ $detalle[0]->fecha_asignacion }}</strong>
                  </div>
                  <div class="col-lg-2">  
                      Hora Asignación
                  </div>
                  <div class="col-lg-4 text-left">  
                     <strong>{{ $detalle[0]->hora_asignacion }}</strong>
                  </div>
            </div>
            <div class="row">
                  <div class="col-lg-2">  
                     Asignado Por
                  </div>
                  <div class="col-lg-4 text-left">  
                     <strong>{{ $detalle[0]->asig_por }}</strong>
                  </div>
                  <div class="col-lg-2">  
                     Asignado A
                  </div>
                  <div class="col-lg-4 text-left">  
                     <strong>{{ $detalle[0]->asig_a }}</strong>
                  </div>
            </div>
            <br>
             <div class="row">
                  <div class="col-lg-3">  
                     Descripción del Requerimiento
                  </div>
                  <div class="col-lg-9 text-left">
                   <textarea class="form-control" rows="4" id="comment"  name="text" disabled="disabled">{{ $detalle[0]->desc_rq }}</textarea>  
                  </div>
            </div>
            <br>
             <form class="md-form" id="logout-form2" action="{{ url('/JefeSistemas/'.$id .'/rq-guardar-solu') }}" method="post" enctype="multipart/form-data">
               {{ csrf_field() }}
                <br><br>
                 <input type="hidden" name="fecha_asignacion" value="{{ $detalle[0]->fecha_asignacion }}">
                 <input type="hidden" name="hora_asignacion" value="{{ $detalle[0]->hora_asignacion }}"> 
                <div class="row">
                  <div class="col-lg-2">  
                     Del Gestor:
                  </div>
                  <div class="col-lg-2 text-left">
                      <select class="form-control form-control-large"  name="gestor" required="">
                          <option value=0 selected="selected">Seleccione</option>
                        @foreach($gestor as $valueg)
                         <option value="{{ $valueg->id }}" >{{ $valueg->name }}</option>
                        @endforeach
                      </select>
                  </div>
                  <div class="col-lg-2">  
                     para Desarrollador:
                  </div>
                  <div class="col-lg-2 text-left">
                    <select class="form-control form-control-large"  name="desarrollador" required="">
                         <option value=0 selected="selected">Seleccione</option>
                       @foreach($desarrollador as $valued)
                         <option value="{{ $valued->id }}" >{{ $valued->name }}</option>
                       @endforeach               
                    </select>
                  </div>
                </div>
            </form>  
            <br>
            <br>
           <!-- @@@@@@@include('jefe_sistemas.subir_adjunto')-->
            <div class="row">
              <div class="col-lg-6 text-right">  
                <div class="my-2"></div>  
                <a href="#" class="btn btn-success btn-icon-split" data-toggle="modal" data-target="#logoutModalApro">
                  <span class="icon text-white-50">
                    <i class="fas fa-check"></i>
                  </span>
                  <span class="text">Asignar</span>
                </a>
              </div>
              <div class="col-lg-6"> 
                 <div class="my-2"></div>
                <a href="#" class="btn btn-danger btn-icon-split" data-toggle="modal" data-target="#logoutModalRech">
                  <span class="icon text-white-50">
                    <i class="fas fa-exclamation-triangle"></i>
                  </span>
                  <span class="text">Rechazar</span>
                </a>
              </div>
            </div>
      </div>
    </div>
    </div>      
  </div>
</div>
        <!-- /.container-fluid -->
  <!-- Logout Modal-->
  <div class="modal fade" id="logoutModalApro" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Asignar requerimiento.</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">x</span>
          </button>
        </div>
        <div class="modal-body">Seleccione "Asignar Requerimiento" para asignar el req.  .</div>
        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
          <!--<a class="btn btn-primary" href="login.html">Logout</a>-->
              <a class="btn btn-primary" href="{{ route('logout') }}"
                  onclick="event.preventDefault();
                           document.getElementById('logout-form2').submit();">
                  Asignar Requerimiento
              </a>
              <form id="logout-form2" action="{{ route('guadarSolucionar','id') }}" method="POST" style="display: none;">
                  {{ csrf_field() }}
                  <input type="hidden" name="id" value="{{ $detalle[0]->Nro_asignacion }}">
              </form>
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
         <div class="modal-body">Observación del rq {{ $detalle[0]->Nro_asignacion }}.</div>
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
                  <input type="hidden" name="id" value="{{ $detalle[0]->Nro_asignacion }}">
                  <input type="hidden" name="nombreFuncion" value="{{ $nombreFuncion }}">
                  <input type="text" name="idAdjunto" id="idAdjunto" value=""/>
              </form>
            </div>
        </div>
      </div>
    </div>
  </div>
@endsection