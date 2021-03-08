<?php
namespace App\Http\Service;

use Illuminate\Support\Facades\DB;


class EventoService{
    
    public static function SqlEvento( $DBTMP =""){
        $DB_MIDIACLIP = \App\Http\Dao\ConfigDao::getSchemaMidiaClip();
        
       // die( "db_midiaclip ". $DB_MIDIACLIP );  
                $sql = "select ev.id, ev.data, pr.nome as programa, em.nome as emissora, '' as form_pai,
                 pr.transcricao_prioridade as prioridade, ev.duracao, ev.tempo_realizado_minutos,
                 case when pr.transcricao_prioridade  = 'Alta' then 3
                 when pr.transcricao_prioridade  = 'Normal' then 2
                 else 1 end as prioridade_int,
                  ev.dia, ev.hora_inicio, ev.hora_fim, ev.tempo_realizado_minutos as tempo_realizado, ev.tempo_total_minutos as tempo_total,
                  em.transcricao_url as path, em.transcricao_url2 as alt_path , '' as blnk, '' as tempo_h
                     from  eventos ev  
                     left join ". $DB_MIDIACLIP .".programa pr on pr.id = ev.id_programa
                    inner join ". $DB_MIDIACLIP .".emissora em on em.id = ev.id_emissora ";
                
                
                return $sql;
    }
    
    
    public static function mudaStatus($id, $id_operador, $status){
        
        
            $reg_antigo = \App\Eventos::find($id);
            $compl = "";
            
            if ( $reg_antigo->status == $status ){
                return array("msg"=>"Já esta neste status!", "id"=>$id, "status"=>$status);
            }
            
            if ( $id_operador != "" ){
                $nome_usuario = \App\Http\Dao\ConfigDao::executeScalar2("select nome as res from usuario where id = ". $id_operador );
                
                if ( $status == 2 ){ //Bloqueado...
                      $compl = ", bloqueado_por_id = ". $id_operador;
                      $compl .= ", bloqueado_por='". $nome_usuario."' ";
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
        

            return  array("msg"=>"Sucesso!", "id"=>$id, "form" => array(
                                   "bloqueado_por_id" => $reg->bloqueado_por_id,
                                   "bloqueado_por" => $reg->bloqueado_por,
                                   "status" => $reg->status,
                                                      "data_status_change" => $reg->data_status_change ) );
    }
    
    public static function sqlEventoFilho(){
        $DB_MIDIACLIP = \App\Http\Dao\ConfigDao::getSchemaMidiaClip();
        
        
        $sql = "select ev.id, ev.hora_inicio, ev.duracao, us.nome as nome_operador , ev.nome_projeto from eventos ev "
                . " left join ". $DB_MIDIACLIP .".usuario us on us.id = ev.id_operador ";
        
        return $sql;
        
    }
    
    public static function sqlArquivos($withJoin = false){
        
        $DB_MIDIACLIP = \App\Http\Dao\ConfigDao::getSchemaMidiaClip();
        
        $colunas = "";
        
         if ( $withJoin ){
              $colunas .= ", convert(ja.nome using utf8) as jornalista, convert(m.titulo using utf8) as titulo_materia, "
                      . " convert( m.sinopse  using utf8) as sinopse_materia, m.data_insert_materia ";
         }
        
        $sql = "select ea.* ". $colunas . " from eventos_arquivos ea ";
        
        if ( $withJoin ){
            $sql .= " left join ".$DB_MIDIACLIP.".materia_radiotv_jornal m on (m.id = ea.id_materia_radiotv_jornal ) 
                      left join ".$DB_MIDIACLIP.".materia_radio_tv mtv on ( mtv.id = ea.id_materia_radiotv_jornal ) 
                      left join ".$DB_MIDIACLIP.".jornalista_apresentador ja on ( ja.id = mtv.id_apresentador ) ";
        }
         
        return $sql;
    }
    
    public static function getListCliente($id_evento){
        
        $DB_MIDIACLIP = \App\Http\Dao\ConfigDao::getSchemaMidiaClip();
        $sql = "select ec.id_cliente as id, c.nome from eventos_clientes ec inner join ".$DB_MIDIACLIP.".cliente c on c.id = ec.id_cliente "
                . "  where ec.id_eventos = ". $id_evento. " order by c.nome ";
        
        return DB::select($sql);
    }
	
	public static $sql_last = "";
	public static $sql_last0 = "";

    public static function getListEvento($dia, $dia_semana, $hora_seg, $write = true, $ret_mclipweb= true ){
        
        $DB_MIDIACLIP = \App\Http\Dao\ConfigDao::getSchemaMidiaClip();
        
        $sql = "select ev.id, 
                    pr.id as id_programa, 
                    'normal' as tipo_hora  
                from ". $DB_MIDIACLIP .".programa pr 
                      left join eventos ev 
                        on ( ev.id_programa = pr.id and ev.dia = ". $dia. " and ev.tipo = 'pai') 
                      where  pr.transcricao_ativar = 1 and 
                      (TIME_TO_SEC(pr.hora_inicio) - TIME_TO_SEC(pr.transcricao_tempo_extra_inicio))
                            <= ".$hora_seg." and 
                      (TIME_TO_SEC(pr.hora_fim) + TIME_TO_SEC(pr.transcricao_tempo_extra_fim)) 
                            >= " . $hora_seg .
                " and ev.id is null and pr.transcricao_dias like '%".$dia_semana."%'  "; 
        
        $hora = $hora_seg / 3600;
        
         if ( $hora >= 20 && $hora <= 24  ){
             
             $sql .= " UNION 
                        select ev.id, pr.id as id_programa, 'virada_ini' as tipo_hora 
                            from ". $DB_MIDIACLIP .".programa pr 
                      
                      left join eventos ev 
                        on ( ev.id_programa = pr.id and ev.dia = ". $dia. " and ev.tipo = 'pai') 
                    where  pr.transcricao_ativar = 1 and 
                    (TIME_TO_SEC(pr.hora_inicio) - TIME_TO_SEC(pr.transcricao_tempo_extra_inicio))
                            > (TIME_TO_SEC(pr.hora_fim) + TIME_TO_SEC(pr.transcricao_tempo_extra_fim)) &&
                    (TIME_TO_SEC(pr.hora_inicio) - TIME_TO_SEC(pr.transcricao_tempo_extra_inicio)) 
                            <= ".$hora_seg. 
                " and ev.id is null and pr.transcricao_dias like '%".$dia_semana."%'  "; 
             
         }
         
         
         if ( $hora >= 0 && $hora <= 4 ){
             
              $sql .= " UNION 
                            select ev.id, pr.id as id_programa, 'virada_fim' as tipo_hora 
                        from ". $DB_MIDIACLIP .".programa pr 
                      left join eventos ev 
                        on ( ev.id_programa = pr.id and ev.dia = ". $dia. " and ev.tipo = 'pai') 
                      where  pr.transcricao_ativar = 1 and 
                        (TIME_TO_SEC(pr.hora_inicio) - TIME_TO_SEC(pr.transcricao_tempo_extra_inicio)) 
                            > (TIME_TO_SEC(pr.hora_fim) + TIME_TO_SEC(pr.transcricao_tempo_extra_fim)) &&  
                        (TIME_TO_SEC(pr.hora_fim) + TIME_TO_SEC(pr.transcricao_tempo_extra_fim)) 
                            >= ".$hora_seg. 
                " and ev.id is null and  pr.transcricao_dias like '%".$dia_semana."%'  "; 
         }
        
		self::$sql_last0 = $sql;
        $lista = DB::select($sql);   

        if ( ! $ret_mclipweb ){
            return $lista;
        }
        
        if (  $write ){
                    for ( $i = 0; $i < count($lista); $i++ ){
                        $item = $lista[$i];

                        self::salvarPrimeiro($item->id_programa, $dia, $item->tipo_hora);
                    }
        }
        
        $sql = "select ev.id, pr.nome as programa, em.nome as emissora,
                 pr.transcricao_prioridade as prioridade, 
                 case when pr.transcricao_prioridade  = 'Alta' then 1
                 when pr.transcricao_prioridade  = 'Normal' then 2
                 else 3 end as prioridade_int,
                  ev.dia, ev.hora_inicio, ev.hora_fim, ev.tempo_realizado_minutos as tempo_realizado, ev.tempo_total_minutos as tempo_total,
                  em.transcricao_url as path, em.transcricao_url2 as alt_path, '' as ultimo_arquivo, 'normal' as tipo_hora 
                     from  eventos ev 
                     left join ". $DB_MIDIACLIP .".programa pr on pr.id = ev.id_programa
                    inner join ". $DB_MIDIACLIP .".emissora em on em.id = ev.id_emissora
                 where
                 ev.dia = ". $dia.
                 " and ev.hora_inicio_seg <=".$hora_seg." and ev.hora_fim_seg >= " . $hora_seg.
                " and ev.tempo_realizado_minutos < ev.tempo_total_minutos and ev.tipo = 'pai' ";
        
        
        // 22 -> 1
        
        if ( false && $hora >= 20 && $hora <= 24  ){
            
            $sql .= " UNION select ev.id, pr.nome as programa, em.nome as emissora,
                 pr.transcricao_prioridade as prioridade, 
                 case when pr.transcricao_prioridade  = 'Alta' then 4
                 when pr.transcricao_prioridade  = 'Normal' then 5
                 else 6 end as prioridade_int,
                  ev.dia, ev.hora_inicio, ev.hora_fim, ev.tempo_realizado_minutos as tempo_realizado, ev.tempo_total_minutos as tempo_total,
                  em.transcricao_url as path, em.transcricao_url2 as alt_path, '' as ultimo_arquivo , 'virada' as tipo_hora 
                     from  eventos ev 
                     left join ". $DB_MIDIACLIP .".programa pr on pr.id = ev.id_programa
                    inner join ". $DB_MIDIACLIP .".emissora em on em.id = ev.id_emissora
                 where
                 ev.dia = ". $dia.
                " and ev.hora_inicio_seg > ev.hora_fim_seg "
                    . "  and ev.hora_inicio_seg <= " . $hora_seg. ""
                    . " and ev.tempo_realizado_minutos < ev.tempo_total_minutos and ev.tipo = 'pai' "; 
            
        }
        
         if (  false && $hora <= 4 ){
            
            $sql .= " UNION select ev.id, pr.nome as programa, em.nome as emissora,
                 pr.transcricao_prioridade as prioridade, 
                 case when pr.transcricao_prioridade  = 'Alta' then 4
                 when pr.transcricao_prioridade  = 'Normal' then 5
                 else 6 end as prioridade_int,
                  ev.dia, ev.hora_inicio, ev.hora_fim, ev.tempo_realizado_minutos as tempo_realizado, ev.tempo_total_minutos as tempo_total,
                  em.transcricao_url as path, em.transcricao_url2 as alt_path, '' as ultimo_arquivo , 'virada' as tipo_hora 
                     from  eventos ev 
                     left join ". $DB_MIDIACLIP .".programa pr on pr.id = ev.id_programa
                    inner join ". $DB_MIDIACLIP .".emissora em on em.id = ev.id_emissora
                 where
                 ev.dia = ". $dia.
                " and ev.hora_inicio_seg > ev.hora_fim_seg "
                    . " and ev.hora_fim_seg >= " . $hora_seg. ""
                    . " and ev.tempo_realizado_minutos < ev.tempo_total_minutos and ev.tipo = 'pai' "; 
            
        }
        
        $tempo_maximo_tentativa = 15 * 60; //1200 segundos..
        
         $sql .= " UNION select ev.id, pr.nome as programa, em.nome as emissora,
                 pr.transcricao_prioridade as prioridade, 
                 case when pr.transcricao_prioridade  = 'Alta' then 4
                 when pr.transcricao_prioridade  = 'Normal' then 5
                 else 6 end as prioridade_int,
                  ev.dia, ev.hora_inicio, ev.hora_fim, ev.tempo_realizado_minutos as tempo_realizado, ev.tempo_total_minutos as tempo_total,
                  em.transcricao_url as path, em.transcricao_url2 as alt_path, '' as ultimo_arquivo , 'extra' as tipo_hora 
                     from  eventos ev 
                     left join ". $DB_MIDIACLIP .".programa pr on pr.id = ev.id_programa
                    inner join ". $DB_MIDIACLIP .".emissora em on em.id = ev.id_emissora
                 where
                 ev.dia = ". $dia.
                " and ev.hora_fim_seg < " . $hora_seg. ""
                 . " and ( ". $hora_seg. " - ev.hora_fim_seg ) <= " . $tempo_maximo_tentativa 
                 . " and ev.tempo_realizado_minutos < ev.tempo_total_minutos and ev.tipo = 'pai' "; 
          
          //terminou as 5h e agora é 6h
        $sql .= " order by prioridade_int asc ";
        
        /* 
                 " and ev.hora_inicio_seg <=".$hora_seg." and ev.hora_fim_seg >= " . $hora_seg. */
        
       // 
        $lista2 = DB::select($sql);
		self::$sql_last = $sql;
        
        for ( $i = 0; $i < count($lista2); $i++ ){
            $item = &$lista2[$i];
            $item->ultimo_arquivo = \App\Http\Dao\ConfigDao::executeScalar("select nome as res from eventos_arquivos where id_evento = ". $item->id. " order by id desc limit 0, 1 ");
        }
        return $lista2;
    }
    
    
    public static function salvarPrimeiro($id_programa, $dia, $tipo_hora = "normal"){
        $DB_MIDIACLIP = \App\Http\Dao\ConfigDao::getSchemaMidiaClip();
       
        $reg = new \App\Eventos();
        $reg->id_programa = $id_programa;
        $reg->dia = $dia;
        $reg->data = date("Y-m-d");
        $reg->tipo = "pai";
        $reg->tipo_hora = $tipo_hora;
        
        $sql = "select id as res from eventos where dia = ". $dia. " and id_programa = ". $id_programa;
        $idtmp = \App\Http\Dao\ConfigDao::executeScalar($sql);
        
        if ( $idtmp != ""){
            return false;
        }
        
        
        $sql = " select 
                    pr.*, 
                    TIME_TO_SEC(pr.hora_inicio) - TIME_TO_SEC(pr.transcricao_tempo_extra_inicio) as transcricao_gordura_inicio,
                    TIME_TO_SEC(pr.hora_fim) + TIME_TO_SEC(pr.transcricao_tempo_extra_fim) as transcricao_gordura_fim,
                    em.id as id_emissora, em.nome as nome_emissora 
                        from ". $DB_MIDIACLIP .".programa pr
                    inner join ". $DB_MIDIACLIP .".associacao_cadastros ac 
                        on (
                            ac.id_pai = pr.id and ac.classificacao = 'programaxcanal_comunicacao' and 
                            ac.tabela_pai='programa' 
                        )
                    inner join ". $DB_MIDIACLIP .".emissora em on em.id = ac.id_filho 
                where pr.id = ". $id_programa;
        
        
        $lista = DB::select($sql);

        if ( count($lista) > 0 ){
            $item = $lista[0];
            
            $reg->id_emissora = $item->id_emissora;
            
            $tempo_inicio_seg = $item->transcricao_gordura_inicio;
            $tempo_fim_seg = $item->transcricao_gordura_fim;
            
            if ( $tipo_hora == "virada_ini"){
                     $tempo_inicio_seg = $item->transcricao_gordura_inicio;
                     $tempo_fim_seg = UtilService::time_to_seconds("23:59:59"); // $item->transcricao_tempo_fim_seg;
            }
            if ( $tipo_hora == "virada_fim"){
                
                    $tempo_inicio_seg = 0; //$item->transcricao_tempo_inicio_seg;
                   $tempo_fim_seg = $item->transcricao_gordura_fim;
                
            }

            if($tempo_fim_seg > 86399) $tempo_fim_seg = 86399;
            
            $duracao_seg = $tempo_fim_seg - $tempo_inicio_seg;
            
            $reg->hora_inicio = UtilService::converteSegundos_ParaHoraMinuto ($tempo_inicio_seg);
            $reg->hora_fim = UtilService::converteSegundos_ParaHoraMinuto($tempo_fim_seg);
            
            $reg->hora_inicio_seg = $tempo_inicio_seg;
            $reg->hora_fim_seg = $tempo_fim_seg;
            
            $reg->duracao_seg = $duracao_seg;
            $reg->duracao = UtilService::converteSegundos_ParaHoraMinuto($duracao_seg);
            
            $reg->tempo_realizado_minutos =  0;
            $reg->tempo_total_minutos = $duracao_seg / 60;
        }
        
        $reg->save();
        
    }
    
    public static function criarDirOld($dia, $id){
        
        $PATH_ARQUIVOS = config("app.PATH_ARQUIVOS");
        
        if ( !is_dir( $PATH_ARQUIVOS . DIRECTORY_SEPARATOR . $dia )){
            mkdir($PATH_ARQUIVOS . DIRECTORY_SEPARATOR . $dia);
        }
        
        
        if ( !is_dir( $PATH_ARQUIVOS . DIRECTORY_SEPARATOR . $dia . DIRECTORY_SEPARATOR . $id )){
            mkdir($PATH_ARQUIVOS . DIRECTORY_SEPARATOR . $dia . DIRECTORY_SEPARATOR . $id );
        }
        
    }
    
       public static function criarDir($dia, $pre, $id){
        
        $PATH_ARQUIVOS = config("app.PATH_ARQUIVOS");
        
        if ( !is_dir( $PATH_ARQUIVOS . DIRECTORY_SEPARATOR . $pre )){
            mkdir($PATH_ARQUIVOS . DIRECTORY_SEPARATOR . $pre);
        }
        
        if ( !is_dir( $PATH_ARQUIVOS . DIRECTORY_SEPARATOR .  $pre .DIRECTORY_SEPARATOR . $dia )){
             $criado = mkdir($PATH_ARQUIVOS . DIRECTORY_SEPARATOR .  $pre  . DIRECTORY_SEPARATOR . $dia);
             if ( ! $criado ){
                 die("não consegui criar a pasta: ". $PATH_ARQUIVOS . DIRECTORY_SEPARATOR . $pre  .DIRECTORY_SEPARATOR . $dia);
             }
        }
        
        
        if ( !is_dir( $PATH_ARQUIVOS . DIRECTORY_SEPARATOR . $pre .DIRECTORY_SEPARATOR . $dia . DIRECTORY_SEPARATOR . $id )){
            mkdir($PATH_ARQUIVOS . DIRECTORY_SEPARATOR . $pre .DIRECTORY_SEPARATOR . $dia . DIRECTORY_SEPARATOR . $id );
        }
		
		//echo("passei no método de criação de pasta? <br>" .
                //$PATH_ARQUIVOS . DIRECTORY_SEPARATOR . $pre .DIRECTORY_SEPARATOR . $dia . DIRECTORY_SEPARATOR . $id		);
        
    }
    
    
        public static function rrmdir($src) {
                            $dir = opendir($src);
                            while(false !== ( $file = readdir($dir)) ) {
                                if (( $file != '.' ) && ( $file != '..' )) {
                                    $full = $src . '/' . $file;
                                    if ( is_dir($full) ) {
                                        rrmdir($full);
                                    }
                                    else {
                                        unlink($full);
                                    }
                                }
                            }
                            closedir($dir);
                            rmdir($src);
     }
    
      
    public static function getPathEvento2($obj, $real = true , $force_new = null ){
          
        $PATH_ARQUIVOS = config("app.PATH_ARQUIVOS");
            $PATH_SUBFOLDER = config("app.PATH_SUBFOLDER");
            
            $pre_real = "";
            $pre_relativo = "";
            
            if ( $obj->tipo == "pai"){
                
                    $pre_real = "eventos". DIRECTORY_SEPARATOR;
                    $pre_relativo = "eventos/";
            }
            
            if ( $obj->tipo == "filho"){
                
                    $pre_real = "projetos". DIRECTORY_SEPARATOR;
                    $pre_relativo = "projetos/";
            }
             if ( !is_null( $force_new ) ){
                 $PATH_SUBFOLDER = $force_new;
            }
            
            if ( $PATH_SUBFOLDER == "0"){
                      $pre_real = "";
                      $pre_relativo = "";
            }
           
            
        
            if (! $real ){
                return  $pre_relativo . $obj->dia ."/". $obj->id;                
            }
        return $PATH_ARQUIVOS .  DIRECTORY_SEPARATOR . $pre_real .  $obj->dia . DIRECTORY_SEPARATOR . $obj->id ;
    }
    
    public static function getPathEvento($id, $real = true ){
            $obj =  \App\Eventos::find($id);
            
            if ( $obj == null )
                return "";
            
            $PATH_ARQUIVOS = config("app.PATH_ARQUIVOS");
            $PATH_SUBFOLDER = config("app.PATH_SUBFOLDER");
            $PATH_SUBFOLDER = "1";
            
            $pre_real = "";
            $pre_relativo = "";
            
            if ( $obj->tipo == "pai"){
                
                    $pre_real = "eventos". DIRECTORY_SEPARATOR;
                    $pre_relativo = "eventos/";
            }
            
            if ( $obj->tipo == "filho"){
                
                    $pre_real = "projetos". DIRECTORY_SEPARATOR;
                    $pre_relativo = "projetos/";
            }
            
            if ( $PATH_SUBFOLDER == "0"){
                      $pre_real = "";
                      $pre_relativo = "";
            }
            
        
            if (! $real ){
                return  $pre_relativo . $obj->dia ."/". $obj->id;                
            }
        return $PATH_ARQUIVOS .  DIRECTORY_SEPARATOR . $pre_real .  $obj->dia . DIRECTORY_SEPARATOR . $obj->id ;
    }
    
    public static function getPrePasta($evento, $real = false ){
        
        $PATH_SUBFOLDER = config("app.PATH_SUBFOLDER");
            $PATH_SUBFOLDER = "1";
         if ( $PATH_SUBFOLDER == ""){
             return "";
         }
         
         $operador = "/";
         
         if ( $real ){
             $operador = DIRECTORY_SEPARATOR;
         }
        
        if ( $evento->tipo == "filho"){
             return "projetos". $operador;
        }
        
        if ( $evento->tipo == "pai"){
             return "eventos". $operador;
        }
        
        return "";
        
        
    }
    
    
    public static function salvarArquivoEventoFilho($evento, $ids_arquivos, $tipo,  $nome_final_arquivo, $ls_tags){
        
                           
                        $reg = new \App\EventosArquivos();

                        $reg->path =  self::getPrePasta($evento) . $evento->dia."/".$evento->id."/". $nome_final_arquivo;  
                        $reg->nome = $nome_final_arquivo; 
                        
                        $hora_inicio_seg = \App\Http\Dao\ConfigDao::executeScalar("select min(hora_inicio_seg) as res from eventos_arquivos where id in ( ". $ids_arquivos ." ) " );
                        $tot = \App\Http\Dao\ConfigDao::executeScalar("select sum(tempo_realizado_minutos) as res from eventos_arquivos where id in ( ". $ids_arquivos ." ) " );

                        if ( is_null($tot) || $tot == ""){
                            $tot = 0;
                        }

                        $reg->tempo_realizado_minutos = $tot;  
                        $reg->hora_inicio = \App\Http\Service\UtilService::converteSegundos_ParaHoraMinuto($hora_inicio_seg);  
                        $reg->hora_inicio_seg = $hora_inicio_seg;  
                        $reg->com_temp_search= 0;


                        $reg->json = self::obtemJson($ids_arquivos);  
                        $reg->id_evento = $evento->id;
                        $reg->tipo = $tipo;
                        $reg->save();
                        
                        $evento->hora_inicio = $reg->hora_inicio;
                        $evento->hora_inicio_seg = $reg->hora_inicio_seg;
                        $evento->duracao_seg = $tot * 60;
                        $evento->duracao = \App\Http\Service\UtilService::converteSegundos_ParaHoraMinuto( $evento->duracao_seg  );
                        $evento->hora_fim_seg = $evento->hora_inicio_seg + $evento->duracao_seg;
                        $evento->hora_fim = \App\Http\Service\UtilService::converteSegundos_ParaHoraMinuto( $evento->hora_fim_seg );
                        $evento->tempo_realizado_minutos = $tot;
                        $evento->tempo_total_minutos = $tot;
                        
                        $evento->save();
                        
                        //try{
                        TempSearchService::searchByArquivo($ls_tags, $reg->id );
                        //} catch ()
                        
                        return $reg->id;
                      
    }
    
    public static function obtemJson($ids_arquivos){
        
         $sql = "select * from eventos_arquivos where id in ( " . $ids_arquivos . " ) order by hora_inicio_seg ";
         $lista = DB::select($sql);
         
         $obj_json  = array();
         
         //$json_inicio = json_decode($lista[0]->json);
         
         $segment_index = 0;
         
         for ( $i = 0; $i < count($lista); $i++ ){
              $inicio = (5*60) * $i;
              
              $objects = json_decode($lista[$i]->json);
              
               for ( $z = 0; $z < count($objects); $z++ ){
                      
                       $object = $objects[$z];
                       
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
    
    
    

}