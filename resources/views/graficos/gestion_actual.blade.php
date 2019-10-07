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
          <h1 class="h3 mb-2 text-gray-800">Gráfico de la gestión actual.</h1>
          <p class="mb-4">El gráfico nos muestra la cantidad de requerimientos por mes. </p>

          <!-- DataTales Example -->
          <div class="card shadow mb-4">
            <div class="card-header py-3">
 

              <h6 class="m-0 font-weight-bold text-primary">Gráfico de barras de los requermientos del año {{ $anio }}.</h6>
            </div>

           <div class="card-body">
                  <div class="chart-bar">
                    <canvas id="reporte1"></canvas>
                  </div>
                  <hr>
            </div>

    <div class="row">
        <div class="col-md-4"></div>
        <div class="col-md-4 text-left"></div>
        <div class="col-md-3"></div>
      </div>  
</div>
<script type="text/javascript">

var barChartData = {
  labels: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
  datasets: [{
   label: 'Requerimientos',
   // hoverBackgroundColor:'#36a2eb',
   hoverBackgroundColor: 'rgba(54, 162, 235, 0.2)',
   borderWidth: 1,
   data: [
      @if(!empty($anio_actual[0]->enero))
        {{ $anio_actual[0]->enero }},
      @else
        0,
      @endif
      @if(!empty($anio_actual[0]->febrero))
        {{ $anio_actual[0]->febrero }},
      @else
        0,
      @endif
      @if(!empty($anio_actual[0]->marzo))
        {{ $anio_actual[0]->marzo }},
      @else
        0,
      @endif
      @if(!empty($anio_actual[0]->abril))
         {{ $anio_actual[0]->abril }},
      @else
        0,
      @endif
      @if(!empty($anio_actual[0]->mayo))
        {{ $anio_actual[0]->mayo }},
      @else
        0,
      @endif
      @if(!empty($anio_actual[0]->junio))
          {{ $anio_actual[0]->junio }},
      @else
        0,
      @endif
      @if(!empty($anio_actual[0]->julio))
         {{ $anio_actual[0]->julio }},
      @else
        0,
      @endif
      @if(!empty($anio_actual[0]->agosto))
        {{ $anio_actual[0]->agosto }},
      @else
        0,
      @endif
      @if(!empty($anio_actual[0]->septiembre))
        {{ $anio_actual[0]->septiembre }},
      @else
        0,
      @endif
      @if(!empty($anio_actual[0]->octubre))
        {{ $anio_actual[0]->octubre }},
      @else
        0,
      @endif
      @if(!empty($anio_actual[0]->noviembre))
        {{ $anio_actual[0]->noviembre }},
      @else
        0,
      @endif
      @if(!empty($anio_actual[0]->diciembre))
        {{ $anio_actual[0]->diciembre }},
      @else
        0,
      @endif
      
    ],
    backgroundColor: 'rgba(54, 162, 235, 0.2)',
    borderColor: 'rgba(54, 162, 235, 1)',
  }]

};

</script>
<script src="{{ asset('js/chartjs.ChartData.js') }}"></script>

 

@endsection