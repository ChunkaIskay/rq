<!-- LIST -->
	<br>
	<div class="card shadow mb-4">
	  <!-- Card Header - Accordion -->
	  <a href="#aprobRq" class="d-block card-header py-3" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="aprobRq">
	    <h6 class="m-0 font-weight-bold text-primary">Requerimientos Aprobados</h6>
	  </a> 
	  <!-- Card Content - Collapse -->
	  <!--<div class="collapse show" id="aprobRq">-->
	  
	  <div class="collapse show" id="aprobRq">
	    <div class="card-body">
	   
	       
		<div class="table-responsive">
	        <table class="table table-bordered"  class="display" id="dataTable" width="100%" cellspacing="0">
	            <thead>
	                <tr>
	                    <th class="text-center">Id Requerimiento</th>
	                    <th class="text-left">Fecha Solicitud</th>
	                    <th class="text-left">Hora Solicitud</th>
	                    <th class="text-left">Fecha Aprobación</th>
	                    <th class="text-left">Hora Aprobación</th>
	                   
	                </tr>
	            </thead>
	        <tbody>
	        @if(!empty($rqAprobadosRecien))	
	        @foreach($rqAprobadosRecien as $key => $value) 
	          <tr class="table">
					<td class="text-center">{{ $value->id_requerimiento }}</td>
					<td>{{ $value->fecha_solicitud }}</td>
					<td>{{ $value->hora_solicitud }}</td>
					<td>{{ $value->fecha_aprobacion }}</td>
					<td>{{ $value->hora_aprobacion }}</td>
	              	
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
	  <a href="#aprobHist" class="d-block card-header py-3" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="aprobHist">
	    <h6 class="m-0 font-weight-bold text-primary">Requerimientos Aprobados Historico</h6>
	  </a> 
	  <!-- Card Content - Collapse -->
	  <!--<div class="collapse show" id="aprobHist">-->
	  
	  <div class="collapse" id="aprobHist">
	    <div class="card-body">
	    
	       
	    <div class="table-responsive">
	    <table class="table table-bordered"  class="display" id="dataTableHist" width="100%" cellspacing="0">
	        <thead>
	            <tr>
	                <th class="text-center">Id Requerimiento</th>
	                <th class="text-left">Fecha Solicitud</th>
	                <th class="text-left">Hora Solicitud</th>
	                <th class="text-left">Fecha Aprobación</th>
	                <th class="text-left">Hora Aprobación</th>
	               
	            </tr>
	        </thead>
	    <tbody>
	    @if(!empty($rqAprobadosHisto))	
	    @foreach($rqAprobadosHisto as $key => $value) 
	      <tr class="table">
				<td class="text-center">{{ $value->id_requerimiento }}</td>
				<td>{{ $value->fecha_solicitud }}</td>
				<td>{{ $value->hora_solicitud }}</td>
				<td>{{ $value->fecha_aprobacion }}</td>
				<td>{{ $value->hora_aprobacion }}</td>
	          	
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