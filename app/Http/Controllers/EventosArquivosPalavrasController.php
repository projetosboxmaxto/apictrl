<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Service\ErrorsService;

use Illuminate\Http\Request;
use App\EventosArquivosPalavras;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Dao\ImageDao;
use App\Http\Dao\PostsDao;
use DateTime;

class EventosArquivosPalavrasController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(Request $request)
	{
             
                 $user = Auth::user();
                 $header = $request->header(); 
                 
                 $id_user = join( $header["apiauth"], ",");
                         
                // print_r( $header );die("  aaa ". join( $header["apiauth"], ",") );
                 
              //$id_user =  Session::get('user.myauth');
               $DB_MIDIACLIP = config("app.DB_MIDIACLIP");
               
               
	      $filtro = "";
              $order = " id "; $order_type = "desc";
              
              
                  $dt_inicio = $request->input("dt_inicio");
            $dt_fim = $request->input("dt_fim");
            $id_cliente = $request->input("id_cliente");
            $id_programa = $request->input("id_programa");
            $id_emissora = $request->input("id_emissora");
            
            
            if ( $dt_inicio == "undefined"){
                $dt_inicio = "";
            }
            
            if ( $dt_fim == "undefined"){
                $dt_fim = "";
            }
            
            if ( $dt_inicio == "" || $dt_fim == ""){
                $dt_fim = \App\Http\Dao\ConfigDao::executeScalar("select max(data) as res from eventos ");
                
                if ( $dt_fim == ""){
                    $dt_fim = date("Y-m-d");
                }
                
                $date_fim = new DateTime($dt_fim);
                
                $dt_fim = $date_fim->format("Y-m-d");
                //$date_fim->modify("-7 days");
                
                $dt_inicio = $date_fim->format("Y-m-d");
                
            }
            
            $filtro = "";
            $filtro .= " and ev.tipo='pai' and p.id_dicionario_tag is not null and p.status = 1 ";
             
            if ( $dt_inicio != ""){
                $filtro .= " and ev.data >='".$dt_inicio." 00:00:00'";
                $filtro .= " and p.data >='".$dt_inicio." 00:00:00'";
            }
             if ( $dt_fim != ""){
                $filtro .= " and ev.data <='".$dt_fim." 23:59:59'";
                $filtro .= " and p.data <='".$dt_fim." 23:59:59'";
            }
            
            if ( $id_programa != "" && is_numeric($id_programa)){
                $filtro .= " and ev.id_programa = ". $id_programa;
            }
            
            if ( trim($id_emissora) != "" && $id_emissora != "-1" && $id_emissora != "0"){
                    $filtro .= " and ev.id_emissora = " . $id_emissora;
            }
            
            $palavra = trim( str_replace("  "," ", $request->input("palavra")));
                 
                 if ( $palavra != ""){
                     $arp = explode(" ",  $palavra);
                     
                     $filtro .= " and ( ";
                     for ( $e = 0; $e < count($arp); $e++){
                         $str_palavra = str_replace("'","''", $arp[$e]);
                         
                         if ( $e > 0 ){
                             $filtro .= " or ";
                         }
                         
                         $filtro .= " p.palavra like '%".$str_palavra."%'";
                     }
                     
                     $filtro .= " ) ";
                     
                 }
              
              if ( $request->input( "filtro")  != ""){
                         	$str_filt = str_replace("'","''", $request->input( "filtro") );
                        	$filtro .= " and ( nome like '%".$str_filt."%' or email like '%".$str_filt."%' ) ";
               }
			
                    if ( $request->input("cliente_nome") != ""){
                     $filtro .= " and c.nome like '%".$request->input("cliente_nome")."%' ";
                 }
               
			   
		if ( $id_cliente != "" && is_numeric($id_cliente)){
                          $filtro .= " and p.id_cliente = ". $id_cliente;
                }
               
            $filtro .= " and ( ifNull(ev.status, 1 ) = 1 or (  ifNull(ev.status, 1 ) = 2 and ev.bloqueado_por_id = ".$id_user.")  ) "; //Não pode ser bloqueado..
            
               
                $sql = "select p.*, ea.nome as nome_arquivo, pr.nome as nome_programa, c.nome as nome_cliente, ea.hora_inicio, t.descricao as nome_midia, '' as blnk "
                        . " from eventos_arquivos_palavras p "
                        . " left join eventos ev on ev.id = p.id_evento "
                        . " left join eventos_arquivos ea on ea.id = p.id_evento_arquivo "
                        . " left join ".$DB_MIDIACLIP.".programa pr on pr.id = ev.id_programa "
                        . " left join ".$DB_MIDIACLIP.".emissora emi on emi.id = ev.id_emissora "
                        . " left join ".$DB_MIDIACLIP.".cliente c on c.id = p.id_cliente "
                        . " left join ".$DB_MIDIACLIP.".cadastro_fixo t on t.id = emi.id_veiculo "
                        . " where 1 = 1 ". $filtro . " order by ".$order. " ".$order_type ;
                $itens = DB::select($sql);
                
                   // 
/*
                for ($i=0; $i < count( $itens) ; $i++) { 
                  // $list[$i]->blnk = "<button class=\"btn btn-lg btn-info tab_row_button\"><i class=\"fa fa-edit\"></i><span style=\"display:none\">". $list[$i]->id. " </span></button>";
                    $titulo = $itens[$i]->nome;

                    $itens[$i]->user_link = "<a href=\"#\" class=\"botao_editar_grid\" idref=\"".$itens[$i]->id."\" >".
                                        $titulo."</a>";

                    $data =  $itens[$i]->created_at;


                    $stat_publicado  ="";
                    $itens[$i]->data = "<span style=\"display:none\">". $data . "</span>" . $this->DataBR(  $data, true );

					$itens[$i]->blnk = "<a href=\"#\" class=\"botao_editar_grid a_edit\" idref=\"".$itens[$i]->id."\" >"
                            . "<i class=\"fa fa-edit\"></i></a>";
					
                }
 * */

                
                $saida = array(
                             "qtde"=> count($itens),
                             "data" => $itens, "sql"=> $sql , "dt_inicio"=>$dt_inicio, "dt_fim" =>$dt_fim);
                         
                         
                return $saida;
	}
	
	/*
	            Route::get('/eventos_arquivos_palavras', 'EventosArquivosPalavrasController@index');
                Route::get('/eventos_arquivos_palavras/{id}', 'EventosArquivosPalavrasController@show');
                Route::put('/eventos_arquivos_palavras/{id}', 'EventosArquivosPalavrasController@update');
                Route::post('/eventos_arquivos_palavras', 'EventosArquivosPalavrasController@create');
                Route::delete('/eventos_arquivos_palavras/{id}', 'EventosArquivosPalavrasController@destroy');
				
				
	            Route::get('/eventos_arquivos_palavras/gridcad', 'EventosArquivosPalavrasController@gridcad');
	            Route::post('/eventos_arquivos_palavras/salvargrid', 'EventosArquivosPalavrasController@salvargrid');
				*/

        public function indicastatus(Request $request)
	{
            
            
            $id = $request->input("id");
            $status = $request->input("status");
            
            $sql  = " update eventos_arquivos_palavras set status = ". $status. " where id = ". $id;
            DB::statement($sql);
            
            return array("msg"=>"Sucesso!", "id"=>$id);
            
        }
        function encrypt( $senha ){
               return md5( env("CRYPT_PASS") . $senha);
            //  return Hash::make( $senha);
        }
		public function testheader(Request $request){

				  $o_auth_header  = $GLOBALS["auth_header"] ;
				  return array("msg"=>"Teste", "header" => $o_auth_header );
		}
		
			
			 public function gridcad(Request $request){
				
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
				 
				 
				 $sql = "select p.* from eventos_arquivos_palavras p where 1 = 1 ". $filtro .
						 " order by ".$order. " ".$order_type;
				 
				 $itens = DB::select($sql);
				 

				 for ($i=0; $i < count( $itens) ; $i++) { 
						$item = &$itens[$i];
						$valor = $item->data;
						
						     $item->data = $this->DataBR($item->data, true); //Colocando como formato BR   
     $reg->id_evento = ConfigDao::numeroTela(  $item_req->id_evento , true );  
     $reg->id_evento_arquivo = ConfigDao::numeroTela(  $item_req->id_evento_arquivo , true );  
     $reg->id_cliente = ConfigDao::numeroTela(  $item_req->id_cliente , true );  
     $reg->cita_diretamente = ConfigDao::numeroTela(  $item_req->cita_diretamente , true );  
     $reg->tempo_seg = ConfigDao::numeroTela(  $item_req->tempo_seg , true );  
     $reg->id_dicionario_tag = ConfigDao::numeroTela(  $item_req->id_dicionario_tag , true );  
     $reg->status = ConfigDao::numeroTela(  $item_req->status , true );  
						//$item->data = $this->DataBR($valor, true); //Colocando como formato BR
				 }
				 
				 

				  $saida = array("data"=>$itens, "qtde" => count($itens));
				  return  $saida;
				
			}
			
			public function salvargrid(Request $request){
				
				$hd_json = $request->input( "hd_json");            
				$json_delete = $request->input( "ids_delete_json");
				
				$ret = \App\Http\Dao\EventosArquivosPalavrasDao::salvarDadosJson($hd_json, $json_delete);
				
				$itens = $this->gridcad($request);
				
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


                         $sql = " select count(*) as res from eventos_arquivos_palavras where 1 = 1 ".$filtro ;
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

                         $sql = "select p.* from eventos_arquivos_palavras p where 1 = 1 ". $filtro . " order by ".$order. " ".$order_type .
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
	private function loadRequests(Request $request, \App\EventosArquivosPalavras &$reg){

          $reg->data = $request->input('data');  
  $reg->id_evento = $request->input('id_evento');  
  $reg->id_evento_arquivo = $request->input('id_evento_arquivo');  
  $reg->id_cliente = $request->input('id_cliente');  
  $reg->cita_diretamente = $request->input('cita_diretamente');  
  $reg->palavra = $request->input('palavra');  
  $reg->tempo = $request->input('tempo');  
  $reg->tempo_seg = $request->input('tempo_seg');  
  $reg->id_dicionario_tag = $request->input('id_dicionario_tag');  
  $reg->status = $request->input('status');  
  $reg->operador = $request->input('operador');  
  $reg->id_operador = $request->input('id_operador');  
  $reg->id_materia_radiotv_jornal = $request->input('id_materia_radiotv_jornal');  
		
		
         PostsDao::blankToNull(  $reg );

	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create(Request $request)
	{
		
		$reg = new \App\EventosArquivosPalavras;

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
		$reg = EventosArquivosPalavras::find($id);
                $reg_arquivo = \App\EventosArquivos::find($reg->id_evento_arquivo);
                
                $url_base = env("PATH_URL_VIDEOS");
                
                $reg->url_load = $url_base. $reg_arquivo->path;
                $reg->tempo_seg = \App\Http\Service\UtilService::time_to_seconds2 ($reg->tempo );
                $reg->nome_arquivo = $reg_arquivo->nome;
                
                if ( is_null( $reg->trecho ) ){
                    $reg->trecho = $reg_arquivo->texto;
                }

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
		   $reg = EventosArquivosPalavras::find($id);

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
		$reg = EventosArquivosPalavras::find($id);
		$ret = $reg->delete();
        return array("msg"=>"sucesso", "code" =>  1 , "success" => $ret, "results"=> $reg);
	}
        
        

}
