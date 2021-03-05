<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class CacheApresentador
 *
 * @package App
*/
class CacheApresentador extends Model
{
    protected $table = 'cache_apresentador';
    protected $fillable = ['id_programa',
'id_apresentador',
'id_operador',
'data',];
    protected $hidden = [];
    
}
