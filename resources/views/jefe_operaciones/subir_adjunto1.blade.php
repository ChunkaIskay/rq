 <div class="card shadow mb-4">
              <!-- Card Header - Accordion -->
              <a href="#adjunto1" class="d-block card-header py-3" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="adjunto1">
                <h6 class="m-0 font-weight-bold text-primary">Descargas</h6>
              </a> 
              <!-- Card Content - Collapse -->
              <!--<div class="collapse show" id="adjunto1">-->
              
              <div class="collapse" id="adjunto1">
                <div class="card-body">
                 <div class="list-group">
                    <form  class="md-form" action="{{ url('/JefeOperaciones/subir-archivo') }}" method="post" enctype="multipart/form-data">
                      {{ csrf_field() }}
                      <div class="row" style="margin-left: 10px; margin-right: 10px;">      <!--<div class="col-lg-1 text-left"> 
                                <a href="#" type="submit" class="btn btn-success btn-circle btn-sm">
                                    <i class="fas fa-upload"></i></a>
                          </div>-->
                          <input type="hidden" name="idrq" value="{{ $adjuntoSol[0]->id_requerimiento }}">
                          <input type="hidden" name="etapa" value="{{ $adjuntoSol[0]->id_etapa }}">
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
                    @foreach($adjuntoSol as $key => $vSol) 
                     <div class="row">
                        <div class="col-lg-11 text-left"> 
                          <a href="{{ route('downloadFile', $vSol->id_adjunto) }}"  class="list-group-item list-group-item-info">{{ $vSol->nombre }}
                          </a>
                          <br>
                        </div>
                        <div class="col-lg-1 text-left"> 
                            <a data-toggle="modal" data-id="{{ $vSol->id_adjunto }}"class="iddelete btn btn-danger btn-circle btn-sm" href="#deletemodal"><i class="fas fa-trash"></i></a>

                        </div>
                      </div>
                    @endforeach
                  </div>
                </div>
              </div>
            </div>