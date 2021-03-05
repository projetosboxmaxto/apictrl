<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
      //  $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       // $user = Auth::user();
        $user = session('user.info');
        
         
        $EH_INTEGRADOR = \App\Http\Dao\ConfigDao::getValor("EH_INTEGRADOR");
        $URL_API_INTEGRADOR = null;
       // echo("eh integrador? ". $EH_INTEGRADOR );
       // die("eh integrador? ". \App\Http\Dao\ConfigDao::getValor("URL_API_INTEGRADOR") );
        
        if ( $EH_INTEGRADOR == 1 ){
            
               $URL_API_INTEGRADOR = \App\Http\Dao\ConfigDao::getValor("URL_API_INTEGRADOR");
        }
        
        $CLIENTE_TITULO = \App\Http\Dao\ConfigDao::getValor("CLIENTE_TITULO");
        $CLIENTE_URL = \App\Http\Dao\ConfigDao::getValor("CLIENTE_URL");
        $CLIENTE_SIGLA = \App\Http\Dao\ConfigDao::getValor("CLIENTE_SIGLA");
        
        
        if ( $CLIENTE_TITULO == ""){
            
            $CLIENTE_TITULO = "MIDIACLIP";
            $CLIENTE_URL = "http://midiaclip.com.br";
            $CLIENTE_SIGLA = "CLIP";
        }
        
       // print_r( $user );
       //die("aqui ??". Auth::check());
       if (!Auth::check() && is_null( $user )){ 
          // print_r($user); die("usuário esta vazio?");
           return view('login.login', [
                               "URL_API_INTEGRADOR" => $URL_API_INTEGRADOR ,
                                "CLIENTE_TITULO"=>$CLIENTE_TITULO ,
                                "CLIENTE_NOME"=>$CLIENTE_TITULO ,
                                "CLIENTE_URL"=>$CLIENTE_URL,
                                "CLIENTE_SIGLA"=>$CLIENTE_SIGLA
                              ]);
            
       }
        
        
        session(['user.myauth' => $user->id."-".$this->getTokenId($user->id) ]);
        session(['user.id' => $user->id ]);
        session(['user.nome' => $user->nome ]);
       
        

        //return Redirect::route('vue');
           return view('vue', ["user_info" =>$user, 
                               "URL_API_INTEGRADOR" => $URL_API_INTEGRADOR ,
                                "CLIENTE_TITULO"=>$CLIENTE_TITULO ,
                                "CLIENTE_NOME"=>$CLIENTE_TITULO ,
                                "CLIENTE_URL"=>$CLIENTE_URL,
                                "CLIENTE_SIGLA"=>$CLIENTE_SIGLA
                              ]);
    }
    
       public function index2()
    {
        
        
          $user = session('user.info');
           return view('vueother', ["user_info" =>$user ]);
    }
}
