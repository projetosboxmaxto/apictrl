<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class MateriaRadioTv
 *
 * @package App
*/
class MateriaRadioTv extends Model
{
    public $incrementing = false;
    protected $connection = 'mysql_midiaclip';
    protected $table = 'materia_radio_tv';
    public $timestamps = false;
    protected $fillable = ['id_programa',
            'id_apresentador',
            'indicar_programa',
            'fixar_programacao',
            'nr_registro_importado',];
    protected $hidden = [];
    
}
