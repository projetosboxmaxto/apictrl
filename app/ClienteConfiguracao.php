<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ClienteConfiguracao
 *
 * @package App
*/
class ClienteConfiguracao extends Model
{
    protected $table = 'cliente_configuracao';
    protected $fillable = ['id_cliente',
'consulta_comum',
'consulta_elastic',
'loaded_praca',
'praca_json',];
    protected $hidden = [];
    
}
