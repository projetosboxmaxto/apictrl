<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Log
 *
 * @package App
*/
class Log extends Model
{
    public $timestamps = false;
    protected $table = 'log';
    protected $fillable = ['data_inicio',
                            'data_fim',
                            'tempo',
                            'texto',
                            'tabela',
                            'registro_id',
                            'operador_id',
                            'nome_operador',
                            'tipo',
                            'titulo'];
    protected $hidden = [];
    
}
