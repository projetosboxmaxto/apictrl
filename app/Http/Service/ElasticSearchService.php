<?php
namespace App\Http\Service;

use Illuminate\Support\Facades\DB;
use App\Http\Service\EventoService;
use App\Http\Service\EventoArquivoService;

use Illuminate\Http\Request;


class ElasticSearchService{
    
    
    static $prefixo = "/midiaclip/transcricao";
    static function create_arquivo($reg_arquivo, $caminho = ""){
        $elasticsearchurl = config("app.ELASTIC_URL");
        $data  = array("id" => $reg_arquivo->id,
                        "texto" => $reg_arquivo->texto,
                        "id_evento" => $reg_arquivo->id_evento,
                        "id_programa"=> \App\Http\Dao\ConfigDao::executeScalar("select id_programa  as res from eventos where id = ". $reg_arquivo->id_evento),
                        "data" => UtilService::getCurrentBDdate() ,
                        "nome" => $reg_arquivo->nome);
        
        if ( $caminho == ""){
            //testeindex/testename
            $caminho = self::$prefixo;
        }
        
        $res = self::call( $elasticsearchurl. $caminho."/". $reg_arquivo->id , "POST",
         (object)$data, "application/json");
        
        
        return $res;
    }

    static function remove_arquivo($reg_arquivo, $caminho = ""){
          $data = array();
        $elasticsearchurl = config("app.ELASTIC_URL");

          if ( $caminho == ""){
                //testeindex/testename
                $caminho = self::$prefixo;
          }

          $res = self::call( $elasticsearchurl. $caminho."/". $reg_arquivo->id , "DELETE",
                    (object)$data, "application/json");


           return $res;
    }
    
    static function executarFila(){
        
        $DB_MIDIACLIP = \App\Http\Dao\ConfigDao::getSchemaMidiaClip();
        $lista =  FilaAtividadeService::getListFila("elastic");
        $str_lista_eventos = "";
        
        //1 - Adiciono todos os arquivos no elastic search.
        for ( $i = 0; $i < count($lista); $i++ ){
             $item = $lista[$i];             
             $reg_arquivo = \App\EventosArquivos::find($item->id_evento_arquivo);
             if ( $str_lista_eventos != ""){
                 $str_lista_eventos .= ", ";
             }
             $str_lista_eventos .= $item->id_evento;
             self::create_arquivo($reg_arquivo);
         }
         //die("aqui?" . $str_lista_eventos ." -- ");
         
         //2-Vou buscar os arquivos que estão na fila. Cada um tem o seu programa.
           //2.1 - Vou trazer os programas.
         $lista_programas = DB::select("select distinct id_programa from eventos where id in ( ".$str_lista_eventos." ) " );
        
         for ( $z = 0; $z < count($lista_programas); $z++ ){
             $item_programa = $lista_programas[ $z ];
             $id_programa = $item_programa->id_programa;
             $id_praca = TempSearchService::getPracaIdByPrograma($id_programa);
             
             
             
                $sql_associacaoes = " select id_pai from ".$DB_MIDIACLIP.".associacao_cadastros "
                            . " where classificacao in ( 'entidadexprograma', 'subentidadexprograma', 'setorxprograma') "
                            . " and tabela_filho ='programa'  "
                            . " and id_filho=". $id_programa ; 

                $ids_clientes_programa = UtilService::arrayToString( DB::select($sql_associacaoes),"id_pai");
                // print_r( $lista_programas );die(" ids cliente ? ". $ids_clientes_programa );
                //Vou trazer os clientes deste programa..
                if ( $ids_clientes_programa != ""){
                    
                            $consultas =  \App\Http\Dao\ElasticQueriesDao::getElasticEnable($id_praca, $ids_clientes_programa);
                           
                            for ( $sub = 0; $sub < count($consultas); $sub++ ){
                                
                                $item_consulta = $consultas[$sub];
                                
                                $reg_log = ElasticSearchService::saveLog($item_consulta->id, $item_consulta->querie, "Cliente: ".
                                        $item_consulta->id_cliente . " - " . $item_consulta->nome_cliente. " praça: ". $id_praca);
                                
                                $json_retornado = self::call_search($item_consulta->querie);
                               // die( $json_retornado );
                                $resultado = json_decode( $json_retornado  );
                                
                                if ( property_exists($resultado, "hits") &&  $resultado->hits->total->value > 0 ){ //Nós achamos. Então vamos adicionar as coisas que encontramos. 
                                         $reg_log->texto = json_encode(  array("querie"=> $item_consulta->querie, "resultado" => $resultado));
                                         $reg_log->tipo="ok";
                                         $reg_log->save();
                                         
                                         self::save_hits_localizados($resultado, $item_consulta->id_cliente, $item_consulta->nome_cliente,
                                                   $id_programa, $item_consulta->id );
                                }

                            }
                    
                }
             
             
         }
         
         //já localizamos, já fizemos o merge.. agora vamos deletar as coisas do elastic que não iremos mais precisar.
             for ( $i = 0; $i < count($lista); $i++ ){
                     $item = $lista[$i];   
                     
                     $reg_arquivo = \App\EventosArquivos::find($item->id_evento_arquivo);
                     \App\Http\Service\FilaAtividadeService::setStatus($item->id_evento_arquivo , "elastic", 1 );
                     self::remove_arquivo( $reg_arquivo  ); //Removendo isso do elastic search, não vamos mais precisar dele...
                     
                     $reg_arquivo->com_elastic_search = 1;
                     $reg_arquivo->save();
             }

             
    }
    static function saveLog($id_arquivo, $querie, $titulo, $tabela= "elastic_search"){
        $registro = new \App\Log();
        
        $registro->data_inicio = UtilService::getCurrentBDdate();
        $registro->tabela = $tabela;
        $registro->registro_id = $id_arquivo;
        $registro->titulo = $titulo;
        $registro->texto = $querie;
        $registro->tipo = "find";
        
        $registro->save();
        
        return $registro;
    }
    static function executaQueries($lista, $caminho = ""){
        //https://stackoverflow.com/questions/21764766/determining-which-words-were-matched-in-a-fuzzy-search
        $DB_MIDIACLIP = config("app.DB_MIDIACLIP");
        
          for ( $i = 0; $i < count($lista); $i++ ){
                $item = $lista[$i];
                
                self::create_arquivo($item);
                
                
                $id_programa = \App\Http\Dao\ConfigDao::executeScalar("select id_programa as res from eventos where id = ". $item->id_evento );
                $id_praca = self::getPracaIdByPrograma($id_programa);
        
                $sql_associacaoes = " select id_pai from ".$DB_MIDIACLIP.".associacao_cadastros "
                            . " where classificacao in ( 'entidadexprograma', 'subentidadexprograma', 'setorxprograma') "
                            . " and tabela_filho ='programa'  "
                            . " and id_filho=". $id_programa ; 

                $ids_clientes_programa = UtilService::arrayToString( DB::select($sql_associacaoes),"id_pai");
                
                if ( $ids_clientes_programa != ""){
                    
                    
                           $consultas =  \App\Http\Dao\ElasticQueriesDao::getElasticEnable($id_praca, $ids_clientes_programa);
                           
                            for ( $z = 0; $z < count($consultas); $z++ ){
                                
                                $item_consulta = $consultas[$z];
                                
                                $reg_log = ElasticSearchService::saveLog($item->id, $item_consulta->querie, "Cliente: ".
                                        $item_consulta->id_cliente . " - " . $item_consulta->nome_cliente. " praça: ". $id_praca);
                                
                                $resultado = json_encode(  self::call_search($item_consulta->querie) );
                                
                                if ( $resultado->hits->value > 0 ){ 
                                         $reg_log->texto = json_encode(  array("querie"=> $item_consulta->querie, "resultado" => $resultado));
                                         $reg_log->tipo="ok";
                                         $reg_log->save();
                                }

                            }
                    
                }
                
                

                
          }
          
    }
    /*
     *       \App\Http\Service\EventosClientesService::salvarClientes($reg->id_evento, $clientes);
        \App\Http\Service\EventosClientesService::salvarClientesArquivos($id, $reg->id_evento, $clientes);
        
        //Salva a lista de palavras que correspondem`ao arquivo solicitado.
        \App\Http\Dao\EventosArquivosPalavrasDao::salvarClientes($clientes, $reg->id_evento, $reg->id);
        \App\Http\Dao\EventosArquivosPalavrasDao::agrupaNotificacoes($reg);
     */
    
    static function ja_tem_palavra($palavras, $palavra){
        
        for ( $y = 0; $y < count($palavras); $y++ ){
            $objeto_teste = (object)$palavras[$y];
            if ( $objeto_teste->nome == $palavra ){
                return true;
            }
        }
        return false;
    }
    static function ja_tem_cliente($obj_meta_dados, $id_cliente, $id_arquivo = ""){
        
        if (is_null($obj_meta_dados)){
            return false;
        }
        
        $clientes = (array)$obj_meta_dados->clientes;
        
        if ( count($clientes) <= 0 ){
            return false;
        }
        
        for ( $y = 0; $y < count($clientes); $y++ ){
            $cliente = (object)$clientes[$y];
            
            if ( $cliente->id == $id_cliente){
                return true;
            }
            
            
        }
        
        return false;
    }
       static function get_item_cliente($obj_meta_dados, $id_cliente){
        $clientes = (array)$obj_meta_dados->clientes;
        for ( $y = 0; $y < count($clientes); $y++ ){
            $cliente = (object)$clientes[$y];
            
            return $cliente;
            
            
        }
        return null;
        
    }
    static function adicionarPalavraAoMetaDados(&$item_cliente, $id_cliente, $palavra, $scored, $id_querie){
        
                   //   print_r( $item_cliente ); echo("<br>");
                    //$item_cliente = self::get_item_cliente( $obj_meta_dados, $id_cliente);
                    //$item_cliente = &$obj_meta_dados->$id_cliente;   
                    $palavras = &$item_cliente->palavras;
                    
                    if ( is_null( $palavras ) ){
                        $palavras = array();
                    }
                    
                    $item = array("id_cliente"=>$id_cliente, "nome"=>$palavra, "tipo"=>"dic",
                        "elastic"=>1,
                        "scored"=>$scored, "id"=>$id_querie );
                    
                    if (! self::ja_tem_palavra( $palavras , $palavra)){
                        
                              array_push($palavras, $item);
                    }
                  
                   //   print_r( $palavras ); echo("<br>");
    }
    
    static function save_hits_localizados( $resultado, $id_cliente, $nome_cliente, $id_programa, $id_querie ){
        $hits = $resultado->hits->hits; //_explanation->details;
        
       
        $obj_meta_dados = (object) array("clientes" => []);
        $clientes = array();
        $ids = "0";
        $arrayIds = new ArrayListService();

        for ( $y = 0; $y < count($hits); $y++ ){
            $item = $hits[$y];
           // echo(", ".  $item->_source->id );
            if ( $item->_source->id_programa != $id_programa ){
                //Se o resultado é do mesmo programa que estamos querendo, então vamos registrar.. 
                continue;
            }
            
            $ids .= ",".$item->_id;
            if ( ! $arrayIds->contains($item->_id)){
                $arrayIds->add( $item->_id );
            }
            $registro =  \App\EventosArquivos::find( $item->_id );
            $texto_completo = EventoArquivoService::getTextoFromJson($registro->json) ; 
            
            
            $clientes_add = null;
            if ( ! self::ja_tem_cliente($obj_meta_dados, $id_cliente) ){
                    $clientes_add = array("id"=>$id_cliente, "nome" => $nome_cliente,
                    "cita_diretamente" => strpos(" ". $texto_completo, $nome_cliente) > 0 ? 1 : 0 , "palavras" => []);
                    
                    $clientes_add = (object)$clientes_add;
                    array_push($obj_meta_dados->clientes,$clientes_add );
             
            }else{
                     $clientes_add = self::get_index_cliente($obj_meta_dados, $id_cliente);
            }
            
            for ( $t = 0; $t < count($item->_explanation->details); $t++ ){
                
                     $description = $item->_explanation->details[$t]->description;
                     
                        $palavra_localizada = $description; //(["'])(?:(?=(\\?))\2.)*?\1
                        //weight(message:\"capital baiana\" in 0) [PerFieldSimilarity], result of:

                        $matches = array();
                        $encontrei_essa_palavra = "";

                        preg_match_all('~(["\'])([^"\']+)\1~', $palavra_localizada, $matches); //Temos aqui as palavras q ele encontrou.

                        if ( count($matches) <= 0 ){   //weight(message:salvador in 0) [PerFieldSimilarity], result of:
                            $ar_tmp = explode(":", $palavra_localizada);
                            $txt = $ar_tmp[1];
                            $ar_tmp2 = explode(" in ", $txt);
                            $encontrei_essa_palavra = $ar_tmp2[0];
                        }else{
                            $encontrei_essa_palavra = $matches[1];
                        }
                        // print_r( $encontrei_essa_palavra );
                        if ( $encontrei_essa_palavra == "" || ( is_array($encontrei_essa_palavra) && count($encontrei_essa_palavra) <= 0  )){
                            $exp = explode(" in ",$palavra_localizada );
                            $exp0 = explode(":", $exp[0]);
                           // print_r( $exp0 );
                            $encontrei_essa_palavra = $exp0[1];
                        } else if (is_array($encontrei_essa_palavra) && count($encontrei_essa_palavra) > 0 ){
                            $encontrei_essa_palavra = join(" ", $encontrei_essa_palavra);
                        }
                        
                      //  print_r( $matches );  die(" achou ? ". $encontrei_essa_palavra);
                        if ( $encontrei_essa_palavra == "" || is_array($encontrei_essa_palavra))
                            continue;
                        //arrayList, $id_cliente, $palavra, $indexed, $id_querie
                        self::adicionarPalavraAoMetaDados( $clientes_add, $id_cliente,$encontrei_essa_palavra, $item->_score, $id_querie  );

            }
            
            
            /*  \App\Http\Service\EventosClientesService::salvarClientes($registro->id_evento, (array)$objeto_meta_dados->clientes );               
            \App\Http\Service\EventosClientesService::salvarClientesArquivos($registro->id, $registro->id_evento, (array)$objeto_meta_dados->clientes );
        
        //Esse aqui vai salvar as palavras..
            \App\Http\Dao\EventosArquivosPalavrasDao::salvarClientes( (array)$objeto_meta_dados->clientes, $registro->id_evento, $registro->id);
        
            //Salva a lista de palavras que correspondem`ao arquivo solicitado.
            \App\Http\Dao\EventosArquivosPalavrasDao::agrupaNotificacoes($registro);
             * */
            //{"clientes":[{"id":120125,"palavras":[{"id":12019174,"id_cliente":120125,
            // "tipo":"dic","texto":"Prefeitura de Camau00e7ari","nome":"Prefeitura de Camau00e7ari","indexed":3511},{"id":120191788,"id_cliente":120125,"tipo":"dic","texto":"Prefeito","nome":"Prefeito","indexed":3805},{"id":120191787,"id_cliente":120125,"tipo":"dic","texto":"Prefeitura","nome":"Prefeitura","indexed":3511},{"id":120191845,"id_cliente":120125,"tipo":"dic","texto":"camau00e7ari","nome":"camau00e7ari","indexed":361}],"nome":"PREFEITURA CAMAu00c7ARI"},{"id":1201233,"palavras":[{"id":12016825,"id_cliente":1201233,"tipo":"dic","texto":"Defensores","nome":"Defensores","indexed":3290}],"nome":"MINISTu00c9RIO Pu00daBLICO DO ESTADO DA BAHIA"},{"id":1201255,"palavras":[{"id":120191431,"id_cliente":1201255,"tipo":"dic","texto":"crime","nome":"crime","indexed":2994}],
            // "nome":"SSP-BA"}]}
            
            //Adiciono a palavra localizada no elastic search ao meta dados desse cliente...
   
            $registro->meta_dados_elastic = json_encode(  $obj_meta_dados ) ;
           
            $registro->save();
            
        }
          
        //print_r( $objeto_meta_dados );die(" ? ");
         //$ar_tmp = $arrayIds-> explode(",", $ids);
         for ( $y = 0; $y < $arrayIds->size(); $y++ ){
             $id_registro = $arrayIds->get($y);
             if ( $id_registro == "" || $id_registro == "0")
                 continue;
             
             $reg_tmp = \App\EventosArquivos::find( $id_registro );
             self::merge_data( $reg_tmp );
             self::salvaPalavrasDoArquivo($reg_tmp);
           

            
         }
        
    }
    
    static function salvaPalavrasDoArquivo($reg_tmp){
        
        
             DB::delete("delete from eventos_arquivos_clientes where id_evento_arquivo = ". $reg_tmp->id );
             DB::delete("delete from eventos_arquivos_palavras where id_evento_arquivo = ". $reg_tmp->id   );
             
            $obj_meta_dados = json_decode($reg_tmp->meta_dados);
            $clientes = $obj_meta_dados->clientes;
            
           // print_r( $clientes ); die(" pare aqui ?? ");
         
            \App\Http\Service\EventosClientesService::salvarClientes($reg_tmp->id_evento, $clientes);
            \App\Http\Service\EventosClientesService::salvarClientesArquivos($reg_tmp->id, $reg_tmp->id_evento, $clientes);

            //Salva a lista de palavras que correspondem`ao arquivo solicitado.
            \App\Http\Dao\EventosArquivosPalavrasDao::salvarClientes($clientes, $reg_tmp->id_evento, $reg_tmp->id);
            return  \App\Http\Dao\EventosArquivosPalavrasDao::agrupaNotificacoes($reg_tmp);
    }
    
    static function merge_data($registro){
        
        if ( is_null( $registro->meta_dados_elastic ) ){
            return false;
        }
        if ( is_null( $registro->meta_dados ) ){
            $registro->meta_dados = "{\"clientes\": []}";
        }
        
        $obj_meta_dados = json_decode($registro->meta_dados);
        $obj_meta_dados_elastic = json_decode($registro->meta_dados_elastic);
        
       // print_r( $obj_meta_dados ); echo("<br><br>"); print_r( $obj_meta_dados_elastic  ); die(" ");
        
        for ( $y = 0; $y < count($obj_meta_dados_elastic->clientes); $y++ ){
            $cliente_item = $obj_meta_dados_elastic->clientes[$y];
            
            if ( self::ja_tem_cliente($obj_meta_dados, $cliente_item->id , $registro->id )){
                
                $cliente_item_existente = self::get_item_cliente($obj_meta_dados, $cliente_item->id);
                if ( is_null(  $cliente_item_existente->palavras)  ){
                    $cliente_item_existente->palavras = array();
                }
                for ( $zz = 0; $zz < count($cliente_item->palavras); $zz++ ){
                    $item_palavra = $cliente_item->palavras[$zz];
                    if ( ! self::ja_tem_palavra($cliente_item_existente->palavras, $item_palavra->nome)){
                        array_push(  $cliente_item_existente->palavras, $item_palavra);
                    }
                }
                
            }else{
                //não tem cliente..
                array_push($obj_meta_dados->clientes, $cliente_item);
            }
        }
        if ( $registro->id == "159366"){
          //  print_r( $obj_meta_dados ); die(" ");
        }
        $registro->meta_dados = json_encode($obj_meta_dados);
        $registro->com_elastic_search = 1;
        $registro->save();
        
        FilaAtividadeService::remove($registro->id, "elastic");
    }
    
    
    
    static function call_search($termos){
        
            $elasticsearchurl = config("app.ELASTIC_URL");
     
           $url = $elasticsearchurl.self::$prefixo."/_search";

            $data = json_decode( '
                    { 
                                "query" :
                                   {
                                       "bool": {
                                           "must": [
                                                    {
                                                        "query_string":
                                                         {

                                                             "default_field" : "texto",
                                                             "query": ""                            
                                                         }
                                                     }
                                                   ]
                                             }
                                   },
                                   
                                   "explain": true
                      }
                      ');
            
            $query_tratada = str_replace("\n\r","", str_replace("\n","", $termos));
            $query_tratada = str_replace('“','"', $query_tratada);
            $query_tratada = str_replace('”','"', $query_tratada);
            $data->query->bool->must[0]->query_string->query = $query_tratada;
        
           // print_r( $data ); die(" mostre o json. ". json_encode($data));
            $res = self::call($url, "POST", $data, "application/json");
        
            return $res;
    }
    
    static function call($url, $method, $data, $contenttype = "application/x-www-form-urlencoded"){
        /*  array(
                            'var1' => 'some content',
                            'var2' => 'doh'
                        ) */
                   $postdata = json_encode($data); /* http_build_query(
                          $data
                       
                    ); */
                    $opts = array('http' =>
                        array(
                            'method'  => $method,
                            'header'  => 'Content-type: '. $contenttype,
                            'content' => $postdata,
                            'ignore_errors'=>true
                        )
                        
                        //
                             //     "Accept: application/json\r\n" ,
                    );
                    
                    //print_r( $opts ); die(" ");
                    $context  = stream_context_create($opts);
                    $result = file_get_contents($url, false, $context);
                    
                    return $result;
    }
    
    
}
