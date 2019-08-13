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
  <h1 class="h3 mb-2 text-gray-800">Seguimiento al requerimiento {{ $detalle[0]->id_requerimiento }} </h1>
  <p class="mb-4">Diagrama de fases del requermiento. </p> 


  <div class="row">
          @foreach($arratFases as $keyf => $valuef) 
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                  <div class="card-body">
                    <div class="row no-gutters align-items-center">
                      <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1"> <a id="mostrar_{{ $valuef['id_fase'] }}" onclick="showDiv('mostrar',{{ $valuef['id_fase'] }} )"  style="cursor:pointer;">{{ $valuef['nom_fase'] }}</a>
                        <a id="ocultar_{{ $valuef['id_fase'] }}" onclick="showDiv('ocultar',{{ $valuef['id_fase'] }} )"  style="cursor:pointer; display: none;">{{ $valuef['nom_fase'] }}</a>
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $valuef['estado'] }}</div>
                      </div>
                      <div class="col-auto">
                        <i class="fa-li fa fa-check-square fa-2x text-gray-300"></i>
                       <!--<i class="fas fa-calendar fa-2x text-gray-300"></i>-->
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              @if($keyf != 'cert_online')
                &rArr;
              @endif   
          @endforeach()
  </div>
  <!-- DataTales Example -->
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
                  @include('jefe_operaciones.subir_adjunto3')
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
        </div> <!--card shadow mb-4 --> 
        
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
                document.getElementById(mmostrar).style.display = 'block';
                document.getElementById(oocultar).style.display = 'none';
                
                $('#target_'+code).hide(2000);
                $('#target_'+code).hide("fast");

                
              }
              else
              { 
                document.getElementById(oocultar).style.display = 'block';
                document.getElementById(mmostrar).style.display = 'none';

                @foreach($arraycodFase as $keyff => $valueff) 
                        cod = {{$valueff}};

                        if(cod == code){
                            document.getElementById('ocultar_'+code).style.display = 'block';
                            document.getElementById('mostrar_'+code).style.display = 'none';

                            $('#target_'+code).show(2000);
                            $('#target_'+code).show("slow");
                        }else{ 
                                document.getElementById('ocultar_'+cod).style.display = 'block';
                                document.getElementById('mostrar_'+cod).style.display = 'none';

                                $('#target_'+cod).hide(2000);
                                $('#target_'+cod).hide("fast");
                          }
                @endforeach

               
              }
    }//primer else
}
    
 
 
</script>


@endsection