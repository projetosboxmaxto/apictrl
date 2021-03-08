<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Artisan;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }
    
    
    
    public function defaultMessage(Request $request){

      
        Artisan::call('config:clear');
        Artisan::call('cache:clear');
        //Artisan::call('config:cache');
        Artisan::call('view:clear');
        Artisan::call('route:clear');
        
        /*
         * php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
composer dump-autoload
         */
        
          //session_destroy();
         //$request->session()->flush();
         return array("msg" => config("app.DB_HOST"),"Use o método POST - ". config("app.env")." - ".  config("app.PATH_ANEXO") ." - ".
              date("Y-m-d H:i:s")," - ". config("app.DB_MIDIACLIP"));
    }
    
    public function showLoginForm(Request $request){
        
        $this->validate($request, [
            'email'           => 'required|max:255|email',
            'password'           => 'required|confirmed',
        ]);
       //die("estou aqui ? ");
        return view('auth.login' )->with('errors',  $this->validate->messages() );
    }
}
