<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Service\ErrorsService;

use Illuminate\Http\Request;
use App\MateriaRascunho;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Dao\ImageDao;
use App\Http\Dao\PostsDao;
use DateTime;

class MateriaRascunhoController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(Request $request)
	{
	      $filtro = "";
              $order = " id "; $order_type = "desc";
              
              $DB_MIDIACLIP = \App\Http\Dao\ConfigDao::getSchemaMidiaClip();
              $all = $request->all();
               $id_emissora = $request->input("id_emissora");
              
              if ( $request->input( "filtro")  != ""){
                         	$str_filt = str_replace("'","''", $request->input( "filtro") );
                        	$filtro .= " and ( nome like '%".$str_filt."%' or email like '%".$str_filt."%' ) ";
               }
               
               
              if ( $request->input( "id_projeto")  != ""){
                         $filtro .= " and p.id_projeto = ".  $request->input( "id_projeto");
              } 
              
              if ( trim($request->input( "id_programa"))  != "" && $request->input( "id_programa") != "-1"){
                         $filtro .= " and p.id_programa = ".  $request->input( "id_programa");
              } 
              
          
                
                if ( trim($id_emissora) != "" && $id_emissora != "-1" && $id_emissora != "0"){
                    $filtro .= " and ev.id_emissora = " . $id_emissora;
                }
              
               if ( $request->input( "cliente_nome")  != "" && $request->input( "cliente_nome")  != "undefined" ){
                         $filtro .= " and p.cliente_list like '%". str_replace("'","''",  $request->input( "cliente_nome"))."%' ";
              } 
                  if ( $request->input( "status")  != "" & !is_null( $request->input( "status")) ){
                         $filtro .= " and p.status = ". $request->input( "status")  ." ";
              } 
              
              
              
            $dt_inicio = $request->input("dt_inicio");
            $dt_fim = $request->input("dt_fim");
            if ( $dt_inicio == "undefined"){
                $dt_inicio = "";
            }
            
            if ( $dt_fim == "undefined"){
                $dt_fim = "";
            }
            
            if ( $dt_inicio == "" || $dt_fim == ""){
                $dt_fim = \App\Http\Dao\ConfigDao::executeScalar("select max(data) as res from materia_rascunho ");
                
                if ( $dt_fim == ""){
                    $dt_fim = date("Y-m-d");
                }
                
                $date_fim = new DateTime($dt_fim);
                
                $dt_fim = $date_fim->format("Y-m-d");
                $date_fim->modify("-10 days");
                
                $dt_inicio = $date_fim->format("Y-m-d");
                
            }
            
            if ( $dt_inicio != ""){
                $filtro .= " and p.data >='".$dt_inicio." 00:00:00'";
            }
             if ( $dt_fim != ""){
                $filtro .= " and p.data <='".$dt_fim." 23:59:59'";
            }
               
               //p.*, '' as blnk, '' as user_link
                $sql = "select p.id, p.id_projeto, p.data, p.titulo, p.id_projeto,p.id_operador, p.data_cadastro,"
                        . " p.status, p.id_materia_radiotv_jornal, "
                        . " p.cliente_list, p.id_programa, p.dia, pro.nome as programa_nome, emi.nome as emissora_nome , us.nome as nome_operador, '' as blnk "
                        . " from materia_rascunho p "
                        . " left join eventos ev on ev.id = p.id_projeto "
                        . " left join ".$DB_MIDIACLIP.".programa pro on pro.id = p.id_programa "
                        . " left join ".$DB_MIDIACLIP.".emissora emi on emi.id = ev.id_emissora "
                        . " left join ".$DB_MIDIACLIP.".usuario us on us.id = p.id_operador "
                        . " where 1 = 1 ". $filtro . " order by ".$order. " ".$order_type ;
                $itens = DB::select($sql);
                
                
                $saida = array(
                             "qtde"=> count($itens),
                             "data" => $itens 
                        , "dt_inicio"=>$dt_inicio, "dt_fim" =>$dt_fim,
                        "sql" => $sql,
                    //"all" => $all,
                        "cliente_nome" => $request->input( "cliente_nome")
                        );
                         
                         
                return $saida;
	}
        
        
        
        public function index2(Request $request)
	{
	      $filtro = "";
              
              $DB_MIDIACLIP = \App\Http\Dao\ConfigDao::getSchemaMidiaClip();
              $all = $request->all();
              $id_emissora = $request->input("id_emissora");
              
              $page = $request->input( "draw");
              $pagesize = $request->input( "length");  
              $inicio = $request->input("start");
              $parameteres = (object)$request->all();
              
              if ( $inicio == ""){
                  $inicio = 0;
              }
              if ( $pagesize == ""){
                  $pagesize = 10;
              }
              
              $order = "id"; // $request->input("order");
              $order_type = "desc"; //$request->input("order_type");
               
              if ( ! is_null($parameteres) && property_exists($parameteres, "columns")){
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
              
              
              if ( $request->input( "filtro")  != ""){
                         	$str_filt = str_replace("'","''", $request->input( "filtro") );
                        	$filtro .= " and ( nome like '%".$str_filt."%' or email like '%".$str_filt."%' ) ";
               }
               
               
              if ( $request->input( "id_projeto")  != ""){
                         $filtro .= " and p.id_projeto = ".  $request->input( "id_projeto");
              } 
              
              if ( trim($request->input( "id_programa"))  != "" && $request->input( "id_programa") != "-1"){
                         $filtro .= " and p.id_programa = ".  $request->input( "id_programa");
              } 
              
          
                
                if ( trim($id_emissora) != "" && $id_emissora != "-1" && $id_emissora != "0"){
                    $filtro .= " and ev.id_emissora = " . $id_emissora;
                }
              
               if ( $request->input( "cliente_nome")  != "" && $request->input( "cliente_nome")  != "undefined" ){
                         $filtro .= " and p.cliente_list like '%". str_replace("'","''",  $request->input( "cliente_nome"))."%' ";
              } 
              if ( $request->input( "status")  != "" & !is_null( $request->input( "status")) ){
                         $filtro .= " and p.status = ". $request->input( "status")  ." ";
              } 
              
              
             $sql = " select count(*) as res from materia_rascunho p "
                          . "  where 1 = 1 ".$filtro ;
             $total_itens = $this->executeScalar(  $sql );
             
            if ( $inicio > $total_itens ){
                      $inicio = 0;
                      $page = 1;
            }
            $fim = $inicio + $pagesize;
              
              
            $dt_inicio = $request->input("dt_inicio");
            $dt_fim = $request->input("dt_fim");
            if ( $dt_inicio == "undefined"){
                $dt_inicio = "";
            }
            
            if ( $dt_fim == "undefined"){
                $dt_fim = "";
            }
            
            if ( $dt_inicio == "" || $dt_fim == ""){
                $dt_fim = \App\Http\Dao\ConfigDao::executeScalar("select max(data) as res from materia_rascunho ");
                
                if ( $dt_fim == ""){
                    $dt_fim = date("Y-m-d");
                }
                
                $date_fim = new DateTime($dt_fim);
                
                $dt_fim = $date_fim->format("Y-m-d");
                $date_fim->modify("-10 days");
                
                $dt_inicio = $date_fim->format("Y-m-d");
                
            }
            
            if ( $dt_inicio != ""){
                $filtro .= " and p.data >='".$dt_inicio." 00:00:00'";
            }
             if ( $dt_fim != ""){
                $filtro .= " and p.data <='".$dt_fim." 23:59:59'";
            }
               
               //p.*, '' as blnk, '' as user_link
                $sql = "select p.id, p.id_projeto, p.data, p.titulo, p.id_projeto,p.id_operador, p.data_cadastro,"
                        . " p.status, p.id_materia_radiotv_jornal, "
                        . " p.cliente_list, p.id_programa, p.dia, pro.nome as programa_nome, emi.nome as emissora_nome , us.nome as nome_operador, '' as blnk "
                        . " from materia_rascunho p "
                        . " left join eventos ev on ev.id = p.id_projeto "
                        . " left join ".$DB_MIDIACLIP.".programa pro on pro.id = p.id_programa "
                        . " left join ".$DB_MIDIACLIP.".emissora emi on emi.id = ev.id_emissora "
                        . " left join ".$DB_MIDIACLIP.".usuario us on us.id = p.id_operador "
                        . " where 1 = 1 ". $filtro . " order by ".$order. " ".$order_type 
                        .  $this->get_limit_sql(  $inicio,  $pagesize) ;
                $itens = DB::select($sql);
                
                
                $saida = array(
                             "qtde"=> count($itens),
                              "total"=> $total_itens, 
                             "data" => $itens 
                        , "dt_inicio"=>$dt_inicio, "dt_fim" =>$dt_fim,
                        "sql" => $sql,
                    //"all" => $all,
                        "pagging" => [ "inicio"=>$inicio, "pagesize"=>$pagesize, "fim" => $fim, "page"=>$page] ,
                        "cliente_nome" => $request->input( "cliente_nome")
                        );
                         
                         
                return $saida;
	}
	
	/*
	            Route::get('/api/materia_rascunho', 'MateriaRascunhoController@index');
                Route::get('/api/materia_rascunho/{id}', 'MateriaRascunhoController@show');
                Route::put('/api/materia_rascunho/{id}', 'MateriaRascunhoController@update');
                Route::post('/api/materia_rascunho', 'MateriaRascunhoController@create');
                Route::delete('/api/materia_rascunho/{id}', 'MateriaRascunhoController@destroy');
				
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

        public function completa_materia($id){
            
                       $url_sistema = env("PATH_SISTEMA_MIDIACLIP");
                       $msg = "";
                       if ( $id != "-1"){
                       $msg =    \App\Http\Service\UtilService::recebe_html($url_sistema."importacao/handlerMateriaRtv.aspx?acao=completa_materia&id=".$id);
                       
                       
                       } else {
                           $ls = DB::select("select id_materia_radiotv_jornal as id from materia_rascunho where status = 1 ");
                           
                           for ( $i = 0; $i < count($ls); $i++ ){
                               $item = $ls[$i];
                               
                                $msg .= "<br>".
                               \App\Http\Service\UtilService::recebe_html($url_sistema."importacao/handlerMateriaRtv.aspx?acao=completa_materia&id=".$item->id );
                               
                           }
                       }
                       die($msg);
        }
        
        public function remove_notificacoes($id){
            
                       $msg =  \App\Http\Service\MateriaRascunhoService::removeNotificacoes($id);
                       die($msg);
        }
        
        public function get_apresentador(Request $request){
            
            $id_programa = $request->input("id_programa");
            
            
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


                         $sql = " select count(*) as res from materia_rascunho where 1 = 1 ".$filtro ;
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

                         $sql = "select p.* from materia_rascunho p where 1 = 1 ". $filtro . " order by ".$order. " ".$order_type .
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
	private function loadRequests(Request $request, \App\MateriaRascunho &$reg){

            $reg->id_projeto = $request->input('id_projeto');  
           
            if ( $reg->id_projeto != ""){
                     $reg->data = \App\Http\Dao\ConfigDao::executeScalar("select data as res from eventos where id = ". $reg->id_projeto);
                                //$request->input('data');
            }
            
            $reg->titulo = $request->input('titulo');  
            $reg->cliente_list = $request->input('cliente_list');  
            $reg->ids_arquivos = $request->input('ids_arquivos');  
            $reg->dados_materia = $request->input('dados_materia');  
            $reg->id_programa = $request->input('id_programa');  
            $reg->dia = $request->input('dia');  
            $reg->id_operador = $request->input('id_operador');  
            
            
            if ( $reg->data_cadastro == ""){
                $reg->data_cadastro = \App\Http\Service\UtilService::getCurrentBDdate();
            }

             //$reg->data_cadastro = $request->input('data_cadastro');  
            $reg->status = $request->input('status');  
            
           
            $reg->id_materia_radiotv_jornal = $request->input('id_materia_radiotv_jornal');  


         PostsDao::blankToNull(  $reg );
         
          if ( $reg->status == ""){
                $reg->status = 0;
            }
            

	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create(Request $request)
	{
		
		$reg = new \App\MateriaRascunho;
                
                $id = $request->input("id");
                
                if ( $id != ""){
		     $reg = MateriaRascunho::find($id);
                }

		$this->loadRequests($request, $reg);
                
		
		$ret = $reg->save();

		$msg = "sucesso!"; $code = 1;
		if (! $ret  ){
                            $code = 0;
                            $msg = "erro";
		}

                \App\Http\Service\MateriaRascunhoService::removeNotificacoes($reg->id);

		return array("msg"=>$msg, "code" =>  $code , "success" => $ret, "data"=> $reg,
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
		
		   $reg = MateriaRascunho::find($id);
                   $obj_json = json_decode( $reg->dados_materia );
                   
                   $obj_json->id_rascunho = $id;
                   $obj_json->id_materia_radiotv_jornal = $reg->id_materia_radiotv_jornal;
                   $reg->obj_json = $obj_json;

                   return array( "code" =>  1,  "results"=> $reg, "item"=> $reg);
	}
        
        
        public function show_materia_gerada($id)
	{
            
             $url_sistema = env("PATH_SISTEMA_MIDIACLIP");
             
             if ( $url_sistema != ""){
                     \App\Http\Service\UtilService::recebe_html($url_sistema."importacao/handlerMateriaRtv.aspx?acao=completa_materia&id=".$id);
                         // UtilService::recebe_html($url_sistema."importacao/handlerMateriaRtv.aspx?acao=completa_materia&id=".$id_materia);
                 
             }
		
		   $reg = \App\Http\Service\MateriaRascunhoService::getMateriaGerada($id);

                   return array( "code" =>  1,  "data"=> $reg);
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
		   $reg = MateriaRascunho::find($id);

		   $this->loadRequests($request, $reg);

			$ret = $reg->save();
                        
                        
                \App\Http\Service\MateriaRascunhoService::removeNotificacoes($reg->id);

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
		$reg = MateriaRascunho::find($id);
		$ret = $reg->delete();
                return array("msg"=>"sucesso", "code" =>  1 , "success" => $ret, "results"=> $reg);
	}
        
        

}
