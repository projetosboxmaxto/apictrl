<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ProjetoArquivos
 *
 * @package App
*/
class ProjetoArquivos extends Model
{
    protected $table = 'projeto_arquivos';
    protected $fillable = ['data',
'id_projeto',
'id_operador',
'arquivo',
'meta_dados',
'path',
'tipo',];
    protected $hidden = [];
    
}
