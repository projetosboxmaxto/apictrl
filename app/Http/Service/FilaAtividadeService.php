<?php

namespace App\Http\Service;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Dao\PostsDao;
use DateTime;

class FilaAtividadeService{
    
    
    
    public static function adicionar($reg_arquivo, $status, $tipo ){
        
        $sql = "select id as res from fila_atividade where id_evento_arquivo = ". $reg_arquivo->id. " and tipo='". $tipo."' ";
        $idtmp = \App\Http\Dao\ConfigDao::executeScalar($sql);
        
        if ( $idtmp != ""){
            DB::statement("update fila_atividade set status = ". $status. " where id = ". $idtmp);
            return $idtmp;
        }
        
        $reg = new \App\FilaAtividade(); //  \App\FilaAtividade::find($idtmp);
        
        $reg->id_evento_arquivo = $reg_arquivo->id_evento_arquivo;
        $reg->id_evento = $reg_arquivo->id_evento;
        $reg->status = $status;
        $reg->tipo = $tipo;
        
        $reg->save();
        
        return $reg->id;
        
        
    }

    public static function removeAntigo(){

        $dt = new DateTime(date("Y-m-d"));
        $dt->modify("-30 days");

        $sql = "delete from fila_atividade where created_at < '". $dt->format("y-m-d")." 00:00:00' ";
        DB::delete($sql);

        $sql = "delete from log where data_inicio < '". $dt->format("y-m-d")." 00:00:00' ";
        DB::delete($sql);

    }

    public static function setStatus($id_evento_arquivo, $tipo, $status){
        $sql = "update fila_atividade set status = ". $status. 
           " where id_evento_arquivo = ". $id_evento_arquivo. " and tipo='". $tipo."' " ;

        DB::statement($sql);
    }
    
    public static function remove($id_evento_arquivo,  $tipo ){
        
        $sql = "select id as res from fila_atividade where id_evento_arquivo = ". $id_evento_arquivo. " and tipo='". $tipo."' ";
        $idtmp = \App\Http\Dao\ConfigDao::executeScalar($sql);
        
        if ( $idtmp != ""){
            DB::delete("delete from fila_atividade where id = ". $idtmp);
            return $idtmp;
        }
        
    }
   // executarFila
     public static function getListFila($tipo){
           
        $sql = " select * from fila_atividade where status = 0 and tipo='". $tipo."' ";
        
        $lista =  DB::select($sql);
        
        return  $lista;
        
     }
    public static function podeExecutarFila($tipo = "elastic"){
        
        $sql = " select max(created_at) as created_at, "
                . " TIMESTAMPDIFF(MINUTE, max(created_at), sysdate() ) as minutos, count(*) as qtde from fila_atividade where status = 0 and tipo='". $tipo."' ";
        
        $lista =  DB::select($sql);
        
        if ( count( $lista) <= 0 ){
            
              return false;
        }
        
        $item = $lista[0];
        
        if ( $item->qtde > 10 || $item->minutos >= 40 ){
            return true;
        }
        
        return false;
        //$res =  \App\Http\Service\ElasticSearchService::create_arquivo($reg_arquivo);
    }
    
    
}