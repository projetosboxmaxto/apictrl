<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Arquivos
 *
 * @package App
*/
class TempSearchPG extends Model
{
    public $timestamps = false;
    
    protected $table = 'temp_search';
    protected $connection = 'pgsql';
    protected $fillable = ['id_evento_arquivo',
                            'id_evento',
                            'texto', 'json'];
    protected $hidden = [];
    
}