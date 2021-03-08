<?php
namespace App\Http\Service;

use Illuminate\Support\Facades\DB;


class TempSearchService{
    
    static function getIdProgramaFromIdArquivo($id_arquivo){
        
        $sql = " select id_programa as res from eventos where id in ( select id_evento from eventos_arquivos where id in ( ".$id_arquivo." )  ) limit 0, 1 ";
        return \App\Http\Dao\ConfigDao::executeScalar($sql);
    }
    
    static function searchByArquivoPG($ls_tags, $reg, $id ){
        
        $lista = DB::connection('pgsql')->select("select * from temp_search where id_evento_arquivo = ". $reg->id );
        
        $temp = new \App\TempSearchPG();
         if ( count($lista) > 0 ){
            $temp = \App\TempSearch::find( $lista[0]->id );
            $temp->texto = $reg->texto;
            $temp->save();
            //print_r( $lista[0] );die(" ");
        }
        
        //https://www.compose.com/articles/mastering-postgresql-tools-full-text-search-and-phrase-search/
        /*
         * select * from temp_search
where texto_srch @@ to_tsquery('polícia & destaque')


 select * from temp_search
 /*  where texto_srch @@ to_tsquery('polícia' <10> 'bombeiros') */
 /* where texto_srch @@ tsquery_phrase('policia', 'bombeiros', 2) */
      /* where texto_srch @ @ to_tsquery('polícia & bombeiros')
         */
        
        $temp->texto = $reg->texto;
        $temp->id_evento_arquivo = $id;
        $temp->id_evento = $reg->id_evento;
        $temp->save();
        
         DB::connection('pgsql')->statement( 
                 " update temp_search set texto_srch = to_tsvector('portuguese', texto) where id =  ".$temp->id);
        
        
    }
    static function removeSemUso(){
        DB::statement("delete from temp_search where em_uso = 0 ");
    }
    
    static function getLikeByWords($words){
        if ( trim($words) == "")
            return "";
        
        $ar = explode(",",$words);
        
        $saida = "";
        for($i = 0; $i < count($ar); $i++ ){
             $querie = trim($ar[$i]);
             $querie = str_replace('+','',str_replace('"','',$querie));
             
             if ( $querie == "")
                 continue;
             
             if (strlen($querie) <= 4 ){
                 
                   $saida .= " and texto like '% ".$querie." %' ";
             } else {
                   $saida .= " and texto like '%".$querie."%' ";
             }
        }
        
        return $saida;
    }
    static function searchByArquivo($ls_tags, $id){
        
        //self::removeSemUso();
        
        $DB_MIDIACLIP = config("app.DB_MIDIACLIP");
        
        $reg = \App\EventosArquivos::find($id);
        
        if (is_null($reg)){
            return false;
        }
        
        if ( is_null(  $reg->texto ) || $reg->texto == "" ){
            $reg->texto = EventoArquivoService::getTextoFromJson($reg->json);
            $reg->save();
        }
        
        $lista = DB::select("select * from temp_search where id_evento_arquivo = ". $reg->id );
        $temp = new \App\TempSearch();
        
        if ( count($lista) > 0 ){
            $temp = \App\TempSearch::find( $lista[0]->id );
            //$temp->texto = $reg->texto;
            //$temp->save();
            //print_r( $lista[0] );die(" ");
        }
        
        $temp->texto = " ". $reg->texto." ";
        $temp->id_evento_arquivo = $id;
        $temp->id_evento = $reg->id_evento;
        $temp->em_uso = 1;
        $temp->save();
        
        //print_r($temp); die(" salvei para o arquivo ". $id . " qual a id do temp search? ". $temp->id );
        $USE_PGSEARCH = env("USE_PGSEARCH");
        
        
        $TRANSCRICAO_BUSCA_EXTERNA = \App\Http\Dao\ConfigDao::getValor("TRANSCRICAO_BUSCA_EXTERNA");  // até agora só boxnet vai usar essa situação..
       
        if ( $TRANSCRICAO_BUSCA_EXTERNA == "1" ){
            \App\Http\Dao\EventosArquivosPalavrasDao::buscaSemLike($id); // monta o json do arquivo e também a tabela de clientes usando as próprias palavras chaves deste arquivo.
            return $temp;
        }
        
        /* 
         * 
ELASTIC_ENABLE=1
ELASTIC_URL="http://rafaeldatabase:9200"
         */
        
        if ( $USE_PGSEARCH == "1"){
           // self::searchByArquivoPG($ls_tags, $reg, $id );
        }
        
        $id_programa = \App\Http\Dao\ConfigDao::executeScalar("select id_programa as res from eventos where id = ". $temp->id_evento );
        
        $sql_associacaoes = " select id_pai from ".$DB_MIDIACLIP.".associacao_cadastros "
                    . " where classificacao in ( 'entidadexprograma', 'subentidadexprograma', 'setorxprograma') "
                    . " and tabela_filho ='programa'  "
                    . " and id_filho=". $id_programa ; 
        
        $ids_clientes_programa = UtilService::arrayToString( DB::select($sql_associacaoes),"id_pai");
        if ( $ids_clientes_programa == ""){
            $ids_clientes_programa = " 0 ";
        }
        
        $ids_dicionario = "0";
        
        //and dt.tipo ='dic'
        
        $ids_clientes = "0";
        $ids_dicionario = "0";
        
        $inicio  = 0;
        $fim = 0;
        $qtde_registros = 200;
        //print_r( $ls_tags );
        
        $paginacao = UtilService::SetaRsetPaginacao($qtde_registros, 1, count($ls_tags), $inicio, $fim);
        $pre_lista = array();
        //print_r( $paginacao ); die(" ");
        
        //$ls_tags[1]->nome = utf8_encode($ls_tags[1]->nome);
        //print_r( $ls_tags ); die ( " " . json_encode($ls_tags, JSON_UNESCAPED_UNICODE));
        
        $id_cliente_last = "";
        $filtro_extra_last = "";
        
        for ( $pagina = 1 ; $pagina <= $paginacao["pageCount"]; $pagina++ ){
            
            $inicio = ($pagina-1)* $qtde_registros;
             if ( $inicio <  0 ){
                $inicio = 0;
            }
            $fim = $inicio + $qtde_registros;
            
           
            
            if ( $fim > count($ls_tags)){
                $fim = count($ls_tags);
            }
            //die(" inicio: ". $inicio . " fim: ". $fim);
            
             $sqlTags = "";
             $i = 0;
             for ( $i = $inicio; $i < $fim; $i++ ){
                 
                $item = $ls_tags[$i];
                
                if ( $id_cliente_last != $item->id_cliente ) {
                    $filtro_extra_last = self::getLikeByWords($item->querie);
                }
                
                if ( $sqlTags != ""){
                    $sqlTags .= "\n UNION ";
                }
                
                //
                //
                //" MATCH(texto) AGAINST ('".$item->nome."')  "
                $compl_consulta =  $filtro_extra_last;
                
                if ( $item->querie != "" && false ){
                    
                            //$compl_consulta = " MATCH(texto) AGAINST ('".$item->nome."' IN BOOLEAN MODE)  ";
                            $compl_consulta .= " and texto like '%".str_replace('+','',str_replace('"','',$item->querie))."%' ";
                           // $compl_consulta = " MATCH(texto) AGAINST ('+\"".$item->nome."\" ".$item->querie."')  ";
                }
                
                $filtro_like = " and texto like '%".$item->nome."%' ";
                if (strlen($item->nome) <= 4 ){                    
                      $filtro_like = " and texto like '% ".$item->nome." %' ";
                }
                
                $sqlTags .= " select ".$item->id." as id, ".$item->id_cliente." as id_cliente, '".$item->tipo."' as tipo, '".$item->nome."' as texto, "
                        . " '".$item->nome."' as nome, LOCATE('".$item->nome."', texto) as indexed " //,  '".$item->nome."' as texto_utf8
                        . "  from temp_search where 1 = 1 ".$compl_consulta. $filtro_like. " AND id = ". $temp->id ." \n "; 

                if ( $item->id_cliente == "120167" && false  ){
                    echo("<br><br>". str_replace("\n","<br>", $sqlTags) );
                           die( $sqlTags );
                }
                 
                $id_cliente_last = $item->id_cliente ;
               }
                //echo("<br><br>". str_replace("\n","<br>", $sqlTags). " percorri: ". $i );
                         // die( $sqlTags );
               if ( $sqlTags != ""){
               //die( $sqlTags );
                $sub_lista = DB::select($sqlTags);
              
                 
                if ( count($sub_lista) > 0 ){
                    
                    for ( $i = 0; $i < count($sub_lista); $i++ ){
                        
                            //$sub_lista[$i]->texto_utf8 = utf8_encode($sub_lista[$i]->texto);

                             array_push($pre_lista, $sub_lista[$i]);
                    }
                }
             }
            
        }
        
     //  die("pare aqui");
       //  die ( $sqlTags );
        $palavra_clientes = array();
        
        $arrayList = new \App\Http\Service\ArrayListService(explode(",", $ids_clientes));
           for ( $i = 0; $i < count($pre_lista); $i++ ){
               
                $item = (object)$pre_lista[$i];
                
                //print_r( $item ); die(" ");
                
                if ( !property_exists($item, "tipo")){
                //    continue;
                }
                
                if ( $item->tipo =='cli'){
                    $ids_clientes .= ",". $item->id;
                    if ( ! $arrayList->contains($item->id_cliente)){
                        $arrayList->add($item->id_cliente);
                    }
                }
                 if ( $item->tipo =='dic'){
                    $ids_dicionario .= ",". $item->id;
                    if ( ! $arrayList->contains($item->id_cliente)){
                        $arrayList->add($item->id_cliente);
                    }
                    if (!is_array( @$palavra_clientes[ $item->id_cliente ] ) ){                        
                        $palavra_clientes[ $item->id_cliente ] = array();
                    }
                    $pl_cliente = &$palavra_clientes[ $item->id_cliente ];
                    array_push($pl_cliente, $item);
                    
                   // print_r( $item );
                   
                }
           }
            
        $json = array();
        //print_r( $pre_lista );
        //print_r( $palavra_clientes );die(" " . $ids_clientes);
      //  die(" idsclientes: ". $ids_clientes . " and ". $ids_dicionario." ". $arrayList->size());
        
        if ( $ids_clientes != "" || $ids_dicionario != ""){
            
            /*
            $sql_cliente = self::getSQLTags($ids_dicionario, " distinct c.nome, c.id ", 
                    " and aca.id_pai in ( ".$ids_clientes_programa." )"
                    . "  UNION select c.nome, c.id from ".$DB_MIDIACLIP.".cliente c"
                    . "    where c.id in ( ". $ids_clientes." ) ".
                    " and c.id in ( ".$ids_clientes_programa." )" );
            
            $list_clientes = DB::select($sql_cliente);
            */
            $clientes = array();
            
            
           for ( $y = 0; $y < $arrayList->size(); $y++ ){
               if ( $arrayList->get($y) == "0")
                   continue;
               
               $item_cliente = (object) array("id"=> $arrayList->get($y), "palavras" =>"",
                     "nome"=> utf8_encode( 
                              \App\Http\Dao\ConfigDao::executeScalar2("select nome  as res from cliente where id = ".$arrayList->get($y))) ); //$list_clientes[$y];
               //$sql_tags = self::getSQLTags($ids_dicionario, " distinct dt.id, dt.nome ", 
               //         " and aca.id_pai = ". $item_cliente->id );
               
               //$itens = DB::select($sql_tags);
               // $item_cliente->palavras = $itens;
              // print_r( $palavra_clientes[ $item_cliente->id ]);die(" ");
               $item_cliente->palavras = @$palavra_clientes[ $item_cliente->id ];
               
               if ( $arrayList->contains($item_cliente->id)){
                   //$item_cliente->citacao_direta = 1;
               }
               
               $clientes[count($clientes)] = (object)$item_cliente;
         
           }
            
            
            
        }
        
       $json_obj = array("clientes" => array() );
       
       if ( ! is_null( $reg->meta_dados ) && trim($reg->meta_dados) !="" ){
           $json_obj = (array) json_decode($reg->meta_dados);
       }
       $json_obj["clientes"] = $clientes;
       
        $reg->meta_dados = json_encode($json_obj, JSON_UNESCAPED_UNICODE);
        $reg->com_temp_search = 1;
        
        $temp->json = json_encode($clientes, JSON_UNESCAPED_UNICODE);
        $temp->em_uso = 0;
        $temp->save();
        
        $reg->save();
        
       // print_r( $clientes );die(" ");
        
        DB::delete("delete from eventos_arquivos_clientes where id_evento_arquivo = ". $reg->id );
        DB::delete("delete from eventos_arquivos_palavras where id_evento_arquivo = ". $reg->id  );
         
        \App\Http\Service\EventosClientesService::salvarClientes($reg->id_evento, $clientes);
        \App\Http\Service\EventosClientesService::salvarClientesArquivos($id, $reg->id_evento, $clientes);
        
        //Salva a lista de palavras que correspondem`ao arquivo solicitado.
        \App\Http\Dao\EventosArquivosPalavrasDao::salvarClientes($clientes, $reg->id_evento, $reg->id);
        \App\Http\Dao\EventosArquivosPalavrasDao::agrupaNotificacoes($reg);
        
        return $temp;


        
    }
    
    static function getPracaIdByPrograma($id_programa = ""){
        
        $DB_MIDIACLIP = config("app.DB_MIDIACLIP");
        
        /*
         * 
                   select id, descricao from cadastro_basico where id in (  
       select distinct id_praca from emissora where id in (
       
  select id_filho from associacao_cadastros 
                   where classificacao in ( 'programaxcanal_comunicacao') 
                    and tabela_pai ='programa'
                   and id_pai in  ( 
       select id from programa where transcricao_ativar = 1 and id in (
         select id_filho from associacao_cadastros 
                   where classificacao in ( 'entidadexprograma', 'subentidadexprograma', 'setorxprograma') 
                    and tabela_pai ='cliente'
                   and id_pai=  1201219    
                  ) ) ) )
       
         * 			  
					  insert into search_queries (id_cliente, id_praca, querie, ativo) values
					     ( 120125, 1201240  , '+"camaçari"', 1 ),
					     ( 120125, 1201244  , '+"camaçari"', 1 ),
					     ( 120125, 1201250  , '+"camaçari"', 1 )
						    
				  
         */
        
        $sql = "  select distinct id_praca as res from emissora where id in (

                            select id_filho from associacao_cadastros 
                                             where classificacao in ( 'programaxcanal_comunicacao') 
                                              and tabela_pai ='programa'
                                             and id_pai in  (  ".$id_programa.") ) ";
        
        return \App\Http\Dao\ConfigDao::executeScalar2($sql);
        
    }
    
    static function getSQLTagsTotal($id_programa = ""){
        
        $DB_MIDIACLIP = config("app.DB_MIDIACLIP");
       
        $ids_clientes = "";
        
        
        if ( $id_programa != ""){
             $sql_associacaoes = " select id_pai from ".$DB_MIDIACLIP.".associacao_cadastros "
                    . " where classificacao in ( 'entidadexprograma', 'subentidadexprograma', 'setorxprograma') "
                    . " and tabela_filho ='programa'  "
                    . " and id_filho=". $id_programa ; 
           
             
             //entidadexprograma
             $ids_clientes = UtilService::arrayToString( DB::select($sql_associacaoes),"id_pai");
       
        }
        
        if ( $ids_clientes == ""){
            $ids_clientes = " 0 ";
        }
        
        $sql = " select dt.id, convert(dt.nome using utf8) as nome, convert(c.nome using utf8)  as nome_cliente,"
                . "  c.id as id_cliente, 'dic' as tipo, '' as querie from ".$DB_MIDIACLIP.".dicionario_tags dt 
                    inner join ".$DB_MIDIACLIP.".associacao_cadastros aca on 
                          (aca.classificacao = 'clientexdicionario' and aca.id_filho = dt.id and aca.tabela_filho = 'dicionario_tags' )
                          inner join ".$DB_MIDIACLIP.".cliente c on c.id = aca.id_pai 
                  where c.status = 1 and dt.tipo in( 'dic' ) ";
        
        if ( $id_programa != ""){
           $id_praca = self::getPracaIdByPrograma($id_programa);
           if ( $id_praca == ""){
               $id_praca = "-1";
           }
           
            $sql = " select dt.id, convert(dt.nome using utf8) as nome, convert(c.nome using utf8) as nome_cliente, 
                        c.id as id_cliente, 'dic' as tipo , sq.querie 
                    from  ".$DB_MIDIACLIP.".associacao_cadastros aca
                        inner join  ".$DB_MIDIACLIP.".dicionario_tags dt on dt.id = aca.id_filho
                        inner join ".$DB_MIDIACLIP.".cliente c on c.id = aca.id_pai 
                        left join search_queries sq on ( sq.id_cliente = aca.id_pai and sq.id_praca = " . $id_praca." and sq.ativo = 1 ) 
                  where c.status = 1 and dt.tipo in( 'dic' )
                   and aca.classificacao = 'clientexdicionario' 
                   and aca.tabela_filho = 'dicionario_tags' and aca.tabela_pai = 'cliente' ";
            
            
        }
        $EH_INTEGRADOR = \App\Http\Dao\ConfigDao::getValor("EH_INTEGRADOR");
        if ( $ids_clientes != ""){
            if ( ! $EH_INTEGRADOR ){
                   $sql .= " and aca.id_pai in ( " . $ids_clientes . " ) ";
            } else {
                $sql .= " and (  aca.id_pai in ( " . $ids_clientes . " ) or c.bl_todos_programas = 1 )  ";
            }
        }
        
         $sql .= " UNION select c.id, convert(c.nome  using utf8) as nome, convert(c.nome using utf8) as nome_cliente, "
                 . " c.id as id_cliente, 'cli' as tipo, '' as querie  from ".$DB_MIDIACLIP.".cliente c  
                  where c.status = 1 ";
         if ( $ids_clientes != ""){
               if ( ! $EH_INTEGRADOR ){
                      $sql .= " and c.id in ( " . $ids_clientes . " ) ";
               }else{
                   
                      $sql .= " and ( c.id in ( " . $ids_clientes . " ) or c.bl_todos_programas = 1 )  ";
               }
         }
         
         
        // die($sql);
        $pre_lista = DB::select($sql);
        
        return $pre_lista;
        
    }
    
    static function getSQLTags($ids_tags, $colunas = "dt.*, c.nome as nome_cliente, c.id as id_cliente ", $compl = ""){
        
        $DB_MIDIACLIP = config("app.DB_MIDIACLIP");
        
         $sql = " select ".$colunas." from ".$DB_MIDIACLIP.".dicionario_tags dt 
                    inner join ".$DB_MIDIACLIP.".associacao_cadastros aca on 
                          (aca.classificacao in  'clientexdicionario' and aca.id_filho = dt.id and aca.tabela_filho = 'dicionario_tags' )
                          inner join ".$DB_MIDIACLIP.".cliente c on c.id = aca.id_pai 
                  where c.status = 1 and dt.id in ( ". $ids_tags." ) ". $compl;
       //  die( $sql );
         return $sql;
        
    }
    
}