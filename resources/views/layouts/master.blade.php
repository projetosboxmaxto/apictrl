{{-- file: /app/views/layouts/master.blade.php --}}
<!DOCTYPE html>
<html lang="pt-br">
<head>
   <!-- Scripts -->
    <script>
        window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(),
        ]) !!};
    
        window.URL_API = "{{ config('app.url_api', '/api') }}";
        window.URL_BASE = "{{ config('app.base_path', '/') }}";
        window.URL_TEMPLATE_BASE = "{{ config('app.base_template', '/') }}";  
        window.CSS_BOOTSTRAP_URL = "{{ config('app.base_template', '/') }}bower_components/bootstrap/dist/css/bootstrap.min.css";
        window.CSS_SITE_URL = "{{ config('app.base_site', '/')  }}assets/css/style.min.css";
        window.URL_BASE_SITE = "{{ config('app.base_site', '/')  }}";
        window.VELOX_URL_API = "{{ config('app.VELOX_URL_API', '/')  }}";
        window.VELOX_PARTNER_CODE = "{{ config('app.VELOX_PARTNER_CODE', '/')  }}";
        window.VELOX_FIRST_CITY = "{{ config('app.VELOX_FIRST_CITY', '/')  }}";
        
        window.K_USER_GROUP_ID = "{{ Session::get('user.group_id')}}";
        window.K_USER_MENU = '<?php echo ( Session::get('user.menu_itens') ) ?>';
        window.URL_API4 = "{{ config('app.url_api4', '') }}";
        window.API_AUTHORIZATION = "{{ Session::get('user.myauth') }}";
        window.API_MYAUTH = "{{ Session::get('user.id') }}";
        window.K_URL_SISTEMA_MIDIACLIP = "{{ config('app.url_midiaclip', '') }}"; 
        window.API4_EXTRA_PARAM = "{{ env('API4_EXTRA_PARAM') }}";
        window.URL_API_INTEGRADOR = "{{ $URL_API_INTEGRADOR}}"; 
        
        window.K_CLIENTE_NOME = "{{ $CLIENTE_NOME }}"; 
        window.K_CLIENTE_URL = "{{ $CLIENTE_URL }}";
    </script>

  
    <meta charset="UTF-8">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    @include('layouts.partials.header')

    @include('layouts.partials.includejs')
    {{-- Part: site title with default value in parent --}}
    @section('head-title')
        <title>{{ config('app.name', 'Laravel') }}</title>
    @show

       <style>
              .palavra_destaque{
                  font-weight: bold; color: red;
              }
          </style>

</head>
<body class="hold-transition skin-blue sidebar-mini">
    <input type="hidden" name="id_operador" value="{{ Session::get('user.id') }}" id="id_operador" />
    <input type="hidden" name="nome_operador" value="{{ Session::get('user.nome') }}" id="nome_operador" />
<div class="wrapper" >

@include('layouts.partials.contentheader')


 @yield('content')
 

 @if (false )
      @include('layouts.partials.contentfooter')

      @include('layouts.partials.mainsidebar')
 @endif

@include('layouts.partials.finaljs')
</div>

    <div id="div_error_api" >
        
        
    </div>


</body>
</html>