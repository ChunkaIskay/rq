<!-- LIST -->
<style>
#div1, #div2 {
  float: left;
  width: 250px;
  height: 350px;
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

.crono_wrapper {text-align:center;width:200px;}

</style>
	<br>
	<div class="card shadow mb-4">
	  <!-- Card Header - Accordion -->
	  <a href="#asig" class="d-block card-header py-3" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="asig">
	    <h6 class="m-0 font-weight-bold text-primary">Lista de requerimiento asignados a desarrollo</h6>
	  </a> 
	  <!-- Card Content - Collapse -->
	  <!--<div class="collapse show" id="asig">-->

	  <div class="collapse show" id="asig">
	    <div class="card-body">
	    <div class="row text-left">
		  <div class="col-4 text-leeft"> <label>Asignación</label></div>
		  <div class="col-4 text-leeft"> <label>Desarrollo</label></div>
		  <div class="col-4 text-leeft"> <label>Prueba</label></div>
		</div>
		
		<div class="table-responsive" id="div1" ondrop="drop(event)" ondragover="allowDrop(event)">	        
        @if(!empty($rqAsignados))	
        @foreach($rqAsignados as $key => $value) 
	       <!--
		<input type="text" name="" value="{{ $value->id_requerimiento }}" onclick="datos();" class="d-block imgBack"  role="button" id='drag_{{ $value->id_requerimiento }}' ondragstart="drag(event)" draggable="true" placeholder="{{ $value->id_requerimiento }}"  >-->

		<a  onclick="showDiv(this,'mostrar',{{ $value->id_requerimiento }} );" class="d-block imgBack"  role="button" id='mostrar_{{ $value->id_requerimiento }}' ondragstart="drag(event)" draggable="true" placeholder="{{ $value->id_requerimiento }}" style="cursor:pointer;" ><br><h4>{{ $value->id_requerimiento }}</h4></a>
		
        @endforeach
        @endif
	 	</div>
	 	<form  class="md-form" id="logout-form2" action="{{ url('/Desarrollador/rev-req-guardar-asig') }}" method="post" enctype="multipart/form-data">
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
	 	<span class="table-responsive"  id="div2" ondrop="drop(event)" ondragover="allowDrop(event)"></span>

	    </div>
	</form>
	  </div>
	</div>
	<br> 

	<!--<a id="mostrar_1" onclick="showDiv('mostrar',1 )"  style="cursor:pointer;">datosss</a>-->

        <!-- Cantidad de horas trabajadas. -->
        @foreach($rqAsignados as $key => $value1) 
        <div class="card shadow mb-5" id="target_{{ $value1->id_requerimiento }}" style="display: none;">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Requerimiento {{ $value1->id_requerimiento }}</h6>
            </div>
            <div class="card-body">
                  
                <h1>Cantidad de horas trabajadas hasta ahora</h1>
				<div class="crono_wrapper">
				<h2 id='crono'></h2>
				
				<input type="button" value="Empezar" onclick="empezarDetener(this);">
				
				</div>
	            <br>
		          <div class="row">
		              <div class="col-lg-3">  
		                  Comentarios
		              </div>
		              <div class="col-lg-9 text-left">  
		                  <textarea class="form-control"  rows="4" disabled="" placeholder="Escriba el comentarios en detalle por favor!"></textarea>
		              </div>
		          </div>
	              <br>
	              <br>
	              <br>
                  <div class="row">
                      <div class="col-lg-12 text-center"> 
                         <div class="my-2"></div>

                         <a id="ocultar_0" onclick="showDiv('del','ocultar', 0 )"  style="cursor:pointer;" class="btn btn-danger btn-icon-split" data-toggle="modal" >
                          <span class="icon text-white-50">
                            <i class="fas fa-exclamation-triangle"></i>
                          </span>
                          <span class="text">Cerrar</span>
                        </a>
                      </div>
                  </div>
              </div>  <!--end card-body -->

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
                     <strong></strong>
                  </div>
                  <div class="col-lg-2">  
                      Prioridad
                  </div>
                  <div class="col-lg-4 text-left">  
                     <strong></strong>
                  </div>
            </div>
            <div class="row">
                  <div class="col-lg-2">  
                     Fecha Solicitud
                  </div>
                  <div class="col-lg-4 text-left">  
                     <strong></strong>
                  </div>
                  <div class="col-lg-2">  
                      Hora Solicitud
                  </div>
                  <div class="col-lg-4 text-left">  
                     <strong></strong>
                  </div>
            </div>
            <div class="row">
                  <div class="col-lg-2">  
                     Solicitado Por
                  </div>
                  <div class="col-lg-4 text-left">  
                     <strong></strong>
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
                  <div class="col-lg-3">  
                     Resultado Deseado
                  </div>
                  <div class="col-lg-9 text-left">  
                     <strong></strong>
                  </div>
            </div>
            <br>
            <div class="row">

                  <div class="col-lg-3">  
                    Descripción
                  </div>
                  <div class="col-lg-9 text-left">  
                     <strong></strong>
                  </div>
            </div>
            <br>
            <br>
            <!-----@i@i@i@@@------>
            <div class="row">
              <div class="col-lg-6 text-right">  
                <div class="my-2"></div>
                  <a class="btn btn-primary" href="{{ route('examinarList') }}">
                  Volver
                  </a>
              </div>
            </div>
      </div>


</div> <!--fin aceptacion cliente --> 
@endforeach
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
	 //  data: $("#drag_{{ $value->id_requerimiento }}").serialize(),
	    dataType : 'html',
	    success: function(data){
	    	//render(selectedEvent, data);
	    	alert(data);
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
	        alert(response);
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
    var minutos_cal = 0;
    var segundos_cal = 0;
    var horas_inicial = 0;
 
	function empezarDetener(elemento)
	{     
		var horaa=document.getElementsByTagName('h2').item(0);
	  	//alert(horaa.innerHTML);
	  	var time_dato = horaa.innerHTML;
	  	var time_split = time_dato.split(':');
   
       // alert(time_split[0]+'----'+time_split[1]+'----'+time_split[2]);
        horas_cal = time_split[0];
        minutos_cal = time_split[1];
        segundos_cal = time_split[2];

 		if(timeout==0)
		{   
		//	alert('1');
			// empezar el cronometro
 			elemento.value="Detener";
 			// Obtenemos el valor actual
			inicio=vuelta=new Date().getTime();
			
			var hora_ini = new Date(inicio);
			var fecha_actual = new Date(inicio);

		alert('sss'+fecha_actual);

			 horas_fun = parseInt(LeadingZero(hora_ini.getUTCHours())) + parseInt(horas_cal);
			 minutos_fun = parseInt(LeadingZero(hora_ini.getUTCMinutes())) + parseInt(minutos_cal);
			 segundos_fun = parseInt(LeadingZero(hora_ini.getUTCSeconds())) + parseInt(segundos_cal);

console.log(fecha_actual.getUTCFullYear());
console.log(fecha_actual.getUTCMonth());
console.log(fecha_actual.getUTCDay());

				var anioo = fecha_actual.getUTCFullYear();
				var mess = fecha_actual.getUTCMonth();
				var diaa = fecha_actual.getUTCDay();
alert('mes:'+mess);
			horas_inicial = new Date(anioo, mess, diaa, horas_fun, minutos_fun, segundos_fun);
alert(horas_inicial);
			// iniciamos el proceso

			funcionando();
		}else{ 
		//	alert('2');
			// detemer el cronometro
			elemento.value="Empezar";
			clearTimeout(timeout);
			timeout=0;
		}
	}
 
	function funcionando()
	{
		// obteneos la fecha actual
		var actual = new Date().getTime();
		//var fecha_actual = new Date();
		
		//alert(' dfdfdf'+actual);

		// obtenemos la diferencia entre la fecha actual y la de inicio
//		var diff=new Date(actual-inicio);

		
 		
 	//	alert("funcionando"+diff.getUTCMinutes());
		// mostramos la diferencia entre la fecha actual y la inicial
 /*
		var horas_fun = parseInt(LeadingZero(diff.getUTCHours())) + parseInt(horas_cal);
		var minutos_fun = parseInt(LeadingZero(diff.getUTCMinutes())) + parseInt(minutos_cal);
		var segundos_fun = parseInt(LeadingZero(diff.getUTCSeconds())) + parseInt(segundos_cal);

		var anioo = fecha_actual.getUTCFullYear();
		var mess = fecha_actual.getUTCMonth();
		var diaa = fecha_actual.getUTCDay();*/

       //	var horas_inicial = new Date(anioo, mess, diaa, horas_fun, minutos_fun, segundos_fun);
		var diff = new Date(actual-horas_inicial)
		//alert(LeadingZero(diff.getUTCSeconds()) + segundos_cal);
		//console.log(horas_actual);

		var result=LeadingZero(diff.getUTCHours())+":"+LeadingZero(diff.getUTCHours())+":"+LeadingZero(diff.getUTCSeconds());
	//	alert(result);
		document.getElementById('crono').innerHTML = result;
 
		// Indicamos que se ejecute esta función nuevamente dentro de 1 segundo
		timeout=setTimeout("funcionando()",1000);
	}
 
	/* Funcion que pone un 0 delante de un valor si es necesario */
	function LeadingZero(Time) {
		return (Time < 10) ? "0" + Time : + Time;
	}


	function mostrarDetalle(){
		alert('mostrar datos');
	}
</script>



<script type="text/javascript">
 

function showDiv(obj,mostrar, code){ 
	
	if(obj == 'del'){
		if(code == 0){

        @foreach($rqAsignados as $keyff => $valuefff) 
            cod = {{$valuefff->id_requerimiento}};
            $('#target_'+cod).hide(2000);
            $('#target_'+cod).hide("fast");
        @endforeach

      }

	}	

	if(document.getElementById('mostrar_'+code).parentNode.nodeName=='SPAN'){

      mostrarDetalle(code);
					
      mmostrar = 'mostrar_'+code;
      oocultar = 'ocultar_'+code;
      ttarget = 'target_'+code;

      if(code == 0){

        @foreach($rqAsignados as $keyff => $valuefff) 
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
                @foreach($rqAsignados as $keyff => $valueff) 
                        cod = {{$valueff->id_requerimiento}};

                        if(cod == code){
                           
                            @foreach($rqAsignados as $keyx => $valuex) 
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

function mostrarDetalle(idReq){

	$.ajax({
		    type:"POST",
		    url:"{{route('revAsigMostrarDet')}}",
		    data:{'name': idReq},
		 //  data: $("#drag_{{ $value->id_requerimiento }}").serialize(),
		    dataType : 'html',
		    success: function(data){
		    	var para_hora = JSON.parse(data);
		        $('#crono').html(para_hora.hora_calculada);
		    
		    },
		   error: function(xhr,status, response ){
		        $('#crono').html('Ocurrio un error!');
		        
		   }
	});

}
    
</script>