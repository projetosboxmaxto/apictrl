<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    function executeScalar( $sql ){
                    $ar = DB::select($sql);

                    //print_r( $ar ); die(" ");

                    return $ar[0]->res;
    }
    
      public function sendResponse($result, $message = "", $code = 200)
    {
        
        return response()->json($result, Response::HTTP_OK, array(), JSON_NUMERIC_CHECK);
        
        //return Response::json(ResponseUtil::makeResponse($message, $result), $code, [], JSON_UNESCAPED_SLASHES);
    }
    
       public function sendError($msg, $message = "", $code = 400)
    {
        
        return response()->json(array("msg"=>$msg), $code, array(), JSON_NUMERIC_CHECK);
        
        //return Response::json(ResponseUtil::makeResponse($message, $result), $code, [], JSON_UNESCAPED_SLASHES);
    }
    
    public function get_limit_sql( $inicio, $pagesize ){
        return " limit ". $inicio.", ". $pagesize;
    }
    
    public function getTokenId($id){
               $remember_token = md5( env("CRYPT_PASS")."|".$id);
               return $remember_token;
    }
	
    function getErrorMsg($msg){

             return array("code"=>2, "msg" =>$msg);

    }

     function ereg_replace($sintax, $replace, $val){
         
        // die(" -- ");
         if (function_exists("ereg_replace")){
             
             return  ereg_replace(
					        $sintax, $replace,  $val );
         }
         
                  $ret =  preg_replace(
					        $sintax, $replace,  $val );
         
              /* $ret =  preg_replace_callback(
					        $sintax,
					        function ($matches) use ($replace) {
					            return $replace;
					        },
					        $val ); */

               return $ret;

     }

    public function removeAcentos($var ){
		
		$ant = $var;
		
		// Variavel recebendo a string já fazendo as substituições
		
		$var = $this->ereg_replace("[ÁÀÂÃ]","A",$var);
		
		$var = $this->ereg_replace("[áàâãª]","a",$var);
		
		$var = $this->ereg_replace("[ÉÈÊ]","E",$var);
		
		$var = $this->ereg_replace("[éèê]","e",$var);
		
		$var = $this->ereg_replace("[ÓÒÔÕ]","O",$var);
		
		$var = $this->ereg_replace("[óòôõº]","o",$var);
		
		$var = $this->ereg_replace("[ÚÙÛ]","U",$var);
		
		$var = $this->ereg_replace("[úùû]","u",$var);
		
		$var = str_replace("Ç","C",$var);
		
		$var = str_replace("ç","c",$var);
		$var = str_replace("Í","I",$var);
		$var = str_replace("í","i",$var);	
		
		return $var;
	}

	
    public function getSlug($name, $id = ""){
    	$ret = str_replace(" ","-", str_replace("  "," ", strtolower(  $this->removeAcentos( $name ) )));
        $ret = str_replace("'","", $ret);

         /*
    	$ret = preg_replace_callback(
					        '/[^A-Za-z0-9\-]/',
					        function ($matches) {
					            return '';
					        },
					        $ret
					    );  */
                              
        $tem_id = true;

        if (is_null($id) || $id == ""){

            $id = " 0 ";       
            $tem_id = false;
        }
                                                
        $max  = \App\Http\Dao\PostsDao::executeScalar("select max(id) as res from posts where slug='".$ret."' and id != ". $id);                                        

        if ( is_null($max) || $max == "")
    	        return $ret;
        
        
        if (!$tem_id ){

        	$new_id = \App\Http\Dao\PostsDao::executeScalar("select max(id) as res from posts ");
        	$new_id++;
            return $ret . $new_id;
        }else{

            return $ret . $id;
        }
    }

	
	
	function SetaRsetPaginacao($selQtdeRegistro, $selPagina,$totalRegistro,
					  &$inicio, &$fim)
	 {
						
						
						if ( ! is_numeric($selQtdeRegistro))
						  $selQtdeRegistro = 0;
						
						
						if ( ! is_numeric($totalRegistro))
						  $totalRegistro = 0;
						
						
						$pageCount =  @($totalRegistro / $selQtdeRegistro);
						
						if ($pageCount < 1)
							$pageCount = 1; 
						
						if ($pageCount > round($pageCount))
							{    $pageCount++;}
						else 
							{  $pageCount = round($pageCount); }
						
						$pageCount = (int)$pageCount;
						
						
						//echo  $selPagina . "-- ".$pageCount;
						
						if ( $selPagina > (int)$pageCount)
							$selPagina = (int)$pageCount;
						
						//die ( $selPagina );
						
						 $inicio = $selQtdeRegistro * ($selPagina -1);
						 $fim = $inicio + $selQtdeRegistro;
						// die ( $inicio . " -- AAAAAAAAA  ". $selPagina );
						 
						 if ($fim > $totalRegistro)
							 @($fim = $totalRegistro);

							 //die($inicio."----".$selQtdeRegistro."-".$selPagina."-".$fim."-".$totalRegistro);

							 return $inicio."_".$fim;
	}
        
        
        
        public function dataBanco($valor)
	{
		if ($valor == "")
			return "";
		
                $hora = "";
                if ( strpos(" ". $valor, ":")){
                    $ar = explode(" ", $valor);
                    
                    if ( @$ar[1] != ""){
                          $hora = " ". $ar[1];
                    }
                    $valor = $ar[0];
                }
                
                
		$arr = explode("/",$valor);
		
		return trim($arr[2])."-".trim($arr[1])."-".trim($arr[0]). $hora;
		
	}
        
          public function DataBR($valor, $semhora =false)
    {
        
        if ($valor == "")
            return "";
    
        
        $valor = str_replace("-","/",$valor);
        
        $ar = explode(" ",$valor);
        $arr = explode("/",$ar[0]);
        
        if ( count($arr) < 3)
            return "";
        
        $hora = "";
        if (! $semhora)
            $hora = " " . @$ar[1];
        
        return $arr[2]."/".$arr[1]."/".$arr[0].$hora;
        
    }



}
