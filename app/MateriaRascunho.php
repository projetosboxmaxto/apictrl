<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class MateriaRascunho
 *
 * @package App
*/
class MateriaRascunho extends Model
{
    protected $table = 'materia_rascunho';
    public $timestamps = false;
    protected $fillable = ['id_projeto',
                            'data',
                            'titulo',
                            'cliente_list',
                            'ids_arquivos',
                            'dados_materia',
                            'id_programa',
                            'dia',
                            'id_operador',
                            'data_cadastro',
                            'status',
                            'id_materia_radiotv_jornal',];
    protected $hidden = [];
    
}
