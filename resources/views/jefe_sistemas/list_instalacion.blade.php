<!-- LIST -->
	<br>
	<div class="card shadow mb-4">
	  <!-- Card Header - Accordion -->
	  <a href="#instalacionn" class="d-block card-header py-3" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="instalacionn">
	    <h6 class="m-0 font-weight-bold text-primary">Lista de requerimiento de instalación</h6>
	  </a> 
	  <!-- Card Content - Collapse -->
	  <!--<div class="collapse show" id="instalacionn">-->
	  <div class="collapse show" id="instalacionn">
	    <div class="card-body">
			<div class="table-responsive">
		        <table class="table table-bordered"  class="display" id="dataTableInst" width="100%" cellspacing="0">
		            <thead>
		                <tr>
		            		<th class="text-center">Id Requerimiento</th>
		                    <th class="text-left">Fecha Asignación</th>
		                    <th class="text-left">Hora Asignación</th>
		                    <th class="text-left">Fecha de Inicio de Instalacion</th>
		                    <th class="text-left">Hora de Inicio de Instalacion</th>
		                   
		                </tr>
		            </thead>
		        <tbody>

		        @if(!empty($rqInstalacion))	
		        @foreach($rqInstalacion as $key => $value) 
		          <tr class="table">
						<td class="text-center">{{ $value->id_requerimiento }}</td>
						<td>{{ $value->fecha_asig_instal }}</td>
						<td>{{ $value->hora_asig_instal }}</td>
						<td>{{ $value->fecha_instal }}</td>
						<td>{{ $value->hora_instal }}</td>
		              	
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
	  <a href="#instHisto" class="d-block card-header py-3" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="instHisto">
	    <h6 class="m-0 font-weight-bold text-primary">Historico de requerimiento de instalación</h6>
	  </a> 
	  <!-- Card Content - Collapse -->
	  <!--<div class="collapse show" id="instHisto">-->
	  
	 	<div class="collapse" id="instHisto">
		    <div class="card-body">
			    <div class="table-responsive">
				    <table class="table table-bordered"  class="display" id="dataTableInstHist" width="100%" cellspacing="0">
				        <thead>
				            <tr>
				                <th class="text-center">Id Requerimiento</th>
				                <th class="text-left">Fecha Asignación</th>
				                <th class="text-left">Hora Asignación</th>
				                <th class="text-left">Fecha Final de la Instalacion</th>
				                <th class="text-left">Hora Final de la Instalacion</th>
				                
				            </tr>
				        </thead>
				    <tbody>

				    @if(!empty($rqInstalacionHisto))	
				    @foreach($rqInstalacionHisto as $key => $value) 
				      <tr class="table">
							<td class="text-center">{{ $value->id_requerimiento }}</td>
							<td>{{ $value->fecha_asig_instal }}</td>
							<td>{{ $value->hora_asig_instal }}</td>
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
  	