<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Service\ErrorsService;

use Illuminate\Http\Request;
use App\AgrupamentoNotificacoes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Dao\ImageDao;
use App\Http\Dao\PostsDao;
use DateTime;

class AgrupamentoNotificacoesController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(Request $request)
	{
	      $filtro = "";
              $order = " id "; $order_type = "desc";
              
              
                 $user = Auth::user();
                 $header = $request->header(); 
                 $id_user = join( $header["apiauth"], ",");
              
              $DB_MIDIACLIP = \App\Http\Dao\ConfigDao::getSchemaMidiaClip();
              
              if ( $request->input( "filtro")  != ""){
                         	$str_filt = str_replace("'","''", $request->input( "filtro") );
                        	$filtro .= " and ( palavras like '%".$str_filt."%'  ) ";
               }
               
               $dt_inicio = $request->input("dt_inicio");
               $dt_fim = $request->input("dt_fim");
               $id_programa = $request->input("id_programa");
               $id_emissora = $request->input("id_emissora");
               $acima_de =  $request->input("acima_de");
               $limit =  $request->input("limit");
               $tinder =  $request->input("tinder");
               $status =  $request->input("status");
               $id_evento_arquivo =  $request->input("id_evento_arquivo");
               $status_evento =  $request->input("status_evento");
               
                 if ( $dt_inicio == "" || $dt_fim == ""){
                        $dt_fim = \App\Http\Dao\ConfigDao::executeScalar("select max(data) as res from agrupamento_notificacoes ");

                        if ( $dt_fim == ""){
                            $dt_fim = date("Y-m-d");
                        }

                        $date_fim = new DateTime($dt_fim);

                        $dt_fim = $date_fim->format("Y-m-d");
                        //$date_fim->modify("-7 days");

                        $dt_inicio = $date_fim->format("Y-m-d");

                    }
                    
                       
                if ( $dt_inicio != "" &&  $id_evento_arquivo == "" ){
                    $filtro .= " and p.data >='".$dt_inicio." 00:00:00'";
                }
                 if ( $dt_fim != "" &&  $id_evento_arquivo == ""){
                    $filtro .= " and p.data <='".$dt_fim." 23:59:59'";
                }
                if ( $id_evento_arquivo != ""){
                    $filtro .= " and p.id_evento_arquivo = ". $id_evento_arquivo;
                }
                
                if ( trim($id_programa) != "" && $id_programa != "-1" && $id_programa != "0"){
                    $filtro .= " and p.id_programa = " . $id_programa;
                }
                
                if ( trim($id_emissora) != "" && $id_emissora != "-1" && $id_emissora != "0"){
                    $filtro .= " and p.id_emissora = " . $id_emissora;
                }
                
                
                
                // $filtro .= " and ifNull(p.status, 1) in ( 1 , 3 ) "; //o 2 eu deixei como descartada..
                 
                // $filtro .= " and ifNull(eve.status, 1 ) in ( 1, 2 )  ";
                
                if ( $status == "" && $status_evento == ""){
                          if ( $tinder == ""){
                                $filtro .= " and ifNull(p.status, 1) in ( 1 , 3 , 4 ) "; //o 2 eu deixei como descartada..
                                $filtro .= " and ifNull(eve.status, 1 ) in ( 1, 2 )  "; //não usado ou bloqueado - em uso..
                           }
                              /* -- Status de agrupamento
                                   1 - não processado,
                                   2 - Descartado,
                                   3 - Em visualização
                                   4 - Permitido para uso.


                                   */
                           if ( $tinder == "1"){
                                $filtro .= " and ifNull(p.status, 1) in ( 1 ) ";  //pro tinder vou pegar apenas o que ninguém estiver mexendo...
                                $filtro .= " and ifNull(eve.status, 1 ) in ( 1 ) ";
                                $filtro .= " and ( ifNull(arq.status,1) = 1 or ( ifNull(arq.status,1) = 2 and arq.bloqueado_por_id = ". $id_user . " ) ) ";
                           }
                    
                } else{
                      $filtro .= " and ifNull(p.status, 1) in ( ".$status." ) ";
                      $filtro .= " and ifNull(eve.status, 1 ) in ( ".$status_evento." )  ";
                }
            
                 
                // $filtro .= " and ( ifNull(eve.status, 1 ) = 1 or (  ifNull(eve.status, 1 ) = 2 and eve.bloqueado_por_id = ".$id_user.")  ) ";
                 
                 $palavra = trim( str_replace("  "," ", $request->input("palavra")));
                 
                 if ( $palavra != ""){
                     $arp = explode(" ",  $palavra);
                     
                     $filtro .= " and ( ";
                     for ( $e = 0; $e < count($arp); $e++){
                         $str_palavra = str_replace("'","''", $arp[$e]);
                         
                         if ( $e > 0 ){
                             $filtro .= " or ";
                         }
                         
                         $filtro .= " p.palavras like '%".$str_palavra."%'";
                     }
                     
                     $filtro .= " ) ";
                     
                 }
                 
                 if ( $request->input("cliente_nome") != ""){
                     $filtro .= " and p.clientes like '%".$request->input("cliente_nome")."%' ";
                 }
                 
                   
                 if ( $acima_de != ""){
                     $filtro .= " and p.id > ". $acima_de;
                 }
                 
                 if (   $request->input("order") != "" ){
                     $order = $request->input("order");
                 }
                 
                 if (   $request->input("order_type") != "" ){
                     $order_type = $request->input("order_type");
                 }
               
                $sql = "select p.*, arq.id_evento, pro.nome as programa, emi.nome as emissora, '' as blnk, '' as user_link, concat('row_data_', p.id) as id_row  "
                        . " from agrupamento_notificacoes p "
                        . " inner join eventos_arquivos arq on arq.id = p.id_evento_arquivo "
                        . " inner join eventos eve on eve.id = arq.id_evento "
                        . " left join ".$DB_MIDIACLIP.".programa pro on pro.id = p.id_programa "
                        . " left join ".$DB_MIDIACLIP.".emissora emi on emi.id = p.id_emissora "
                        . " where 1 = 1 ". $filtro . " order by ".$order. " ".$order_type ;
                
                
                if ( $limit != ""){
                    $sql .= " limit 0, ". $limit;
                }
                $itens = DB::select($sql);
                
                
               
                $saida = array(
                             "qtde"=> count($itens),
                             "data" => $itens, "sql"=> $sql , "dt_inicio"=>$dt_inicio, "dt_fim" =>$dt_fim);
                         
                         
                return $saida;
	}
        
        
        public function index2(Request $request)
	{
	      $filtro = "";
              $order = " id "; $order_type = "desc";
              
     
              
              
                 $user = Auth::user();
                 $header = $request->header(); 
                 $id_user = join( $header["apiauth"], ",");
              
              $DB_MIDIACLIP = \App\Http\Dao\ConfigDao::getSchemaMidiaClip();
              
              if ( $request->input( "filtro")  != ""){
                         	$str_filt = str_replace("'","''", $request->input( "filtro") );
                        	$filtro .= " and ( palavras like '%".$str_filt."%'  ) ";
               }
               
               $dt_inicio = $request->input("dt_inicio");
               $dt_fim = $request->input("dt_fim");
               $id_programa = $request->input("id_programa");
               $id_emissora = $request->input("id_emissora");
               $page = $request->input( "draw");
               $pagesize = $request->input( "length");  
               $length = $request->input("length");
               $parameteres = (object)$request->all();
               $inicio = $request->input("start");
               $acima_de =  $request->input("acima_de");
               $tinder =  $request->input("tinder");
               $status =  $request->input("status");
               $status_evento =  $request->input("status_evento");
               
               
              $order = "id"; // $request->input("order");
              $order_type = "desc"; //$request->input("order_type");
               
              if ( ! is_null($parameteres)){
                   $colunas_grid = $parameteres->columns;

                    if (  is_array($request->input("order"))){
                                   $order_p = $request->input("order");
                                   $coluna_indice =  $order_p[0]["column"];
                                   
                                   $order = $colunas_grid[$coluna_indice]["data"];
                                   $order_type  = $order_p[0]["dir"] ;
                    }
              }
              if ( $order_type == ""){
                         	$order_type = "asc";
              }
               
               if ( $pagesize == ""){
                   $pagesize = 10;
               }
               if ( $page == ""){
                   $page = 1;
               }
               
               
                 if ( $dt_inicio == "" || $dt_fim == ""){
                        $dt_fim = \App\Http\Dao\ConfigDao::executeScalar("select max(data) as res from agrupamento_notificacoes ");

                        if ( $dt_fim == ""){
                            $dt_fim = date("Y-m-d");
                        }

                        $date_fim = new DateTime($dt_fim);

                        $dt_fim = $date_fim->format("Y-m-d");
                        //$date_fim->modify("-7 days");

                        $dt_inicio = $date_fim->format("Y-m-d");

                    }
                    
                       
                if ( $dt_inicio != ""){
                    $filtro .= " and p.data >='".$dt_inicio." 00:00:00'";
                }
                
                if ( trim($id_programa) != "" && $id_programa != "-1" && $id_programa != "0"){
                    $filtro .= " and p.id_programa = " . $id_programa;
                }
                
                if ( trim($id_emissora) != "" && $id_emissora != "-1" && $id_emissora != "0"){
                    $filtro .= " and p.id_emissora = " . $id_emissora;
                }
                
                
                 if ( $dt_fim != ""){
                    $filtro .= " and p.data <='".$dt_fim." 23:59:59'";
                }
                
                  if ( $status == "" && $status_evento == ""){
                                if ( $tinder == ""){
                                     $filtro .= " and ifNull(p.status, 1) in ( 1 , 3 , 4 ) "; //o 2 eu deixei como descartada..
                                     $filtro .= " and ifNull(eve.status, 1 ) in ( 1, 2 )  "; //não usado ou bloqueado - em uso..
                                }
                                   /* -- Status de agrupamento
                                        1 - não processado,
                                        2 - Descartado,
                                        3 - Em visualização
                                        4 - Permitido para uso.


                                        */
                                if ( $tinder == "1"){
                                     $filtro .= " and ifNull(p.status, 1) in ( 1 ) ";  //pro tinder vou pegar apenas o que ninguém estiver mexendo...
                                     $filtro .= " and ifNull(eve.status, 1 ) in ( 1 ) ";
                                }
                  } else{
                      $filtro .= " and ifNull(p.status, 1) in ( ".$status." ) ";
                      $filtro .= " and ifNull(eve.status, 1 ) in ( ".$status_evento." )  ";
                  }
                // $filtro .= " and ( ifNull(eve.status, 1 ) = 1 or (  ifNull(eve.status, 1 ) = 2 and eve.bloqueado_por_id = ".$id_user.")  ) ";
                 
                 $palavra = trim( str_replace("  "," ", $request->input("palavra")));
                 
                 if ( $palavra != ""){
                     $arp = explode(" ",  $palavra);
                     
                     $filtro .= " and ( ";
                     for ( $e = 0; $e < count($arp); $e++){
                         $str_palavra = str_replace("'","''", $arp[$e]);
                         
                         if ( $e > 0 ){
                             $filtro .= " or ";
                         }
                         
                         $filtro .= " p.palavras like '%".$str_palavra."%'";
                     }
                     
                     $filtro .= " ) ";
                     
                 }
                 
                 if ( $request->input("cliente_nome") != ""){
                     $filtro .= " and p.clientes like '%".$request->input("cliente_nome")."%' ";
                 }
                 
                 if ( $acima_de != ""){
                     $filtro .= " and p.id > ". $acima_de;
                 }
                 
                  $sql = " select count(*) as res from agrupamento_notificacoes p"
                          . " inner join eventos_arquivos arq on arq.id = p.id_evento_arquivo "
                          . " inner join eventos eve on eve.id = arq.id_evento "
                          . "  where 1 = 1 ".$filtro ;
                  $total_itens = $this->executeScalar(  $sql );
                  
                  if ( $inicio > $total_itens ){
                      $inicio = 0;
                      $page = 1;
                  }
                 
                //$inicio = 0; $fim = 0;
                //$this->SetaRsetPaginacao($pagesize, $page,$total_itens, $inicio, $fim);
                  $fim = $inicio + $pagesize;
               
                $sql = "select p.*, ifNull(eve.status, 1 ) as status_evento, arq.id_evento, pro.nome as programa, emi.nome as emissora,"
                        . " '' as blnk, '' as user_link, concat('row_data_', p.id) as id_row  "
                        . " from agrupamento_notificacoes p "
                        . " inner join eventos_arquivos arq on arq.id = p.id_evento_arquivo "
                        . " inner join eventos eve on eve.id = arq.id_evento "
                        . " left join ".$DB_MIDIACLIP.".programa pro on pro.id = p.id_programa "
                        . " left join ".$DB_MIDIACLIP.".emissora emi on emi.id = p.id_emissora "
                        . " where 1 = 1 ". $filtro . 
                        " order by "
                        . $order. " ".
                         $order_type
                         .$this->get_limit_sql(  $inicio,  $pagesize) ;
                
                $itens = DB::select($sql);
                
                
               /*  $saida = array("page"=>$page, "pagesize" => $pagesize, "order"=>$order,
                          "total"=>$total_itens, "total_itens"=> $total_itens,
                          "order_type"=> $order_type, "itens" =>  $itens); */
                $saida = array(
                             "qtde"=> $total_itens, "total"=> $total_itens, 
                    //"draw" => $page,
                             "data" => $itens, 
                    // "sql"=> $sql , 
                     "dt_inicio"=>$dt_inicio, 
                    "dt_fim" =>$dt_fim, "order" => $order, 
                    "pagging" => [ "inicio"=>$inicio, "pagesize"=>$pagesize, "fim" => $fim, "page"=>$page] ,
                    "parameters" => $parameteres
                        );
                         
                         
                return $saida;
	}
        
        public function getStatus(Request $request){
            
              $ids = $request->input("ids");
              $DB_MIDIACLIP = \App\Http\Dao\ConfigDao::getSchemaMidiaClip();
              
              $filtro = " and p.id in ( ". $ids." ) ";
              
                  $sql = "select p.id, ifNull(eve.status, 1 ) as status, p.palavras, p.status as status_notificacao, eve.bloqueado_por_id , eve.id as id_evento "
                        . " from agrupamento_notificacoes p "
                        . " inner join eventos_arquivos arq on arq.id = p.id_evento_arquivo "
                        . " inner join eventos eve on eve.id = arq.id_evento "
                        . " left join ".$DB_MIDIACLIP.".programa pro on pro.id = p.id_programa "
                        . " left join ".$DB_MIDIACLIP.".emissora emi on emi.id = p.id_emissora "
                        . " where 1 = 1 ". $filtro . " order by p.id asc " ;
                $itens = DB::select($sql);
              
              
                $saida = array(
                             "qtde"=> count($itens),
                             "data" => $itens);
                         
                         
                return $saida;
        }
	
	/*
	            Route::get('/api/agrupamento_notificacoes', 'AgrupamentoNotificacoesController@index');
                Route::get('/api/agrupamento_notificacoes/{id}', 'AgrupamentoNotificacoesController@show');
                Route::put('/api/agrupamento_notificacoes/{id}', 'AgrupamentoNotificacoesController@update');
                Route::post('/api/agrupamento_notificacoes', 'AgrupamentoNotificacoesController@create');
                Route::delete('/api/agrupamento_notificacoes/{id}', 'AgrupamentoNotificacoesController@destroy');
				
				Route::resource('users', 'UserAPIController');
				
				*/

        
        function encrypt( $senha ){
               return md5( env("CRYPT_PASS") . $senha);
            //  return Hash::make( $senha);
        }
		public function testheader(Request $request){

				  $o_auth_header  = $GLOBALS["auth_header"] ;
				  return array("msg"=>"Teste", "header" => $o_auth_header );
		}

                
                
        public function indicastatus(Request $request)
	{
            
            
            $id = $request->input("id");
            $status = $request->input("status");
            
            $sql  = " update agrupamento_notificacoes set status = ". $status. " where id = ". $id;
            DB::statement($sql);
            
            //{"arquivos":[{"id_evento_arquivo_palavra":23817,"id_cliente":1201245,"palavra":"m\u00fasica","nome_cliente":"SECULT","tempo_seg":"63.000"},{"id_evento_arquivo_palavra":23823,"id_cliente":1201291,"palavra":"m\u00fasica","nome_cliente":"FUNCEB","tempo_seg":"63.000"}]}
            $sql  = " update eventos_arquivos_palavras set status = ". $status. " where id_notificacao_agrupamento = ". $id;
            DB::statement($sql);
            
            
            return array("msg"=>"Sucesso!", "id"=>$id);
            
        }
        
         public function indicastatusEventoGlobal(Request $request)
	 {
            
            $header = $request->header(); 
                 
            $id_user = join( $header["apiauth"], ",");
            
            $id = $request->input("id");
            $status = $request->input("status");
            $id_operador = $request->input("id_operador");
            
            $sql = "select id_evento as res from eventos_arquivos where id in ( 
                              select id_evento_arquivo from agrupamento_notificacoes where id = ".$id.") ";
            
        
            $id_evento = \App\Http\Dao\ConfigDao::executeScalar($sql);
            
            return \App\Http\Service\EventoService::mudaStatus($id_evento, $id_operador, $status);
            
                   
            
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


                         $sql = " select count(*) as res from agrupamento_notificacoes where 1 = 1 ".$filtro ;
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

                         $sql = "select p.* from agrupamento_notificacoes p where 1 = 1 ". $filtro . " order by ".$order. " ".$order_type .
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
	private function loadRequests(Request $request, \App\AgrupamentoNotificacoes &$reg){

          $reg->dia = $request->input('dia');  
  $reg->id_programa = $request->input('id_programa');  
  $reg->id_emissora = $request->input('id_emissora');  
  $reg->palavras = $request->input('palavras');  
  $reg->clientes = $request->input('clientes');  
  $reg->hora_inicio_seg = $request->input('hora_inicio_seg');  
  $reg->hora_fim_seg = $request->input('hora_fim_seg');  
  $reg->tempo_seg = $request->input('tempo_seg');  
  $reg->json = $request->input('json');  
  $reg->data = $request->input('data');  
  $reg->hora_inicio = $request->input('hora_inicio');  
  $reg->hora_fim = $request->input('hora_fim');  
  $reg->id_evento_arquivo = $request->input('id_evento_arquivo');  
		
		
         PostsDao::blankToNull(  $reg );

	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create(Request $request)
	{
		
		$reg = new \App\AgrupamentoNotificacoes;

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
		
		    $reg = AgrupamentoNotificacoes::find($id);
                    $reg_arquivo = \App\EventosArquivos::find($reg->id_evento_arquivo);
                
                    $url_base = env("PATH_URL_VIDEOS");

                    $reg->url_load = $url_base. $reg_arquivo->path;
                    $reg->tempo_seg = \App\Http\Service\UtilService::time_to_seconds2 ($reg->tempo );
                    $reg->nome_arquivo = $reg_arquivo->nome;
                    $reg->trecho = 
                            \App\Http\Service\EventoArquivoService::getTextoFromJson(
                            \App\Http\Service\EventoArquivoService::obtemJsonCorte($reg->id_evento_arquivo, $reg->tempo_seg, $reg->tempo_seg + 60));
                  //  if ( is_null( $reg->trecho ) ){
                  //      $reg->trecho = $reg_arquivo->texto;
                  //  }
                    
                    $reg->tempos = \App\Http\Dao\EventosArquivosPalavrasDao::getListReduzido(" and id_notificacao_agrupamento = ".$id );

                return array( "code" =>  1,  "results"=> $reg, "item"=> $reg, "url_load" =>  $reg->url_load );
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
		   $reg = AgrupamentoNotificacoes::find($id);

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
		$reg = AgrupamentoNotificacoes::find($id);
		$ret = $reg->delete();
        return array("msg"=>"sucesso", "code" =>  1 , "success" => $ret, "results"=> $reg);
	}
        
        

}
