<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
   <!-- LOGIN -->
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
    
      <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <!-- Bootstrap 3.3.7 -->
        <link rel="stylesheet" href="{{ config('app.base_template', '/') }}bower_components/bootstrap/dist/css/bootstrap.min.css">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="{{ config('app.base_template', '/') }}bower_components/font-awesome/css/font-awesome.min.css">
        <!-- Ionicons -->
        <link rel="stylesheet" href="{{ config('app.base_template', '/') }}bower_components/Ionicons/css/ionicons.min.css">
        <!-- Theme style -->
        <link rel="stylesheet" href="{{ config('app.base_template', '/') }}dist/css/AdminLTE.min.css">
        <!-- iCheck -->
        <link rel="stylesheet" href="{{ config('app.base_template', '/') }}plugins/iCheck/square/blue.css">
        
        
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

       <!-- Styles -->
       <link href="{{ asset('css/app.css?g=13') }}" rel="stylesheet">
       
          <script>
                window.Laravel = {!! json_encode([
                    'csrfToken' => csrf_token(),
                ]) !!};

                window.URL_API = "{{ config('app.url_api', '/api') }}";
                window.URL_BASE = "{{ config('app.base_path', '/') }}";
                window.URL_TEMPLATE_BASE = "{{ config('app.base_template', '/') }}";  
                window.CSS_BOOTSTRAP_URL = "{{ config('app.base_template', '/') }}bower_components/bootstrap/dist/css/bootstrap.min.css";
                window.CSS_SITE_URL = "{{ config('app.base_site', '/')  }}assets/css/style.min.css";
                window.URL_MIDIACLIP = "{{ config('app.url_midiclip', '') }}";
                window.URL_API4 = "{{ config('app.url_api4', '') }}";
                window.URL_API_INTEGRADOR = "{{ @$URL_API_INTEGRADOR}}"; 
       
          
                window.K_CLIENTE_NOME = "{{ @$CLIENTE_NOME }}"; 
                window.K_CLIENTE_URL = "{{ @$CLIENTE_URL }}";
          
          </script>
          <style>
              .palavra_destaque{
                  font-weight: bold; color: red;
              }
          </style>
</head>
      <body class="hold-transition login-page">

 @yield('content')
        <!-- /.login-box -->

        <!-- jQuery 3 -->
        <script src="{{ config('app.base_template', '/') }}bower_components/jquery/dist/jquery.min.js"></script>
        <!-- Bootstrap 3.3.7 -->
        <script src="{{ config('app.base_template', '/') }}bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
        <!-- iCheck -->
        <script src="{{ config('app.base_template', '/') }}plugins/iCheck/icheck.min.js"></script>
        <script>
          $(function () {
            $('input').iCheck({
              checkboxClass: 'icheckbox_square-blue',
              radioClass: 'iradio_square-blue',
              increaseArea: '20%' /* optional */
            });
          });
        </script>
            <!-- Scripts -->
           <!-- <script src="{{ asset('js/app.js') }}"></script> -->
        </body>

</html>
