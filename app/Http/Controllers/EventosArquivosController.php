<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Service\ErrorsService;

use Illuminate\Http\Request;
use App\EventosArquivos;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Dao\ImageDao;
use App\Http\Dao\PostsDao;
use App\Http\Service\EventoArquivoService;
use Illuminate\Support\Facades\Cache;
use DateTime;

set_time_limit ( 3000 );

class EventosArquivosController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(Request $request)
	{
	      $filtro = "";
              $order = " id "; $order_type = "desc";
              
              if ( $request->input( "id_evento")  != ""){
                         	$str_filt = str_replace("'","''", $request->input( "id_evento") );
                        	$filtro .= " and id_evento =  ". $str_filt;
               }


              if ( $request->input( "id_agrupamento")  != ""){
                       $filtro .= " and id in ( select id_evento_arquivo from 
                            agrupamento_notificacoes where id =  ". $request->input( "id_agrupamento") . " ) ";
              }
               
               
                $sql = " select ea.*, e.data,  '' as blnk, '' as tempo_h_realizado "
                        . " from eventos_arquivos ea inner join eventos e on e.id = ea.id_evento where  1 = 1 ". $filtro; 
               
                $itens = DB::select($sql);
                 for ( $i = 0; $i < count($itens) ; $i++ ){
                     $item = &$itens[$i];
                     
                     $item->tempo_h_realizado = \App\Http\Service\UtilService::converteMinutos_ParaHHMM( $item->tempo_realizado_minutos );
                     
                     
                 }
                
                
                $saida = array(
                             "qtde"=> count($itens),
                             "data" => $itens );
                         
                         
                return $saida;
	}
        
        
        public function index2(Request $request)
	{
	      $filtro = "";
              $order = " id "; $order_type = "desc";
              
               $DB_MIDIACLIP = \App\Http\Dao\ConfigDao::getSchemaMidiaClip(); // config("app.DB_MIDIACLIP");
            
            
            $id_cliente = $request->input("id_cliente");
            $id_programa = $request->input("id_programa");
            $tipo = $request->input("tipo");
            
            
            $dt_inicio = $request->input("dt_inicio");
            $dt_fim = $request->input("dt_fim");
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
                $date_fim->modify("-7 days");
                
                $dt_inicio = $date_fim->format("Y-m-d");
                
            }
            
            $filtro = "";
            if ( $dt_inicio != ""){
                $filtro .= " and ev.data >='".$dt_inicio." 00:00:00'";
            }
             if ( $dt_fim != ""){
                $filtro .= " and ev.data <='".$dt_fim." 23:59:59'";
            }
            
            if ( $tipo == ""){
                $tipo = "cut";
            }
            
             if ( $tipo != ""){
                  $filtro .= " and ea.tipo = '". $tipo . "' ";
             }
            
            if ( $id_programa != "" && is_numeric($id_programa)){
                $filtro .= " and ev.id_programa = ". $id_programa;
            }
            
            $inner_sub = "";
            if ( $id_cliente != "" && is_numeric($id_cliente)){
                
                $inner_sub = " inner join eventos_arquivos_clientes eac on ( eac.id_evento_arquivo = ea.id and eac.id_cliente = ". $id_cliente. ") ";
                /* $filtro .= " and exists ( select sub.id from eventos_clientes sub where sub.id_cliente= ". $id_cliente;
                
                    if ( $dt_inicio != ""){
                        $filtro .= " and sub.data >='".$dt_inicio." 00:00:00'";
                    }
                     if ( $dt_fim != ""){
                        $filtro .= " and sub.data <='".$dt_fim." 23:59:59'";
                    }
                    $filtro .= " and sub.id_eventos = ev.id )  "; */
            }
            
            
              
               if ( $request->input( "id_evento")  != ""){
                         	$str_filt = str_replace("'","''", $request->input( "id_evento") );
                        	$filtro .= " and ev.id_evento =  ". $str_filt;
               }
               
                
               if ( $request->input( "id_cliente")  != "" && is_numeric( $request->input( "id_cliente")) ){
                         	$str_filt = str_replace("'","''", $request->input( "id_cliente") );
                        	$filtro .= " and ea.meta_dados like '%".$str_filt."%' ";
               }
               
               
                $sql = " select ea.*, ev.data,  '' as blnk, '' as tempo_h_realizado, convert( pr.nome using utf8) as programa_nome, ".
                       " convert( m.titulo using utf8) as titulo_materia "
                        . " from eventos_arquivos ea"
                        . "  inner join eventos ev on ev.id = ea.id_evento "
                        . " left join ". $DB_MIDIACLIP. ".programa pr on pr.id = ev.id_programa "
                        . " left join ". $DB_MIDIACLIP. ".materia_radiotv_jornal m on m.id = ea.id_materia_radiotv_jornal ".
                        $inner_sub 
                        . " where  1 = 1 ". $filtro; 
               
                $itens = DB::select($sql);
                 for ( $i = 0; $i < count($itens) ; $i++ ){
                     $item = &$itens[$i];
                     
                     $item->tempo_h_realizado = \App\Http\Service\UtilService::converteMinutos_ParaHHMM( $item->tempo_realizado_minutos );
                     
                     
                 }
                
                
                $saida = array(
                             "qtde"=> count($itens),
                             "data" => $itens, "dt_inicio"=>$dt_inicio, "dt_fim" =>$dt_fim );
                         
                         
                return $saida;
	}
        
        public function buscaTempo(Request $request){
            
            $id_arquivo = $request->input("id_arquivo"); //37666
            $search = $request->input("search"); //37666
            $trecho = "";
            if ( $id_arquivo != ""){
                    $reg_arquivo = EventosArquivos::find($id_arquivo);

                    $tempo_seg = \App\Http\Service\EventoArquivoService::buscarTextoFrom($reg_arquivo->json, $search, $trecho );
                    
                    return array("tempo_seg" => $tempo_seg, "trecho" => $trecho);

            }
           
            
              
                $saida = array("msg" => "vazio!");
                         
                         
                return $saida;
        }
        
        public function clonaDadosTeste(Request $request){
            
            $acao = $request->input("acao");
            if ( $acao == "clona"){
                
                $itens = DB::select ("select * from midiaclip_transcricao.eventos_arquivos where id_evento = 43 ");
                 for ( $i = 0; $i < count($itens) ; $i++ ){
                     $item = &$itens[$i];
                     
                     $id_arquivo_filho = \App\Http\Dao\ConfigDao::executeScalar("select id as res from midiaclip_transcricao_tmp.eventos_arquivos where id_evento = 1 and nome='". $item->nome."' " );
                     
                     if ( $id_arquivo_filho != ""){
                         
                              $reg_evento_arquivos = \App\EventosArquivos::find($id_arquivo_filho);
                              
                              //id_notificacao_agrupamento
                              $sql_insert = "insert into eventos_arquivos_palavras ( data, id_evento, id_evento_arquivo, id_cliente, "
                                      . "                 cita_diretamente, palavra, tempo, tempo_seg, id_dicionario_tag, status, operador, "
                                      . "          id_operador, id_materia_radiotv_jornal, trecho,  indexed  ) "
                                      . "    select  ea.data, ".$reg_evento_arquivos->id_evento.", ".$reg_evento_arquivos->id.", 1 , "
                                      . "                 ea.cita_diretamente, ea.palavra, ea.tempo, ea.tempo_seg, ea.id_dicionario_tag, ea.status, ea.operador, "
                                      . "          ea.id_operador, ea.id_materia_radiotv_jornal, ea.trecho,  ea.indexed 
                                           from midiaclip_transcricao.eventos_arquivos_palavras ea
                                            where ea.id_evento = 43 and ea.id_evento_arquivo = " . $item->id . "  and ea.id_cliente in (   1201519 , 1201518 ) ";
                              
                              DB::statement($sql_insert);
                         
                         
                         
                     }
                     
                     
                     
                 }
                 
                     die("Feito para ". count($itens). " itens ");
                
            }
        }
        
        public function gerarRecorte(Request $request){
            $TRANSCRICAO_BUSCA_EXTERNA = \App\Http\Dao\ConfigDao::getValor("TRANSCRICAO_BUSCA_EXTERNA");
            
            $id_evento = $request->input("id_evento");
            $inicio = $request->input("inicio");
            $fim = $request->input("fim");
            $titulo = $request->input("titulo");
            $id_arquivo_pai = $request->input("id_arquivo_pai");
            
            $evento_pai = \App\Eventos::find( $id_evento );
            
             //    EventoArquivoService::clonaPalavrasArquivoPai(36, $evento_pai, $id_arquivo_pai, $inicio, $fim);
            
            $novo =  EventoArquivoService::gerarArquivoCorte($evento_pai, $id_arquivo_pai,  $inicio, $fim , $titulo);
            
            if ( $TRANSCRICAO_BUSCA_EXTERNA ){
                
                //Pegando palavras do arquivo pai, somente do trecho desejado, e colocando pro arquivo filho.
                 EventoArquivoService::clonaPalavrasArquivoPai($novo->id, $evento_pai, $id_arquivo_pai, $inicio, $fim);
                 
                 //Faz o resumo disso e coloca
                \App\Http\Dao\EventosArquivosPalavrasDao::buscaSemLike($novo->id);
                
            }else {
                
                  $ls_tags = \App\Http\Service\TempSearchService::getSQLTagsTotal( $evento_pai->id_programa );
                  $temp_search =  \App\Http\Service\TempSearchService::searchByArquivo($ls_tags, $novo->id);
                
            }
            
            // $ls_tags =  self::get_lsTags();
           

             $ELASTIC_ENABLE = config("app.ELASTIC_ENABLE");
             $ELASTIC_URL = config("app.ELASTIC_URL");
             if ( $ELASTIC_ENABLE ){
                 $reg_arquivo = \App\EventosArquivos::find( $novo->id ) ;
                 \App\Http\Service\FilaAtividadeService::adicionar($reg_arquivo, 0, "elastic");
               
            }
             
             $retorno = array();// \App\EventosArquivos::find( $novo->id );
             
             $ls_novo = $this->getArquivos($novo->id, "" ,"cut");
            
            if ( count($ls_novo) > 0 ){
                $retorno = $ls_novo[0];
            }
            
            return $this->sendResponse(array("data" => $retorno, "ffmpeg" =>  EventoArquivoService::$ffmpeg_last ), "recorte feito com sucesso!");
            
        }
        
        
        
        public function get_lsTags(){
            $ls_tags = array();
            
            if ( Cache::has("ls_tags") ){
                $ls_tags =Cache::get("ls_tags");
            }else{
               $ls_tags =  \App\Http\Service\TempSearchService::getSQLTagsTotal();
                Cache::put("ls_tags", $ls_tags , 15); //Vou usar um cache de 15 minutos..
            }
            
            return $ls_tags;
            
        }
        
        public function gerarMateria(Request $request){
            
            
            //$id = $request->input("id");
            $id = $request->input("ids_arquivos");
            $json_data = $request->input("json_data");
            $id_materia = $request->input("id_materia");
            $id_materia_frags = $request->input("id_materia_frags");
            $clientes = $request->input("clientes");
            
            $id_programa = "";
            
            if ( $id != ""){
               $sql_tmp = " select id_programa as res from eventos where id in ( select id_evento from eventos_arquivos where id in ( ". $id. " )  ) limit 0, 1 ";
               $id_programa = \App\Http\Dao\ConfigDao::executeScalar($sql_tmp);
            }
            
           // $ls_tags = $this->get_lsTags();
             $ls_tags = \App\Http\Service\TempSearchService::getSQLTagsTotal( $id_programa );
            
            $nova_materia = EventoArquivoService::geraMateriaByArquivos($id, $id_materia, $id_materia_frags, $json_data, $clientes, $ls_tags, $request);
            
            return $this->sendResponse(array("data"=>$nova_materia));
        }
        
        public function temp_search_programa($id, Request $request){
            
            $id_evento = $id;
            
            if ( $id_evento == ""){
                  return $this->sendResponse(array("msg"=>"ID do evento vazio!"));
            }
            
            $registro = \App\Eventos::find($id_evento);
            
            $ls_tags =  \App\Http\Service\TempSearchService::getSQLTagsTotal($registro->id_programa);
           
            $ls_arquivos = EventosArquivos::where('id_evento', $id_evento)->get();
            
            for ( $i = 0; $i < count($ls_arquivos); $i++ ){
                $item = $ls_arquivos[$i];
                
                $id_arquivo = $item->id;
                $temp_search =  \App\Http\Service\TempSearchService::searchByArquivo($ls_tags, $id_arquivo);
            }
            
            return $this->sendResponse(array("msg"=>"Sucesso!","qtde"=> count($ls_arquivos)));
        }
        public function temp_search($id, Request $request){
            
            $ls_tags = array();
           
            /*
            if ( Cache::has("ls_tags") ){
                $ls_tags =Cache::get("ls_tags");
            }else{
               $ls_tags =  \App\Http\Service\TempSearchService::getSQLTagsTotal();
                Cache::put("ls_tags", $ls_tags , 15); //Vou usar um cache de 15 minutos..
            }
            
            */
            
            
            $TRANSCRICAO_BUSCA_EXTERNA = \App\Http\Dao\ConfigDao::getValor("TRANSCRICAO_BUSCA_EXTERNA");  
            
            if ( $TRANSCRICAO_BUSCA_EXTERNA ){
                
                 \App\Http\Dao\EventosArquivosPalavrasDao::buscaSemLike($id);
                 
                 
                 return $this->sendResponse(array("msg"=>"feito sem o like."));
                
            }else {
                
                $id_programa = \App\Http\Service\TempSearchService::getIdProgramaFromIdArquivo($id);
                $ls_tags =  \App\Http\Service\TempSearchService::getSQLTagsTotal($id_programa);
                $temp_search =  \App\Http\Service\TempSearchService::searchByArquivo($ls_tags, $id);

                $ELASTIC_ENABLE = config("app.ELASTIC_ENABLE");
                $ELASTIC_URL = config("app.ELASTIC_URL");
                 if ( $ELASTIC_ENABLE ){
                     $reg_arquivo = \App\EventosArquivos::find( $id ) ;
                     \App\Http\Service\FilaAtividadeService::adicionar($reg_arquivo, 0, "elastic");

                     if ( \App\Http\Service\FilaAtividadeService::podeExecutarFila() ){
                         \App\Http\Service\ElasticSearchService::executarFila();
                     }
                     //die("o que deu? ". $res );
                }
                
                
                 return $this->sendResponse($temp_search);
                
            }
            
            
            
           
        }
        
        public function automatico_palavras( Request $request ){
            
            
                 $sql = " select id, nome, meta_dados from eventos_arquivos where meta_dados not like '%:[]%' ";
                  
                 $lista = DB::select($sql);
                 
                 
                      for ( $i = 0; $i < count($lista); $i++ ){
                                 $item = $lista[$i];
                                 
                                 $id_programa = \App\Http\Service\TempSearchService::getIdProgramaFromIdArquivo($item->id);
                                 $ls_tags =  \App\Http\Service\TempSearchService::getSQLTagsTotal($id_programa);
                                 
                                 $temp_search =  \App\Http\Service\TempSearchService::searchByArquivo($ls_tags, $item->id );
                
                
                      }
                      
                      
            return $this->sendResponse(array("msg"=>"Feito para ". count($lista) ));
            
            
        }
        
        public function copiarFromServer( Request $request ){
            
            $acao = $request->input("acao");
            $data = date("Y-m-d")." 00:00:00";
            
            if ( $acao == "copiar"){
                
            }
            
        }
        
        public function insere_elastic( Request $request){
            
            $data_str = "2019-12-27";
            
            $sql = " select * from eventos_arquivos where  created_at >= '".$data_str." 00:00:00' limit 0, 20 ";
            //die($sql);
            $lista = DB::select($sql);
        
            
                     $ELASTIC_ENABLE = config("app.ELASTIC_ENABLE");
                     $ELASTIC_URL = config("app.ELASTIC_URL");



                    for ( $i = 0; $i < count($lista); $i++ ){
                        $item = $lista[$i];
                         if ( $ELASTIC_ENABLE && $ELASTIC_URL != ""){
                                   $res =  \App\Http\Service\ElasticSearchService::create_arquivo($item, \App\Http\Service\ElasticSearchService::$prefixo);
                          }
                    }
               
            die("Feito para ". count($lista));
        }
        public function automatico_search( Request $request){
            
             ini_set("display_errors", 1);
             //error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
             set_time_limit ( 12000 );
            $filtro = " and com_temp_search = 0  ";
            
            if ( $request->input("todos") == "1"){
                
                    $filtro = " ";
            }
               
            if ( $request->input("tipo") != ""){
                $filtro .= " and tipo='".$request->input("tipo")."' ";
                
            }
            if ( $request->input("id") != ""){
                
                    $filtro = " and id = ". $request->input("id");
            }
             if ( $request->input("hoje") != ""){
                
                    $filtro .= " and id_evento in ( select id from eventos where  data >= '".date("Y-m-d")." 00:00:00' and data <= '".date("Y-m-d")." 23:59:59' ) ";
            }
            
          
            
             if ( $request->input("tempo_hora") != ""){
               $ar = explode("|", $request->input("tempo_hora"));
                 
               $filtro .= " and created_at >= '".$ar[0]."' and created_at <= '".$ar[1]."'  ";
             }
            $sql = " select * from eventos_arquivos where  1 = 1 ". $filtro; 
             if ( $request->input("limit") != ""){
                 
                 $sql .= " limit 0, ". $request->input("limit");
             }
            
             if ( $request->input("write") != ""){
                 die(  $sql );
             }
             // die("oi? ". $sql );
            $lista = DB::select($sql);
            $ls_tags = array();
            
            if ( count($lista) > 0 ){
                
                //      $ls_tags =  \App\Http\Service\TempSearchService::getSQLTagsTotal();
            }
            
            $ELASTIC_ENABLE = \App\Http\Dao\ConfigDao::getValor("ELASTIC_ENABLE"); // config("app.ELASTIC_ENABLE");
            $ELASTIC_URL = config("app.ELASTIC_URL");
            
            $TRANSCRICAO_BUSCA_EXTERNA = \App\Http\Dao\ConfigDao::getValor("TRANSCRICAO_BUSCA_EXTERNA");
           
            
            
            for ( $i = 0; $i < count($lista); $i++ ){
                $item = $lista[$i];
                
                if ( ! $TRANSCRICAO_BUSCA_EXTERNA ){
                            $id_programa =  \App\Http\Service\TempSearchService::getIdProgramaFromIdArquivo($item->id);
                            $ls_tags =  \App\Http\Service\TempSearchService::getSQLTagsTotal($id_programa);
                            if ( $request->input("debug2") != ""){
                             print_r(  $ls_tags );
                            }
                            //return $ls_tags;
                            $temp_search =  \App\Http\Service\TempSearchService::searchByArquivo($ls_tags, $item->id );
                            if ( $ELASTIC_ENABLE && $ELASTIC_URL != ""){
                                        \App\Http\Service\FilaAtividadeService::adicionar($item, 0, "elastic");
                            }
                } else {
                    \App\Http\Dao\EventosArquivosPalavrasDao::buscaSemLike($item->id);
                }
                
                  
            }
            
            if ( $ELASTIC_ENABLE && $ELASTIC_URL != ""){
                
                  if ( \App\Http\Service\FilaAtividadeService::podeExecutarFila() ){
                                \App\Http\Service\ElasticSearchService::executarFila();
                  }


                 \App\Http\Service\FilaAtividadeService::removeAntigo(); //Removendo coisa antiga, não precisamos ter isso guardado muito tempo..
            }
           
            $dt_pesquisa = \App\Http\Service\UtilService::getCurrentBDdate();
            $dt = new DateTime(date("Y-m-d H:i:s"));
            $dt->modify('-10 minutes');
            
            $sql = "select * from eventos ev where ev.data_status_change is not null and  ev.data_status_change <= '". $dt->format("Y-m-d H:i:s")."' ";
            $sql_update = " update eventos set data_status_change= '".date("Y-m-d H:i:s"). "', bloqueado_por = null, bloqueado_por_id = null, status = 1 ".
                     "  where data_status_change is not null and  data_status_change <= '". $dt->format("Y-m-d H:i:s")."' ";
            
            DB::statement($sql_update);
            
            
            //$reg_arquivo_teste = EventosArquivos::find(159371);
            //$qtde_feito = \App\Http\Service\ElasticSearchService::salvaPalavrasDoArquivo( $reg_arquivo_teste );
            //echo("qtde feita?? ". $qtde_feito );
            //\App\Http\Dao\EventosArquivosPalavrasDao::agrupaNotificacoes($reg_arquivo_teste);
            
            return $this->sendResponse(array("msg"=>"Feito para ". count($lista) ));
        }
        
        
        public function ajustatempo_arquivopalavras( Request $request ){
            
            $lista = DB::select("select id, tempo_seg, tempo from eventos_arquivos_palavras ");
             for ( $i = 0; $i < count($lista); $i++ ){
                $item = $lista[$i];
                
                $segundos = \App\Http\Service\UtilService::time_to_seconds2($item->tempo);
                
                DB::statement("update eventos_arquivos_palavras set tempo_seg = ". $segundos . " where id = ". $item->id);
            }
            
            echo("Feito para" . count($lista));
        }
	
	
	/*
	            Route::get('/api/eventos_arquivos', 'EventosArquivosController@index');
                Route::get('/api/eventos_arquivos/{id}', 'EventosArquivosController@show');
                Route::put('/api/eventos_arquivos/{id}', 'EventosArquivosController@update');
                Route::post('/api/eventos_arquivos', 'EventosArquivosController@create');
                Route::delete('/api/eventos_arquivos/{id}', 'EventosArquivosController@destroy');
				
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


                         $sql = " select count(*) as res from eventos_arquivos where 1 = 1 ".$filtro ;
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

                         $sql = "select p.* from eventos_arquivos p where 1 = 1 ". $filtro . " order by ".$order. " ".$order_type .
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
	private function loadRequests(Request $request, \App\EventosArquivos &$reg){

          $reg->path = $request->input('path');  
            $reg->nome = $request->input('nome');  
            $reg->id_evento = $request->input('id_evento');  
            $reg->tempo_realizado_minutos = $request->input('tempo_realizado_minutos');  
            $reg->hora_inicio = $request->input('hora_inicio');  
            $reg->hora_inicio_seg = $request->input('hora_inicio_seg');  
            $reg->inserted_at = $request->input('inserted_at');  
            $reg->texto = $request->input('texto');  
            $reg->json = $request->input('json');  
		
		
         PostsDao::blankToNull(  $reg );

	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create(Request $request)
	{
		
		$reg = new \App\EventosArquivos;

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
        
        function charset_decode_utf_8 ($string) {
            /* Only do the slow convert if there are 8-bit characters */
            /* avoid using 0xA0 (\240) in ereg ranges. RH73 does not like that */
            if (!preg_match("/[\200-\237]/", $string)
             && !preg_match("/[\241-\377]/", $string)
            ) {
                return $string;
            }

            // decode three byte unicode characters
            $string = preg_replace("/([\340-\357])([\200-\277])([\200-\277])/e",
                "'&#'.((ord('\\1')-224)*4096 + (ord('\\2')-128)*64 + (ord('\\3')-128)).';'",
                $string
            );

            // decode two byte unicode characters
            $string = preg_replace("/([\300-\337])([\200-\277])/e",
                "'&#'.((ord('\\1')-192)*64+(ord('\\2')-128)).';'",
                $string
            );

            return $string;
        }
        
        public function removeUnicode($json){
            $str = preg_replace_callback('/\\\\u([0-9a-fA-F]{4})/', function ($match) {
                return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UTF-16BE');
            }, $json);
            
            return $str;
        }
        public function show_simples($id){
            
	    $reg = EventosArquivos::find($id);
             $myobj = json_decode( $reg->json );
           
            $reg->json = json_encode($myobj, JSON_PRETTY_PRINT  | JSON_UNESCAPED_UNICODE);
            // print_r( $myobj );
            //die(" " . $this->removeUnicode(  $reg->json ) );
             return array( "code" =>  1,  "item"=> $reg);
        }

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		
	    $reg = EventosArquivos::find($id);
            $ls = $this->getArquivos($id, "", "cut");
            
           
            
            if ( count($ls) > 0 ){
                $reg->current_video = $ls[0];
            }

           return array( "code" =>  1,  "results"=> $reg, "item"=> $reg);
	}
        
         public function getArquivos($id, $compl = "", $tipo = ""){
           $sql = \App\Http\Service\EventoService::sqlArquivos($tipo == "cut")." where 1 = 1 and ea.id = ". $id . $compl . " order by ea.hora_inicio_seg ";
           $arquivos = DB::select( $sql );
           
           $url_base = config("app.PATH_URL_VIDEOS");
            
            for ( $i = 0; $i < count($arquivos); $i++ ){
                $item = &$arquivos[$i];
                
                $arquivo_nome = str_replace("//","/", $item->path );                
                $item ->url_load = $url_base .  $arquivo_nome;
                $item->duracao = \App\Http\Service\UtilService::converteSegundos_ParaHoraMinuto(  $item->tempo_realizado_minutos * 60 );
                
                if ( $id != "" && false ){
                      $myobj = json_decode( $item->json );
            
                      $reg->json = json_encode($myobj, JSON_UNESCAPED_UNICODE);
                }
            }
            
            
            return $arquivos;
            
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
		   $reg = EventosArquivos::find($id);

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
	public function destroy(Request $request)
	{
            
                $id = $request->input("id");
            
		        $reg = EventosArquivos::find($id);

                if ( ! is_null($reg)){

                         $path = \App\Http\Service\EventoService::getPathEvento($reg->id_evento, true );
                
                if (file_exists($path. DIRECTORY_SEPARATOR . $reg->nome) && $reg->nome != ""){
                    unlink($path. DIRECTORY_SEPARATOR . $reg->nome);
                }
                
                     $ret = $reg->delete();


                }
                
           
               
                return array("msg"=>"sucesso", "code" =>  1 , "success" => $ret, "results"=> $reg);
	}
        
        

}
