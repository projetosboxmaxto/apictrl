<?php
namespace App\Http\Dao;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Dao\ConfigDao;

use App\ElasticQueries;

class ElasticQueriesDao {
    
       public static function getListGridCad($filtro, $order, $order_type){
           
             $sql = "select p.* from elastic_queries p where 1 = 1 ". $filtro .
                     " order by ".$order. " ".$order_type;
             
             $itens = DB::select($sql);
             

             for ($i=0; $i < count( $itens) ; $i++) { 
                    $item = &$itens[$i];
                    $valor = $item->data;
                    
                    
                   // $item->data = $this->DataBR($valor, true); //Colocando como formato BR
             }
             
             return $itens;
           
       }
       
       public static function getElasticEnable($id_praca = "", $ids_clientes = ""){
           
           $filtro = "";
           $database = ConfigDao::getSchemaMidiaClip();
           
           if ( $id_praca != ""){
               $filtro.= " and ifNull(p.id_praca, -1 ) in ( -1, ". $id_praca.") ";
           }
           
           if ( $ids_clientes != ""){
               $filtro .= " and id_cliente in ( ". $ids_clientes ." ) ";
           }
           
           $sql = "select p.*, convert(c.nome using utf8) as nome_cliente from elastic_queries p "
                   . " left join ".$database.".cliente  c on c.id = p.id_cliente "
                   . " where 1 = 1 and p.ativo = 1 ".$filtro."  order by id_cliente, id desc ";
           
           return DB::select($sql);
       }
    
       public static function salvarDadosJson( $hd_json, $ids_delete_json, $id_cliente ){
           
           $itens = json_decode($hd_json);
           $ids_delete = json_decode($ids_delete_json);
           
           $qtde_salvo = 0; $qtde_delete = 0;
           
           for ( $i = 0; $i < count($itens); $i++){
                           
                       $item_req = $itens[$i];    
                       
                       $reg = new ElasticQueries();
                       
                       if ( ! is_null($item_req->id) && $item_req->id != ""){
                             $reg = ElasticQueries::find($item_req->id);
                       }
			// $reg->id = $item_req->id;   
                         $reg->titulo = $item_req->titulo;   
                         $reg->querie = $item_req->querie;   
                         $reg->ativo = ConfigDao::numeroBanco(  $item_req->ativo  );  
                         $reg->id_cliente = $id_cliente ;  
                         $reg->id_praca = ConfigDao::numeroBanco(  $item_req->id_praca  );  
                         //$reg->data = ConfigDao::dataBanco(  $item_req->data  );  
                         
                         if (! $reg->data){
                             $reg->data = \App\Http\Service\UtilService::getCurrentBDdate();
                         }
                       
                       
                       
                       ConfigDao::blankToNull($reg);
                       
		       $ret = $reg->save();
                       $qtde_salvo++;
           }
           
           for ( $i = 0; $i < count($ids_delete); $i++){
               $item_req = $ids_delete[$i];
               
                 if ( $item_req->id != ""){
                             $reg = ElasticQueries::find($item_req->id);
                             $reg->delete();//Removendo o item desejado para deletar.
                             $qtde_delete++;
                 }
           }
           
           return array("qtde_salvo" => $qtde_salvo, "qtde_deleted" => $qtde_delete , "success"=> true );
           
       }
       
       
        
}

