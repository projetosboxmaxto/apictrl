<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Arquivos
 *
 * @package App
*/
class TempSearch extends Model
{
    public $timestamps = false;
    
    protected $table = 'temp_search';
    protected $fillable = ['id_evento_arquivo',
                            'id_evento',
                            'texto', 'json','em_uso'];
    protected $hidden = [];
    
}