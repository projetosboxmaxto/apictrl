<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class EventosArquivos
 *
 * @package App
*/
class EventosArquivosClientes extends Model
{
    
    protected $table = 'eventos_arquivos_clientes';
    public $timestamps = false;
    protected $fillable = ['data',
                            'id_evento',
                            'id_evento_arquivo',
                            'id_cliente'];
    protected $hidden = [];
    
}
