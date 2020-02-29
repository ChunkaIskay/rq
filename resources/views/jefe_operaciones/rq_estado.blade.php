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
<p class="mb-4">Tiene el listado completo de los requerimientos.</p>
<!-- progress bar -->

<!-- DataTales Example -->
<div class="card shadow mb-4">
	<div class="card-header py-3">
		<h6 class="m-0 font-weight-bold text-primary">Listado de Requerimientos</h6>
	</div>

	<div class="card-body">
		<ul class="nav nav-tabs" id="myTab" role="tablist">
		  <li class="nav-item">
		    <a class="nav-link  {{ $activo['aprobado']['active'] }}"  id="aprobados-tab" data-toggle="tab" href="#aprobados" role="tab" aria-controls="aprobados"
		      aria-selected="true">Aprobados</a>
		  </li>
		  <li class="nav-item">
		    <a class="nav-link {{ $activo['asignado']['active'] }}" id="asignados-tab" data-toggle="tab" href="#asignados" role="tab" aria-controls="asignados"
		      aria-selected="false">Asignados</a>
		  </li>
		  <li class="nav-item">
		    <a class="nav-link {{ $activo['desarrollo']['active'] }}" id="desarrollo-tab" data-toggle="tab" href="#desarrollo" role="tab" aria-controls="desarrollo"
		      aria-selected="false">En desarrollo</a>
		  </li>
		  <li class="nav-item">
		    <a class="nav-link {{ $activo['pruebas']['active'] }}" id="pruebas-tab" data-toggle="tab" href="#pruebas" role="tab" aria-controls="pruebas"
		      aria-selected="false">En pruebas</a>
		  </li>
		  <li class="nav-item">
		    <a class="nav-link {{ $activo['instalacion']['active'] }}" id="instalacion-tab" data-toggle="tab" href="#instalacion" role="tab" aria-controls="instalacion"
		      aria-selected="false">En instalacion</a>
		  </li>
		  <li class="nav-item">
		    <a class="nav-link {{ $activo['certificado']['active'] }}" id="certificado-tab" data-toggle="tab" href="#certificado" role="tab" aria-controls="certificado"
		      aria-selected="false">En certificado</a>
		  </li>
		  
		</ul>

		<div class="tab-content" id="myTabContent">
			<!-- aprobado -->
			<div class="tab-pane fade {{ $activo['aprobado']['show_active'] }}" id="aprobados" role="tabpanel" aria-labelledby="aprobados-tab">
				<div class="card-body">
					<form action="{{ url('/JefeOperaciones/rq-estado-all') }}"  method="post"  enctype="multipart/form-data" class="navbar-form navbar-left" >
						 {{ csrf_field() }}
						<h4>Presione el boton 'Ver listados'.!</h4>
						
						<button type="submit" class="btn btn-info">
							<span >Ver listado</span>
						</button>
					</form>
				</div>
				 @include('jefe_operaciones.list_aprobado')
			</div>
			<!---asignado---->
			<div class="tab-pane fade {{ $activo['asignado']['show_active'] }}" id="asignados" role="tabpanel" aria-labelledby="asignados-tab">
				<div class="card-body">
					<form action="{{ url('/JefeOperaciones/rq-estado-all-asig') }}"  method="post"  enctype="multipart/form-data" class="navbar-form navbar-left" >
						 {{ csrf_field() }}
						<h4>Presione el boton 'Ver listados'.!</h4>
						
						<button type="submit" class="btn btn-info">
							<span >Ver listado</span>
						</button>
					</form>
				</div>
				@include('jefe_operaciones.list_asignado')
			</div>
			<!---desarrollo---->
			<div class="tab-pane fade {{ $activo['desarrollo']['show_active'] }}" id="desarrollo" role="tabpanel" aria-labelledby="desarrollo-tab">
				<div class="card-body">
					<form action="{{ url('/JefeOperaciones/rq-estado-all-desa') }}"  method="post"  enctype="multipart/form-data" class="navbar-form navbar-left" >
						 {{ csrf_field() }}
						<h4>Presione el boton 'Ver listados'.!</h4>
						
						<button type="submit" class="btn btn-info">
							<span >Ver listado</span>
						</button>
					</form>
				</div>
				@include('jefe_operaciones.list_desarrollo')
			</div>
			<!---pruebas---->
			<div class="tab-pane fade {{ $activo['pruebas']['show_active'] }}" id="pruebas" role="tabpanel" aria-labelledby="pruebas-tab">
				<div class="card-body">
					<form action="{{ url('/JefeOperaciones/rq-estado-all-pruebas') }}"  method="post"  enctype="multipart/form-data" class="navbar-form navbar-left" >
						 {{ csrf_field() }}
						<h4>Presione el boton 'Ver listados'.!</h4>
						
						<button type="submit" class="btn btn-info">
							<span >Ver listado</span>
						</button>
					</form>
				</div>
				@include('jefe_operaciones.list_pruebas')
			</div>
			<!---instalacion---->
			<div class="tab-pane fade {{ $activo['instalacion']['show_active'] }}" id="instalacion" role="tabpanel" aria-labelledby="instalacion-tab">
				<div class="card-body">
					<form action="{{ url('/JefeOperaciones/rq-estado-all-inst') }}"  method="post"  enctype="multipart/form-data" class="navbar-form navbar-left" >
						 {{ csrf_field() }}
						<h4>Presione el boton 'Ver listados'.!</h4>
						
						<button type="submit" class="btn btn-info">
							<span >Ver listado</span>
						</button>
					</form>
				</div>
				@include('jefe_operaciones.list_instalacion')
			</div>
			<!---certificado---->
			<div class="tab-pane fade {{ $activo['certificado']['show_active'] }}" id="certificado" role="tabpanel" aria-labelledby="certificado-tab">
				<div class="card-body">
					<form action="{{ url('/JefeOperaciones/rq-estado-all-cert') }}"  method="post"  enctype="multipart/form-data" class="navbar-form navbar-left" >
						 {{ csrf_field() }}
						<h4>Presione el boton 'Ver listados'.!</h4>
						<button type="submit" class="btn btn-info">
							<span >Ver listado</span>
						</button>
					</form>
				</div>
				@include('jefe_operaciones.list_certificacion')
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