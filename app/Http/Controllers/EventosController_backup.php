<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Service\ErrorsService;

use Illuminate\Http\Request;
use App\Eventos;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Dao\ImageDao;
use App\Http\Dao\PostsDao;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use DateTime;


class EventosController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(Request $request)
	{
            
            $hora = date("H:i:s");
            $dia = date("Ymd");
            $dia_semana = date("w");
            
            if ( $request->input("data") != ""){
                $odata = new DateTime( $request->input("data") );
                
                $dia = $odata->format("Ymd");
                $dia_semana =  $odata->format("w");
            }
             if ( $request->input("hora") != ""){
                $hora = $request->input("hora");
            }
            
            //die("hora: ". $hora );
            $hora_seg = \App\Http\Service\UtilService::time_to_seconds($hora);
              
  //45900
  //46500
            //die( \App\Http\Service\UtilService::converteSegundos_ParaHoraMinuto(45900) ." -- ".
             //     \App\Http\Service\UtilService::converteSegundos_ParaHoraMinuto(46500)   );
            
            $write = false;
            
            if ( $request->input("write") == "1" ){
                $write = true;
            }
            
            $lista = \App\Http\Service\EventoService::getListEvento($dia, $dia_semana, $hora_seg);
            
            if (  $request->input("ret") =="api"  ){
                
                    return $this->sendResponse(array("data"=>$lista, "sql" => \App\Http\Service\EventoService::$sql_last, "sql2" => \App\Http\Service\EventoService::$sql_last0  ));  
            }
            
            return $this->sendResponse($lista);   
            
            
	    
	}
        
        
        public function index3(Request $request)
	{
              $DB_MIDIACLIP = \App\Http\Dao\ConfigDao::getSchemaMidiaClip();
      
            $hora = date("H:i:s");
            $dia = date("Ymd");
            $dia_semana = date("w");
            
            if ( $request->input("data") != ""){
                $odata = new DateTime( $request->input("data") );
                
                $dia = $odata->format("Ymd");
                $dia_semana =  $odata->format("w");
            }
            if ( $request->input("hora") != ""){
                $hora = $request->input("hora");
            }
            
            $hora_seg = \App\Http\Service\UtilService::time_to_seconds($hora);
            
            $sql = " select pr.nome as programa, em.nome as emissora,
                                    pr.transcricao_prioridade as prioridade,
                                    case when pr.transcricao_prioridade = 'Alta' then 3
                                    when pr.transcricao_prioridade = 'Normal' then 2
                                    else 1 end as prioridade_int,
                                    pr.hora_inicio, pr.hora_fim,
                                    em.transcricao_url as path, em.transcricao_url2 as alt_path, '' as ultimo_arquivo

                                    from ". $DB_MIDIACLIP .".programa pr 
                                     inner join  ". $DB_MIDIACLIP .".associacao_cadastros ac on (ac.id_pai = pr.id and ac.classificacao = 'programaxcanal_comunicacao' and ac.tabela_pai='programa' )
                                    inner join ". $DB_MIDIACLIP .".emissora em on em.id =ac.id_filho
                                    where ifNull(pr.transcricao_ativar,0) = 1 and
                                     pr.transcricao_tempo_inicio_seg <=".$hora_seg." and pr.transcricao_tempo_fim_seg >= ".$hora_seg." and pr.transcricao_dias like  '%".$dia_semana."%' order by prioridade_int asc  ";

           if ( $request->input("white") == "1"){
                die( $sql );
              //$hora = $request->input("hora");
            }
       // 
            $lista2 = DB::select($sql);
        
            return $this->sendResponse($lista2);   
            
            
	    
	}
        
        public function hasfilho($id, Request $request ){
            
                $sql = "select id as res from eventos where tipo='filho' and id_evento_pai = ". $id. " and id_operador = ". $request->input("id_operador");
                $tmp_id = \App\Http\Dao\ConfigDao::executeScalar($sql);
                
                $has = false;
                if ( $tmp_id != ""){
                    
                     $has = true;
                }else{
                        $this->createFilho($id, $request);
                     
                        $sql = "select id as res from eventos where tipo='filho' and id_evento_pai = ". $id. " and id_operador = ". $request->input("id_operador");
                        $tmp_id = \App\Http\Dao\ConfigDao::executeScalar($sql);
                        $has = true;
                    
                }
                return array("has"=>$has,"id"=>$tmp_id);
        }
        
        
        public function createFilho($id, Request $request){
            
            
          //  $json = \App\Http\Service\EventoService::obtemJson("2,3");
          //  die($json);
            
	        $pai = Eventos::find($id);
		$reg = new \App\Eventos;   
                
                

                $reg->data = $pai->data;
                $reg->id_programa = $pai->id_programa;
                $reg->id_emissora = $pai->id_emissora;
                $reg->hora_inicio = $pai->hora_inicio;
                $reg->hora_fim = $pai->hora_fim;
                $reg->hora_inicio_seg = $pai->hora_inicio_seg;
                $reg->hora_fim_seg = $pai->hora_fim_seg;
                $reg->duracao = $pai->duracao;
                $reg->duracao_seg = $pai->duracao_seg;
                $reg->tempo_realizado_minutos = $pai->tempo_realizado_minutos;
                $reg->tempo_total_minutos = $pai->tempo_total_minutos;
                $reg->dia = $pai->dia;
                $reg->tipo = "filho";
                $reg->id_operador = $request->input("id_operador") ;
                $reg->id_evento_pai = $pai->id;
                $reg->nome_projeto = $request->input("nome_projeto") ;
                
                $arquivos_input = $request->input("arquivos");
               
                
                PostsDao::blankToNull(  $reg );
                
                $reg->save();
                
                
                $path_evento = \App\Http\Service\EventoService::getPathEvento($pai->id);
                
                $subfolder = \App\Http\Service\EventoService::getPrePasta($reg);
                
                \App\Http\Service\EventoService::criarDir($reg->dia, $subfolder , $reg->id );
                $path_evento_filho = \App\Http\Service\EventoService::getPathEvento($reg->id);
                
                
                if ( $arquivos_input != "" && false ){
                     $ids_arquivos = "0";
                      $arquivos = json_decode($arquivos_input);
                      $files = array();
                      $extensao = "";
                      for($i = 0; $i < count($arquivos); $i++ ){
                          $arquivo = (object)$arquivos[$i];
                          $files[$i] = $path_evento. DIRECTORY_SEPARATOR . $arquivo->nome;
                          $ids_arquivos .= ",".$arquivo->id;
                          
                          if ( $i == 0 ){
                             $ext =  explode(".",  $arquivo->nome );
                             $extensao = $ext[count($ext) -1 ];
                          }
                      }
                      
                     // print_r( $files );die(" ");
                      $txt_file = \App\Http\Service\FFmpegService::getFileTxt($files, $path_evento_filho  );
                      
                      
                      $nome_final_arquivo = "file_join.".$extensao;
                      
                      
                      $comando_final = "-f concat -safe 0 -i \"" . $txt_file . "\" -c copy " . "\"" . $path_evento_filho . DIRECTORY_SEPARATOR . $nome_final_arquivo . "\"";
                     
                      $ret = \App\Http\Service\FFmpegService::executeCommand($comando_final);
                      
                      $ls_tags = $this->get_lsTags($reg->id_programa);
                      // die($ret );
                      \App\Http\Service\EventoService::salvarArquivoEventoFilho($reg, $ids_arquivos, "join", $nome_final_arquivo, $ls_tags);
                      
                         
                      
                      
                }

            
            return $this->sendResponse($reg);   
            
        }
        
        public function getEventoFilho(Request $request){
            $id = $request->input("id");
            
            $sql = \App\Http\Service\EventoService::sqlEventoFilho()." where 1 = 1 and ev.id_evento_pai = " . $id. " order by ev.id desc ";
            
            $lista = DB::select($sql);
            
            return $this->sendResponse(array("data"=>$lista)); 
            
        }
        
           public function getEventoFilho2(Request $request){
            $id = $request->input("id");
            
            $sql = \App\Http\Service\EventoService::sqlEventoFilho()." where 1 = 1 and ev.id = " . $id. " order by ev.id desc ";
            
            $lista = DB::select($sql);
            
            return $this->sendResponse(array("data"=>$lista)); 
            
        }
        
        
        
        public function index2(Request $request)
	{
            
            $DB_MIDIACLIP = config("app.DB_MIDIACLIP");
            
            
            $dt_inicio = $request->input("dt_inicio");
            $dt_fim = $request->input("dt_fim");
            $id_cliente = $request->input("id_cliente");
            $id_programa = $request->input("id_programa");
            $id_emissora = $request->input("id_emissora");
            $id = $request->input("id");
            
            
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
            if ( $dt_inicio != ""){
                $filtro .= " and ev.data >='".$dt_inicio." 00:00:00'";
            }
             if ( $dt_fim != ""){
                $filtro .= " and ev.data <='".$dt_fim." 23:59:59'";
            }
            
            if ( $id_programa != "" && is_numeric($id_programa)){
                $filtro .= " and ev.id_programa = ". $id_programa;
            }
            
            if ( $id_emissora != "" && is_numeric($id_emissora)){
                $filtro .= " and ev.id_emissora = ". $id_emissora;
            }
            
            if ( $id_cliente != "" && is_numeric($id_cliente)){
                $filtro .= " and exists ( select sub.id from eventos_clientes sub where sub.id_cliente= ". $id_cliente;
                
                    if ( $dt_inicio != ""){
                        $filtro .= " and sub.data >='".$dt_inicio." 00:00:00'";
                    }
                     if ( $dt_fim != ""){
                        $filtro .= " and sub.data <='".$dt_fim." 23:59:59'";
                    }
                    $filtro .= " and sub.id_eventos = ev.id )  ";
            }
            
            if ( trim($id) != "" && is_numeric($id)){
                $filtro = " and ev.id = ". $id;
            }
            
            
            
            $sql = \App\Http\Service\EventoService::SqlEvento($DB_MIDIACLIP)." where 1 = 1  and ifNull(ev.tipo, 'tipo') = 'pai' ". $filtro . " order by ev.id desc "; //
            
            
            
            //die($sql );
            $lista = DB::select($sql);
           
            for ($i = 0; $i < count($lista); $i++ ){
                $item = &$lista[$i];
                $item->tempo_h = \App\Http\Service\UtilService::converteMinutos_ParaHHMM( $item->tempo_realizado_minutos );
            }
            
            return $this->sendResponse(array("data"=>$lista, "dt_inicio"=>$dt_inicio, "dt_fim" =>$dt_fim, "sql" => $sql));   //, 
            
            
	    
	}
        
        
        
        public function indicastatus(Request $request)
	{
            $header = $request->header(); 
                 
            $id_user = join( $header["apiauth"], ",");
            
            
            $id = $request->input("id");
            $status = $request->input("status");
            
            
            $reg_antigo = \App\Eventos::find($id);
            $compl = "";
            
            $id_operador = $request->input("id_operador");
            
            if ( $id_operador == ""){
                $id_operador = $id_user;
            }
            
            if ( $id_operador != "" ){
                $nome_usuario = \App\Http\Dao\ConfigDao::executeScalar2("select nome  as res from usuario where id = ". $id_operador );
                
                if ( $status == 2 ){
                      $compl = ", bloqueado_por_id = ". $id_operador;
                      $compl .= ", bloqueado_por='". utf8_encode( $nome_usuario )."' ";
                }else{
                      $compl = ", bloqueado_por_id = null, bloqueado_por = null ";
                }
            }
            if ( is_null($reg_antigo->status) ){
                $reg_antigo->status = 1;
            }
            if ( $reg_antigo->status != 3 ){
                $sql  = " update eventos set status = ". $status. " ". $compl . 
                         " , data_status_change='".\App\Http\Service\UtilService::getCurrentBDdate()."' where id = ". $id;
                DB::statement($sql);

            }
            
            $reg = \App\Eventos::find($id);
            
            
            
            return array("msg"=>"Sucesso!", "id"=>$id, "form" => array(
                              "bloqueado_por_id" => $reg->bloqueado_por_id,
                              "bloqueado_por" => $reg->bloqueado_por,
                              "status" => $reg->status,
                                                 "data_status_change" => $reg->data_status_change ) );
            
        }
        
        public function clear(Request $request)
	{
            
                Artisan::call('config:clear');
                Artisan::call('cache:clear');
                Artisan::call('view:clear');
                Artisan::call('route:clear');
                
                return $this->sendResponse(array("msg" => config("app.DB_HOST")." Cache limpo com sucesso - ".  config("app.PATH_ARQUIVOS"). " - ". config("app.DB_MIDIACLIP") ) );

            
        }
        
        public function show_byid(Request $request)
	{
             $ids = $request->input("ids");
             
             if ( $ids == ""){
                 $ids = "0 ";
             }
             $items = $this->getArquivos(""," and ea.id in ( ". $ids . ") ");
             return array( "code" =>  1,  "data"=> $items);
        }
        
        public function show2($id)
	{
            
	   $reg = Eventos::find($id);
           return array( "code" =>  1,  "data"=> $reg);
        }
        
	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id, Request $request)
	{
		
	   $reg = Eventos::find($id);
           
           $simples = $request->input("simples");
           $clean = $request->input("clean");
           
           $sql_completo = \App\Http\Service\EventoService::SqlEvento()." where ev.id = ".$id;
           
           $ls_meta = array();
           $ls_meta[0] = array();
           
           if ( $simples == ""){
               $ls_meta = DB::select($sql_completo);
           }
           
           
           if ( $reg->tipo == "pai" &&  $simples == "" ){
               
                  $reg->arquivos = $this->getArquivos($id); //É um evento principal,pai, gerado a partir da fila. Então posso trazer todos os arquivos.
                  $reg->arquivos_cortados = array();
                  $reg->start_clientes = array();
           }
           
            
           if ( $reg->tipo == "filho" &&  $simples == "" ){
               
               if ( $reg->id_evento_pai != ""){
                   
                  //$reg->form_pai = Eventos::find($reg->id_evento_pai);
                   
                  $reg->arquivos =  $this->getArquivos($reg->id_evento_pai); 
                  $reg->start_clientes =  \App\Http\Service\EventoService::getListCliente($reg->id_evento_pai); 
               }else { 
                    $reg->arquivos =  $this->getArquivos($id, " and ea.tipo='join' "); //Vou trazer o arquivo que engloba tudo o que o cara definiu.
               }
                  $reg->arquivos_cortados = $this->getArquivos($id, " and ea.tipo='cut' ", "cut"); //Vou trazer os arquivos cortados pelo usuário.
           }
           
           if ( $clean == "1"){
               
                  $arq = $reg->arquivos;
                  for ( $i = 0; $i < count($arq); $i++ ){
                            $item = &$arq[$i];
                            $item->json = null;
                
                
                  }
                  $reg->arquivos = $arq;
                  
               
                  //$reg->arquivos = array();
                  //$reg->arquivos_cortados = array();
           }
           $reg->meta = @$ls_meta[0];
           //$reg->programa = \App\Http\Dao\ConfigDao::executeScalar2("select nome as res from programa where id = ".$reg->id_programa );
           return array( "code" =>  1,  "results"=> $reg, "item"=> $reg);
	}
        
        public function getArquivos($id, $compl = "", $tipo = ""){
            
            $filtro = "";
            
            if ( $id != ""){
                $filtro .= " and ea.id_evento = ". $id;
            }
           $sql = \App\Http\Service\EventoService::sqlArquivos($tipo == "cut")." where 1 = 1  ". $filtro.  $compl . " order by ea.hora_inicio_seg ";
           $arquivos = DB::select( $sql );
           
           $url_base = config("app.PATH_URL_VIDEOS");
            
            for ( $i = 0; $i < count($arquivos); $i++ ){
                $item = &$arquivos[$i];
                
                $arquivo_nome = str_replace("//","/", $item->path );
                
                $item->url_load = $url_base .  $arquivo_nome;
               // $item->url_load = $url_base .  $arquivo_path;
                $item->duracao = \App\Http\Service\UtilService::converteSegundos_ParaHoraMinuto(  $item->tempo_realizado_minutos * 60 ); 
                
                if ( $id != "" && false ){
                    
                      $myobj = json_decode( $item->json );
            
                      $item->json = json_encode($myobj, JSON_UNESCAPED_UNICODE);
                }
            }
            return $arquivos;
            
        }
        
        public function ajustabyId($id_evento, &$tempos){
              $lista = \App\EventosArquivos::where('id_evento', '=', $id_evento)->get();
                
                for ( $i = 0; $i < count($lista); $i++ ){
                       $item = &$lista[$i];
                       $hora_inicio =  \App\Http\Service\EventoArquivoService::getTimeFromFileName($item->nome);
                       
                       $item->hora_inicio = $hora_inicio;
                       $item->hora_inicio_seg = \App\Http\Service\UtilService::time_to_seconds( $hora_inicio );
                       $item->tempo_realizado_minutos = 5;
                       $item->save();
                       
                       $tempos[count($tempos)] = array("file"=> $item->nome, "hora" => $item->hora_inicio, "seg"=>$item->hora_inicio_seg);
                       //   
                 
                }
                $qtde = count($lista) ;
                $evento = \App\Eventos::find($id_evento);
                $tot2 = \App\Http\Dao\ConfigDao::executeScalar("select sum(tempo_realizado_minutos) as res from eventos_arquivos where id_evento = ". $evento->id );
                $evento->tempo_realizado_minutos = $tot2;
                $evento->save();
        }
        
        public function ajustaTempo(Request $request){
            
            $id_evento = $request->input("id_evento");
            $dia = $request->input("dia");
            $qtde = 0;
            $tempos = array();
            
            if ( $id_evento != ""){
                
              $this->ajustabyId($id_evento, $tempos);
            }
            
            if ( $dia != ""){
                $sql = " select id from eventos where dia =  ". $dia. " and tipo='pai' ";
                $lista = DB::select($sql);
                
                            for ( $i = 0; $i < count($lista); $i++ ){
                                       $item = $lista[$i];
                                       $this->ajustabyId($item->id, $tempos);                
                       
                            }
            }
            
            return array("msg"=>"Feito! " . $qtde, "tempos" => $tempos);
            
        }
	
        public function salvarArquivo(Request $request){
            //$xmlData = file_get_contents('http://user:pass@example.com/file.xml');
          
            
            $id_event = $request->input("id_event");
            $tempo = $request->input("tempo");
            $texto = $request->input("texto");
            $json = $request->input("json");
            
            if ( $id_event == ""){
                return $this->sendError("Informe o id_event");
            }
            
            $evento = Eventos::find($id_event);
            
            
            $objUpload = null;  
            
            $EventoArmazenamento = \App\Http\Dao\ConfigDao::getValor("EventoArmazenamento");
            $eh_remoto = false;
            if ( $EventoArmazenamento == "remoto" && $evento->tipo=="pai"){
                 //Para o caso dissoe estar em outro servidor.
               $objUpload =new \App\Http\Service\Imagem\CurlService();
               $eh_remoto = true;
            }else{
               $objUpload =new \App\Http\Service\Imagem\ImagePathService();
            }
            
            if (is_null($evento)){
                
                return $this->sendError("Não foi localizado evento com ID". $id_event);
            }
            
            
            
            
            $path_evento = \App\Http\Service\EventoService::getPathEvento($evento->id);
            $subfolder = \App\Http\Service\EventoService::getPrePasta($evento);
            if ( $eh_remoto ){
                  \App\Http\Service\EventoService::criarDir($evento->dia, $subfolder , $evento->id );
            }
            
            if ( $tempo == "" ){
                $tempo = 5;
            }
            
            $file = \Request::file('arquivo'); 
            
            $objUpload->sendImagem($file, $subfolder."/". $evento->dia."/".$evento->id."/". $file->getClientOriginalName());
            
            $reg = new \App\EventosArquivos();
            
            $reg->id_evento = $evento->id;
            $reg->com_temp_search = 0;
            
            $reg->path =  $subfolder. "/". $evento->dia."/".$evento->id."/". $file->getClientOriginalName();  
            $reg->nome = $file->getClientOriginalName();  
            
            $hora_inicio = \App\Http\Service\EventoArquivoService::getTimeFromFileName($reg->nome);
            
            if ( $hora_inicio != ""){
                    $hora_inicio_seg =  \App\Http\Service\UtilService::time_to_seconds($hora_inicio); // \App\Http\Service\EventoService::g
            } else {
                    $hora_inicio_seg = $evento->hora_inicio_seg;

                    $tot = \App\Http\Dao\ConfigDao::executeScalar("select sum(tempo_realizado_minutos) as res from eventos_arquivos"
                            . " where id_evento = ". $evento->id );

                    if ( is_null($tot) || $tot == ""){
                        $tot = 0;
                    }

                    $hora_inicio_seg += ($tot*60);

                    if ( $request->input("hora_inicio") != ""){
                        $hora_inicio_seg = \App\Http\Service\UtilService::time_to_seconds($request->input("hora_inicio"));
                    }
            }
           
            $reg->tempo_realizado_minutos = $tempo;  
            $reg->hora_inicio = \App\Http\Service\UtilService::converteSegundos_ParaHoraMinuto($hora_inicio_seg);  
            $reg->hora_inicio_seg = $hora_inicio_seg;  
            
            
            $reg->texto = $request->input('texto');  
            $reg->json = $request->input('json');  
            $reg->id_evento = $evento->id;
            $reg->save();
            
            $tot2 = \App\Http\Dao\ConfigDao::executeScalar("select sum(tempo_realizado_minutos) as res from eventos_arquivos "
                    . " where id_evento = ". $evento->id );
            
            $evento->tempo_realizado_minutos = $tot2;
            $evento->save();
            
           // $this->callSearchTemp($reg->id);
            //\App\Http\Service\TempSearchService::searchByArquivo($ls_tags, $id);
            
            return array("msg"=>"Arquivo salvo com sucesso!", "data"=> array("id" => $reg->id, "id_event"=>  $reg->id_evento ) );
        }
        
        public function get_lsTags($id_programa = ""){
            $ls_tags = array();
            
            /* if ( Cache::has("ls_tags") ){
                $ls_tags =Cache::get("ls_tags");
            }else{
                Cache::put("ls_tags", $ls_tags , 15); //Vou usar um cache de 15 minutos..
            }
             * 
             */
            
               $ls_tags =  \App\Http\Service\TempSearchService::getSQLTagsTotal($id_programa);
            return $ls_tags;
            
        }
        public function callSearchTemp($id){
            
               
            $ls_tags = array();
            
            /*
            if ( Cache::has("ls_tags") ){
                $ls_tags =Cache::get("ls_tags");
            }else{
               $ls_tags =  \App\Http\Service\TempSearchService::getSQLTagsTotal();
                Cache::put("ls_tags", $ls_tags , 15); //Vou usar um cache de 15 minutos..
            }
             * */
             
            //$ls_tags =  \App\Http\Service\TempSearchService::getSQLTagsTotal();
            
           $ls_tags =  \App\Http\Service\TempSearchService::getSQLTagsTotal( \App\Http\Service\TempSearchService::getIdProgramaFromIdArquivo($id) );
            $temp_search =  \App\Http\Service\TempSearchService::searchByArquivo($ls_tags, $id);
        }
        

}
