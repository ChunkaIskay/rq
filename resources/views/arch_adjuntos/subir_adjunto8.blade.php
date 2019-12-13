 <div class="card shadow mb-4">
    <!-- Card Header - Accordion -->
    <a href="#instalid" class="d-block card-header py-3" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="instalid">
      <h6 class="m-0 font-weight-bold text-primary">Descargas</h6>
    </a> 
    <!-- Card Content - Collapse -->
    <!--<div class="collapse show" id="instalid">-->
    <div class="collapse" id="instalid">
      <div class="card-body">
        <div class="list-group">
          <br>  
            @if(!empty($adjuntosInsta))
              @foreach($adjuntosInsta as $keyin => $valuein) 
               <div class="row">
                  <div class="col-lg-11 text-left"> 
                    <a href="{{ route('downloadFileSeg', $valuein->id_adjunto)  }}?ide={{ $valuein->id_etapa }}"  class="list-group-item list-group-item-info">{{ $valuein->nombre }}
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