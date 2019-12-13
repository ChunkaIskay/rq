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

            @if(!empty($adjuntos))
              @foreach($adjuntos as $key => $value) 
               <div class="row">
                  <div class="col-lg-11 text-left"> 
                    <a href="{{ route('downloadFileSeg', $value->id_adjunto)  }}?ide={{ $value->id_etapa }}"  class="list-group-item list-group-item-info">{{ $value->nombre }}
                    </a>
                    <br>
                  </div>
               </div>
              @endforeach
            @endif
             
        </div>
      </div>
    </div>
</div>