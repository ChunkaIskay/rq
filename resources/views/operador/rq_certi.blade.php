@extends('layouts.app')

@section('content')
 <!-- Begin Page Content -->
  
@if(session('message'))
	<div class="alert alert-success">
	    {{ session('message') }}
	</div>
@endif  

@if(session('error'))
	<div class="alert alert-danger">
	    {{ session('error') }}
	</div>
@endif
<!-- Page Heading -->
<h1 class="h3 mb-2 text-gray-800">{{ $pagTitulo }}</h1>
<p class="mb-4">Requerimientos solucionados a ser Certificados.</p>
<!-- progress bar -->

<!-- DataTales Example -->
<div class="card shadow mb-4">
	<div class="card-header py-3">
		<h6 class="m-0 font-weight-bold text-primary">Listado de Requerimientos a Certificar</h6>
	</div>

	<div class="card-body">
		<ul class="nav nav-tabs" id="myTab" role="tablist">
		  <li class="nav-item">
		    <a class="nav-link {{ $activo['asignado']['active'] }}" id="asignados-tab" data-toggle="tab" href="#asignados" role="tab" aria-controls="asignados"
		      aria-selected="false">Certificaciones</a>
		  </li>
		</ul>
		<div class="tab-content" id="myTabContent">
			<!---asignado---->
			<div class="tab-pane fade {{ $activo['asignado']['show_active'] }}" id="asignados" role="tabpanel" aria-labelledby="asignados-tab">
				@include('operador.list_asignado')
			</div>
		</div>
		<div class="table-responsive"></div>
	</div>
    <div class="row">
      <div class="col-md-4"></div>
      <div class="col-md-4 text-left"></div>
      <div class="col-md-3"></div>
    </div> 
</div>

@endsection