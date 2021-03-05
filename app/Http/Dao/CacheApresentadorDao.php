<?php
namespace App\Http\Dao;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Dao\ConfigDao;

use App\CacheApresentador;

class CacheApresentadorDao {
    
       public static function getListGridCad($filtro, $order, $order_type){
           
             $sql = "select p.* from cache_apresentador p where 1 = 1 ". $filtro .
                     " order by ".$order. " ".$order_type;
             
             $itens = DB::select($sql);
             

             for ($i=0; $i < count( $itens) ; $i++) { 
                    $item = &$itens[$i];
                    $valor = $item->data;
                    
                    
                   // $item->data = $this->DataBR($valor, true); //Colocando como formato BR
             }
             
             return $itens;
           
       }
       
       public static function removeAntigo(){
           $dt = new DateTime(date("Y-m-d"));
           $dt->modify("-2 days");
           $sql = "delete from cache_apresentador where data<= '". $dt->format("Y-m-d")." 00:00:00' ";
           DB::delete($sql);
       }
       
       public static function getApresentadorByDia($id_programa, $id_operador, DateTime $dtPadrao){
           
           $dtPadrao->modify("-10 hours");
           $sql = "select id_apresentador as res from cache_apresentador where id_programa = ". $id_programa. " and id_operador = ". $id_operador.
                   " and data >= '".$dtPadrao->format("Y-m-d H:m:s")."' ";
           
           $id_apresentador = ConfigDao::executeScalar($sql);
           return $id_apresentador;
       }
       
       public static function setApresentadorByDia($id_apresentador, $id_programa, $id_operador, DateTime $dtPadrao){
          
                     $sql = "select id as res from cache_apresentador where id_programa = ". $id_programa. " and id_operador = ". $id_operador.
                          " and data >= '".$dtPadrao->format("Y-m-d H:m:s")."' ";
                     
                      $id_tmp = ConfigDao::executeScalar($sql);
                      
                      $reg = new CacheApresentador();
                      
                      if ( $id_tmp != ""){
                          $reg = CacheApresentador::find($id_tmp);
                      }
                      
                      $reg->id_programa = $id_programa;
                      $reg->id_operador = $id_operador;
                      $reg->id_apresentador = $id_apresentador;
                      $reg->data = $dtPadrao->format("Y-m-d H:m:s");
                      
                      $reg->save();
                      
                      return $reg->id;
           
       }
    
       public static function salvarDadosJson( $hd_json, $ids_delete_json ){
           
           $itens = json_decode($hd_json);
           $ids_delete = json_decode($ids_delete_json);
           
           $qtde_salvo = 0; $qtde_delete = 0;
           
           for ( $i = 0; $i < count($itens); $i++){
                           
                       $item_req = $itens[$i];    
                       
                       $reg = new CacheApresentador();
                       
                       if ( $item_req->id != ""){
                             $reg = CacheApresentador::find($item_req->id);
                       }
					            $reg->id = $item_req->id;   
   $reg->id_programa = ConfigDao::numeroBanco(  $item_req->id_programa  );  
   $reg->id_apresentador = ConfigDao::numeroBanco(  $item_req->id_apresentador  );  
   $reg->id_operador = ConfigDao::numeroBanco(  $item_req->id_operador  );  
   $reg->data = ConfigDao::dataBanco(  $item_req->data  );  
                       
                       
                       ConfigDao::blankToNull($reg);
                       
		       $ret = $reg->save();
                       $qtde_salvo++;
           }
           
           for ( $i = 0; $i < count($ids_delete); $i++){
               $item_req = $ids_delete[$i];
               
                 if ( $item_req->id != ""){
                             $reg = CacheApresentador::find($item_req->id);
                             $reg->delete();//Removendo o item desejado para deletar.
                             $qtde_delete++;
                 }
           }
           
           return array("qtde_salvo" => $qtde_salvo, "qtde_deleted" => $qtde_delete , "success"=> true );
           
       }
       
       
        
}

