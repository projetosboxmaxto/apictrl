<?php
namespace App\Http\Dao;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Dao\ConfigDao;

use App\SearchQueries;
use App\ClienteConfiguracao;

class SearchQueriesDao {
    
       public static function getListGridCad($filtro, $order, $order_type){
           
             $sql = "select p.* from search_queries p where 1 = 1 ". $filtro .
                     " order by ".$order. " ".$order_type;
             
             $itens = DB::select($sql);
             

             for ($i=0; $i < count( $itens) ; $i++) { 
                    $item = &$itens[$i];
                    $valor = $item->data;
                    
                    
                   // $item->data = $this->DataBR($valor, true); //Colocando como formato BR
             }
             
             return $itens;
           
       }

     
    
       public static function salvarDadosJson( $hd_json, $ids_delete_json ){
           
           $itens = json_decode($hd_json);
           $ids_delete = json_decode($ids_delete_json);
           
           $qtde_salvo = 0; $qtde_delete = 0;
           
           for ( $i = 0; $i < count($itens); $i++){
                           
                       $item_req = $itens[$i];    
                       
                       $reg = new SearchQueries();
                       
                       if ( $item_req->id != ""){
                             $reg = SearchQueries::find($item_req->id);
                       }
			 $reg->id = $item_req->id;   
                       //  $reg->id_cliente = ConfigDao::numeroBanco(  $item_req->id_cliente  );  
                         //     $reg->titulo = $item_req->titulo;   
                              $reg->querie = $item_req->querie;   
                        $reg->ativo = ConfigDao::numeroBanco(  $item_req->ativo  );  
                       // $reg->data = ConfigDao::dataBanco(  $item_req->data  );  
                       // $reg->id_praca = ConfigDao::numeroBanco(  $item_req->id_praca  );  
                        
                        if ( $reg->id_praca  != "" && $reg->titulo ==""  ){
                            $reg->titulo =  ConfigDao::executeScalar2("select convert(descricao using utf8) as res from cadastro_basico where id = ". $reg->id_praca);
                        }

                       
                       ConfigDao::blankToNull($reg);
                       
		       $ret = $reg->save();
                       $qtde_salvo++;
           }
           
           for ( $i = 0; $i < count($ids_delete); $i++){
               $item_req = $ids_delete[$i];
               
                 if ( $item_req->id != ""){
                             $reg = SearchQueries::find($item_req->id);
                             $reg->delete();//Removendo o item desejado para deletar.
                             $qtde_delete++;
                 }
           }
           
           return array("qtde_salvo" => $qtde_salvo, "qtde_deleted" => $qtde_delete , "success"=> true );
           
       }
       
       
        
}

