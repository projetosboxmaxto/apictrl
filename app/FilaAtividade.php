<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class EventosArquivos
 *
 * @package App
*/
class FilaAtividade extends Model
{
    
    protected $table = 'fila_atividade';
    protected $fillable = ['id_evento',
                            'id_evento_arquivo',
                            'tipo',
                            'status'];
    protected $hidden = [];
    
}
