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
  <h1 class="h3 mb-2 text-gray-800">Revisar requerimentos pendientes a instalar</h1>
  <p class="mb-4">Detalle del requerimiento pendiente a instalar. </p>

  <!-- DataTales Example -->
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary">Datos del Requerimiento</h6>
    </div>
    <div class="card-body">
          <div class="row">
                <div class="col-lg-2">  
                   Id Requerimiento
                </div>
                <div class="col-lg-4 text-left">  
                   <strong>{{ $detalle[0]->id_requerimiento }}</strong>
                </div>
                <div class="col-lg-2">  
                    Accesible
                </div>
                <div class="col-lg-4 text-left">  
                   <strong>{{ $detalle[0]->req_acces }}</strong>
                </div>
          </div>
          <div class="row">
                  <div class="col-lg-2">  
                     Tipo
                  </div>
                  <div class="col-lg-4 text-left">  
                     <strong>{{ $detalle[0]->tipo }}</strong>
                  </div>
                  <div class="col-lg-2">  
                      Prioridad
                  </div>
                  <div class="col-lg-4 text-left">  
                     <strong>{{ $detalle[0]->prioridad }}</strong>
                  </div>
            </div>
            <div class="row">
                  <div class="col-lg-2">  
                     Fecha Solicitud
                  </div>
                  <div class="col-lg-4 text-left">  
                     <strong>{{ $detalle[0]->fecha_solicitud }}</strong>
                  </div>
                  <div class="col-lg-2">  
                      Hora Solicitud
                  </div>
                  <div class="col-lg-4 text-left">  
                     <strong>{{ $detalle[0]->hora_solicitud }}</strong>
                  </div>
            </div>
            <div class="row">
                  <div class="col-lg-2">  
                     Solicitado Por
                  </div>
                  <div class="col-lg-4 text-left">  
                     <strong>{{ $detalle[0]->name.' '.$detalle[0]->ap_paterno }}</strong>
                  </div>
                  <div class="col-lg-2">  
                     Para el Cliente
                  </div>
                  <div class="col-lg-4 text-left">  
                     <strong></strong>
                  </div>
            </div>
            <br>
             <div class="row">
                  <div class="col-lg-2">  
                     Resultado Deseado
                  </div>
                  <div class="col-lg-10 text-left">  
                     <strong>{{ $detalle[0]->resultado }}</strong>
                  </div>
            </div>
            <br>
            <div class="row">

                  <div class="col-lg-2">  
                    Descripción
                  </div>
                  <div class="col-lg-10 text-left">
                    <div class="md-form mb-4 pink-textarea active-pink-textarea">
                      <textarea class="md-textarea form-control" rows="3" cols="20" disabled>{{ $detalle[0]->rq_desc }}</textarea>
                    </div>



                     <strong></strong>
                  </div>
            </div>
            <br>
            @include('jefe_operaciones.subir_adjunto')

            <div class="row">
              <div class="col-lg-6 text-right">  
                <div class="my-2"></div>
                <a href="#" class="btn btn-success btn-icon-split" data-toggle="modal" data-target="#logoutModalApro">
                  <span class="icon text-white-50">
                    <i class="fas fa-check"></i>
                  </span>
                  <span class="text">Aprobar</span>
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
       
<div class="container-fluid">
    <div class="card shadow mb-4">
        <!-- Card Header - Accordion -->
        <a href="#aprobreq" class="d-block card-header py-3" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="aprobreq">
          <h6 class="m-0 font-weight-bold text-primary">Datos de Aprobación del requerimiento</h6>
        </a>
        <!-- Card Content - Collapse -->
        <div class="collapse show" id="aprobreq">
           <div class="card-body">
            <div class="row">
                  <div class="col-lg-2">  
                     Fecha aprobación
                  </div>
                  <div class="col-lg-4 text-left">  
                     <strong>{{ $aproRq[0]->fecha_aprobacion }}</strong>
                  </div>
                  <div class="col-lg-6"></div>
            </div>
            <div class="row">
                  <div class="col-lg-2">  
                      Hora aprobación
                  </div>
                  <div class="col-lg-4 text-left">  
                     <strong>{{ $aproRq[0]->hora_aprobacion }}</strong>
                  </div>
                  <div class="col-lg-6"></div>
            </div>
        </div>
      </div>
    </div>
</div>
<div class="container-fluid">
    <div class="card shadow mb-4">
        <!-- Card Header - Accordion -->
        <a href="#asigReq" class="d-block card-header py-3" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="asigReq">
          <h6 class="m-0 font-weight-bold text-primary">Datos de Asignación del requerimiento</h6>
        </a>
        <!-- Card Content - Collapse -->
        <div class="collapse show" id="asigReq">
           <div class="card-body">
            <div class="row">
                  <div class="col-lg-2">  
                     Fecha asignación
                  </div>
                  <div class="col-lg-4 text-left">  
                     <strong>{{ $asigRq[0]->fecha_asignacion }}</strong>
                  </div>
                  <div class="col-lg-6"></div>
            </div>
            <div class="row">
                  <div class="col-lg-2">  
                      Hora asignación
                  </div>
                  <div class="col-lg-4 text-left">  
                     <strong>{{ $asigRq[0]->hora_asignacion }}</strong>
                  </div>
                  <div class="col-lg-6"></div>
            </div>
            <div class="row">
                  <div class="col-lg-2">  
                      Asignado por
                  </div>
                  <div class="col-lg-4 text-left">  
                     <strong>{{ $asigRq[0]->asignado_por }}</strong>
                  </div>
                  <div class="col-lg-6"></div>
            </div>
            <div class="row">
                  <div class="col-lg-2">  
                      Asignado a
                  </div>
                  <div class="col-lg-4 text-left">  
                     <strong>{{ $asigRq[0]->asignado_a }}</strong>
                  </div>
                  <div class="col-lg-6"></div>
            </div>
        </div>
      </div>
    </div>
</div>
<div class="container-fluid">
    <div class="card shadow mb-4">
        <!-- Card Header - Accordion -->
        <a href="#datosSolu" class="d-block card-header py-3" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="datosSolu">
          <h6 class="m-0 font-weight-bold text-primary">Datos de la Solución</h6>
        </a>
        <!-- Card Content - Collapse -->
        <div class="collapse show" id="datosSolu">
           <div class="card-body">
            <div class="row">
                  <div class="col-lg-2">  
                     Id solución
                  </div>
                  <div class="col-lg-4 text-left">  
                     <strong>{{ $detalle[0]->id_solucion }}</strong>
                  </div>
                  <div class="col-lg-6"></div>
            </div>
            <div class="row">
                  <div class="col-lg-2">  
                      Nro secuencial
                  </div>
                  <div class="col-lg-4 text-left">  
                     <strong>{{ $detalle[0]->secuencia }}</strong>
                  </div>
                  <div class="col-lg-6"></div>
            </div>
            <div class="row">
                  <div class="col-lg-2">  
                      Fecha solución
                  </div>
                  <div class="col-lg-4 text-left">  
                     <strong>{{ $detalle[0]->fecha_fin }}</strong>
                  </div>
                  <div class="col-lg-6"></div>
            </div>
            <div class="row">
                  <div class="col-lg-2">  
                      Hora solución
                  </div>
                  <div class="col-lg-4 text-left">  
                     <strong>{{ $detalle[0]->hora_fin }}</strong>
                  </div>
                  <div class="col-lg-6"></div>
            </div>
            <div class="row">
                  <div class="col-lg-2">  
                      Asignado a
                  </div>
                  <div class="col-lg-4 text-left">  
                     <strong>{{ $responsableSolu[0]->name .' '. $responsableSolu[0]->ap_paterno }}</strong>
                  </div>
                  <div class="col-lg-6"></div>
            </div>
            <div class="row">
                  <div class="col-lg-2">  
                      Descripción
                  </div>
                  <div class="col-lg-10 text-left">
                      <div class="md-form mb-4 pink-textarea active-pink-textarea">
                        <textarea class="md-textarea form-control" rows="4" cols="30" disabled>{{ $detalle[0]->sol_desc }}</textarea>
                      </div>  
                  </div>
            </div>
            <br>
            @include('jefe_operaciones.subir_adjunto1')
        </div>
      </div>
    </div>
</div>
<div class="container-fluid">
    <div class="card shadow mb-4">
        <!-- Card Header - Accordion -->
        <a href="#certiPre" class="d-block card-header py-3" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="certiPre">
          <h6 class="m-0 font-weight-bold text-primary">Datos de certificación pre-instalación</h6>
        </a>
        <!-- Card Content - Collapse -->
        <div class="collapse show" id="certiPre">
           <div class="card-body">
            <div class="row">
                  <div class="col-lg-2">  
                     Id certificación
                  </div>
                  <div class="col-lg-4 text-left">  
                     <strong>{{ $certiPreInst[0]->id_solucion }}</strong>
                  </div>
                  <div class="col-lg-6"></div>
            </div>
            <div class="row">
                  <div class="col-lg-2">  
                      Fecha certificación
                  </div>
                  <div class="col-lg-4 text-left">  
                     <strong>{{ $certiPreInst[0]->fecha_certificacion }}</strong>
                  </div>
                  <div class="col-lg-6"></div>
            </div>
            <div class="row">
                  <div class="col-lg-2">  
                      Hora certificación
                  </div>
                  <div class="col-lg-4 text-left">  
                     <strong>{{ $certiPreInst[0]->hora_certificacion }}</strong>
                  </div>
                  <div class="col-lg-6"></div>
            </div>
            <div class="row">
                  <div class="col-lg-2">  
                      Responsable de certificación
                  </div>
                  <div class="col-lg-4 text-left">  
                     <strong>{{ $certiPreInst[0]->name.' '.$certiPreInst[0]->ap_paterno }}</strong>
                  </div>
                  <div class="col-lg-6"></div>
            </div>
          
            <div class="row">
                  <div class="col-lg-3">  
                      Detalle de la certificación
                  </div>
                  <div class="col-lg-9 text-left">
                      <div class="md-form mb-4 pink-textarea active-pink-textarea">
                        <textarea class="md-textarea form-control" rows="4" cols="30" disabled>{{ $certiPreInst[0]->detalle_certificacion }}</textarea>
                      </div>  
                  </div>
            </div>
            <div class="row">
                  <div class="col-lg-3">  
                      Detalle de las funcionalidades
                  </div>
                  <div class="col-lg-9 text-left">
                      <div class="md-form mb-4 pink-textarea active-pink-textarea">
                        <textarea class="md-textarea form-control" rows="4" cols="30" disabled>{{ $certiPreInst[0]->detalle_funcionalidades }}</textarea>
                      </div>  
                  </div>
            </div>
            <br>
            @include('jefe_operaciones.subir_adjunto2')
        </div>
      </div>
    </div>
</div>


  <!-- Logout Modal-->
  <div class="modal fade" id="logoutModalApro" tabindex="-1" role="dialog" aria-labelledby="logoutModalApro" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="logoutModalApro">Esta seguro de aprobar el requerimiento.</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">x</span>
          </button>
        </div>
        <div class="modal-body">Seleccione "Aprobar Requerimiento" para aprobar el  rq {{ $detalle[0]->id_requerimiento }}.</div>
        <form id="logout-formInst" action="{{ route('aprobarAsigInstalarDesc') }}" method="POST" style="display: block;">
                  {{ csrf_field() }}
                  <input type="hidden" name="id" value="{{ $detalle[0]->id_requerimiento }}">
                  <div class="modal-body"><textarea class="form-control" name="desc_instal" rows="3" id="desc_instal" placeholder="Descripción del la aprobación " name="text"></textarea></div>
        </form><br>
        <div class="modal-footer">
            <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
            <!--<a class="btn btn-primary" href="login.html">Logout</a>-->
            <a class="btn btn-primary" href="{{ route('logout') }}"
                onclick="event.preventDefault();
                         document.getElementById('logout-formInst').submit();">
                Aprobar Requerimiento
            </a>
        </div>
      </div>
    </div>
  </div>
  <!-- Logout Modal-->
  <div class="modal fade" id="logoutModalRech" tabindex="-1" role="dialog" aria-labelledby="logoutModalRech" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="logoutModalRech">Esta seguro de rechazar el requerimiento !!!</h5>
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
  <div class="modal fade" id="deletemodal" tabindex="-1" aria-labelledby="deletemodal" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="deletemodal">Esta seguro de borrar el archivo?.</h5>
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
                  <input type="hidden" name="nombreFuncion" value="{{ $nombreFuncion }}">
                  <input type="text" name="idAdjunto" id="idAdjunto" value=""/>
                </form>
            </div>
 
        </div>
      </div>
    </div>
  </div>

@endsection