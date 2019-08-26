 <div class="card shadow mb-4">
              <!-- Card Header - Accordion -->
    <a href="#onlineid" class="d-block card-header py-3" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="onlineid">
      <h6 class="m-0 font-weight-bold text-primary">Descargas</h6>
    </a> 
    <!-- Card Content - Collapse -->
    <!--<div class="collapse show" id="onlineid">-->
    
    <div class="collapse" id="onlineid">
      <div class="card-body">
        <div class="list-group">
          <br>  

            @if(!empty($adjuntosCeOnLine))
              @foreach($adjuntosCeOnLine as $keyce => $valuece) 
               <div class="row">
                  <div class="col-lg-11 text-left"> 
                    <a href="{{ route('downloadFileSeg', $valuece->id_adjunto)  }}?ide={{ $valuece->id_etapa }}"  class="list-group-item list-group-item-info">{{ $valuece->nombre }}
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