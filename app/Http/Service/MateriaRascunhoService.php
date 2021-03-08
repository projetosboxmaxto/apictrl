<?php

namespace App\Http\Service;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Dao\PostsDao;
use DateTime;

class MateriaRascunhoService{
    
    public static function removeNotificacoes($id){
        
          $saida = "";
             $reg = \App\MateriaRascunho::find($id);
             
             if (  is_null($reg))
                 return "matéria rascunho não localizada";
             
             $ids_arquivos = $reg->ids_arquivos;
             
             $id_evento_pai = \App\Http\Dao\ConfigDao::executeScalar(
                     "select id_evento_pai as res from eventos where id = ".$reg->id_projeto);
             
             $arquivos  = DB::select("select * from eventos_arquivos where id in ( ".
                     $ids_arquivos.") ");
             
             $saida .= " ids_arquivos: ". $ids_arquivos. " achei? ". count($arquivos);
             
               for ( $ii = 0; $ii< count($arquivos); $ii++ ){
                              $item_arquivo = &$arquivos[$ii];
                              
                              
                              
                              $hora_inicio_seg = $item_arquivo->hora_inicio_seg;
                              $meta_dados = $item_arquivo->meta_dados;
                              
                              $ls_notificacao = self::getIdNotificacaoByArquivo($item_arquivo, $id_evento_pai);
                              $saida .= " achei notificação? " . count($ls_notificacao);
                              
                              for ( $u =0; $u < count($ls_notificacao); $u++ ){
                              $id_notificacao =  $ls_notificacao[$u]->res;
                                       //self::getIdNotificacaoByArquivo($item_arquivo, $id_evento_pai);
                              
                              //if ( $id_notificacao != ""){
                                  $agrup =  \App\AgrupamentoNotificacoes::find($id_notificacao);
                                  
                                  if (is_null($agrup)){
                                      continue;
                                  }
                                  
                                  if ( $agrup->status == 2){
                                      $saida .= "  / notificação " . $id_notificacao . " já removida da lista ";
                                      //já não preciso fazer nada nesse aqui, pois ele já foi retirado da lista.
                                      continue;
                                  }
                                  
                                  
                                 $saida .= " , agrupamento: ". $agrup->id;
                                  
                                  if ( is_null( @$agrup->palavras_backup )){
                                     $agrup->palavras_backup = $agrup->palavras; 
                                  }
                                  
                                   $saida .= " , palavras_origem: ". $agrup->palavras_backup;
                                  if ( $meta_dados != ""){
                                      $obj = json_decode($meta_dados);
                                      
                                      $palavras_agrupamento = @$agrup->palavras;
                                            if (property_exists($obj, "clientes")){

                                                 for ( $o = 0; $o < count(@$obj->clientes); $o++){
                                                      $cliente = $obj->clientes[$o];
                                                      if (property_exists($cliente, "palavras") && is_array($cliente->palavras) ){
                                                              $palavras = $cliente->palavras;
                                                              for ( $e = 0; $e < count($palavras); $e++){                                                            
                                                                  $palavra = $palavras[$e];
                                                                  $saida .= " ; palavra de busca: " . $palavra->nome;
                                                                  $palavras_agrupamento = str_ireplace($palavra->nome, "", $palavras_agrupamento);
                                                                  $palavras_agrupamento = str_ireplace(UtilService::removeAcentos( $palavra->nome ), "", $palavras_agrupamento);
                                                                  $palavras_agrupamento = str_ireplace(",,", ",", $palavras_agrupamento);
                                                                  if ( trim($palavras_agrupamento) == ","){
                                                                      $palavras_agrupamento = "";
                                                                  }

                                                              }                                                    
                                                      }                                          

                                                  }
                                            }
                                            
                                        $saida .= " , palavras_final: ". $palavras_agrupamento;
                                        $agrup->palavras     = $palavras_agrupamento;
                                        if ( trim($agrup->palavras) == "" ){
                                            $agrup->status = 2;
                                        }
                                        $agrup->save();
                                            
                                  }
                                  
                                  
                                  
                              } 
                              
                              if ( count($ls_notificacao) <= 0 )
                              {
                                  $saida .= " , notificação não encontrada para o arquivo ". $item_arquivo->id;
                              }
                              
               
               }
               
               
               return $saida;
    }
    
    public static function getPalavras($obj){
                        for ( $i = 0; $i < count($obj->clientes); $i++){
                            $cliente = $obj->clientes[$i];
                        }
                              //{"clientes":[{"nome":"SEBRAE","id":1201221,"palavras":[{"id":120191931,"nome":"aplicativo"}]}]}
    }
    
    public static function getIdNotificacaoByArquivo($item_arquivo, $id_evento_pai ){
           
               $hora_inicio_seg = $item_arquivo->hora_inicio_seg;
               $hora_inicio_seg2 = $item_arquivo->hora_inicio_seg;
               if ( $hora_inicio_seg2 > 60 ){
                   $hora_inicio_seg2 -= 80; //vou pedir um minuto atrás também..
               }
               //	`hora_inicio_seg` INT(11) NULL DEFAULT NULL,
	           // `hora_fim_seg`
               $sql = "select id as res from agrupamento_notificacoes where id_evento_arquivo in ( "
                       . " select id from eventos_arquivos where id_evento =  ". $id_evento_pai ." ) ";
                //$sql .= " and  ".$hora_inicio_seg." >= hora_inicio_seg and ".$hora_inicio_seg." <= hora_fim_seg  ";                     
                $sql .= " and  ( ( ".$hora_inicio_seg." >= hora_inicio_seg and ".$hora_inicio_seg." <= hora_fim_seg )  
                              or ( ".$hora_inicio_seg." >= hora_inicio_seg and ".$hora_inicio_seg." <= hora_fim_seg+60 ) ) ";
               
               //die ( $sql ." id_arquivo: ". $item_arquivo->id ." - ".  $item_arquivo->hora_inicio);
               
               return DB::select($sql);
               //return \App\Http\Dao\ConfigDao::executeScalar($sql);
    }
    
    public static function salvar(Request $request, $id = "", $id_materia_radiotv_jornal = ""){
        
        $reg = new \App\MateriaRascunho();
        
        if ( $id != ""){
             $reg = \App\MateriaRascunho::find($id);
        }
        
        
            $reg->id_projeto = $request->input('id_projeto');  
            
            if ( $reg->id_projeto != ""){
                     $reg->data = \App\Http\Dao\ConfigDao::executeScalar("select data as res from eventos where id = ". $reg->id_projeto);
                                //$request->input('data');
            }
            $reg->titulo = $request->input('titulo');  
            $reg->cliente_list = $request->input('cliente_list');  
            $reg->ids_arquivos = $request->input('ids_arquivos');  
            $reg->dados_materia = $request->input('dados_materia');  
            if ( $reg->dados_materia == ""){
                      $reg->dados_materia = $request->input('json_data');  
            }
            $reg->id_programa = $request->input('id_programa');  
            $reg->dia = $request->input('dia');  
            $reg->id_operador = $request->input('id_operador');  
            
            
            if ( $reg->data_cadastro == ""){
                $reg->data_cadastro = \App\Http\Service\UtilService::getCurrentBDdate();
            }

             //$reg->data_cadastro = $request->input('data_cadastro');  
            $reg->status = $request->input('status');  
            
            if ( $reg->status == ""){
                $reg->status = 0;
            }
            
            $reg->id_materia_radiotv_jornal = $id_materia_radiotv_jornal;  
            
            if (  $reg->id_materia_radiotv_jornal != ""){
                $reg->status = 1;
            }

            

            PostsDao::blankToNull(  $reg );
            
            $reg->save();
            
            
            self::removeNotificacoes($reg->id);
            
            return  $reg;
    }
    
    public static function getMateriaGerada($id){
        
        $DB_MIDIACLIP = config("app.DB_MIDIACLIP"); 
        
        $sql = "select m.id,  m.titulo,  m.data_insert_materia,  m.data_materia, ja.nome as apresentador, pr.nome as programa,  m.hora_inicio, "
                . " m.duracao, em.nome as emissora, '' as clientes, '' as arquivos, m.status_atual as status "
                . " from  ". $DB_MIDIACLIP . ".materia_radiotv_jornal m 
                    inner join ". $DB_MIDIACLIP . ".materia_radio_tv mtv on mtv.id = m.id 
                    left join ". $DB_MIDIACLIP . ".emissora em on em.id = m.id_emissora 
                    left join ". $DB_MIDIACLIP . ".programa pr on pr.id = mtv.id_programa
                    left join ". $DB_MIDIACLIP . ".jornalista_apresentador ja on ja.id = mtv.id_apresentador
                        where m.id = " . $id ;
        
        $lista  = DB::select($sql);
        
        if ( count($lista) > 0 ){
            $sql = " select av.id, av.cita_cliente, bas.descricao as impacto , convert(cla.nome using utf8) as topico, "
                    . "  convert(c.nome using utf8) as cliente, c.id as id_cliente from ". $DB_MIDIACLIP . ".avaliacao_impacto av "
                    . " left join ". $DB_MIDIACLIP . ".cliente c on c.id = av.id_cliente "
                    . " left join ". $DB_MIDIACLIP . ".cadastro_basico bas on bas.id = av.id_impacto "
                    . " left join ". $DB_MIDIACLIP . ".classes_cliente cla on cla.id = av.id_categoria_cliente "
                    . " where av.id_materia = ". $id.
                    " and av.tabela_materia='materia_radio_tv_jornal' ";
            
            
           $clientes  = DB::select($sql);
           
           $item = $lista[0];
           $item->clientes = $clientes;
           
           $dt = new DateTime( $item->data_insert_materia );
           $ano = $dt->format("Y");
           $mes = (int)$dt->format("m");
           
           
            $sql = " select av.id, av.nome , '' as url  from ". $DB_MIDIACLIP . ".arquivos av "
                    . " where av.id_materia = ". $id.
                    " and av.tabela in ( 'materia_radio_tv_jornal' , 'materia_radiotv_jornal') ";
            
            
            $PATH_SISTEMA_MIDIACLIP = config("app.url_midiaclip") . "arquivos/";
            
            $EH_INTEGRADOR = \App\Http\Dao\ConfigDao::getValor("EH_INTEGRADOR");
            if ( $EH_INTEGRADOR ){
                
                $URL_ARQUIVOS_MATERIA = \App\Http\Dao\ConfigDao::getValor("URL_ARQUIVOS_MATERIA");
                if ( $URL_ARQUIVOS_MATERIA != ""){
                    $PATH_SISTEMA_MIDIACLIP = $URL_ARQUIVOS_MATERIA;
                }
            }
            
           $arquivos  = DB::select($sql);
           
           for ( $ii = 0; $ii< count($arquivos); $ii++ ){
               $item_arquivo = &$arquivos[$ii];
               $item_arquivo->url =  $PATH_SISTEMA_MIDIACLIP . "RTV/".$ano."/".$mes."/". 
                       $item_arquivo->nome;
           }
           
           $item->arquivos = $arquivos;
           
           return $item;
                    
        }
        
        return array();
                        
        
    }
    
    
    
}
