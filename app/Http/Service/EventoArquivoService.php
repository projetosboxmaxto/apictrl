<?php
namespace App\Http\Service;

use Illuminate\Support\Facades\DB;
use App\Http\Service\EventoService;

use Illuminate\Http\Request;


class EventoArquivoService{

      public static function getArquivos($id, $compl = "", $tipo = ""){
           $sql = \App\Http\Service\EventoService::sqlArquivos($tipo == "cut")." where 1 = 1 and ea.id_evento = ". $id . $compl . " order by ea.hora_inicio_seg ";
           $arquivos = DB::select( $sql );
           
           $url_base = env("PATH_URL_VIDEOS");
            
            for ( $i = 0; $i < count($arquivos); $i++ ){
                $item = &$arquivos[$i];
                 
                
                $item ->url_load = $url_base .  $item->path;
                $item->duracao = \App\Http\Service\UtilService::converteSegundos_ParaHoraMinuto(  $item->tempo_realizado_minutos * 60 ); 
            }
            
            return $arquivos;
            
        }
        
        
        public static function getTimeFromFileName($nome){
            //1554-sociedadeam_2019-11-12_14-55-03.mp3
            $ar = explode("_", $nome);
            $fim = $ar[count($ar) -1];
            $fim = str_replace(".mp3","", $fim);
            $fim = str_replace(".mp4","", $fim);
            $hora = str_replace("-",":", $fim);
            
            return $hora;
            
        }
     
     public static function getExtension($arq){
         $ar = explode(".", $arq);
         
         return $ar[count($ar) -1];
     }
    
      public static function buscarTextoFrom($json, $texto, &$trecho ){
            
              $txt = "";
              $objects = json_decode($json);
              $inicio = 0;
              $texto_semacento = UtilService::removeAcentos($texto);
              
                  for ( $z = 0; $z < count($objects); $z++ ){
                      
                       $object = $objects[$z];
                       
                        if ( !property_exists($object, "alternatives") || count($object->alternatives) <= 0 )
                           continue;
                        
                        $completo = $object->alternatives[0]->text;
                        $completo_semacento = UtilService::removeAcentos( $completo);
                        
                       // echo("\n <br>achei? ". $texto ." - ". $completo ." -- ". strstr(" ".  $completo, $texto ) );
                        
                        if (  stripos (" ".  $completo, $texto ) > 0 || stripos (" ".  $completo_semacento, $texto_semacento ) > 0 ){
                            
                            $trecho = $completo;
                            $time_saida = $object->start_time;
                            
                            if ( strlen($time_saida) > 10 ){
                                return UtilService::left($time_saida, 10);
                            }
                            
                            return $object->start_time;
                        } else {
                            
                            $proximo_index = self::localizaProximoIndex($objects, $z);
                            
                            if ( $z < count($objects)-1 && $proximo_index >-1  ){
                                
                                
                                  if (  stripos (" ".  $completo. " ". $objects[$proximo_index]->alternatives[0]->text, $texto ) > 0 
                                          || 
                                         stripos (" ".  $completo. " ". UtilService::removeAcentos( $objects[$proximo_index]->alternatives[0]->text) , $texto_semacento ) > 0  
                                          ){
                                      
                                               $trecho = $completo. " ". $objects[$proximo_index]->alternatives[0]->text;
                                      
                                      
                                               $time_saida = $object->start_time;
                                                if ( strlen($time_saida) > 10 ){
                                                    return UtilService::left($time_saida, 10);
                                                }
                                      
                                               return $object->start_time;
                                  }
                                
                            }
                            
                        }
                  }
                  
                  return 0;
                       

      }
      
      public static function localizaProximoIndex($objects,$index_atual ){
           for ( $z = $index_atual+1; $z < count($objects); $z++ ){
                       $object = $objects[$z];
                 if ( !property_exists($object, "alternatives") || count($object->alternatives) <= 0 )
                           continue;
                 
                 return $z;
           }
           
           return -1;
      }
    
     
      public static function getTextoFromJson($json){
            
              $txt = "";
              $objects = json_decode($json);
              $inicio = 0;

              if (! is_array( $objects ) &&  !( $objects instanceof Countable ) ){
              	return $txt;
         
              }
              
                  for ( $z = 0; $z < count($objects); $z++ ){
                      
                       $object = $objects[$z];
                       
                        if ( !property_exists($object, "alternatives") || count($object->alternatives) <= 0 )
                           continue;
                       
                        if ( property_exists($object->alternatives[0], "words")  ){
                             
                              $words = array();
                               for ( $u = 0; $u < count($object->alternatives[0]->words); $u++ ){
                                   
                                   $word = $object->alternatives[0]->words[$u];
                                   
                                   $word->start_time = $word->start_time + $inicio;
                                   $word->end_time = $word->end_time + $inicio;
                                   
                                   if ( $txt != ""){
                                       $txt .= " ";
                                   }
                                   
                                   $txt .= $word->text;
                                   
                                  // $words[count($words)] = $word;                                   
                               }
                               
                           // $alternatives[0]["words"] = $words;
                         }else {
                                   if ( $txt != ""){
                                       $txt .= " ";
                                   }
                                   
                                   $txt .= $object->alternatives[0]->text;
                         }
                      
                      
                  }
                  
                  return $txt;

      }
    
     public static function obtemJsonCorte($id_arquivo_pai, $inicio_corte, $fim_corte){
        
         $sql = "select * from eventos_arquivos where id = ".$id_arquivo_pai." order by hora_inicio_seg ";
         $lista = DB::select($sql);
         
         $obj_json  = array();
         
         //$json_inicio = json_decode($lista[0]->json);
         
         $segment_index = 0;
         
         for ( $i = 0; $i < count($lista); $i++ ){
              $inicio = $inicio_corte * -1; //-1 para retirar o tempo inicio da legenda.
              
              $objects = json_decode($lista[$i]->json);
              
              if ( ! is_array($objects)){
                  continue;
              }
              
               for ( $z = 0; $z < count($objects); $z++ ){
                      
                       $object = $objects[$z];
                       
                        if ( !property_exists($object, "start_time")){
                            continue;
                        } 
                       
                       if ( $object->start_time < $inicio_corte )
                           continue; //Legenda abaixo do tempo, não precisamos pegar..
                       
                       if ( $object->start_time > $fim_corte ) //o início dessa legenda ja passou o trecho que pedimos no corte. Podemos cortar isso.
                           break;
                       
                       if ( !property_exists($object, "alternatives") || count($object->alternatives) <= 0 )
                           continue;
                   
                       $alternatives = array();
                       $alternatives[0] = array("text"=> $object->alternatives[0]->text, "words" => array() );
                       
                          
                         if ( property_exists($object->alternatives[0], "words")  ){
                             
                              $words = array();
                               for ( $u = 0; $u < count($object->alternatives[0]->words); $u++ ){
                                   
                                   $word = $object->alternatives[0]->words[$u];
                                   
                                   $word->start_time = $word->start_time + $inicio;
                                   $word->end_time = $word->end_time + $inicio;
                                   
                                   $words[count($words)] = $word;                                   
                               }
                               
                            $alternatives[0]["words"] = $words;
                         }
                          
                       $item_add = array("alternatives"=>$alternatives, "start_time" => $object->start_time + $inicio,
                            "end_time"=> $object->end_time + $inicio , "segment_index" => $segment_index);
                   
                       
                       $segment_index++;
                       
                       $obj_json[count($obj_json)] = (object)$item_add;
                         
               }
              
         }
         
         return json_encode($obj_json);
        
    }
    
    //$id, $id_materia, $id_materia_frags, $json_data, $clientes
    public static function geraMateriaByArquivos($ids_arquivos, $id_materia, $id_materia_frags, $json_data, $clientes, $ls_tags, Request $request ){
        
        //Nessa função aqui vou apenas testar se temos mais de um arquivo, caso tenha, vamos precisar dar um join neles.
        
        $path_evento = "";
        $id_arquivo_final = "";
        
        $reg_evento = null; //  
        $nome_final_arquivo = "";
        
        $lista_arquivo_ordenado = DB::select("select id from eventos_arquivos where id in ( ". $ids_arquivos. " ) order by hora_inicio_seg asc ");
        
        $ids_arquivos = UtilService::arrayToString($lista_arquivo_ordenado, "id");
        
        $ar = explode(",",  $ids_arquivos );
        
                      $files = array();
                      $extensao = "";
        
                      if ( count($ar) > 1){
                           //Mais de um arquivo? Vou juntar todos eles num arquivo só.
                                        for($i = 0; $i < count($ar); $i++ ){

                                            $id_arquivo = $ar[$i];
                                            if ( $id_arquivo == ""){ 
                                                continue;                             
                                            }

                                            $arquivo = \App\EventosArquivos::find($id_arquivo);
                                            $files[$i] = $path_evento. DIRECTORY_SEPARATOR . $arquivo->nome;
                                            
                                            if ( $i == 0 ){

                                               $reg_evento =  \App\Eventos::find($arquivo->id_evento);
                                               $ext =  explode(".",  $arquivo->nome );
                                               $extensao = $ext[count($ext) -1 ];

                                               $path_evento = \App\Http\Service\EventoService::getPathEvento($arquivo->id_evento);
                                            }


                                            $arquivo = \App\EventosArquivos::find($id_arquivo);
                                            $files[$i] = $path_evento. DIRECTORY_SEPARATOR . $arquivo->nome;
                                           // $ids_arquivos .= ",".$arquivo->id;

                                        }

                                       // print_r( $files );die(" ");
                                        $txt_file = \App\Http\Service\FFmpegService::getFileTxt($files, $path_evento  );


                                        $nome_final_arquivo = "file".date("YmdHis")."_join.".$extensao;

                                        $comando_final = "-f concat -safe 0 -i \"" . $txt_file . "\" -c copy " . "\"" .
                                                    $path_evento . DIRECTORY_SEPARATOR . $nome_final_arquivo . "\"";

                                        $ret = \App\Http\Service\FFmpegService::executeCommand($comando_final);
                                        

                                        $id_arquivo_final =  \App\Http\Service\EventoService::salvarArquivoEventoFilho($reg_evento, $ids_arquivos, "join",
                                                $nome_final_arquivo, $ls_tags);
                      
                                        
                                        
                      } else {
                          
                                        //Não preciso fazer nada aqui...
                                        $id_arquivo_final = $ids_arquivos;
                      }
                      
                      // die($ret );
                      
                      return self::geraMateriaByArquivo($id_arquivo_final,
                              $id_materia, $id_materia_frags, $json_data, $clientes, $request);
                      
                     
    }
    
    
    public static function limpaNotificacoes( $id_materia ){
        
        
    }
    
    
    public static function geraMateriaByArquivo($id, $id_materia, $id_materia_frags, $json_data, $clientes,  Request $request){
        
         $obj_json = json_decode($json_data);
         $reg =  \App\EventosArquivos::find($id);
         $reg_evento = \App\Eventos::find($reg->id_evento);
         
         $obj_materia_frags = json_decode($id_materia_frags);
         
         if (false && property_exists($obj_materia_frags, "clientes") ){
            
            $obj_clientes = json_decode($clientes);
            
            $frag_clientes = $obj_materia_frags->clientes;
            $frag_impactos = $obj_materia_frags->impactos;
            
                //   print_r( $frag_clientes );
            
         }
         
         
         $AUTO_INCREMENT = \App\Http\Dao\ConfigDao::getValor("AUTO_INCREMENT");
         
        // die("espere aqui");
         
         $registro = new \App\MateriaRadiotvJornal();
         
         if ( $AUTO_INCREMENT ){
             $registro->incrementing = true;
         }
         
         if (! is_null($id_materia) && $id_materia != ""){
             
                 $registro->id = $id_materia;
         }
         
         $registro->servidor = $obj_materia_frags->servidor;
         $registro->ano = $obj_materia_frags->ano;
         $registro->sequencial = $obj_materia_frags->sequencial;
         
         $registro->id_modulo = 3;
         $registro->data_insert_materia = UtilService::getCurrentBDdate();
         $registro->banco_importado = "transcricao";
         $registro->id_registro_importado =$reg->id;
         $registro->tabela_importado = "eventos_arquivos";
         
         
         $registro->hora_inicio = $reg->hora_inicio;
         //$registro->hora_inicio_seg = $reg->hora_inicio_seg;
         $registro->duracao_segundos = $reg->tempo_realizado_minutos * 60;
         $registro->duracao = UtilService::converteSegundos_ParaHoraMinuto(  $registro->duracao_segundos  ) ;
         $registro->hora_fim = UtilService::converteSegundos_ParaHoraMinuto(  $reg->hora_inicio_seg + $registro->duracao_segundos ) ;
         
         $arp = explode(" ", $reg_evento->data);
         
         $registro->data_hora_materia = $arp[0]." ".$registro->hora_inicio;
         $registro->data_materia = $reg_evento->data;
         
         $registro->id_emissora = $reg_evento->id_emissora;
         $registro->id_veiculo = \App\Http\Dao\ConfigDao::executeScalar2("select id_veiculo as res from emissora where id = ". $reg_evento->id_emissora);
         $registro->titulo = utf8_decode(  $obj_json->titulo );
         $registro->texto = utf8_decode(  $obj_json->sinopse );
         $registro->texto_html = utf8_decode( $obj_json->sinopse );
         $registro->id_operador = $reg_evento->id_operador;
         $registro->status_atual = 0;
         
         
         $registro->save();
         
         $id_materia = $registro->id;
         
         $registro_rtv = new \App\MateriaRadioTv();
         if ( $AUTO_INCREMENT ){
            // $registro_rtv->incrementing = true;
         }
         $registro_rtv->id = $registro->id;
         $registro_rtv->id_programa = $reg_evento->id_programa;
         
         if (property_exists($obj_json, "id_apresentador")){
                  $registro_rtv->id_apresentador = $obj_json->id_apresentador;
             
         }
         
         $registro_rtv->save();
         
         
         $pasta_destino = $obj_materia_frags->path;
         $pasta_origem = EventoService::getPathEvento($reg_evento->id);
         //"yyyyMMdd_HHmm"
         $date_ind =  date("Ymd_Hi");
         $nome_destino = $id_materia.".1.".$date_ind.".".self::getExtension($reg->nome);
         
         copy($pasta_origem.DIRECTORY_SEPARATOR.$reg->nome, $pasta_destino.DIRECTORY_SEPARATOR.$nome_destino);
         
         $meta_dados = $obj_materia_frags->meta_dados;
         
         for($i =0; $i < count($meta_dados); $i++ ){
             
             $meta = $meta_dados[$i];
             
             if ( $meta->Codigo == "arquivo"){
                 
                 $ar = explode(";", $meta->Nome );
                 
                 $obj_arquivo = new \App\Arquivos();
                 if ( @$ar[0] != ""){                    
                          $obj_arquivo->id = $ar[0];
                 }
                 $obj_arquivo->servidor = $ar[1];
                 $obj_arquivo->ano = $ar[2];
                 $obj_arquivo->sequencial = $ar[3];
                 $obj_arquivo->id_materia = $id_materia;
                 $obj_arquivo->tabela = "materia_radiotv_jornal";
                 $obj_arquivo->nome = $nome_destino;
                 $obj_arquivo->data_cadastro = UtilService::getCurrentBDdate();
                 $obj_arquivo->duracao_segundos = UtilService::time_to_seconds( $reg->duracao  );
                 $obj_arquivo->duracao = UtilService::converteSegundos_ParaHoraMinuto(  $reg->duracao_segundos );
                 $obj_arquivo->codigo = "dvd";
                 
                 if ( $AUTO_INCREMENT ){
                        $obj_arquivo->incrementing = true;
                 }
                 
                 $obj_arquivo->save();
                 
                 
             }
         }
         
         $reg->id_materia_radiotv_jornal = $id_materia;
         $reg->save();
         //Agora preciso de um registro de arquivo...
         
         $reg_rascunho =  MateriaRascunhoService::salvar($request, $obj_json->id_rascunho,$id_materia );
         //Já avisei que o rascunho está com o arquivo enviado para a matéria, vou deletar o antigo.
        // @unlink( $pasta_origem.DIRECTORY_SEPARATOR.$reg->nome ); //Removi o arquivo antigo.
         
        $sql = \App\Http\Service\EventoService::sqlArquivos(true)." where 1 = 1 and ea.id = ". $reg->id . " order by ea.hora_inicio_seg ";
        $arquivos = DB::select( $sql );
        
     
        
        /*
         * {"id":"120192","servidor":"1","ano":"2019","sequencial":"2","meta_dados":[{"Codigo":"arquivo","Nome":"120191;1;2019;1"}],
         * "clientes":[{"Codigo":"120191","Nome":"1;2019;1"}],
         * "impactos":[{"Codigo":"120191","Nome":"1;2019;1"}]}
         */
        if ( property_exists($obj_materia_frags, "clientes") ){
            
            $obj_clientes = json_decode($clientes);
            
            $frag_clientes = $obj_materia_frags->clientes;
            $frag_impactos = $obj_materia_frags->impactos;

             for($i =0; $i < count($obj_clientes); $i++ ){
                 //"clientes":[{"Codigo":"120191","Nome":"1;2019;1"}],"impactos":[{"Codigo":"120191","Nome":"1;2019;1"}
                 $frag_cliente = $frag_clientes[$i];
                 $frag_impacto = $frag_impactos[$i];
                 
                 $oAssociacao = new  \App\AssociacaoMateriaRadiotvJornal();
                 
                 if ( !is_null($frag_cliente->Codigo) &&  $frag_cliente->Codigo != ""){
                           $oAssociacao->id =  $frag_cliente->Codigo; 
                 }
                 $ar = explode(";", $frag_cliente->Nome );

                 $oAssociacao->servidor = $ar[0];
                 $oAssociacao->ano = $ar[1];
                 $oAssociacao->sequencial = $ar[2];
                 $oAssociacao->id_materia_radiotv_jornal = $id_materia;
                 $oAssociacao->id_entidade = $obj_clientes[$i]->id;
                 $oAssociacao->id_tipo_entidade = \App\Http\Dao\ConfigDao::executeScalar2("select id_tipo as res from cliente where id = " .  $obj_clientes[$i]->id);
                 
                 $oAssociacao->data_materia =  $registro->data_materia ;
                 $oAssociacao->id_emissora =  $registro->id_emissora ;
                // $oAssociacao->id_veiculo =  $registro->id_veiculo ;
                 $oAssociacao->id_categoria =1;
                 
                 
                $classificacao = "materiartv";
                
                if ($oAssociacao->id_tipo_entidade == "21")
                {
                    $classificacao .= "xentidade";

                }
                else if ($oAssociacao->id_tipo_entidade == "22")
                {
                    $classificacao .=  "xsubentidade";
                }
                else if ($oAssociacao->id_tipo_entidade  == "23"){
                    $classificacao .=  "xsetor";
                }
                $oAssociacao->classificacao = $classificacao;
                
                 if ( $AUTO_INCREMENT ){
                        $oAssociacao->incrementing = true;
                 }
                $oAssociacao->save();
                  
                
                
                $oImpacto = new \App\AvaliacaoImpacto();
                
                 $oImpacto->id_materia = $id_materia;
                 $oImpacto->id_cliente =  $oAssociacao->id_entidade;
                 $oImpacto->tabela_materia = "materia_radio_tv_jornal";
                 $oImpacto->data_materia =  $registro->data_materia ;
                 $oImpacto->id_impacto = $obj_clientes[$i]->id_impacto;
                 
                 //id_categoria_cliente
                 if (property_exists($obj_clientes[$i], "id_topico") && !is_null($obj_clientes[$i]->id_topico)  && $obj_clientes[$i]->id_topico != ""){
                     $oImpacto->id_categoria_cliente =$obj_clientes[$i]->id_topico;
                 }
                 
                 $ar = explode(";", $frag_impacto->Nome );
                 
                 if ( !is_null($frag_impacto->Codigo) &&  $frag_impacto->Codigo != ""){
                        $oImpacto->id = $frag_impacto->Codigo;
                 }
                 $oImpacto->servidor = $ar[0];
                 $oImpacto->ano = $ar[1];
                 $oImpacto->sequencial = $ar[2];
                 
                 $citacao_direta = 0;
                 
                 if (property_exists($frag_cliente, "citacao_direta")){
                     
                   //     $citacao_direta =  $frag_cliente->citacao_direta;
                 }
                  if (property_exists($obj_clientes[$i], "cita_diretamente")){
                     
                     $citacao_direta =  $obj_clientes[$i]->cita_diretamente;
                 }
                 
                 $oImpacto->cita_cliente  = $citacao_direta;
                 
                 if ( $AUTO_INCREMENT ){
                        $oImpacto->incrementing = true;
                 }
                 $oImpacto->save();
                 
             }
             
             $url_sistema = env("PATH_SISTEMA_MIDIACLIP");
             $eh_integrador  = \App\Http\Dao\ConfigDao::getValor("EH_INTEGRADOR");
             $url_integrador  = \App\Http\Dao\ConfigDao::getValor("URL_API_INTEGRADOR");
             
             if ( $url_sistema != "" && $eh_integrador == ""){
                     \App\Http\Service\UtilService::recebe_html($url_sistema."importacao/handlerMateriaRtv.aspx?acao=completa_materia&id=".$id_materia);
                         // UtilService::recebe_html($url_sistema."importacao/handlerMateriaRtv.aspx?acao=completa_materia&id=".$id_materia);
                 
             }
             if ( $url_integrador != "" && $eh_integrador == "1"){
                 
                     \App\Http\Service\UtilService::recebe_html($url_integrador."integrador?acao=completa_materia&id=".$id_materia);
             }
            
        }
        
        //if ( count($arquivos) > 0 ){
        //    return $arquivos[0];
       // }
         
         return array("id_materia_radiotv_jornal" => $id_materia, 
                   "id_rascunho" => $reg_rascunho->id, 
             "ids_arquivos" => $reg->id,
             "id_materia_gerada" =>  $id_materia); 
         //$registro;
         
        
    }
    
    public static $ffmpeg_last = array();
    
    public static function ajustaTempoFilhos($id_arquivo_pai){
        
            $id_join = $id_arquivo_pai; 
            $arquivo_pai = \App\EventosArquivos::find($id_join);
            $sql = "select id, tempo_seg, tempo from eventos_arquivos_palavras where id_evento_arquivo = ".$id_arquivo_pai;
            
            $hora_inicio_seg = UtilService::time_to_seconds2( $arquivo_pai->hora_inicio );
                
                
            $lista = DB::select($sql);
            
            for ( $i = 0; $i < count($lista); $i++ ){
                
                $item = $lista[$i];
                $tempo_seg = UtilService::time_to_seconds2(  $item->tempo ) + $hora_inicio_seg;
                DB::update("update eventos_arquivos_palavras set tempo_seg = " . $tempo_seg . " where id = ". $item->id );
                
                
            }
    }
    
    public static function clonaPalavrasArquivoPai($id_arquivo_filho, $evento_pai, $id_arquivo_pai,  $inicio, $fim){
        
            $id_join = $id_arquivo_pai; 
            $arquivo_pai = \App\EventosArquivos::find($id_join);
            
            self::ajustaTempoFilhos($id_arquivo_pai);
            
            if (is_null($arquivo_pai->hora_inicio_seg) &&  $arquivo_pai->hora_inicio != ""){
                $arquivo_pai->hora_inicio_seg = UtilService::time_to_seconds2($arquivo_pai->hora_inicio);
                $arquivo_pai->save();
            }
            
            $arquivo_filho = \App\EventosArquivos::find($id_arquivo_filho);
            
            $hora_inicio_seg = $arquivo_pai->hora_inicio_seg + $inicio;
            $hora_fim_seg = $arquivo_pai->hora_inicio_seg + $fim;
            
            $sql = "select * from eventos_arquivos_palavras where id_evento_arquivo = ".$id_arquivo_pai. 
                    " and tempo_seg >= ". $hora_inicio_seg . " and tempo_seg <=  ". $hora_fim_seg;
            
            //21300
           // die($sql  . " - ". UtilService::converteSegundos_ParaHoraMinuto(21300) ." - ". $arquivo_pai->hora_inicio ." - ". UtilService::time_to_seconds2($arquivo_pai->hora_inicio));
            $lista = DB::select($sql);
            
            for ( $i = 0; $i < count($lista); $i++ ){
                
                $item = $lista[$i];
                
                $obj = new \App\EventosArquivosPalavras();
                
                
                    $obj->data = $item->data;
                    $obj->id_evento = $arquivo_filho->id_evento;
                    $obj->id_evento_arquivo = $arquivo_filho->id;
                    $obj->id_cliente = $item->id_cliente;
                    $obj->cita_diretamente = $item->cita_diretamente;
                    $obj->palavra = $item->palavra;
                    $obj->tempo = $item->tempo;
                    $obj->tempo_seg = $item->tempo_seg;
                    $obj->id_dicionario_tag = $item->id_dicionario_tag;
                    $obj->status = $item->status;
                    $obj->operador = $item->operador;
                    $obj->id_operador = $item->id_operador;
                    $obj->id_materia_radiotv_jornal = $item->id_materia_radiotv_jornal;
                    $obj->trecho = $item->trecho;
                    $obj->id_notificacao_agrupamento = $item->id_notificacao_agrupamento;
                    $obj->indexed = $item->indexed;
                    
                    \App\Http\Dao\EventosArquivosPalavrasDao::salvar($obj, $obj->palavra, $obj->operador, $obj->id_dicionario_tag); // salva..
                
            }
            
    }
    
     //Vamos gerar um novo arquivo..
     public static function gerarArquivoCorte($evento_pai, $id_arquivo_pai,  $inicio, $fim, $titulo = ""){
         
            $id_join = $id_arquivo_pai; // \App\Http\Dao\ConfigDao::executeScalar("select id as res from eventos_arquivos where id_evento = ".
                     // $evento_pai->id. " and tipo='join' ");
            
            $arquivo_pai = \App\EventosArquivos::find($id_join);
            $nome_arquivo_pai = $arquivo_pai->nome;
            $extensao = self::getExtension($nome_arquivo_pai);
         
            $path_evento_pai = \App\Http\Service\EventoService::getPathEvento($evento_pai->id_evento_pai);
            $path_evento = \App\Http\Service\EventoService::getPathEvento($evento_pai->id);
            
            if (!file_exists($path_evento)){
                mkdir($path_evento);
            }
            
            $json = self::obtemJsonCorte($arquivo_pai->id, $inicio, $fim);
            $texto = self::getTextoFromJson($json);
            
            $hora_inicio_seg = $arquivo_pai->hora_inicio_seg + $inicio;
            $hora_inicio = UtilService::converteSegundos_ParaHoraMinuto($hora_inicio_seg);
            
            
            $inicio_str = UtilService::converteSegundos_ParaHoraMinuto($inicio);
            $fim_str = UtilService::converteSegundos_ParaHoraMinuto($fim);
            
            $duracao_seg = $fim - $inicio;
            $tempo_realizado_minutos = $duracao_seg / 60;
            
            
            $reg_novo = new \App\EventosArquivos();
            $reg_novo->hora_inicio = $hora_inicio;
            $reg_novo->hora_inicio_seg = $hora_inicio_seg;
            $reg_novo->id_evento = $evento_pai->id;
            $reg_novo->json = $json;
            $reg_novo->texto = $texto;
            $reg_novo->tipo ="cut";
            $reg_novo->com_temp_search = 0;
            $reg_novo->titulo = $titulo;
            $reg_novo->tempo_realizado_minutos = $tempo_realizado_minutos;
            $reg_novo->save();
            
            $arquivo_novo = str_replace(":", "_", $inicio_str)."_".str_replace(":", "_", $fim_str)."_". $reg_novo->id.".".$extensao;
            
            $caminho_arquivo_pai = $path_evento_pai.DIRECTORY_SEPARATOR.$nome_arquivo_pai;
            
            $evento_armazenamento = \App\Http\Dao\ConfigDao::getValor("EventoArmazenamento");
            $UrlUploadEvento =  \App\Http\Dao\ConfigDao::getValor("UrlUploadEvento"); 
            
            //No caso do armazenamento da pasta eventos estar num outro servidor..
            if ( $evento_armazenamento == "remoto" && $UrlUploadEvento != ""){
                 //$url_download = $UrlUploadEvento."/eventos/".$evento_pai->dia."/".$evento_pai->id."/".$nome_arquivo_pai;
                 $url_download = $UrlUploadEvento . str_replace("//","/", "/". $arquivo_pai->path);
                 //die("url_download ". $url_download  );
                 $pasta_tmp = env("PATH_ARQUIVOS").DIRECTORY_SEPARATOR."tmp";
                 
                 if (!file_exists($pasta_tmp)){
                     mkdir($pasta_tmp);
                 }
                 
                 $caminho_arquivo_pai = $pasta_tmp.DIRECTORY_SEPARATOR.$nome_arquivo_pai;
                 if ( !file_exists($caminho_arquivo_pai)){
                          Imagem\CurlService::download($url_download, $caminho_arquivo_pai);
                 }
            }
            
            $ffmeg_retorno = FFmpegService::gerarCorte( $caminho_arquivo_pai  ,
                       $path_evento.DIRECTORY_SEPARATOR.$arquivo_novo, $inicio, $fim);
            
            //die(" -- ". $ffmeg_retorno);
            
            $reg_novo->nome = $arquivo_novo;
            $reg_novo->path = \App\Http\Service\EventoService::getPathEvento($evento_pai->id, false)."/".$arquivo_novo;
            
            $obj_meta_dados = array("corte"=>array("inicio"=>$inicio, "fim" => $fim),"titulo"=> $titulo );
            
            $reg_novo->meta_dados = json_encode($obj_meta_dados);
            $reg_novo->save();
            
            
            $ls_novo = self::getArquivos($reg_novo->id, "","cut");
            
            if ( count($ls_novo) > 0 ){
                return $ls_novo[0];
            }
            
            
            return $reg_novo;    

     }
     
    
    
}