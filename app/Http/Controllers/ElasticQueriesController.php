<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Service\ErrorsService;

use Illuminate\Http\Request;
use App\SearchQueries;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Dao\ImageDao;
use App\Http\Dao\PostsDao;

class ElasticQueriesController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(Request $request)
	{
            
            
	      $filtro = "";
              $order = " id "; $order_type = "desc";
              
              if ( $request->input( "filtro")  != ""){
                         	$str_filt = str_replace("'","''", $request->input( "filtro") );
                        	$filtro .= " and ( nome like '%".$str_filt."%' ) ";
               }
               
                /*
                $sql = "select p.*, '' as blnk, '' as user_link, '' as data "
                        . " from elastic_queries p where 1 = 1 ". $filtro . " order by ".$order. " ".$order_type ;
                $itens = DB::select($sql);
                */
              
               $DB_MIDIACLIP = \App\Http\Dao\ConfigDao::getSchemaMidiaClip();

                $todos = $request->input("todos");
           
                $sql  = "select c.id, convert(c.nome using utf8) as nome from ". $DB_MIDIACLIP. ".cliente c "
                    . " where ifNull(status, 1) = 1  ";

                     if ( !$todos ){
                              $sql  .=  $filtro . " and c.id in ( select distinct id_cliente from eventos_clientes ) order by c.nome ";
                     } else {
                              $sql .= $filtro . " order by c.nome ";
                     }
            
            
            $lista = DB::select($sql);
                
                $saida = array(
                             "qtde"=> count($lista),
                             "data" => $lista );
                         
                         
                return $saida;
	}
	
	/*
	            Route::get('/elastic_queries', 'SearchQueriesController@index');
                Route::get('/elastic_queries/{id}', 'SearchQueriesController@show');
                Route::put('/elastic_queries/{id}', 'SearchQueriesController@update');
                Route::post('/elastic_queries', 'SearchQueriesController@create');
                Route::delete('/elastic_queries/{id}', 'SearchQueriesController@destroy');
				
				
	            Route::get('/elastic_queries/gridcad', 'SearchQueriesController@gridcad');
	            Route::post('/elastic_queries/salvargrid', 'SearchQueriesController@salvargrid');
				*/

        
        function encrypt( $senha ){
               return md5( env("CRYPT_PASS") . $senha);
            //  return Hash::make( $senha);
        }
		public function testheader(Request $request){

				  $o_auth_header  = $GLOBALS["auth_header"] ;
				  return array("msg"=>"Teste", "header" => $o_auth_header );
		}
		
			
			 public function gridcad(Request $request, $id_cliente = ""){
				
				//$equipamento_id = $request->input( "equipamento_id");
				//$projeto_id = $request->input( "projeto_id");
				$filtro = "";
				
				 //if ( $equipamento_id  != ""){					 
				//				$filtro .= " and p.equipamento_id = ". $equipamento_id;
				 //}
				 //if ( $projeto_id  != ""){					 
				//				$filtro .= " and p.projeto_id = ". $projeto_id;
				// }
				 
				 $order = "id"; $order_type = "asc";
				 
                                 $filtro .= " and p.id_cliente = ". $id_cliente ;
				 
				 $sql = "select p.* from elastic_queries p where 1 = 1 ". $filtro .
						 " order by ".$order. " ".$order_type;
				 
				 $itens = DB::select($sql);
				 

                                 /*
				 for ($i=0; $i < count( $itens) ; $i++) { 
						$item = &$itens[$i];
						$valor = $item->data;
						
						     $reg->id_cliente = ConfigDao::numeroTela(  $item_req->id_cliente , true );  
                                                    $reg->ativo = ConfigDao::numeroTela(  $item_req->ativo , true );  
                                                    $item->data = $this->DataBR($item->data, true); //Colocando como formato BR   
                                                    $reg->id_praca = ConfigDao::numeroTela(  $item_req->id_praca , true );  
                                                                                               //$item->data = $this->DataBR($valor, true); //Colocando como formato BR
                                 }
				 
				 */

				  $saida = array("data"=>$itens, "qtde" => count($itens));
				  return  $saida;
				
			}
			
			public function salvargrid(Request $request){
				
				$hd_json = $request->input( "hd_json");            
				$json_delete = $request->input( "ids_delete_json");
				
				$ret = \App\Http\Dao\ElasticQueriesDao::salvarDadosJson($hd_json, $json_delete, $request->input("id_cliente"));
				
				$itens = $this->gridcad($request, $request->input("id_cliente"));
				
				return array_merge( $ret, $itens);
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


                         $sql = " select count(*) as res from elastic_queries where 1 = 1 ".$filtro ;
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

                         $sql = "select p.* from elastic_queries p where 1 = 1 ". $filtro . " order by ".$order. " ".$order_type .
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
	private function loadRequests(Request $request, \App\SearchQueries &$reg){

                   $reg->id_cliente = $request->input('id_cliente');  
                    $reg->titulo = $request->input('titulo');  
                    $reg->querie = $request->input('querie');  
                    $reg->ativo = $request->input('ativo');  
                    $reg->data = $request->input('data');  
                    $reg->id_praca = $request->input('id_praca');  
		
		
         PostsDao::blankToNull(  $reg );

	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create(Request $request)
	{
		
		$reg = new \App\SearchQueries;

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
        
        
        
        public function getPraca($id_cliente){
            
            
            $DB_MIDIACLIP = \App\Http\Dao\ConfigDao::getSchemaMidiaClip();
            
                              $sql = " select id, convert( descricao using utf8) as descricao from ".$DB_MIDIACLIP.".cadastro_basico where id in (  
                                        select distinct id_praca from ".$DB_MIDIACLIP.".emissora where id in (

                                        select distinct id_filho from ".$DB_MIDIACLIP.".associacao_cadastros 
                                                    where classificacao in ( 'programaxcanal_comunicacao') 
                                                     and tabela_pai ='programa'
                                                    and id_pai in  ( 
                                          select id from ".$DB_MIDIACLIP.".programa where transcricao_ativar = 1 and id in (
                                          select distinct id_filho from ".$DB_MIDIACLIP.".associacao_cadastros 
                                                    where classificacao in ( 'entidadexprograma', 'subentidadexprograma', 'setorxprograma') 
                                                     and tabela_pai ='cliente'
                                                    and id_pai=  ".$id_cliente.    
                                                   " ) ) ) ) ";

                              $lista = DB::select($sql);
                              
                              return $lista;
        }

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
            set_time_limit(300);
		
            $id_cliente = $id;
            
            if ( $id_cliente == ""){
                $id_cliente = " 0 ";
            }
            
            $DB_MIDIACLIP = \App\Http\Dao\ConfigDao::getSchemaMidiaClip();
            $sql  = "select c.id, convert(c.nome using utf8) as nome from ". $DB_MIDIACLIP. ".cliente c where c.id = ". $id;
            $lista_cliente = DB::select($sql);
            
            $lista = DB::select("select * from elastic_queries where id_cliente = ". $id_cliente . " order by id "); 
            $praca_json = \App\Http\Dao\ConfigDao::executeScalar("select praca_json as res from cliente_configuracao where id_cliente = ". $id );
            
            $items_praca = array();
            
            if ( $praca_json != ""){
               $items_praca = json_decode($praca_json);
               
                 //
            
            } else {
                $items_praca = $this->getPraca($id_cliente);
            }
            
              //    $cliente_configuracao->praca_json = json_encode($lista, JSON_UNESCAPED_UNICODE);
            
            $saida = array(
                             "qtde"=> count($lista),
                             "data" => $lista,
                             "praca"=>$items_praca,
                             "cliente" => $lista_cliente[0]
                    
                    );
                         
                         
            return $saida;
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
		   $reg = SearchQueries::find($id);

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
		$reg = SearchQueries::find($id);
		$ret = $reg->delete();
        return array("msg"=>"sucesso", "code" =>  1 , "success" => $ret, "results"=> $reg);
	}
        
        

}
