<!-- LIST -->
	<br>
	<div class="card shadow mb-4">
	  <!-- Card Header - Accordion -->
	  <a href="#desa" class="d-block card-header py-3" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="desa">
	    <h6 class="m-0 font-weight-bold text-primary">Lista de requerimiento de desarrollo</h6>
	  </a> 
	  <!-- Card Content - Collapse -->
	  <!--<div class="collapse show" id="desa">-->
	  <div class="collapse show" id="desa">
	    <div class="card-body">
			<div class="table-responsive">
		        <table class="table table-bordered"  class="display" id="dataTableDesa" width="100%" cellspacing="0">
		            <thead>
		                <tr>
		                    <th class="text-center">Id Requerimiento</th>
		                    <th class="text-left">Fecha Asignación</th>
		                    <th class="text-left">Hora Asignación</th>
		                    <th class="text-left">Fecha de Inicio de Desarrollo</th>
		                    <th class="text-left">Hora de Inicio de Desarrollo</th>
		                    
		                </tr>
		            </thead>
		        <tbody>
		        @if(!empty($rqDesarrollo))	
		        @foreach($rqDesarrollo as $key => $value) 
		          <tr class="table">
						<td class="text-center">{{ $value->id_requerimiento }}</td>
						<td>{{ $value->fecha_asignacion }}</td>
						<td>{{ $value->hora_asignacion }}</td>
						<td>{{ $value->fecha_inicio }}</td>
						<td>{{ $value->hora_inicio }}</td>
		              	
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
	  <a href="#desaHist" class="d-block card-header py-3" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="desaHist">
	    <h6 class="m-0 font-weight-bold text-primary">Historico de requerimiento de desarrollo</h6>
	  </a> 
	  <!-- Card Content - Collapse -->
	  <!--<div class="collapse show" id="desaHist">-->
	  
	 	<div class="collapse" id="desaHist">
		    <div class="card-body">
			    <div class="table-responsive">
				    <table class="table table-bordered"  class="display" id="dataTableDesaHist" width="100%" cellspacing="0">
				        <thead>
				            <tr>
				            <th class="text-center">Id Requerimiento</th>
		                    <th class="text-left">Fecha Asignación</th>
		                    <th class="text-left">Hora Asignación</th>
		                    <th class="text-left">Fecha de Inicio de Desarrollo</th>
		                    <th class="text-left">Hora de Inicio de Desarrollo</th>
		                    
				            </tr>
				        </thead>
				    <tbody>
				    @if(!empty($rqDesarrolloHisto))	
				    @foreach($rqDesarrolloHisto as $key => $valueH) 
				      <tr class="table">
							<td class="text-center">{{ $valueH->id_requerimiento }}</td>
							<td>{{ $valueH->fecha_asignacion }}</td>
							<td>{{ $valueH->hora_asignacion }}</td>
							<td>{{ $valueH->fecha_inicio }}</td>
							<td>{{ $valueH->hora_inicio }}</td>
				          	
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