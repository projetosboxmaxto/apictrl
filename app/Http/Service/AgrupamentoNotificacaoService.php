<?php
namespace App\Http\Service;

use Illuminate\Support\Facades\DB;
use App\Http\Service\EventoService;

use Illuminate\Http\Request;


class AgrupamentoNotificacaoService{
    
    
    public static function agrupaNotificacao($id_evento ){
        
               $sql = "  select eap.id, eap.palavra, eap.id_evento, ea.hora_inicio_seg, ea.hora_inicio, eap.tempo_seg, eap.tempo from eventos_arquivos_palavras eap 
                    inner join eventos_arquivos ea on ea.id = eap.id_evento_arquivo 
                   where 
			  eap.id_evento = 1 ";
        
                $sql = "select * from eventos_arquivos_palavras eap "
                        . " where eap.id_evento = ". $id_evento . 
                         " and ";
    }
    
    
    
    
    
    
    
}

