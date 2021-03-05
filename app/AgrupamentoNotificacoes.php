<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class AgrupamentoNotificacoes
 *
 * @package App
*/
class AgrupamentoNotificacoes extends Model
{
    protected $table = 'agrupamento_notificacoes';
    public $timestamps = false;
    protected $fillable = ['dia',
                    'id_programa',
                    'id_emissora',
                    'palavras','palavras_backup',
                    'clientes',
                    'hora_inicio_seg',
                    'hora_fim_seg',
                    'tempo_seg',
                    'json',
                    'data',
                    'hora_inicio',
                    'hora_fim',
                    'id_evento_arquivo',];
    protected $hidden = [];
    
}
