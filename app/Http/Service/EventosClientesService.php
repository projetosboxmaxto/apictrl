<?php

namespace App\Http\Service;

use Illuminate\Support\Facades\DB;


class EventosClientesService{
    
    
    public static function adicionaCliente($id_eventos, $id_cliente, $cita_diretamente = 0 ){
        
        $reg = new \App\EventosClientes();
        
        $data = \App\Http\Dao\ConfigDao::executeScalar("select data as res from eventos where id = " . $id_eventos);
        
        $id_tmp = \App\Http\Dao\ConfigDao::executeScalar("select id as res from eventos_clientes where id_cliente = ".$id_cliente . " and id_eventos = ". $id_eventos);
        
        if ( !is_null($id_tmp) && $id_tmp != ""){
            $reg = \App\EventosClientes::find( $id_tmp );
        }
        
        $reg->id_eventos = $id_eventos;
        $reg->id_cliente = $id_cliente;
        $reg->data = $data;
        $reg->cita_diretamente = $cita_diretamente;
        
        $reg->save();
        
    }
    
     
    public static function adicionaClienteArquivo($id, $id_eventos, $id_cliente, $cita_diretamente = 0 ){
        
        $reg = new \App\EventosArquivosClientes();
        
        $data = \App\Http\Dao\ConfigDao::executeScalar("select data as res from eventos where id = " . $id_eventos);
        
        $id_tmp = \App\Http\Dao\ConfigDao::executeScalar("select id as res from eventos_arquivos_clientes "
                . " where id_cliente = ".$id_cliente . " and id_evento = ". $id_eventos. " and id_evento_arquivo = ". $id);
        
        if ( !is_null($id_tmp) && $id_tmp != ""){
            $reg = \App\EventosArquivosClientes::find( $id_tmp );
        }
        
        $reg->id_evento = $id_eventos;
        $reg->id_cliente = $id_cliente;
        $reg->id_evento_arquivo = $id;
        $reg->data = $data;
        $reg->cita_diretamente = $cita_diretamente;
        
        $reg->save();
        
    }
   
    
    public static function salvarClientes($id_eventos, $clientes){
        
        //print_r( $clientes );die(" ");
        for ( $i = 0; $i < count($clientes); $i++ ){
            $item = @$clientes[$i];
            
            if (is_null($item))
                continue;
            
            $cita_diretamente = 0;
            
            if ( property_exists($item, "citacao_direta")  ){
                $cita_diretamente = $item->citacao_direta;
            }
            
            self::adicionaCliente($id_eventos, $item->id, $cita_diretamente);
        }
    }
    
    
    public static function salvarClientesArquivos($id, $id_eventos,  $clientes){
        
        for ( $i = 0; $i < count($clientes); $i++ ){
            $item = @$clientes[$i];
          
             if (is_null($item))
                continue;
            $cita_diretamente = 0;
            
            if ( property_exists($item, "citacao_direta")  ){
                $cita_diretamente = $item->citacao_direta;
            }
            
            self::adicionaClienteArquivo($id, $id_eventos, $item->id, $cita_diretamente);
        }
    }
    
    
}

?>
