<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class EventosArquivos
 *
 * @package App
*/
class EventosClientes extends Model
{
    
    protected $table = 'eventos_clientes';
    protected $fillable = ['data',
                            'id_eventos',
                            'id_cliente',
                            'cita_diretamente'];
    protected $hidden = [];
    
}
