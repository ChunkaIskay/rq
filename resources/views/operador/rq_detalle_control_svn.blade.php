@extends('layouts.app')

@section('content')
 <!-- Begin Page Content -->
<style>
#div1, #div2, #div3 {
  float: left;
  width: 280px;
  height: 450px;
  margin: 10px;
  padding: 10px;
  border: 1px solid black;

}
.imgBack {
 background-image: url( {{ asset('img/img_req.png') }}); 
  background-repeat: no-repeat;
  background-position: center;
 
  height: 89px;
  width:84px;
  background-color: #ccc;
  color: #000;
    text-align: center;
    margin-top: 10px;

}

.crono_wrapper {text-align:left;width:100%;}

hr {
  height: 1px;
  background-color: #adb0b9;
}

</style>
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
<h1 class="h3 mb-2 text-gray-800">{{ $pagTitulo }}</h1>
<p class="mb-4">Detalle del Requerimiento, control de versiones pendientes.</p>
<!-- progress bar -->

<!-- DataTales Example -->
<div class="card shadow mb-4">
	<div class="card-header py-3">
		<h6 class="m-0 font-weight-bold text-primary">Detalle</h6>
	</div>

	<div class="card-body">

	<div class="card shadow mb-4">
	<!-- Cantidad de horas trabajadas. -->
    @foreach($rqAsig as $key => $value1) 
    
    	@if($key == 0 )
	    	<!--{{ $idrequerimiento = $value1->id_requerimiento }}-->
    	@endif
    	@if($req_id == $value1->id_requerimiento)
    	<div class="card shadow mb-5" id="target_{{ $value1->id_requerimiento }}" style="display: block;">
    	@else
    	<div class="card shadow mb-5" id="target_{{ $value1->id_requerimiento }}" style="display: block;">
    	@endif
        <div class="card-header py-3">
          <h6 class="m-0 font-weight-bold text-primary">Requerimiento {{ $value1->id_requerimiento }}</h6>
        </div>
        
        <br>
		  <div class="card-body">
          <div class="row">
                <div class="col-lg-2">  
                   Id Requerimiento
                </div>
                <div class="col-lg-4 text-left">  
                   <strong>{{ $value1->id_requerimiento }}</strong>
                </div>
                <div class="col-lg-2">  
                    Accesible
                </div>
                <div class="col-lg-4 text-left">  
                   <strong>{{ $value1->accesible }}</strong>
                </div>
          </div>
          <div class="row">
                  <div class="col-lg-2">  
                     Tipo
                  </div>
                  <div class="col-lg-4 text-left">  
                     <strong>{{ $value1->tipo }}</strong>
                  </div>
                  <div class="col-lg-2">  
                      Prioridad
                  </div>
                  <div class="col-lg-4 text-left">  
                     <strong>{{ $value1->prioridad }}</strong>
                  </div>
            </div>
            <div class="row">
                  <div class="col-lg-2">  
                     Fecha Solicitud
                  </div>
                  <div class="col-lg-4 text-left">  
                     <strong>{{ $value1->fecha_solicitud }}</strong>
                  </div>
                  <div class="col-lg-2">  
                      Hora Solicitud
                  </div>
                  <div class="col-lg-4 text-left">  
                     <strong>{{ $value1->hora_solicitud }}</strong>
                  </div>
            </div>
            <div class="row">
                  <div class="col-lg-2">  
                     Solicitado Por
                  </div>
                  <div class="col-lg-4 text-left">  
                     <strong>{{ $value1->solicitado_por }}</strong>
                  </div>
                  <div class="col-lg-2">  
                     &nbsp;
                  </div>
                  <div class="col-lg-4 text-left">  
                     <strong></strong>
                  </div>
            </div>
            <br>
             <div class="row">
                  <div class="col-lg-3">  
                     Descripción
                  </div>
                  <div class="col-lg-9 text-left">  
                     <strong>{{ $value1->descripcion }}</strong>
                  </div>
            </div>
            <br>
            <div class="row">

                  <div class="col-lg-3">  
                    Resultado Deseado
                  </div>
                  <div class="col-lg-9 text-left">  
                     <strong>{{ $value1->resultado }}</strong>
                  </div>
            </div>
            <hr>
            <div class="row">
             	 <div class="col-lg-12 text-center">  
                    <h5>Datos de la Aprobación del Requerimiento</h5>
                  </div>	
                  <div class="col-lg-3">  
                    Fecha de Aprobación
                  </div>
                  <div class="col-lg-2 text-left">  
                     <strong>{{ $value1->fecha_aprobacion }}</strong>
                  </div>
                  <div class="col-lg-3">  
                    Hora de Aprobación
                  </div>
                  <div class="col-lg-2 text-left">  
                     <strong>{{ $value1->hora_aprobacion }}</strong>
                  </div>
            </div>
            <hr>
            <div class="row">
             	 <div class="col-lg-12 text-center">  
                    <h5>Datos de la Asignación del Requerimiento</h5>
                  </div>	
                  <div class="col-lg-3">
                    Fecha de Asignación
                  </div>
                  <div class="col-lg-2 text-left">  
                     <strong>{{ $value1->fecha_asignacion }}</strong>
                  </div>
                  <div class="col-lg-3">  
                    Hora de Asignación
                  </div>
                  <div class="col-lg-4 text-left">  
                     <strong>{{ $value1->hora_asignacion }}</strong>
                  </div>
                  <div class="col-lg-3">
                    Asignado por
                  </div>
                  <div class="col-lg-2 text-left">  
                     <strong>{{ $value1->asig_por }}</strong>
                  </div>
                  <div class="col-lg-3">  
                    Asignado a
                  </div>
                  <div class="col-lg-2 text-left">  
                     <strong>{{ $value1->asig_a }}</strong>
                  </div>
            </div>
            <hr>
            <div class="row">
               <div class="col-lg-12 text-center">  
                    <h5>Datos de la Solución al requerimiento</h5>
                  </div>  
                  <div class="col-lg-3">
                    Fecha de Solución
                  </div>
                  <div class="col-lg-2 text-left">  
                     <strong>{{ $value1->fecha_fin }}</strong>
                  </div>
                  <div class="col-lg-3">  
                    Hora de Solución
                  </div>
                  <div class="col-lg-4 text-left">  
                     <strong>{{ $value1->hora_fin }}</strong>
                  </div>
                  
                  <div class="col-lg-3">
                    Responsable de la Solución
                  </div>
                  <div class="col-lg-2 text-left">  
                     <strong>{{ $value1->asig_a }}</strong>
                  </div>
                  <div class="col-lg-3">  
                    Nro Secuencia
                  </div>
                  <div class="col-lg-4 text-left">  
                     <strong>{{ $value1->secuencia }}</strong>
                  </div>
                  <div class="col-lg-3">
                    Descripción de la Solución
                  </div>
                  <div class="col-lg-9 text-left">  
                     <strong>{{ $value1->descripcion }}</strong>
                  </div>
            </div>
            <hr>
            <div class="row">
               <div class="col-lg-12 text-center">  
                  <h5>Datos de Certificación Pre-instalación</h5>
                  </div>  
                  <div class="col-lg-3">
                    Fecha certificación
                  </div>
                  <div class="col-lg-2 text-left">  
                     <strong>{{ $value1->fecha_certificacion }}</strong>
                  </div>
                  <div class="col-lg-3">  
                    Hora certificación
                  </div>
                  <div class="col-lg-4 text-left">  
                     <strong>{{ $value1->hora_certificacion }}</strong>
                  </div>
                  
                  <div class="col-lg-3">
                    Responsable certificación
                  </div>
                  <div class="col-lg-9 text-left">  
                     <strong>{{ $value1->certificado_por }}</strong>
                  </div>
                  <div class="col-lg-4">  
                    Detalle de la certificación
                  </div>
                  <div class="col-lg-8 text-left">  
                     <strong>{{ $value1->detalle_certificacion }}</strong>
                  </div>
                  <div class="col-lg-4">
                    Descripción de la funcionalidades
                  </div>
                  <div class="col-lg-8 text-left">  
                     <strong>{{ $value1->detalle_funcionalidades }}</strong>
                  </div>
            </div>
            <hr>
            <div class="row">
               <div class="col-lg-12 text-center">  
                  <h5>Datos de la Asignación a Instalación</h5>
                  </div>  
                  <div class="col-lg-3">
                    Fecha de Asignación
                  </div>
                  <div class="col-lg-2 text-left">  
                     <strong>{{ $value1->fecha_certificacion }}</strong>
                  </div>
                  <div class="col-lg-3">  
                    Hora de Asignación
                  </div>
                  <div class="col-lg-4 text-left">  
                     <strong>{{ $value1->hora_certificacion }}</strong>
                  </div>
                  
                  <div class="col-lg-3">
                    Asignado Por
                  </div>
                  <div class="col-lg-2 text-left">  
                     <strong>{{ $value1->asig_por }}</strong>
                  </div>
                  <div class="col-lg-3">  
                    Asignado A
                  </div>
                  <div class="col-lg-3 text-left">  
                     <strong>{{ $value1->asig_a }}</strong>
                  </div>
                  
            </div>
            <hr>
            <div class="row">
               <div class="col-lg-12 text-center">  
                  <h5>Datos Instalación</h5>
                  </div>  
                  <div class="col-lg-3">
                    Fecha de Instalación
                  </div>
                  <div class="col-lg-2 text-left">  
                     <strong>{{ $value1->fecha_instal }}</strong>
                  </div>
                  <div class="col-lg-3">  
                    Hora de Instalación
                  </div>
                  <div class="col-lg-4 text-left">  
                     <strong>{{ $value1->hora_instal }}</strong>
                  </div>
                  
                  <div class="col-lg-3">
                    Responsble de la Instalación
                  </div>
                  <div class="col-lg-2 text-left">  
                     <strong>{{ $value1->asig_a }}</strong>
                  </div>
                  <div class="col-lg-3">  
                    Soporte de la Instalación
                  </div>
                  <div class="col-lg-3 text-left">  
                     <strong>{{ $value1->instal_por }}</strong>
                  </div>
            </div>
            <hr>
            <div class="row">
               <div class="col-lg-12 text-center">  
                  <h5>Datos de Certificación Online</h5>
                  </div>  
                  <div class="col-lg-3">
                    Fecha de certificación online
                  </div>
                  <div class="col-lg-2 text-left">  
                     <strong>{{ $value1->fecha_certificacion_online }}</strong>
                  </div>
                  <div class="col-lg-3">  
                    Hora de certificación online
                  </div>
                  <div class="col-lg-4 text-left">  
                     <strong>{{ $value1->hora_certificacion_online }}</strong>
                  </div>
                  
                  <div class="col-lg-3">
                    Certificado Online por
                  </div>
                  <div class="col-lg-2 text-left">  
                     <strong>{{ $value1->cert_online_por }}</strong>
                  </div>
                  <div class="col-lg-3">  
                    Conformidad
                  </div>
                  <div class="col-lg-3 text-left">  
                     <strong>{{ $value1->conformidad }}</strong>
                  </div>
            </div>
            <hr>
            <div class="row">
               <div class="col-lg-12 text-center">  
                  <h5>Control de Versión</h5>
                  </div>  
                  <div class="col-lg-3">
                    Fecha Control de Versión
                  </div>
                  <div class="col-lg-2 text-left">  
                     <strong>{{ $value1->fecha_subversion }}</strong>
                  </div>
                  <div class="col-lg-3">
                    Hora Control de versión
                  </div>
                  <div class="col-lg-2 text-left">  
                     <strong>{{ $value1->hora_subversion }}</strong>
                  </div>
                  <div class="col-lg-3">  
                    Instalado por
                  </div>
                  <div class="col-lg-2 text-left">  
                     <strong>{{ $value1->instal_por }}</strong>
                  </div>
                  <div class="col-lg-3">  
                    Comentario
                  </div>
                  <div class="col-lg-3 text-left">  
                     <strong>{{ $value1->comentarios }}</strong>
                  </div>
            </div>
            <hr>                  
            @include('operador.listado_adjunto_cert')
            <br>
            <br>
            <div class="row">
            <div class="col-lg-4 text-right">  
                <div class="my-2"></div>  
                <a href="#" class="btn btn-success btn-icon-split" data-toggle="modal" data-target="#textoModal_{{ $value1->id_requerimiento }}">
                  <span class="icon text-white-50">
                    <i class="fas fa-check"></i>
                  </span>
                  <span class="text">Certificar</span>
                </a>
              </div>
              <div class="col-lg-3 text-center">  
                <div class="my-2"></div>  
                <a href="#" class="btn btn-danger btn-icon-split" data-toggle="modal" data-target="#textoModalR_{{ $value1->id_requerimiento }}">
                  <span class="icon text-white-50">
                    <i class="fas fa-check"></i>
                  </span>
                  <span class="text">Rechazar</span>
                </a>
              </div>
              <div class="col-lg-4 text-left">  
                <div class="my-2"></div>
                  <a class="btn btn-primary" href="{{ route('revCertificacionSvn') }}">
                  Cerrar ventana
                  </a>
              </div>
            </div>
      </div>
</div> 
<!--fin aceptacion cliente --> 
<!-- Modal texto-->
  <div class="modal fade" id="textoModal_{{ $value1->id_requerimiento }}" tabindex="-1" aria-labelledby="modalLabel_{{ $value1->id_requerimiento }}" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalLabel_{{ $value1->id_requerimiento }}">Certificar control de versiones del requerimiento {{ $value1->id_requerimiento }}.</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">x</span>
          </button>

        </div>
        <div class="modal-body">
              <form id="logout-form1_{{ $value1->id_requerimiento }}" action="{{ route('guadarCertSvn', $value1->id_requerimiento ) }}" method="POST" >
                  {{ csrf_field() }}
                  <input type="hidden" name="id_reqqq" value="{{ $value1->id_requerimiento }}">

              	<div class="col-lg-12">
	                  Comentario 
				        </div>
	              <div class="col-lg-12 text-left">
	                  <textarea class="form-control" name="textDesc" rows="4" id="textDesc_{{ $value1->id_requerimiento }}" required pattern="[A-Za-z0-9]" placeholder="Escriba comentario por favor."></textarea>
	              </div>
              </form>
            </div>
            <div class="modal-footer">
	            <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
	              <a class="btn btn-primary" href=""
	                  onclick="event.preventDefault();
	                           document.getElementById('logout-form1_{{ $value1->id_requerimiento }}').submit();">
	                  Certificar Control de versiones
	              </a>
	        
        	</div>
      </div>
    </div>
  </div>
  <!-- Modal texto rechazar-->
  <div class="modal fade" id="textoModalR_{{ $value1->id_requerimiento }}" tabindex="-1" aria-labelledby="modalLabelR_{{ $value1->id_requerimiento }}" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalLabelR_{{ $value1->id_requerimiento }}">Certificar control de versiones del requerimiento {{ $value1->id_requerimiento }}.</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">x</span>
          </button>

        </div>
        <div class="modal-body">
              <form id="logout-form3_{{ $value1->id_requerimiento }}" action="{{ route('rechazarCertSvn', $value1->id_requerimiento ) }}" method="POST" >
                  {{ csrf_field() }}
                  <input type="hidden" name="id_reqqq" value="{{ $value1->id_requerimiento }}">

                <div class="col-lg-12">
                    Comentario 
                </div>
                <div class="col-lg-12 text-left">
                    <textarea class="form-control" name="textDesc" rows="4" id="textDesc_{{ $value1->id_requerimiento }}" required pattern="[A-Za-z0-9]" placeholder="Escriba comentario por favor."></textarea>
                </div>
              </form>
            </div>
            <div class="modal-footer">
              <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                <a class="btn btn-primary" href=""
                    onclick="event.preventDefault();
                             document.getElementById('logout-form3_{{ $value1->id_requerimiento }}').submit();">
                    Certificar Control de versiones
                </a>
          
          </div>
      </div>
    </div>
  </div>
@endforeach
<!-- Logout Modal validar requerimiento file-->
  <div class="modal fade" id="valiModal" tabindex="-1" aria-labelledby="exampleModalLabel1" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel1">Error !.</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">x</span>
          </button>
        </div>
        <div class="modal-body"><p id="modalText"></p></div>
        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-dismiss="modal">Aceptar</button>
          
            </div>
        </div>
      </div>
    </div>
  </div>
<!-- Modal delete modal-->
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
                    document.getElementById('logout-formD').submit();">
                    Eliminar Archivo
              </a>
              <div class="modal-body">
              <form id="logout-formD" action="{{ route('deleteFileCertO') }}" method="POST" style="display: none;">
                  {{ csrf_field() }}
                  <input type="hidden" name="id_requerimiento" value="{{ $value1->id_requerimiento }}">
                  <input type="hidden" name="nombreFuncion" value="{{ $nombreFuncion }}">
                  <input type="text" name="idAdjunto" id="idAdjunto" value=""/>
              </form>
            </div>
        </div>
      </div>
    </div>
  </div>
  </div>
    <div class="row">
      <div class="col-md-4"></div>
      <div class="col-md-4 text-left"></div>
      <div class="col-md-3"></div>
    </div> 
</div>
<!--	
	<script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>-->
<script type = "text/javascript">

         $.ajaxSetup({
             headers: {
                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
             }
         });

function allowDrop(ev) {
  ev.preventDefault();
}

function drag(ev) {
  ev.dataTransfer.setData("text", ev.target.id);
}

function drop(ev) {
 
  ev.preventDefault();
  var data1 = ev.dataTransfer.getData("text");

	$.ajax({
	    type:"POST",
	    url:"{{route('guadarReqAsig')}}",
	    data:{'name': data1},

	    dataType : 'html',
	    success: function(data){
	    	//render(selectedEvent, data);
	 
	        //La variable data toma su valor en tu controlador, tu se la puedes asignar. Mas adelante te muestro como la defino
	    },
	   error: function(xhr,status, response ){
 	        //Obtener el valor de los errores devueltos por el controlador
	        //var error = jQuery.parseJSON(xhr.responseText);

	        //Obtener los mensajes de error
//	        var info = error.message;
	        
	        //Crear la lista de errores
	    /*    var errorsHtml = '<ul>';
	           $.each(info, function (key,value) {
	                errorsHtml += '<li>' + value[0] + '</li>';
	            });
	           errorsHtml += '</ul>';*/
	        
	   }

	});

/*
  ev.preventDefault();
  var data = ev.dataTransfer.getData("text");
  alert(data);*/
  ev.target.appendChild(document.getElementById(data1));
  
  obj = document.getElementById('div2');
  numero = obj.getElementsByTagName('div').length;

}

</script>

<script>

	var inicio=0;
	var timeout=0;
	var horas_cal = 0;
	var minutzzos_cal = 0;
	var segundos_cal = 0;
	var horas_inicial = 0;
	var anioo = 0;
	var mess = 0;
	var diaa = 0;
	var id_rq = 0;
	var tiempo_i = 0;
	var timeout1 = 0;
	var id_reqq = 0
	var nom_fase = 'no_fase';


	function iniciarFinalizar(elemento,idReq)
	{     
		id_rq = idReq;
	    //  timeout = 'timeout_'+idReq;
	    //	alert(timeout);
		tiempo_i=document.getElementById("tiempo_i_"+idReq).value;
		var horaa=document.getElementsByTagName('h2').item(0);
		var tarea=document.getElementById("tarea_"+idReq).value;
			  	
	  	var time_dato = horaa.innerHTML;
	  	var time_split = time_dato.split(':');
        // alert(time_split[0]+'----'+time_split[1]+'----'+time_split[2]);
        horas_cal = time_split[0];
        minutos_cal = time_split[1];
        segundos_cal = time_split[2];

 		if(timeout==0)
		{ 
			if(tiempo_i==0){  //alert('388');
				mostrarDetalle(idReq,'insert',0);
				// INICIAR TAREA INSTALACION ONLINE el cronometro
	 			elemento.value="DETENER TAREA CERTIFICACION ONLINE";
	 			// Obtenemos el valor actual
				inicio=vuelta=new Date().getTime();
				funcionando();
			}else{  
				mostrarDetalle(idReq,'updateI',tiempo_i);
				elemento.value="INICIAR TAREA CERTIFICACION ONLINE";
				clearTimeout(timeout);
				timeout=0;
			}
		}else{     
			// detemer el cronometro
			mostrarDetalle(idReq,'update',0);
			
			elemento.value="INICIAR TAREA CERTIFICACION ONLINE";
			clearTimeout(timeout);
			timeout=0;
		}
	}
 
	function funcionando()
	{

		var actual = new Date().getTime();
		var actuall = new Date(actual);
		// obtenemos la diferencia entre la fecha actual y la de inicio
		var diff=new Date(actual-inicio);

		var result=LeadingZero(diff.getUTCHours())+":"+LeadingZero(diff.getUTCMinutes())+":"+LeadingZero(diff.getUTCSeconds());

		document.getElementById('crono1_'+id_rq).innerHTML = result;
 
		// Indicamos que se ejecute esta función nuevamente dentro de 1 segundo
		timeout=setTimeout("funcionando()",1000);
	}
 
	/* Funcion que pone un 0 delante de un valor si es necesario */
	function LeadingZero(Time) {
		return (Time < 10) ? "0" + Time : + Time;
	}

	//***** AJAX *****//
</script>
<script type="text/javascript">
 $.ajaxSetup({
             headers: {
                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
             }
         });


function showDiv(obj,mostrar, code){ 

	if(obj == 'del'){
		if(code == 0){

        @foreach($rqAsig as $keyff => $valuefff) 
            cod = {{$valuefff->id_requerimiento}};
            $('#target_'+cod).hide(2000);
            $('#target_'+cod).hide("fast");
        @endforeach

      }

	}else{	
			if(document.getElementById('mostrar_'+code).parentNode.nodeName=='SPAN'){
			
		      mostrarDetalle(code,'ver',0);
							
		      mmostrar = 'mostrar_'+code;
		      oocultar = 'ocultar_'+code;
		      ttarget = 'target_'+code;

		      if(code == 0){

			        @foreach($rqAsig as $keyff => $valuefff) 
			            cod = {{$valuefff->id_requerimiento}};
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
		                @foreach($rqAsig as $keyff => $valueff) 
		                        cod = {{$valueff->id_requerimiento}};

		                        if(cod == code){
		                           
		                            @foreach($rqAsig as $keyx => $valuex) 
		                               cod1 = {{$valuex->id_requerimiento}}; 
		                              
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
	}
}

function mostrarDetalle(idReq,accion,tiempo_i){

	var tiempoId=0;
	nom_fase=document.getElementById('nom_fase').value;

	if(accion=='ver'){
		tiempoId=document.getElementById('tiempo_i_'+idReq).value;	
  }
	if(accion=='update'){
		tiempoId=document.getElementById('tiempo_i_'+idReq).value;	
	}
	if(accion=='updateI'){
		tiempoId=document.getElementById('tiempo_i_'+idReq).value;
	}

	$.ajax({
		    type:"POST",
		    url:"{{route('revAsigMostrarCertOl')}}",
		    data:{'name': idReq,'accion':accion,'tiempo_id':tiempoId,'nom_fase':nom_fase},
	        dataType : 'html',
		    success: function(data){
		    	var para_hora = JSON.parse(data);
		    
		        $('#crono_'+idReq).html(para_hora.hora_calculada);
		            
		            var inputNombre = document.getElementById("tiempo_i_"+idReq);
    				inputNombre.value = para_hora.tiempo_id;
		        //$('#tiempo_i_'+idReq).html(para_hora.tiempo_id);
		        	
		    },
		   error: function(xhr,status, response ){
		        $('#crono_'+idReq).html('Ocurrio un error!');
		        
		   }
	});

}
    
function mouseDown(idReq,accion,tiempo_i) {
	
	if(document.getElementById('mostrar_'+idReq).parentNode.nodeName=='SPAN'){ 

		$.ajax({
					type: "POST",
			        url: "{{route('revValidarReq')}}",
			        data: {'name': idReq,'accion':accion,'tiempo_id':tiempo_i},
			        dataType : 'html',
			       
	    success: function(data){
	    	id_reqq = idReq; 
	    	//alert('success:'+id_reqq);
	    	//$('#valiModal').modal('show');
	    	//Llenar el formularios la descripción del requerimiento solu.
	    	var xx=document.getElementById('textDesc_'+idReq).value;
	    	if (xx.length == 0 ) {
	    	  		$('#textoModal_'+idReq).modal('show');
	    	}	

	    	
	    },

	   error: function(data ){
	   
			var resText = data.status.toString();
			var status = JSON.stringify(resText);
			
			if(resText == 422 ){ 
				 $('#modalText').html('Error: El requerimiento esta en desarrollo, para terminar la tarea presione el boton DETENER TAREA CERTIFICACION ONLINE!');
			}

			if(resText == 421){  
				 $('#modalText').html('Error: Requerimiento no tiene horas trabajadas!');

			}

			if(resText == 420){  
				 $('#modalText').html('Error: Por favor suba la documentación que corresponde al requerimiento!');
			}

	   		$('#valiModal').modal('show');  

		}

	   });
	
	} 	//alert('mouseDown'); //document.getElementById("demo").innerHTML = "The mouse button is held down.";
}

function mouseUp() { 
	//console.log(' mouseUp:');
}

function iniSolucion(){
	//console.log(' id req actual:'+id_reqq);
}


function darSolucion(){
	
if(id_reqq != 0){
	if(document.getElementById('mostrar_'+id_reqq).parentNode.nodeName=='DIV'){
		  
		 var texto_desc=document.getElementById('textDesc_'+id_reqq).value;
		 /*var texto_cliente=document.getElementById('textClient_'+id_reqq).value;
		 var texto_servidores=document.getElementById('textServ_'+id_reqq).value;
		 var texto_tabla=document.getElementById('textModi_'+id_reqq).value;*/
		 var texto_cliente='';
		 var texto_servidores='';
		 var texto_tabla='';

		$.ajax({
			type: "POST",
	        url: "{{route('revSolTarea')}}",
	        data: {'idRq': id_reqq,'accion':'insert','tiempo_id':'','texto_desc':texto_desc,'texto_cliente':texto_cliente,'texto_servidores':texto_servidores,'texto_tabla':texto_tabla},
	        dataType : 'html',
	       
		    success: function(data){
		    	
			    var elem = document.getElementById('mostrar_'+id_reqq);
				elem.setAttribute('draggable', false);	
				id_reqq = 0;
		    },

		    error: function(data ){
		   
				var resText = data.status.toString();
				var status = JSON.stringify(resText);
				
				if(resText == 422 ){ 
					 $('#modalText').html('Error: El requerimiento esta en desarrollo, para terminar la tarea presione el boton DETENER TAREA CERTIFICACION ONLINE!');
				}

				if(resText == 421){  
					 $('#modalText').html('Error: Requerimiento no tiene horas trabajadas!');

				}

				if(resText == 420){  
					 $('#modalText').html('Error: Por favor suba la documentación que corresponde al requerimiento!');
				}

				$('#valiModal').modal('show');  

		   }

   		});

	  }
    }
 
}

mostrarDetalle({{ $idrequerimiento }},'ver',0);
</script>
@endsection