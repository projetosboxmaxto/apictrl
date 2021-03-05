{{-- file: /app/views/layouts/master.blade.php --}}
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">

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
        
        window.K_USER_GROUP_ID = "{{ Session::get('user.group_id')}}";
        window.K_USER_MENU = "{{ Session::get('user.menu_itens')}}";
    </script>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    @include('layouts.partials.header')

    @include('layouts.partials.includejs')
    {{-- Part: site title with default value in parent --}}
    @section('head-title')
        <title>{{ config('app.name', 'Laravel') }}</title>
    @show


</head>
<body >
<div  >



       <div  >
          @yield('content')

      </div>

@include('layouts.partials.finaljs')
</div>



    <script>
      /*
  $.ajaxSetup({
       beforeSend: function (xhr)
       {
          xhr.setRequestHeader("Accept","application/vvv.website+json;version=1");
          xhr.setRequestHeader("Authorization","Token token=\"FuHCLyY46\"");        
       }
    }); */
</script>
</body>
</html>