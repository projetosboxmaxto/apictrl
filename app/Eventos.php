<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Eventos
 *
 * @package App
*/
class Eventos extends Model
{
    protected $table = 'eventos';
    protected $fillable = ['data',
                            'id_programa',
                            'id_emissora',
                            'hora_inicio',
                            'hora_fim',
                            'hora_inicio_seg',
                            'hora_fim_seg',
                            'duracao',
                            'duracao_seg',
                            'tempo_realizado_minutos',
                            'tempo_total_minutos','dia',
                            'tipo','id_operador','id_evento_pai', 'tipo_hora'];
    protected $hidden = [];
    
}