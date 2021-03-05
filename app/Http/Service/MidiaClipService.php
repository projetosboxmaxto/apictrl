<?php

namespace App\Http\Service;
use Illuminate\Support\Facades\DB;

class MidiaClipService{
    
        public static function getListaApresentador($id_programa, $id_emissora = ""){
            
               $DB_MIDIACLIP = \App\Http\Dao\ConfigDao::getSchemaMidiaClip();
               $DB_MIDICALIP  = \App\Http\Dao\ConfigDao::getSchemaMidiaClip();
              
               $ids_colunas =   self::getIDAssociacoesPai("apresentadorxprograma", $id_programa, "jornalista_apresentador", "programa");
               
               $id_emissora = \App\Http\Dao\ConfigDao::executeScalar2("select id_emissora as res from programa where id = ". $id_programa);
               
               if ( $id_emissora != ""){
                   
                         $ids_by_emissora =   self::getIDAssociacoesPai("apresentadorxemissora", $id_emissora, "jornalista_apresentador", "emissora");
                         if ( $ids_by_emissora != ""){
                             $ids_colunas = UtilService::AdicionaStr($ids_colunas, $ids_by_emissora, ",");
                         }
               }
			   
			   //$id_emissora = 
               
               if ( $ids_colunas == "")
                   $ids_colunas = "0";
               
               
               $sql = "select * from ".$DB_MIDIACLIP.".jornalista_apresentador where id in ( ". $ids_colunas." ) ";
               return DB::select($sql);


        }
        
        public static function getListTopico($id_cliente){
            
               $DB_MIDIACLIP = \App\Http\Dao\ConfigDao::getSchemaMidiaClip();
            $complemento_filtro = " and ifNull(status, 1 ) = 1 ";
            $sql = " select id, convert(nome using utf8) as nome , id_cliente, 0 as pai from "
                    . " ".$DB_MIDIACLIP.".classes_cliente "
                    . " where id_cliente = " . $id_cliente . $complemento_filtro. " and id_pai is null order by nome, ordem ";
            
            $dt = DB::select($sql);
            
            $saida = array();

            
            for ($i = 0; $i < count($dt); $i++)
            {
                $dr = $dt[$i];
                
                $sqltmp = " select id, convert(nome using utf8) as nome , id_cliente, 0 as pai from "
                        . " ".$DB_MIDIACLIP.".classes_cliente where id_cliente = " .
                     $id_cliente. " and id_pai = " . $dr->id. $complemento_filtro. " order by ordem ";
                
                $dttmp = DB::select($sqltmp);
                
                if ( count($dttmp) > 0 ){
                    $dr->pai = 1;
                }
                
                $saida[count($saida)] = $dr;
                
                for ($zz = 0; $zz < count($dttmp); $zz++)
                {
                    $dr_item = $dttmp[$zz];
                    $saida[count($saida)] = $dr_item;
                    
                }
            }
            
            return $saida;
        }
        
        public static function getListEmissora(){
            $sql = "select id, nome from emissora where id ( select distinct id_emissora from eventos ) order by nome asc ";
                 return DB::select($sql);
        }
    
       public static function getListCadastroBasico($id_tipo, $compl= ""){
            
               $DB_MIDIACLIP = \App\Http\Dao\ConfigDao::getSchemaMidiaClip();
              $DB_MIDICALIP  = \App\Http\Dao\ConfigDao::getSchemaMidiaClip();
              
              //select id, descricao from cadastro_basico where tipo_cadastro_basico = 3 
               
               $sql = "select id, descricao, descricao as nome from ".$DB_MIDIACLIP.".cadastro_basico where tipo_cadastro_basico = ". $id_tipo. $compl;
               return DB::select($sql);


        }
    
    
        public static function getIDAssociacoesFilho($classificacao, $id_pai, $tabela_pai, $tabela_filho)
        {
               $DB_MIDIACLIP = \App\Http\Dao\ConfigDao::getSchemaMidiaClip();
              $DB_MIDICALIP  = \App\Http\Dao\ConfigDao::getSchemaMidiaClip();
            $sql = "select id_filho from ".$DB_MIDIACLIP.".associacao_cadastros where tabela_pai='" . $tabela_pai . "' and tabela_filho ='". $tabela_filho. 
                "' and id_pai in ( " . $id_pai. " ) " ;

            if ($classificacao != "")
                $sql .= " and classificacao='" . $classificacao . "' ";


            $dt_saida = DB::select($sql);
            
            $saida = "";
            for ($i = 0; $i < count( $dt_saida) ; $i++)
            {
                if ($saida != "")
                    $saida .= ",";


                $saida .= $dt_saida[$i]->id_filho;
            }

            return $saida;

        }
        
        
        public static function getParameterByItem($id_registro, $tabela, $codigo){
            
            
              $DB_MIDIACLIP = \App\Http\Dao\ConfigDao::getSchemaMidiaClip();
              $DB_MIDICALIP  = \App\Http\Dao\ConfigDao::getSchemaMidiaClip();
              $sql = "select convert(value using utf8) as res from ".$DB_MIDIACLIP.".parameters_by_item where id_registro = ".$id_registro.
                    " and nome_tabela='". $tabela."' and code='".$codigo."' ";
            
              return \App\Http\Dao\ConfigDao::executeScalar($sql);
            
        }

        public static function getIDAssociacoesPai($classificacao, $id_filho, $tabela_pai, $tabela_filho)
        {
              $DB_MIDIACLIP = \App\Http\Dao\ConfigDao::getSchemaMidiaClip();
              $DB_MIDICALIP  = \App\Http\Dao\ConfigDao::getSchemaMidiaClip();
            $sql = "select id_pai from ".$DB_MIDICALIP.".associacao_cadastros where tabela_pai='" . $tabela_pai . "' and tabela_filho ='" . $tabela_filho. 
                "' and id_filho in ( " . $id_filho . " ) and classificacao='" . $classificacao . "' ";


            $dt_saida = DB::select($sql);

            $saida = "";
            $saida = "";
            for ($i = 0; $i < count( $dt_saida) ; $i++)
            {
                if ($saida != "")
                    $saida .= ",";


                $saida .= $dt_saida[$i]->id_pai;
            }

            return $saida;

        }
}
