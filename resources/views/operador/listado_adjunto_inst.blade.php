@foreach($arrayAdjTodos as $key => $adjCont)

@foreach($adjCont as $k => $v)

@if($k == $value1->id_requerimiento)  
 <div class="card shadow mb-4">
      <!-- Card Header - Accordion -->
      <a href="#collapseCardExample" class="d-block card-header py-3" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="collapseCardExample">
        <h6 class="m-0 font-weight-bold text-primary">Listado de Descargas por Fases</h6>
      </a> 
      <!-- Card Content - Collapse -->
      <!--<div class="collapse show" id="collapseCardExample">-->
      <div class="collapse" id="collapseCardExample">
        <div class="card-body">
         <div class="list-group">
            <br>  
            @foreach($v as $keydes => $descargas) 
              @if($descargas->id_etapa == 1 )
                 <h4 class="h6 mb-2 text-gray-800">Fase de Levantamiento del Requerimiento</h4>
              @endif
              @if($descargas->id_etapa == 4)
              <h4 class="h6 mb-2 text-gray-800">Fase de Solucion del Requerimiento</h4>
              @endif
              @if($descargas->id_etapa == 0)
              <h4 class="h6 mb-2 text-gray-800">Prueba Opr.s</h4>
              @endif
              @if($descargas->id_etapa == 5)
               <h4 class="h6 mb-2 text-gray-800">Fase de Certificacion Pre-Instalacion</h4>
              @endif
              @if($descargas->id_etapa == 7)
              <h4 class="h6 mb-2 text-gray-800">Instalaci&oacute;n.</h4>
              @endif
              @if($descargas->id_etapa == 8)
              <h4 class="h6 mb-2 text-gray-800">Certificación Online.</h4>
              @endif
               <div class="row">
                  <div class="col-lg-11 text-left"> 
                    <a href="{{ route('downloadFileOpe', $descargas->id_adjunto) }}"  class="list-group-item list-group-item-info">{{ $descargas->nombre }}
                    </a>
                    <br>
                  </div>
               
              </div>
            @endforeach
          </div>
        </div>
      </div>
</div>
@endif
@endforeach
@endforeach