@extends('layouts.app')

@section('content')
<style type="text/css">

  

   .boton-subir{
      text-decoration:none;
      font-weight: 100;
      font-size: 14px;
      color:#fff;
      padding-top:5px;
      padding-bottom:5px;
      padding-left:15px;
      padding-right:15px;
      background-color:#1cc88a;
      border-color: #78dcb8;
      border-width: 3px;
      border-style: solid;
      border-radius:10px;
  }
  .boton-buscar{
      text-decoration:none;
      font-weight: 200;
      font-size: 14px;
      color:#fff;
      padding-top:5px;
      padding-bottom:5px;
      padding-left:25px;
      padding-right:25px;
      background-color:#36b9cc;
      border-color: #4e73df;
      border-width: 1px;
      border-style: solid;
      border-radius:5px;
  }

$custom-file-text: (
en: "Browses",
es: "Elegir"
);
</style>
 <!-- Begin Page Content -->
<div class="container-fluid">
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
  <h1 class="h3 mb-2 text-gray-800">Modificar el operador</h1>
  <p class="mb-4">Modificar los datos del operador {{ $operador->name }}.</p>

  <!-- DataTales Example -->
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary">Modificar el operador</h6>
    </div>
      <div class="card-body">
        <form  class="md-form" id="logout-form2" action="{{ url('/JefeOperaciones/'.$operador->id .'/editar-operador') }}" method="post" enctype="multipart/form-data">
           {{ csrf_field() }}
           @if($errors->any())
            <div class="alert alert-danger">
              <ul>
                @foreach($errors->all() as $error)
                  <li>{{$error}}</li>
                @endforeach
              </ul>
            </div>
           @endif
           <input type="hidden" name="ido" value="{{ $operador->id }}">
            <div class="row">
                  <div class="col-lg-2">  
                     Nombre 
                  </div>
                  <div class="col-lg-3 text-left">  
                     <strong><input class="form-control" type="input" name="nombre_ope" value="{{ $operador->name }}"></strong>
                  </div>
                  <div class="col-lg-7">  
                     &nbsp; 
                  </div>
            </div><br>
            <div class="row">
                  <div class="col-lg-2">  
                     Ap. paterno 
                  </div>
                  <div class="col-lg-3 text-left">  
                     <strong><input class="form-control" type="input" name="paterno" value="{{ $operador->ap_paterno }}"></strong>
                  </div>
                  <div class="col-lg-2">  
                    Ap. materno
                  </div>
                  <div class="col-lg-3 text-left">  
                      <strong><input class="form-control" type="input" name="materno" value="{{ $operador->ap_materno }}"></strong>
                  </div>
            </div><br>
            <div class="row">
                  <div class="col-lg-2">  
                     Dirección 
                  </div>
                  <div class="col-lg-3 text-left">  
                     <strong><input class="form-control" type="input" name="direccion" value="{{ $operador->direccion }}"></strong>
                  </div>
                  <div class="col-lg-2">  
                      Teléfono
                  </div>
                  <div class="col-lg-3 text-left">  
                      <strong><input class="form-control" type="input" name="telefono" value="{{ $operador->telefono }}"></strong>
                  </div>
            </div><br>
            <div class="row">
                  <div class="col-lg-2">  
                     eMail 
                  </div>
                  <div class="col-lg-3 text-left">  
                     <strong><input class="form-control" type="email" name="email" value="{{ $operador->email }}"></strong>
                  </div>
                  <div class="col-lg-2">  
                      Departamento
                  </div>
                  <div class="col-lg-3 text-left">
                      <select class="form-control form-control-large"  name="departamento" required="">
                          @foreach($departamentos as $depa)
                                  <option value="{{ $depa->id_region }}" @if( $depa->id_region == old('departamento', $operador->id_region)) selected @endif >{{ $depa->nombre }}</option>
                          @endforeach             
                      </select>
                      
                  </div>
            </div><br>
             <div class="row">
                  <div class="col-lg-2">  
                      Estado
                  </div>
                  <div class="col-lg-3 text-left">
                      <select name="estado" class="form-control form-control-large"  name="departamento" required="">
                         <option value="Si" @if($operador->activo == old('estado', 'Si')) selected @endif >Activo</option>
                         <option value="No" @if($operador->activo == old('estado', 'No')) selected @endif >Inactivo</option> 
                      </select>
                  </div>
                   <div class="col-lg-7">  
                     &nbsp;
                  </div>
            </div><br>
            <div class="row">
              <div class="col-lg-6 text-right">  
                <div class="my-2"></div>
                <a href="#" class="btn btn-success btn-icon-split" data-toggle="modal" data-target="#modalActualizar">
                  <span class="icon text-white-50">
                    <i class="fas fa-check"></i>
                  </span>
                  <span class="text">Modificar operador</span>
                </a>
              </div>
              <div class="col-lg-6"> 
                 <div class="my-2"></div>
                 <a class="btn btn-primary" href="{{ route('listaOperadores') }}">
                  <span class="icon text-white-50">
                    <i class="fas fa-exclamation-triangle"></i>
                  </span>
                  <span class="text">Cancelar</span>
                </a>
              </div>
            </div>
        </form>
      </div>
    </div>
    </div>
  </div>
</div>
  <!-- /.container-fluid -->
  <!-- Logout Modal-->
  <div class="modal fade" id="modalActualizar" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Modificar el operador.</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">x</span>
          </button>
        </div>
        <div class="modal-body">Esta seguro e modificar los datos del operador?.</div>
        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
          <!--<a class="btn btn-primary" href="login.html">Logout</a>-->
              <a class="btn btn-primary" href="{{ route('guardarOperador') }}"
                  onclick="event.preventDefault();
                           document.getElementById('logout-form2').submit();">
                  Guardar datos operador
              </a>
        </div>
      </div>
    </div>
  </div>
@endsection