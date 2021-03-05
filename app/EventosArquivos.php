<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class EventosArquivos
 *
 * @package App
*/
class EventosArquivos extends Model
{
    
    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);

        $this->com_temp_search = 0;
    }
    
    
    protected $table = 'eventos_arquivos';
    protected $fillable = ['path',
                            'nome',
                            'id_evento',
                            'tempo_realizado_minutos',
                            'hora_inicio',
                            'hora_inicio_seg',
                            'inserted_at',
                            'texto',
                            'tipo',
                            'json','com_temp_search', 'meta_dados',
                             'meta_dados_elastic', 
                            'com_elastic_search'];
    protected $hidden = [];
    
}
