@extends('layouts.app')

@section('content')
<script>
        window.localStorage.removeItem("ls_clientes");
		
</script>
<div class="login-box">
  <div class="login-logo">
      <a href="{{ config('app.base_path', '/') }}"><b>{{ config('app.name', 'Laravel') }}</b></a>
  </div>
  <!-- /.login-logo -->
  <div class="login-box-body">
    <p class="login-box-msg">Faça o login para acessar o painel de controle</p>

    <form action="{{ route('login') }}" method="post">
         {{ csrf_field() }}
      <div class="form-group has-feedback {{ isset($errors) && $errors->has('email') ? ' has-error' : '' }}">
        <input type="texto" class="form-control" value="{{ old('email') }}"  required autofocus 
               placeholder="Email" name="email">
        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
        
                 @if (isset($errors) &&  $errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
      </div>
      <div class="form-group has-feedback {{ isset($errors) && $errors->has('password') ? ' has-error' : '' }}">
        <input type="password" name="password" class="form-control" placeholder="Password" required>
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
          @if (isset($errors) && $errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
      </div>
      <div class="row">
        <div class="col-xs-8">
          <div class="checkbox icheck" style="display: none">
            <label>
              <input type="checkbox"
                     name="remember"
                     {{ old('remember') ? 'checked' : '' }}> Lembrar senha
            </label>
          </div>
        </div>
        <!-- /.col -->
        <div class="col-xs-4">
          <button type="submit" class="btn btn-primary btn-block btn-flat">Login</button>
          
        </div>
        <!-- /.col -->
      </div>
    </form>

    <div class="social-auth-links text-center" style="display:none">
      <p>- OR -</p>
      <a href="#" class="btn btn-block btn-social btn-facebook btn-flat"><i class="fa fa-facebook"></i> Sign in using
        Facebook</a>
      <a href="#" class="btn btn-block btn-social btn-google btn-flat"><i class="fa fa-google-plus"></i> Sign in using
        Google+</a>
    </div>
    <!-- /.social-auth-links -->

    <a href="{{ route('password.request') }}">Esqueci minha senha</a><br>
    <a href="register.html" style="display:none" class="text-center">Register a new membership</a>

  </div>
  <!-- /.login-box-body -->
</div>

@endsection
