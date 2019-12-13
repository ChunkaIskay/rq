<!-- LIST -->
	<br>
	<div class="card shadow mb-4">
	  <!-- Card Header - Accordion -->
	  <a href="#pruebaa" class="d-block card-header py-3" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="pruebaa">
	    <h6 class="m-0 font-weight-bold text-primary">Lista de requerimiento de pruebas</h6>
	  </a> 
	  <!-- Card Content - Collapse -->
	  <!--<div class="collapse show" id="pruebaa">-->
	  <div class="collapse show" id="pruebaa">
	    <div class="card-body">
			<div class="table-responsive">
		        <table class="table table-bordered"  class="display" id="dataTablePrue" width="100%" cellspacing="0">
		            <thead>
		                <tr>
		                    <th class="text-center">Id Requerimiento</th>
		                    <th class="text-left">Fecha de Inicio</th>
		                    <th class="text-left">Hora de Inicio</th>
		                    <th class="text-left">Fecha de Culminación</th>
		                    <th class="text-left">Hora de Culminación</th>
		                    
		                </tr>
		            </thead>
		        <tbody>
		        @if(!empty($rqPruebas))	
		        @foreach($rqPruebas as $key => $value) 
		          <tr class="table">
						<td class="text-center">{{ $value->id_requerimiento }}</td>
						<td>{{ $value->fecha_fin }}</td>
						<td>{{ $value->hora_fin }}</td>
						<td>Pendiente</td>
						<td>Pendiente</td>
		              	
		          </tr> 
		        @endforeach
		        @endif
		        </tbody>
		    	</table>
		 	</div>
	    </div>
	  </div>
	</div>
	<br> 
	<!-- historico -->
	<div class="card shadow mb-4">
	  <!-- Card Header - Accordion -->
	  <a href="#pruebaHisto" class="d-block card-header py-3" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="pruebaHisto">
	    <h6 class="m-0 font-weight-bold text-primary">Historico de requerimiento de pruebas</h6>
	  </a> 
	  <!-- Card Content - Collapse -->
	  <!--<div class="collapse show" id="pruebaHisto">-->
	  
	 	<div class="collapse" id="pruebaHisto">
		    <div class="card-body">
			    <div class="table-responsive">
				    <table class="table table-bordered"  class="display" id="dataTablePrueH" width="100%" cellspacing="0">
				        <thead>
				            <tr>
				                <th class="text-center">Id Requerimiento</th>
				                <th class="text-left">Fecha de Inicio</th>
			                    <th class="text-left">Hora de Inicio</th>
			                    <th class="text-left">Fecha de Culminación</th>
			                    <th class="text-left">Hora de Culminación</th>
				                
				            </tr>
				        </thead>
				    <tbody>
				    @if(!empty($rqPruebasHisto))	
				    @foreach($rqPruebasHisto as $key => $value) 
				      <tr class="table">
							<td class="text-center">{{ $value->id_requerimiento }}</td>
							<td>{{ $value->fecha_fin }}</td>
							<td>{{ $value->hora_fin }}</td>
							<td>{{ $value->fecha_certificacion }}</td>
							<td>{{ $value->hora_certificacion }}</td>
				          	
				      </tr> 
				    @endforeach
				    @endif
				    </tbody>
				    </table>
			    </div>
			</div>
		</div>
	</div>
	<!-- END LIST -->
  	