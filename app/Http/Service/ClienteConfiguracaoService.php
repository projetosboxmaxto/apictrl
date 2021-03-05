<?php

/* 
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
 * 
 *     for ( $i = 0; $i < count($lista); $i++ ){
            $item = $lista[$i];
            
        }
 */


namespace App\Http\Service;

use Illuminate\Support\Facades\DB;
use App\ClienteConfiguracao;


class ClienteConfiguracaoService {


    public static function getClienteConfiguracao($id_cliente){

           $list =    ClienteConfiguracao::whereRaw("id_cliente = ? ",$id_cliente)->get();

           if ( count($list) > 0 ){
              return $list[0];
           }

           $registro = new ClienteConfiguracao();
           $registro->id_cliente = $id_cliente;
           $registro->consulta_comum = 1;
           $registro->save();

           return $registro;
       }
    
    
    static function carregarPracaPorCliente($id_cliente, $bool_forca = false){

        $sql = "select * from cliente_configuracao where id_cliente = ". $id_cliente;
        $lista = DB::select($sql);

        if (false && count($lista) > 0 && !$bool_forca){
          $item = $lista[0];
           if ( $item->loaded_praca ){
               $json = $item->praca_json;

               return json_decode($json);
           }
        }

        $DB_MIDIACLIP = \App\Http\Dao\ConfigDao::getSchemaMidiaClip();
        $cliente_configuracao = self::getClienteConfiguracao($id_cliente);

        //cheguei até aqui, vou pegar novamente todas as praças e salvar 
        $ls_queries = DB::select("select * from search_queries where id_cliente = ". $id_cliente);

        if ( count($ls_queries ) <= 0 || $bool_forca ){

                    $sql = " select id, descricao from cadastro_basico where id in (  
                   select distinct id_praca from emissora where id in (
                   
                   select distinct id_filho from associacao_cadastros 
                               where classificacao in ( 'programaxcanal_comunicacao') 
                                and tabela_pai ='programa'
                               and id_pai in  ( 
                     select id from programa where transcricao_ativar = 1 and id in (
                     select distinct id_filho from associacao_cadastros 
                               where classificacao in ( 'entidadexprograma', 'subentidadexprograma', 'setorxprograma') 
                                and tabela_pai ='cliente'
                               and id_pai=  ".$id_cliente.    
                              " ) ) ) ) ";
                    
                    $lista = DB::connection('mysql_midiaclip')->select($sql);
                    
                    for ( $i = 0; $i < count($lista); $i++ ){
                        $item = $lista[$i];
                        
                        $id_tmp = \App\Http\Dao\ConfigDao::executeScalar("select id as res from search_queries where id_cliente = ".$id_cliente. " and id_praca = ".$item->id);
                        
                        if ( $id_tmp == ""){
                            $reg = new \App\SearchQueries();
                            $reg->id_cliente = $id_cliente;
                            $reg->id_praca = $item->id;
                            $reg->titulo = utf8_encode( $item->descricao );
                            $reg->data = UtilService::getCurrentBDdate();
                            $reg->ativo = 0;
                            $reg->save();
                            
                        }
                    }


                  $cliente_configuracao->praca_json = json_encode($lista, JSON_UNESCAPED_UNICODE);
                  $cliente_configuracao->loaded_praca = 1;
                  $cliente_configuracao->save();

        } else {



                $ls_queries = DB::select("select distinct c.id, c.descricao 
                                from search_queries s 
                                         inner join ".$DB_MIDIACLIP.".cadastro_basico c on c.id = s.id_praca
                                where s.id_cliente = ". $id_cliente);

                $cliente_configuracao->praca_json = json_encode($ls_queries,JSON_UNESCAPED_UNICODE );
                $cliente_configuracao->loaded_praca = 1;
                $cliente_configuracao->save();

        }

        return DB::select("  select s.*, c.descricao as titulo from search_queries s 
              left join ".$DB_MIDIACLIP.".cadastro_basico c on c.id = s.id_praca
                  where s.id_cliente = ". $id_cliente );


        
         // return DB::select(" select id, descricao from ".$DB_MIDIACLIP.".cadastro_basico where id in ( select id_praca from search_queries where id_cliente = ". $id_cliente);
        
    }
    
    
}
