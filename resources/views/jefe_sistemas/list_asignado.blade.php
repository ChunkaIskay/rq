<!-- LIST -->
	<br>
	<div class="card shadow mb-4">
	  <!-- Card Header - Accordion -->
	  <a href="#asig" class="d-block card-header py-3" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="asig">
	    <h6 class="m-0 font-weight-bold text-primary">Lista de requerimiento asignados a desarrollo</h6>
	  </a> 
	  <!-- Card Content - Collapse -->
	  <!--<div class="collapse show" id="asig">-->
	  <div class="collapse show" id="asig">
	    <div class="card-body">
		<div class="table-responsive">
	        <table class="table table-bordered"  class="display" id="dataTableAsig" width="100%" cellspacing="0">
	            <thead>
	                <tr>
	                    <th class="text-center">Nro Asignación</th>
	                    <th class="text-left">Fecha Asignación</th>
	                    <th class="text-left">Hora Asignación</th>
	                    <th class="text-left">Fecha Aprobación</th>
	                    <th class="text-left">Hora Aprobación</th>
	                 
	                </tr>
	            </thead>
	        <tbody>
	        @if(!empty($rqAsignados))	
	        @foreach($rqAsignados as $key => $value) 
	          <tr class="table">
					<td class="text-center">{{ $value->Nro_asignacion }}</td>
					<td>{{ $value->fecha_asignacion }}</td>
					<td>{{ $value->hora_asignacion }}</td>
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
	  <a href="#asigHist" class="d-block card-header py-3" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="asigHist">
	    <h6 class="m-0 font-weight-bold text-primary">Historico de requerimiento asignados</h6>
	  </a> 
	  <!-- Card Content - Collapse -->
	  <!--<div class="collapse show" id="asigHist">-->
	  
	  <div class="collapse" id="asigHist">
	    <div class="card-body">
	    
	       
	    <div class="table-responsive">
	    <table class="table table-bordered"  class="display" id="dataTableAsigHist" width="100%" cellspacing="0">
	        <thead>
	            <tr>
	                <th class="text-center">Id Requerimiento</th>
	                <th class="text-left">Fecha Asignación</th>
	                <th class="text-left">Hora Asignación</th>
	                <th class="text-left">Fecha Aprobación</th>
	                <th class="text-left">Hora Aprobación</th>
	                
	            </tr>
	        </thead>
	    <tbody>
	    @if(!empty($rqAsignadosHisto))	
	    @foreach($rqAsignadosHisto as $key => $value) 
	      <tr class="table">
				<td class="text-center">{{ $value->id_requerimiento }}</td>
				<td>{{ $value->fecha_asignacion }}</td>
				<td>{{ $value->hora_asignacion }}</td>
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
  	