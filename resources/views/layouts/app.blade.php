<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Bienvenidos al sistema de Requerimientos!<!--{{ config('app.name', 'Bienvenidos al sistema de Requerimientos!') }}--></title>
    <!-- Custom fonts for this template-->
    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <!-- Custom styles for this template-->
    <link href="{{ asset('css/sb-admin-2.css') }}" rel="stylesheet">

</head>
<body>
    <div id="app">
        <nav class="navbar navbar-default navbar-static-top">
            <div class="container">
                <div class="navbar-header">

                    <!-- Collapsed Hamburger -->
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse" aria-expanded="false">
                        <span class="sr-only">Toggle Navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                </div>

                <div class="collapse navbar-collapse" id="app-navbar-collapse">
                    <!-- Left Side Of Navbar -->
                    <ul class="nav navbar-nav">
                        &nbsp;
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="nav navbar-nav navbar-right">
                        <!-- Authentication Links -->
                        @guest
                           
                         
                        @else
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" aria-haspopup="true" v-pre>
                                    {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

                                <ul class="dropdown-menu">
                                    <li>
                                        <a href="{{ route('logout') }}"
                                            onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                            Logout
                                        </a>

                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                            {{ csrf_field() }}
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        @yield('content')
    </div>

      <!-- Scripts -->
      <script src="{{ asset('js/app.js') }}"></script>
      <!-- Bootstrap core JavaScript-->
      <script src="{{ asset('vendor/jquery/jquery.js') }}"></script>
      <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.js') }}"></script>
      <!-- Core plugin JavaScript-->
      <script src="{{ asset('vendor/jquery-easing/jquery.easing.js') }}"></script>
      <!-- Custom scripts for all pages-->
      <script src="{{ asset('js/sb-admin-2.js') }}"></script>
      <!-- Page level plugins -->
      <script src="{{ asset('vendor/chart.js/Chart.js') }}"></script>
      <!-- Page level custom scripts -->
      <script src="{{ asset('js/demo/chart-area-demo.js') }}"></script>
      <script src="{{ asset('js/demo/chart-pie-demo.js') }}"></script>
</body>
</html>
