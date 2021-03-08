<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Service\ErrorsService;

use Illuminate\Http\Request;
use App\ClienteConfiguracao;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Dao\ImageDao;
use App\Http\Dao\PostsDao;

class ClienteConfiguracaoController extends Controller {



	public function index(Request $request)
	{
            
            
	      $filtro = "";
              $order = " id "; $order_type = "desc";
              
              if ( $request->input( "filtro")  != ""){
                         	$str_filt = str_replace("'","''", $request->input( "filtro") );
                                $str_comp = "";
                                $str_comp .= " or cast(c.id as char)  = '" .$request->input( "filtro")."' ";
                                
                             
                        	$filtro .= " and ( c.nome like '%".$str_filt."%' ".$str_comp." ) ";
               }
               
                /*
                $sql = "select p.*, '' as blnk, '' as user_link, '' as data "
                        . " from elastic_queries p where 1 = 1 ". $filtro . " order by ".$order. " ".$order_type ;
                $itens = DB::select($sql);
                */
              
               $DB_MIDIACLIP = \App\Http\Dao\ConfigDao::getSchemaMidiaClip();

                $todos = $request->input("todos");
           
                $sql  = "select c.id, cc.id_cliente, convert(c.nome using utf8) as nome, ifNull(cc.consulta_comum, 1 ) as consulta_comum, cc.consulta_elastic, '' as blnk from ". $DB_MIDIACLIP. ".cliente c left join 
                               cliente_configuracao cc on cc.id_cliente = c.id "
                    . " where ifNull(status, 1) = 1  ";

                     if ( !$todos ){
                              $sql  .=  $filtro . " and c.id in ( select distinct id_cliente from eventos_clientes ) order by c.nome ";
                     } else {
                              $sql .= $filtro . " order by c.nome ";
                     }
            
            
                $lista = DB::select($sql);
                
                $saida = array(
                             "qtde"=> count($lista),
                             "data" => $lista 
                         //, "sql" => $sql
                        );
                         
                         
                return $saida;
	}
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index_old(Request $request)
	{
	      $filtro = "";
              $order = " id "; $order_type = "desc";
              
              if ( $request->input( "filtro")  != ""){
                         	$str_filt = str_replace("'","''", $request->input( "filtro") );
                        	$filtro .= " and ( nome like '%".$str_filt."%' or email like '%".$str_filt."%' ) ";
               }
               
               
                $sql = "select p.*, '' as blnk, '' as user_link, '' as data "
                        . " from cliente_configuracao p where 1 = 1 ". $filtro . " order by ".$order. " ".$order_type ;
                $itens = DB::select($sql);
                
                $saida = array(
                             "qtde"=> count($itens),
                             "data" => $itens );
                         
                         
                return $saida;
	}
	
	/*
	            Route::get('/api/cliente_configuracao', 'ClienteConfiguracaoController@index');
                Route::get('/api/cliente_configuracao/{id}', 'ClienteConfiguracaoController@show');
                Route::put('/api/cliente_configuracao/{id}', 'ClienteConfiguracaoController@update');
                Route::post('/api/cliente_configuracao', 'ClienteConfiguracaoController@create');
                Route::delete('/api/cliente_configuracao/{id}', 'ClienteConfiguracaoController@destroy');
				
				Route::resource('users', 'UserAPIController');
				
				*/

        
        function encrypt( $senha ){
               return md5( config("app.CRYPT_PASS") . $senha);
            //  return Hash::make( $senha);
        }
		public function testheader(Request $request){

				  $o_auth_header  = $GLOBALS["auth_header"] ;
				  return array("msg"=>"Teste", "header" => $o_auth_header );
		}

        
	
	public function grid(Request $request){
		
		
		         $filtro = ""; $str_filt = "";

                         $page = $request->input( "page");
                         $pagesize = $request->input( "pagesize");  

                         if ( $pagesize == "")
                         	$pagesize = 10;

                         if ( $page == "")
                         	$page = 1;

                         if ( $request->input( "filtro")  != ""){
                         	$str_filt = str_replace("'","''", $request->input( "filtro") );
                         	$filtro .= " and ( nome like '%".$str_filt."%' or email like '%".$str_filt."%' ) ";
                         }


                         $sql = " select count(*) as res from cliente_configuracao where 1 = 1 ".$filtro ;
                         $total_itens = $this->executeScalar(  $sql );

                         $inicio = 0; $fim = 0;
                         $this->SetaRsetPaginacao($pagesize, $page,$total_itens, $inicio, $fim);

                         $order = $request->input("order");
                         $order_type = $request->input("order_type");
                         if ( $order == ""){
                         	$order = "id";
                         }
                          if ( $order_type == ""){
                         	$order_type = "asc";
                         }

                         $sql = "select p.* from cliente_configuracao p where 1 = 1 ". $filtro . " order by ".$order. " ".$order_type .
						    $this->get_limit_sql(  $inicio,  $pagesize) ;
                         $itens = DB::select($sql);
                         //OFFSET 50 ROWS FETCH NEXT 100 ROWS ONLY 

                         $saida = array("page"=>$page, "pagesize" => $pagesize, "order"=>$order,
                          "total"=>$total_itens, "total_itens"=> $total_itens,
                          "order_type"=> $order_type, "itens" =>  $itens);

                         return $saida;
		
		
	}

	public function teste(Request $request){
		    
		   $msg_encriptado =   $this->encrypt("Teste");

		   $msg_final  = $this->decrypt(  $msg_encriptado );
		
		   return   $msg_encriptado . " --- a senha antes decriptada Ã©: " . $msg_final;
         }

	public function testpost(Request $request){
		
		
                         $msg = $request->input( "msg");
						 
						 $txt = "Recebido um post. A msg Ã©: ". $msg;
						 
						 return $txt;
	}
	private function loadRequests(Request $request, \App\ClienteConfiguracao &$reg){

          $reg->id_cliente = $request->input('id_cliente');  
  $reg->consulta_comum = $request->input('consulta_comum');  
  $reg->consulta_elastic = $request->input('consulta_elastic');  
		
		
         PostsDao::blankToNull(  $reg );

	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create(Request $request)
	{
		
		$reg = new \App\ClienteConfiguracao;

		$this->loadRequests($request, $reg);
		
		$ret = $reg->save();

		$msg = "sucesso!"; $code = 1;
		if (! $ret  ){
              $code = 0;
              $msg = "erro";
		}


		return array("msg"=>$msg, "code" =>  $code , "success" => $ret, "results"=> $reg,
                       "item"=> $reg);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		   $reg = \App\Http\Service\ClienteConfiguracaoService::getClienteConfiguracao(  $id );
		  // $reg = ClienteConfiguracao::find($id);

           return array( "code" =>  1,  "results"=> $reg, "item"=> $reg);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id, Request $request)
	{
		 return "metodo EDIT";
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id, Request $request)
	{
		   $reg = ClienteConfiguracao::find($id);

		   $this->loadRequests($request, $reg);

			$ret = $reg->save();

		     $msg = "sucesso!"; $code = 1;
			if (! $ret  ){
                  $code = 0;
	              $msg = "erro";
			}
			
        return array("msg"=>$msg, "code" =>  $code , "success" => $ret, "results"=> $reg, "item" => $reg);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$reg = ClienteConfiguracao::find($id);
		$ret = $reg->delete();
        return array("msg"=>"sucesso", "code" =>  1 , "success" => $ret, "results"=> $reg);
	}
        
        

}
