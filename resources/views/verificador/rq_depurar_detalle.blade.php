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


hr.style15 {
  border-top: 4px double #8c8b8b;
  text-align: center;
}
hr.style2 {
  border-top: 3px double #8c8b8b;
}


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
  <h1 class="h3 mb-2 text-gray-800">Desea depurar este requerimiento {{ $detalle[0]->id_requerimiento }} ?</h1>

  <br><br>
  <form  class="md-form" id="logout-form3" action="{{ url('/Verificador/'.$detalle[0]->id_requerimiento .'/rq-depurar-guardar') }}" method="post" enctype="multipart/form-data">
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
  <div class="row">   
      <div class="col-lg-10">  
         Escriba el motivo de la Depuración:
      </div>
      <div class="col-lg-9 text-left"> 
          <input type="hidden" name="idr" value="{{ $detalle[0]->id_requerimiento }}"> 
         <textarea class="form-control" name="detalle_depurar"  rows="4" placeholder="Motivo de la depuración....."></textarea>
      </div>
  </div>
  <div class="row">
        <div class="col-lg-6 text-right">  
          <div class="my-2"></div>
          <a href="#" class="btn btn-success btn-icon-split" data-toggle="modal" onclick="event.preventDefault();
                    document.getElementById('logout-form3').submit();">
            <span class="icon text-white-50">
              <i class="fas fa-check"></i>
            </span>
            <span class="text">Depurar</span>
          </a>
        </div>
        <div class="col-lg-6"> 
           <div class="my-2"></div>

            <a class="btn btn-primary" href="{{ route('rqDepurarReqVeri') }}">
                <span class="icon text-white-50">
                  <i class="fas fa-exclamation-triangle"></i>
                </span>
                <span class="text">Volver</span>
              </a>
        </div>
  </div>
  </form>
  <br><br>
  @foreach($arrayFases as $keyn => $valuen) 
  @if($valuen['fase_actual'] == 1)
  <div class=" font-weight-bold text-warning text-uppercase mb-3"> 
  <p class="mb-4">El Requermiento {{ $detalle[0]->id_requerimiento }} se encuentra en la {{ $valuen['nom_fase'] }}.</p> </div>
   @endif 
  @endforeach   
  
  <div class="row">
          @foreach($arrayFases as $keyf => $valuef) 
            <div class="col-xl-3 col-md-6 mb-4">
                @if($valuef['fase_actual'] == 2)
                <div class="card border-left-success shadow h-100 py-2 border-bottom-success">
                @endif 
                @if($valuef['fase_actual'] == 0) 
                <div class="card border-left-primary shadow h-100 py-2 border-bottom-primary"  id="div_0" style="filter:alpha(opacity=25);-moz-opacity:.25;opacity:.25">
                @endif 
                @if($valuef['fase_actual'] == 1)  
                <div class="card border-left-warning shadow h-100 py-2 border-bottom-warning">
                @endif
                  <div class="card-body">
                    <div class="row no-gutters align-items-center">
                      <div class="col mr-2">
                        @if($valuef['fase_actual'] == 2)
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                        @endif 
                        @if($valuef['fase_actual'] == 0) 
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                        @endif 
                        @if($valuef['fase_actual'] == 1)  
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">  
                        @endif

                        @if($valuef['fase_actual'] > 0)
                        <a id="mostrar_{{ $valuef['id_fase'] }}" onclick="showDiv('mostrar',{{ $valuef['id_fase'] }} )"  style="cursor:pointer;">{{ $valuef['nom_fase'] }}</a>
                        <!-- <a id="ocultar_{{ $valuef['id_fase'] }}" onclick="showDiv('ocultar',{{ $valuef['id_fase'] }} )"  style="cursor:pointer; display: none;">{{ $valuef['nom_fase'] }}</a>-->
                        @else
                        {{ $valuef['nom_fase'] }}
                        @endif

                        </div>
                        @if($valuef['fase_actual'] == 2)
                        <div class="row no-gutters align-items-center">
                          <div class="col-auto">
                            <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">100%</div>
                          </div>
                          <div class="col">
                            <div class="progress progress-sm mr-2">
                              <div class="progress-bar bg-success" role="progressbar" style="width: 50%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                          </div>
                        </div>
                        @endif 
                        @if($valuef['fase_actual'] == 0)
                        <div class="row no-gutters align-items-center">
                          <div class="col-auto">
                            <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">0%</div>
                          </div>
                          <div class="col">
                            <div class="progress progress-sm mr-2">
                              <div class="progress-bar bg-info" role="progressbar" style="width: 0%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="0"></div>
                            </div>
                          </div>
                        </div>
                        @endif 
                        @if($valuef['fase_actual'] == 1)  
                        <div class="row no-gutters align-items-center">
                          <div class="col-auto">
                            <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">80%</div>
                          </div>
                          <div class="col">
                            <div class="progress progress-sm mr-2">
                              <div class="progress-bar bg-warning" role="progressbar" style="width:80%" aria-valuenow="40" aria-valuemin="0" aria-valuemax="50"></div>
                            </div>
                          </div>
                        </div>
                        @endif
                      </div>
                      <div class="col-auto">
                        @if($valuef['fase_actual'] == 2)
                        <i class="fa-li fa fa-check-square fa-2x text-gray-300"></i> 
                        @endif 
                        @if($valuef['fase_actual'] == 0)
                        <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        @endif 
                        @if($valuef['fase_actual'] == 1)  
                        <i class="fas fa-comments fa-2x text-gray-300"></i>  
                        @endif
                        
                       <!--<i class="fas fa-calendar fa-2x text-gray-300"></i>-->
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              @if($keyf != 'aceptacion_cliente')
                &rArr;
              @endif   
          @endforeach()
  </div>
        <!-- fase incio de requermiento -->
        <div class="card shadow mb-4" id="target_1" style="display: none;">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Requerimiento {{ $detalle[0]->id_requerimiento }}</h6>
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
                           <strong>{{ $detalle[0]->accesible }}</strong>
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
                             Por el operador
                          </div>
                          <div class="col-lg-4 text-left">  
                             <strong>{{ $detalle[0]->name.' '.$detalle[0]->ap_paterno }}</strong>
                          </div>
                          <div class="col-lg-2">  
                             Para el Cliente
                          </div>
                          <div class="col-lg-4 text-left">  
                             <strong>{{ $detalle[0]->nombre }}</strong>
                          </div>
                  </div>
                    <br>
                  <div class="row">
                          <div class="col-lg-3">  
                             Resultado Deseado
                          </div>
                          <div class="col-lg-9 text-left">  
                             <textarea class="form-control"  rows="4" disabled="">{{ $detalle[0]->resultado }}</textarea>
                          </div>
                  </div>
                    <br>
                  <div class="row">
                      <div class="col-lg-3">  
                          Descripción
                      </div>
                      <div class="col-lg-9 text-left">  
                          <textarea class="form-control"  rows="4" disabled="">{{ $detalle[0]->descripcion }}</textarea>
                      </div>
                  </div>
                  <br>
                  <br>
                  @include('jefe_sistemas.subir_adjunto3')
                  <div class="row">
                      
                      <div class="col-lg-12 text-center"> 
                         <div class="my-2"></div>

                        <a id="ocultar_0" onclick="showDiv('ocultar', 0 )"  style="cursor:pointer;" class="btn btn-danger btn-icon-split" data-toggle="modal" >
                          <span class="icon text-white-50">
                            <i class="fas fa-exclamation-triangle"></i>
                          </span>
                          <span class="text">Cerrar</span>
                        </a>
                      </div>
                  </div>
              </div>  <!--end card-body -->
        </div> <!--fin incio de requermiento --> 

        <!-- fase de aprobación-->
        @if(!empty($aprobacion))
        <div class="card shadow mb-4" id="target_2" style="display: none;">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Requerimiento {{ $detalle[0]->id_requerimiento }}</h6>
            </div>
            <div class="card-body">
                  <div class="row">
                        
                        <div class="col-lg-2">  
                            Aprobado en fecha 
                        </div>
                        <div class="col-lg-4 text-left">  
                           <strong>{{ $aprobacion->fecha_aprobacion }}</strong>
                        </div>
                        <div class="col-lg-2">  
                           A horas
                        </div>
                        <div class="col-lg-4 text-left">  
                           <strong>{{ $aprobacion->hora_aprobacion }}</strong>
                        </div>
                  </div>
                  
                  <!-- boton cerrar-->
                  <br>                 
                  <div class="row">
                      <div class="col-lg-12 text-center"> 
                         <div class="my-2"></div>

                        <a id="ocultar_0" onclick="showDiv('ocultar', 0 )"  style="cursor:pointer;" class="btn btn-danger btn-icon-split" data-toggle="modal" >
                          <span class="icon text-white-50">
                            <i class="fas fa-exclamation-triangle"></i>
                          </span>
                          <span class="text">Cerrar</span>
                        </a>
                      </div>
                  </div>
              </div>  <!--end card-body -->
        </div> <!--fin de aprobación --> 
        @endif
        @if(!empty($asignacion))
        <!-- fase de asignacion-->
        <div class="card shadow mb-4" id="target_3" style="display: none;">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Requerimiento {{ $detalle[0]->id_requerimiento }}</h6>
            </div>
            <div class="card-body">
                  <div class="row">
                        
                        <div class="col-lg-2">  
                            Asignado en fecha 
                        </div>
                        <div class="col-lg-2 text-left">  
                           <strong>{{ $asignacion[0]->fecha_asignacion }}</strong>
                        </div>
                        <div class="col-lg-1 text-left">  
                           A horas
                        </div>
                        <div class="col-lg-4 text-left">  
                           <strong>{{ $asignacion[0]->hora_asignacion }}</strong>
                        </div>
                  </div>
                  <div class="row">
                        
                        <div class="col-lg-2">  
                            Asignado por 
                        </div>
                        <div class="col-lg-2 text-left">  
                           <strong>{{ $asignacion[0]->asignado_por }}</strong>
                        </div>
                        <div class="col-lg-1 text-left">  
                           a 
                        </div>
                        <div class="col-lg-4 text-left">  
                           <strong>{{ $asignacion[0]->asignado_a }}</strong>
                        </div>
                  </div>
                  
                  <!-- boton cerrar-->
                  <br>                 
                  <div class="row">
                      <div class="col-lg-12 text-center"> 
                         <div class="my-2"></div>

                        <a id="ocultar_0" onclick="showDiv('ocultar', 0 )"  style="cursor:pointer;" class="btn btn-danger btn-icon-split" data-toggle="modal" >
                          <span class="icon text-white-50">
                            <i class="fas fa-exclamation-triangle"></i>
                          </span>
                          <span class="text">Cerrar</span>
                        </a>
                      </div>
                  </div>
              </div>  <!--end card-body -->
        </div> <!--fin de asignacion -->
        @endif
        @if(!empty($desarrollo))
        <!-- fase de desarrollo -->
        <div class="card shadow mb-4" id="target_4" style="display: none;">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Requerimiento {{ $detalle[0]->id_requerimiento }}</h6>
            </div>
            <div class="card-body">
                  <div class="row">
                        <div class="col-lg-3">  
                           Fue solucionado en fecha
                        </div>
                        <div class="col-lg-3 text-left">  
                           <strong>{{ $desarrollo[0]->fecha_fin }}</strong>
                        </div>
                        <div class="col-lg-2 text-left">  
                            a horas
                        </div>
                        <div class="col-lg-4 text-left">  
                           <strong>{{ $desarrollo[0]->hora_fin }}</strong>
                        </div>
                  </div>
                  <br>
                  <div class="row">
                        <div class="col-lg-3">  
                           Descripción de la Solución 
                        </div>
                        <div class="col-lg-9 text-left">  
                           <textarea class="form-control"  rows="4" disabled="">{{ $desarrollo[0]->descripcion }}</textarea>
                        </div>
                  </div>
                  <br> 
                  <br>
                  <br>
                  @include('jefe_sistemas.subir_adjunto4')
                  <div class="row">
                      <div class="col-lg-12 text-center"> 
                         <div class="my-2"></div>
                        <a id="ocultar_0" onclick="showDiv('ocultar', 0 )"  style="cursor:pointer;" class="btn btn-danger btn-icon-split" data-toggle="modal" >
                          <span class="icon text-white-50">
                            <i class="fas fa-exclamation-triangle"></i>
                          </span>
                          <span class="text">Cerrar</span>
                        </a>
                      </div>
                  </div>
              </div>  <!--end card-body -->
        </div> <!--fin incio de desarrollo --> 
        @endif
        @if(!empty($prueba))
        <!-- fase de prueba -->
        <div class="card shadow mb-5" id="target_5" style="display: none;">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Requerimiento {{ $detalle[0]->id_requerimiento }}</h6>
            </div>
            <div class="card-body">
                  <div class="row">
                        <div class="col-lg-3">  
                           Comenzo a ser probado desde el 
                        </div>
                        <div class="col-lg-2 text-left">  
                           <strong>{{ $prueba[0]->fecha_fin }}</strong>
                        </div>
                        <div class="col-lg-2 text-left">  
                            a horas
                        </div>
                        <div class="col-lg-4 text-left">  
                           <strong>{{ $prueba[0]->hora_fin }}</strong>
                        </div>
                  </div>
                  <br>
                  <div class="row">
                        <div class="col-lg-3">  
                            Termino de ser probado el
                        </div>
                        <div class="col-lg-2 text-left">  
                           <strong>{{ $prueba[0]->fecha_certificacion }}</strong>
                        </div>
                        <div class="col-lg-2 text-left">  
                           a horas
                        </div>
                        <div class="col-lg-4 text-left">  
                           <strong>{{ $prueba[0]->hora_certificacion }}</strong>
                        </div>
                  </div>
                  <br>
                  <br>
                  <br>
                  @include('jefe_sistemas.subir_adjunto5')
                  <div class="row">
                      <div class="col-lg-12 text-center"> 
                         <div class="my-2"></div>
                        <a id="ocultar_0" onclick="showDiv('ocultar', 0 )"  style="cursor:pointer;" class="btn btn-danger btn-icon-split" data-toggle="modal" >
                          <span class="icon text-white-50">
                            <i class="fas fa-exclamation-triangle"></i>
                          </span>
                          <span class="text">Cerrar</span>
                        </a>
                      </div>
                  </div>
              </div>  <!--end card-body -->
        </div> <!--fin incio de prueba -->
        @endif
        @if(!empty($certificacion))
        <!-- fase de certificación -->
        <div class="card shadow mb-5" id="target_6" style="display: none;">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Requerimiento {{ $detalle[0]->id_requerimiento }}</h6>
            </div>
            <div class="card-body">
                  <div class="row">
                        <div class="col-lg-3">  
                           Fue certificado por  
                        </div>
                        <div class="col-lg-5 text-left">  
                           <strong>{{ $certificacion[0]->name.' '.$certificacion[0]->ap_paterno }}</strong>
                        </div>
                        <div class="col-lg-1 text-left">  
                         </div>
                        <div class="col-lg-3text-left">  
                           
                        </div>
                  </div>
                  <br>
                  <div class="row">
                        <div class="col-lg-3">  
                            La certificación de efectuo el 
                        </div>
                        <div class="col-lg-2 text-left">  
                           <strong>{{ $certificacion[0]->fecha_certificacion }}</strong>
                        </div>
                        <div class="col-lg-2 text-left">  
                           a horas
                        </div>
                        <div class="col-lg-4 text-left">  
                           <strong>{{ $certificacion[0]->hora_certificacion }}</strong>
                        </div>
                  </div>
                  <br>
                  <br>
                  <br>
                  @include('jefe_sistemas.subir_adjunto6')
                  <div class="row">
                      <div class="col-lg-12 text-center"> 
                         <div class="my-2"></div>
                        <a id="ocultar_0" onclick="showDiv('ocultar', 0 )"  style="cursor:pointer;" class="btn btn-danger btn-icon-split" data-toggle="modal" >
                          <span class="icon text-white-50">
                            <i class="fas fa-exclamation-triangle"></i>
                          </span>
                          <span class="text">Cerrar</span>
                        </a>
                      </div>
                  </div>
              </div>  <!--end card-body -->
        </div> <!--fin de certificación --> 
        @endif
        @if(!empty($instalacionAsig))
        <!-- fase de instalación -->
        <div class="card shadow mb-5" id="target_7" style="display: none;">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Requerimiento {{ $detalle[0]->id_requerimiento }}</h6>
            </div>
            <div class="card-body">
                  <div class="row">
                        <div class="col-lg-3">  
                           Fue asignado por  
                        </div>
                        <div class="col-lg-5 text-left">  
                           <strong>{{ $instalacionAsig[0]->asignado_por }}</strong>
                        </div>
                        <div class="col-lg-1 text-left">
                        a  
                         </div>
                        <div class="col-lg-3text-left">  
                           <strong>{{ $instalacionAsig[0]->asignado_a }}</strong>
                        </div>
                  </div>
                  <br>
                  <div class="row">
                        <div class="col-lg-3">  
                            La instalación fue hecha por 
                        </div>
                        <div class="col-lg-2 text-left">  
                           <strong>{{ $instalacionRqSeg[0]->name.' '.$instalacionRqSeg[0]->ap_paterno }}</strong>
                        </div>
                        <div class="col-lg-2 text-left">  
                         
                        </div>
                        <div class="col-lg-4 text-left">  
                           
                        </div>
                  </div>
                   <div class="row">
                        <div class="col-lg-3">  
                            El soporte de instalación por 
                        </div>
                        <div class="col-lg-2 text-left">  
                            <strong>{{ $instalacionAsig[0]->asignado_a }}</strong>
                        </div>
                        <div class="col-lg-2 text-left">  
                         
                        </div>
                        <div class="col-lg-4 text-left">  
                           
                        </div>
                  </div>
                  <div class="row">
                        <div class="col-lg-3">  
                            La asignación se efectuo el 
                        </div>
                        <div class="col-lg-2 text-left">  
                            <strong>{{ $instalacionAsig[0]->fecha_asig_instal }}</strong>
                        </div>
                        <div class="col-lg-2 text-left">  
                         a horas
                        </div>
                        <div class="col-lg-4 text-left">  
                            <strong>{{ $instalacionAsig[0]->hora_asig_instal }}</strong>
                        </div>
                  </div>
                  @if(!empty($instalacion))
                  <div class="row">
                        <div class="col-lg-3">  
                            La instalación se realizo en fecha  
                        </div>
                        <div class="col-lg-2 text-left">  
                            <strong>{{ $instalacion[0]->fecha_instal }}</strong>
                        </div>
                        <div class="col-lg-2 text-left">  
                         a horas
                        </div>
                        <div class="col-lg-4 text-left">  
                            <strong>{{ $instalacion[0]->hora_instal }}</strong>
                        </div>
                  </div>
                   <br>
                  <div class="row">
                      <div class="col-lg-3">  
                          Backup
                      </div>
                      <div class="col-lg-9 text-left">  
                          <textarea class="form-control"  rows="4" disabled="">{{ $instalacion[0]->backup }}</textarea>
                      </div>
                  </div>
                  <br>
                  <div class="row">
                      <div class="col-lg-3">  
                          Comentario
                      </div>
                      <div class="col-lg-9 text-left">  
                          <textarea class="form-control"  rows="4" disabled="">{{ $instalacion[0]->comentario }}</textarea>
                      </div>
                  </div>
                  @else
                  <div class="row">
                
                        <div class="col-lg-12 text-center text-xs font-weight-bold text-info text-uppercase mb-3"><br>
                            <strong>El requerimiento ha sido asignado a instalación pero todavia no se efectuado la instalación</strong>
                        </div>
                       
                  </div>
                  @endif
                  <br>
                  <br>
                  <br>
                  @include('jefe_sistemas.subir_adjunto7')
                  @include('jefe_sistemas.subir_adjunto8')
                  <div class="row">
                      <div class="col-lg-12 text-center"> 
                         <div class="my-2"></div>
                         <a id="ocultar_0" onclick="showDiv('ocultar', 0 )"  style="cursor:pointer;" class="btn btn-danger btn-icon-split" data-toggle="modal" >
                          <span class="icon text-white-50">
                            <i class="fas fa-exclamation-triangle"></i>
                          </span>
                          <span class="text">Cerrar</span>
                        </a>
                      </div>
                  </div>
              </div>  <!--end card-body -->
        </div> <!--fin de instlacion --> 
        @endif

         @if(!empty($certOnLine))
        <!-- fase de certificación on line -->
        <div class="card shadow mb-5" id="target_8" style="display: none;">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Requerimiento {{ $detalle[0]->id_requerimiento }}</h6>
            </div>
            <div class="card-body">
                  <div class="row">
                        <div class="col-lg-3">  
                           Fue certificado por  
                        </div>
                        <div class="col-lg-7 text-left">  
                           <strong>{{ $certOnLine[0]->fecha_certificacion.' '.$certOnLine[0]->ap_paterno }}</strong>
                        </div>
                        <div class="col-lg-1 text-left">
                  
                         </div>
                        <div class="col-lg-1text-left">  
                           
                        </div>
                  </div>
                  <br>
                  <div class="row">
                        <div class="col-lg-3">  
                            La certificación on line se efectuo el  
                        </div>
                        <div class="col-lg-2 text-left">  
                           <strong>{{ $certOnLine[0]->fecha_certificacion }}</strong>
                        </div>
                        <div class="col-lg-2 text-left">  
                         a horas
                        </div>
                        <div class="col-lg-4 text-left">  
                          <strong>{{ $certOnLine[0]->hora_certificacion }}</strong>  
                        </div>
                  </div>
                    <br>
                  <div class="row">
                      <div class="col-lg-3">  
                          Conformidad
                      </div>
                      <div class="col-lg-9 text-left">  
                          <textarea class="form-control"  rows="4" disabled="">{{ $certOnLine[0]->conformidad }}</textarea>
                      </div>
                  </div>
                  <br>
                  
                  <br>
                  <br>
                  <br>
                  @include('jefe_sistemas.subir_adjunto9')
                  <div class="row">
                      <div class="col-lg-12 text-center"> 
                         <div class="my-2"></div>
                         <a id="ocultar_0" onclick="showDiv('ocultar', 0 )"  style="cursor:pointer;" class="btn btn-danger btn-icon-split" data-toggle="modal" >
                          <span class="icon text-white-50">
                            <i class="fas fa-exclamation-triangle"></i>
                          </span>
                          <span class="text">Cerrar</span>
                        </a>
                      </div>
                  </div>
              </div>  <!--end card-body -->
        </div> <!--fin de certificación on line --> 
        @endif
        @if(!empty($controlSvn))
        <!-- fase de control de versión -->
        <div class="card shadow mb-5" id="target_9" style="display: none;">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Requerimiento {{ $detalle[0]->id_requerimiento }}</h6>
            </div>
            <div class="card-body">
                  
                  <div class="row">
                        <div class="col-lg-3">  
                            Fue realizado por 
                        </div>
                        <div class="col-lg-2 text-left">  
                           <strong>{{ $instalacionAsig[0]->asignado_a }}</strong>
                        </div>
                        <div class="col-lg-2 text-left">  
                         
                        </div>
                        <div class="col-lg-4 text-left">  
                           
                        </div>
                  </div>
                   <div class="row">
                        <div class="col-lg-3">  
                            Efectuado el 
                        </div>
                        <div class="col-lg-2 text-left">  
                            <strong>{{ $controlSvn[0]->fecha_subversion }}</strong>
                        </div>
                        <div class="col-lg-2 text-left">  
                         a horas
                        </div>
                        <div class="col-lg-4 text-left">  
                           <strong>{{ $controlSvn[0]->hora_subversion }}</strong>
                        </div>
                  </div>
                  <br>
                  <div class="row">
                      <div class="col-lg-3">  
                          Comentarios
                      </div>
                      <div class="col-lg-9 text-left">  
                          <textarea class="form-control"  rows="4" disabled="">{{ $controlSvn[0]->comentarios }}</textarea>
                      </div>
                  </div>
                  <br>
                  <br>
                  <br>
                  @include('jefe_sistemas.subir_adjunto3')
                  <div class="row">
                      <div class="col-lg-12 text-center"> 
                         <div class="my-2"></div>
                         <a id="ocultar_0" onclick="showDiv('ocultar', 0 )"  style="cursor:pointer;" class="btn btn-danger btn-icon-split" data-toggle="modal" >
                          <span class="icon text-white-50">
                            <i class="fas fa-exclamation-triangle"></i>
                          </span>
                          <span class="text">Cerrar</span>
                        </a>
                      </div>
                  </div>
              </div>  <!--end card-body -->
        </div> <!--fin contro de versión --> 
        @endif
        @if(!empty($aceptacionCliente))
        <!-- fase de aceptacion cliente -->
        <div class="card shadow mb-5" id="target_10" style="display: none;">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Requerimiento {{ $detalle[0]->id_requerimiento }}</h6>
            </div>
            <div class="card-body">
                  
                  <div class="row">
                        <div class="col-lg-3">  
                            Fue realizado por 
                        </div>
                        <div class="col-lg-2 text-left">  
                           <strong>{{ $aceptacionCliente[0]->name.' '.$aceptacionCliente[0]->ap_paterno }}</strong>
                        </div>
                        <div class="col-lg-2 text-left">  
                         
                        </div>
                        <div class="col-lg-4 text-left">  
                           
                        </div>
                  </div>
                  <div class="row">
                        <div class="col-lg-3">  
                            Efectuado el 
                        </div>
                        <div class="col-lg-2 text-left">  
                            <strong>{{ $aceptacionCliente[0]->fecha_aceptacion }}</strong>
                        </div>
                        <div class="col-lg-2 text-left">  
                         a horas
                        </div>
                        <div class="col-lg-4 text-left">  
                           <strong>{{ $aceptacionCliente[0]->hora_aceptacion }}</strong>
                        </div>
                  </div>
                  <br>
                  <div class="row">
                      <div class="col-lg-3">  
                          Comentarios
                      </div>
                      <div class="col-lg-9 text-left">  
                          <textarea class="form-control"  rows="4" disabled="">{{ $aceptacionCliente[0]->comentarios }}</textarea>
                      </div>
                  </div>
                  <br>
                  <br>
                  <br>
                  @include('jefe_sistemas.subir_adjunto3')
                  <div class="row">
                      <div class="col-lg-12 text-center"> 
                         <div class="my-2"></div>
                         <a id="ocultar_0" onclick="showDiv('ocultar', 0 )"  style="cursor:pointer;" class="btn btn-danger btn-icon-split" data-toggle="modal" >
                          <span class="icon text-white-50">
                            <i class="fas fa-exclamation-triangle"></i>
                          </span>
                          <span class="text">Cerrar</span>
                        </a>
                      </div>
                  </div>
              </div>  <!--end card-body -->
        </div> <!--fin aceptacion cliente --> 
        @endif
    </div>


<script type="text/javascript">
 

  function showDiv(mostrar, code){ 
      
      mmostrar = 'mostrar_'+code;
      oocultar = 'ocultar_'+code;
      ttarget = 'target_'+code;

      if(code == 0){
       
        @foreach($arraycodFase as $keyff => $valueff) 
                cod = {{$valueff}};
            $('#target_'+cod).hide(2000);
            $('#target_'+cod).hide("fast");
        @endforeach

      }else{  
              if(mostrar == 'ocultar'){   
               
                $('#target_'+code).hide(2000);
                $('#target_'+code).hide("fast");
              }
              else
              {      
              
                @foreach($arraycodFase as $keyff => $valueff) 
                        cod = {{$valueff}};

                        if(cod == code){
                           
                            @foreach($arraycodFase as $keyx => $valuex) 
                               cod1 = {{$valuex}}; 
                              
                                if(cod1 != code){ 
                                  if (document.getElementById('target_'+cod1)){  
                                        $('#target_'+cod1).hide(2000);
                                        $('#target_'+cod1).hide("fast");
                                   }
                               }
                            @endforeach
                             $('#target_'+code).show(2000);
                            $('#target_'+code).show("slow");
                        }else{
                              if (document.getElementById('mostrar'+cod)){  
                                  $('#target_'+cod).hide(2000);
                                  $('#target_'+cod).hide("fast");
                                }
                          }
                @endforeach

               
              }
    }//primer else
}
    

</script>


@endsection