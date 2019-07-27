 <div class="card shadow mb-4">
      <!-- Card Header - Accordion -->
      <a href="#collapseCardExample" class="d-block card-header py-3" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="collapseCardExample">
        <h6 class="m-0 font-weight-bold text-primary">Descargas</h6>
      </a> 
      <!-- Card Content - Collapse -->
      <!--<div class="collapse show" id="collapseCardExample">-->
      <div class="collapse" id="collapseCardExample">
        <div class="card-body">
         <div class="list-group">
            <br>  
            @foreach($adjuntos as $key => $value) 
              @if($value->id_etapa == 1 )
                 <h4 class="h6 mb-2 text-gray-800">Fase de Levantamiento del Requerimiento</h4>
              @endif
              @if($value->id_etapa == 4)
              <h4 class="h6 mb-2 text-gray-800">Fase de Solucion del Requerimiento</h4>
              @endif
              @if($value->id_etapa == 0)
              <h4 class="h6 mb-2 text-gray-800">Prueba Opr.s</h4>
              @endif
              @if($value->id_etapa == 5)
               <h4 class="h6 mb-2 text-gray-800">Fase de Certificacion Pre-Instalacion</h4>
              @endif
              @if($value->id_etapa == 7)
              <h4 class="h6 mb-2 text-gray-800">Instalaci&oacute;n por Des.</h4>
              @endif
               <div class="row">
                  <div class="col-lg-11 text-left"> 
                    <a href="{{ route('downloadFile', $value->id_adjunto) }}"  class="list-group-item list-group-item-info">{{ $value->nombre }}
                    </a>
                    <br>
                  </div>
                <!--  <div class="col-lg-1 text-left"> 
                      <a data-toggle="modal" data-id="{{ $value->id_adjunto }}"class="iddelete btn btn-danger btn-circle btn-sm" href="#deletemodal"><i class="fas fa-trash"></i></a>

                  </div>-->
              </div>
            @endforeach
          </div>
        </div>
      </div>
</div>