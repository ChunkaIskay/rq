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
          <h1 class="h3 mb-2 text-gray-800">Gráfico comparativo de años.</h1>
          <p class="mb-4">El gráfico nos muestra la cantidad de requerimientos por mes del año selecionado, en comparación a un años distinto. </p>

          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Seleccione fechas de dos años distintos.</h6>
            </div> 
            <br>

            <form action="{{ url('/graficos/search-graficos') }}"  method="post"  enctype="multipart/form-data" class="navbar-form navbar-left" >
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
               <div class="row text-left">
                <div class="col-2 text-right"> <label>Año desde</label></div>
                <div class="col-3 text-left">
                @if(!empty($dateFrom))
                    <input type="date" class="form-control " id="dateTimeFrom" name=
                  "dateFrom" value="{{ $dateFrom }}" autocomplete="off" placeholder="Fecha desde">
                @else
                    <input type="date" class="form-control " id="dateTimeFrom" name=
                  "dateFrom" value="" autocomplete="off" placeholder="Fecha desde">
                @endif  
                </div>
                <div class="col-2 text-right"> <label>Año hasta</label></div>
                <div class="col-3 text-left">
                  
                @if(!empty($dateTo))
                  <input type="date" class="form-control" id="dateTimeUntil" name="dateTo" value="{{ $dateTo }}"  autocomplete="off"  placeholder="Fecha hasta">
                @else
                 <input type="date" class="form-control" id="dateTimeUntil" name="dateTo" value=""  autocomplete="off"  placeholder="Fecha hasta">
                @endif
                </div>
                 <div class="col-2 text-left">
                  <button type="submit" class="btn btn-info">
                    <span >Buscar por fechas</span>
                  </button>
                </div>
              </div>
            </form>
            <br>
          </div>

          <!-- DataTales Example -->
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Gráfico de barras - Requerimientos de las gestiones {{ $anio_antes.' y ' . $anio_actual}}.</h6>
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
          </div><br><br>

<script type="text/javascript">
  
  var barChartData = { 
              labels: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
               hoverBackgroundColor: 'rgba(54, 162, 235, 0.2)',
               borderWidth: 1,

              datasets: [{
                label: '{{ $anio_actual }}',
               // yAxisID: 'A',
                data: [
                        
                        @if(!empty($rq_anio_actual[0]->enero))
                            {{ $rq_anio_actual[0]->enero }},
                        @else
                          0,
                        @endif
                        @if(!empty($rq_anio_actual[0]->febrero))
                          {{ $rq_anio_actual[0]->febrero }},
                        @else
                          0,
                        @endif
                        @if(!empty($rq_anio_actual[0]->marzo))
                          {{ $rq_anio_actual[0]->marzo }},
                        @else
                          0,
                        @endif
                        @if(!empty($rq_anio_actual[0]->abril))
                           {{ $rq_anio_actual[0]->abril }},
                        @else
                          0,
                        @endif
                        @if(!empty($rq_anio_actual[0]->mayo))
                          {{ $rq_anio_actual[0]->mayo }},
                        @else
                          0,
                        @endif
                        @if(!empty($rq_anio_actual[0]->junio))
                            {{ $rq_anio_actual[0]->junio }},
                        @else
                          0,
                        @endif
                        @if(!empty($rq_anio_actual[0]->julio))
                           {{ $rq_anio_actual[0]->julio }},
                        @else
                          0,
                        @endif
                        @if(!empty($rq_anio_actual[0]->agosto))
                          {{ $rq_anio_actual[0]->agosto }},
                        @else
                          0,
                        @endif
                        @if(!empty($rq_anio_actual[0]->septiembre))
                          {{ $rq_anio_actual[0]->septiembre }},
                        @else
                          0,
                        @endif
                        @if(!empty($rq_anio_actual[0]->octubre))
                          {{ $rq_anio_actual[0]->octubre }},
                        @else
                          0,
                        @endif
                        @if(!empty($rq_anio_actual[0]->noviembre))
                          {{ $rq_anio_actual[0]->noviembre }},
                        @else
                          0,
                        @endif
                        @if(!empty($rq_anio_actual[0]->diciembre))
                          {{ $rq_anio_actual[0]->diciembre }}
                        @else
                          0
                        @endif

                      ],
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',

              }, {
                label: '{{ $anio_antes }}',
              // yAxisID: 'B',
                data: [
                        
                        @if(!empty($rq_anio_antes[0]->enero))
                            {{ $rq_anio_antes[0]->enero }},
                        @else
                          0,
                        @endif
                        @if(!empty($rq_anio_antes[0]->febrero))
                          {{ $rq_anio_antes[0]->febrero }},
                        @else
                          0,
                        @endif
                        @if(!empty($rq_anio_antes[0]->marzo))
                          {{ $rq_anio_antes[0]->marzo }},
                        @else
                          0,
                        @endif
                        @if(!empty($rq_anio_antes[0]->abril))
                           {{ $rq_anio_antes[0]->abril }},
                        @else
                          0,
                        @endif
                        @if(!empty($rq_anio_antes[0]->mayo))
                          {{ $rq_anio_antes[0]->mayo }},
                        @else
                          0,
                        @endif
                        @if(!empty($rq_anio_antes[0]->junio))
                            {{ $rq_anio_antes[0]->junio }},
                        @else
                          0,
                        @endif
                        @if(!empty($rq_anio_antes[0]->julio))
                           {{ $rq_anio_antes[0]->julio }},
                        @else
                          0,
                        @endif
                        @if(!empty($rq_anio_antes[0]->agosto))
                          {{ $rq_anio_antes[0]->agosto }},
                        @else
                          0,
                        @endif
                        @if(!empty($rq_anio_antes[0]->septiembre))
                          {{ $rq_anio_antes[0]->septiembre }},
                        @else
                          0,
                        @endif
                        @if(!empty($rq_anio_antes[0]->octubre))
                          {{ $rq_anio_antes[0]->octubre }},
                        @else
                          0,
                        @endif
                        @if(!empty($rq_anio_antes[0]->noviembre))
                          {{ $rq_anio_antes[0]->noviembre }},
                        @else
                          0,
                        @endif
                        @if(!empty($rq_anio_antes[0]->diciembre))
                          {{ $rq_anio_antes[0]->diciembre }},
                        @else
                          0,
                        @endif


                      ],

                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                borderColor: 'rgba(255, 99, 132, 1)',
              }]
  };
  var tipo = 'bar';
  var canvas1= 'reporte1';

</script>
<script src="{{ asset('js/chartjs.generic.js') }}"></script>

@endsection