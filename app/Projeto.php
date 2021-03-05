<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Projeto
 *
 * @package App
*/
class Projeto extends Model
{
    protected $table = 'projeto';
    protected $fillable = ['data',
                            'id_evento',
                            'id_operador',
                            'arquivos',
                            'meta_dados',
                            'path',
                            'dia',];
    protected $hidden = [];
    
}
