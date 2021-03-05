<?php namespace App\Http\Middleware;

use Closure;

class ApiAuthenticated {

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		 $header =  $request->headers->all(); // $request->header('Authorization');

                $arr_teste = array("tkn_correto" => "0");
                $myauth_str = "";
                //return $header;
		 $auth = @$header["authorization"];
		 $came_myauth = true;

            //print_r( $header );die(" aaaa  ");
		 if (! is_array(  $auth  )) {
                  //Não será possível autorizar.. vou retornar algum problema.
				//return response('Unauthorized.', 401);
		 	$came_myauth = false;
		 }

		 if ( is_array(  $auth  ) && count($auth) > 0  ){
                     
                     
                     $str  = $auth[0];
				 
                     if ( $str == md5("mclip#trans.criSao")){
                         $arr_teste["tkn_correto"] = true;
                     }
		 }
                 
                 if ( is_array(  $auth  ) && count($auth) > 0  ){
                     
                     $str  = $auth[0];
                     
                     if ( strpos(" ". $str,"-") > 0 ){
                         $arp = explode("-", $str);
                         
                         $id = $arp[0];
                         $token_testa  = $arp[1];
                         
                         if ( $token_testa == $this->getTokenTest($id) ){
                             $arr_teste["tkn_correto"] = true;
                             $arr_teste["id_user"] = $id;
                         }
                     }
                     
                 }

		 if ( !  $arr_teste["tkn_correto"] ){
                         //.var_dump( $request->headers->all() )
		 	    return response('Unauthorized.--' . $came_myauth, 401);
		 }


        // print_r( $header ); echo("<br><br>") ; print_r( $arr_teste ); die(" ");
		 $GLOBALS["auth_header"] = $arr_teste;

         //Chegou aqiu é porque esta tudo certo. Vou deixar ele seguir adiante com o myauth

           return $next($request)
                    ->header('myauth',  $myauth_str)
			        ->header('Access-Control-Allow-Origin', '*')
					->header('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
			        ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');

	}

	public function encrypt ($msg){
		return md5($msg);
	}

	public function getTokenTest($id){
               $remember_token = $this->encrypt( env("CRYPT_PASS")."|".$id);
               return $remember_token;
	}

}
