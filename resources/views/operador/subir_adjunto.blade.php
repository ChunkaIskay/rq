
@foreach($arrayAdjuntos as $key => $adjCont)

@foreach($adjCont as $k => $v)

@if($k == $value1->id_requerimiento)  
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
          <form  class="md-form" action="{{ url('/Desarrollador/subir-archivo') }}" method="post" enctype="multipart/form-data">
            {{ csrf_field() }}
            <div class="row" style="margin-left: 10px; margin-right: 10px;">      <!--<div class="col-lg-1 text-left"> 
                      <a href="#" type="submit" class="btn btn-success btn-circle btn-sm">
                          <i class="fas fa-upload"></i></a>
                </div>-->
                <input type="hidden" name="idrq" value="{{ $value1->id_requerimiento }}">
                 @if(empty($adjuntos[0]->id_etapa))
                <input type="hidden" name="etapa" value="4">
                 @else
                <input type="hidden" name="etapa" value="{{ $adjuarrayAdjuntosntos[$value1->id_requerimiento]->id_etapa }}">
                 @endif
                <input type="hidden" name="nombreFuncion" value="{{ $nombreFuncion }}">
              <div class="col-lg-8 text-left"> 
                    <input class="boton-buscar" type="file" name="rqdoc">
              </div>
              <div class="col-lg-4  text-left"> 
                <input class="boton-subir" type="submit" id="btn_agregar" value="Subir Archivo">  
              </div>
            </div>
          </form>
          <br>
          @foreach($v as $keyc => $valuec)
          
            @if(!empty($valuec->id_adjunto))
             <div class="row">
                <div class="col-lg-11 text-left"> 
                  <a href="{{ route('downFileAprob', $valuec->id_adjunto) }}"  class="list-group-item list-group-item-info">{{ $valuec->nombre }}
                  </a>
                  <br>
                </div>
                <div class="col-lg-1 text-left"> 
                    <a data-toggle="modal" data-id="{{ $valuec->id_adjunto }}"class="iddelete btn btn-danger btn-circle btn-sm" href="#deletemodal"><i class="fas fa-trash"></i></a>
                </div>
              </div>
            @endif  
          @endforeach
        </div>
      </div>
    </div>
</div>

@endif
@endforeach
@endforeach