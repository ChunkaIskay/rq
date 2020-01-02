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
  <h1 class="h3 mb-2 text-gray-800">Datos del Requerimentos.</h1>
  <p class="mb-4">Datos del Requerimientos. </p>

  <!-- DataTales Example -->
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary">Ingrese datos del Requerimiento nuevo</h6>
    </div>
    <div class="card-body">
    <label><strong>Paso 1</strong></label>
    </div>
    @include('operador.subir_adjunto_ope')
    <br>
    <div class="card-body">
          <label><strong>Paso 2</strong></label>
          <form  class="md-form" id="logout-form2" action="{{ url('/Operador/guardar-req') }}" method="post" enctype="multipart/form-data">
           {{ csrf_field() }}
           @if($errors->any())
            <div class="alert alert-danger">
              <ul>
                @foreach($errors->all() as $error)
                  <li>{{$error}}</li>
                @endforeach
              </ul>
            </div>
           @endif
           <input type="hidden" name="id_req" value="{{ $idReqUltimo }}">
           <input type="hidden" name="fecha_soli" value="{{ $fecha }}">
           <input type="hidden" name="hora_soli" value="{{ $hora }}">
          <div class="row">
                <div class="col-lg-2">  
                   Id Requerimiento
                </div>
                <div class="col-lg-4 text-left">  
                   <strong>{{ $idReqUltimo }}</strong>
                </div>
                <div class="col-lg-2">  
                    
                </div>
                <div class="col-lg-4 text-left">  
                   <strong></strong>
                </div>
          </div><br>
          <div class="row">
                  <div class="col-lg-2">  
                     Tipo
                  </div>
                  <div class="col-lg-4 text-left">  
                    <select class="form-control form-control-large"  name="tipo" required="">
                      @foreach($arrayTipo as $tkey => $tipoo)
                            <option value="{{ $tkey }}" @if( $tkey == old('tipo', '')) selected @endif >{{ $tipoo }}</option>
                      @endforeach             
                    </select>
                  </div>
                  <div class="col-lg-2">  
                      Prioridad
                  </div>
                  <div class="col-lg-4 text-left">  
                     <select class="form-control form-control-large"  name="prioridad" required="">
                      @foreach($arrayPrioridad as $pkey => $prioridad)
                            <option value="{{ $pkey }}" @if( $pkey == old('prioridad', '')) selected @endif >{{ $prioridad }}</option>
                      @endforeach             
                    </select>
                  </div>
            </div><br>
            <div class="row">
                  <div class="col-lg-2">  
                     Fecha Solicitud
                  </div>
                  <div class="col-lg-4 text-left">  
                     <strong>{{ $fecha }}</strong>
                  </div>
                  <div class="col-lg-2">  
                      Hora Solicitud
                  </div>
                  <div class="col-lg-4 text-left">  
                     <strong>{{ $hora }}</strong>
                  </div>
            </div><br>
            <div class="row">
                  <div class="col-lg-2">  
                     Fecha Limite
                  </div>
                  <div class="col-lg-4 text-left">  
                     <strong><input type='date' name='fechalim' class="form-control form-control-large" size='10' maxlength='10' value='{{ $fecha }}'></strong>
                  </div>
                  <div class="col-lg-2">  
                      
                  </div>
                  <div class="col-lg-4 text-left">  
                     <strong></strong>
                  </div>
            </div><br>
            <div class="row">
                  <div class="col-lg-2">  
                     Tipo de tarea
                  </div>
                  <div class="col-lg-4 text-left">  
                     <select class="form-control form-control-large"  name="tipotarea" required="">
                      @foreach($arrayTipoTarea as $ttkey => $ttarea)
                            <option value="{{ $ttkey }}" @if( $ttkey == old('tipotarea', '')) selected @endif >{{ $ttarea }}</option>
                      @endforeach             
                     </select>
                  </div>
                  <div class="col-lg-2">  
                      
                  </div>
                  <div class="col-lg-4 text-left">  
                     <strong></strong>
                  </div>
            </div><br>
            <div class="row">
                  <div class="col-lg-2">  
                     Cliente
                  </div>
                  <div class="col-lg-4 text-left">  
                     <select class="form-control form-control-large"  name="cliente" required="">
                        @foreach($listaClientes as $lcliente)
                              <option value="{{ $lcliente->id_cliente }}" @if( $lcliente->id_cliente == old('cliente', '')) selected @endif >{{ $lcliente->nombre }}</option>
                        @endforeach   
                    </select> 
                  </div>
                  <div class="col-lg-2">  
                     Operador
                  </div>
                  <div class="col-lg-4 text-left">   
                     <select class="form-control form-control-large"  name="operadorr" required="">
                        @foreach($operador as $ooperador)
                              <option value="{{ $ooperador->id }}" @if( $ooperador->id == old('operadorr', '')) selected @endif >{{ $ooperador->name.' '.$ooperador->ap_paterno }}</option>
                        @endforeach
                      </select>
                  </div>
            </div><br>
             <div class="row">
                  <div class="col-lg-3">  
                     Resultado Deseado
                  </div>
                  <div class="col-lg-9 text-left">  
                     <textarea class="form-control" rows="3" id="comment" placeholder="Escriba el resultado deseado" name="resul_dese">{{ old('resul_dese') }}</textarea>
                  </div>
            </div>
            <br>
            <div class="row">
                  <div class="col-lg-3">  
                    Descripción
                  </div>
                  <div class="col-lg-9 text-left">  
                     <textarea class="form-control" rows="3" id="comment" placeholder="Escriba la descripción del requerimiento" name="desc">{{ old('desc') }}</textarea>
                  </div>
            </div>
            <br>
            <br>
            </form>
            
            <div class="row">
              <div class="col-lg-6 text-right">  
                <div class="my-2"></div>
                <a href="#" class="btn btn-success btn-icon-split" data-toggle="modal" data-target="#modalActualizar">
                  <span class="icon text-white-50">
                    <i class="fas fa-check"></i>
                  </span>
                  <span class="text">Nuevo requerimiento</span>
                </a>
              </div>
              <div class="col-lg-6 text-left">  
                <div class="my-2"></div>
                  <a class="btn btn-primary" href="{{ route('rqList') }}">
                  Volver
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
  <div class="modal fade" id="modalActualizar" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Esta seguro de crear el requerimiento.</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">x</span>
          </button>
        </div>
        <div class="modal-body">Seleccione "Crear Requerimiento" para crear un nuevo requerimiento.</div>
        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
          <!--<a class="btn btn-primary" href="login.html">Logout</a>-->
              <a class="btn btn-primary" href="{{ route('logout') }}"
                  onclick="event.preventDefault();
                           document.getElementById('logout-form2').submit();">
                  Nuevo Requerimiento
              </a>
              <form id="logout-form" action="{{ route('aprobarPendiente') }}" method="POST" style="display: none;">
                  {{ csrf_field() }}
                  <input type="hidden" name="id" value="88888">
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
         <div class="modal-body">Observación del 000000.</div>
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
              <form id="logout-form1" action="{{ route('deleteFileOpe') }}" method="POST" style="display: none;">
                  {{ csrf_field() }}
                  <input type="hidden" name="id" value="{{ $idReqUltimo }}">
                  <input type="hidden" name="nombreFuncion" value="{{ $nombreFuncion }}">
                  <input type="hidden" name="idAdjunto" id="idAdjunto"/>
              </form>


            </div>
        </div>
      </div>
    </div>
  </div>

<script>
  $(document).ready(function(e){
    $('#deletemodal').on('show.bs.modal', function(e){     
       var id = $(e.relatedTarget).data().id;  
        document.getElementById('idAdjunto').value = id;
    });
  });
</script>
@endsection
