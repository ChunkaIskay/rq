<!-- LIST -->
	<br>
	<div class="card shadow mb-4">
	  <!-- Card Header - Accordion -->
	  <a href="#certificacion" class="d-block card-header py-3" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="certificacion">
	    <h6 class="m-0 font-weight-bold text-primary">Lista de requerimientos certificados on line</h6>
	  </a> 
	  <!-- Card Content - Collapse -->
	  <!--<div class="collapse show" id="certificacion">-->
	  <div class="collapse show" id="certificacion">
	    <div class="card-body">
			<div class="table-responsive">
		        <table class="table table-bordered"  class="display" id="dataTableCert" width="100%" cellspacing="0">
		            <thead>
		                <tr>
		                    <th class="text-center">Id Requerimiento</th>
		                    <th class="text-left">Fecha Inicial Certificacion Online</th>
		                    <th class="text-left">Hora Inicial Certificacion Online</th>
		                    <th class="text-left">Fecha Final de Certificacion</th>
		                    <th class="text-left">Hora Final de Certificacion</th>
		                    
 		                </tr>
		            </thead>
		        <tbody>
		        @if(!empty($rqCertificado))	
		        @foreach($rqCertificado as $key => $valueC) 
		          <tr class="table">
						<td class="text-center">{{ $valueC->id_requerimiento }}</td>
						<td>{{ $valueC->fecha_instal }}</td>
						<td>{{ $valueC->hora_instal }}</td>
						<td>{{ $valueC->fecha_certificacion }}</td>
						<td>{{ $valueC->hora_certificacion }}</td>
		              	
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
	

	<!-- END LIST -->
  	