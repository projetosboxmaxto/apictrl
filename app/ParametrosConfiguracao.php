<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ParametrosConfiguracao
 *
 * @package App
*/
class ParametrosConfiguracao extends Model
{
    public $timestamps = false;
    protected $table = 'parametros_configuracao';
    protected $fillable = ['codigo',
                            'valor',
                            'titulo'];
    protected $hidden = [];
    
}
