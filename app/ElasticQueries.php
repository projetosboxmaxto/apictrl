<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ElasticQueries
 *
 * @package App
*/
class ElasticQueries extends Model
{
    public $timestamps = false;
    protected $table = 'elastic_queries';
    protected $fillable = ['id_cliente',
                    'titulo',
                    'querie',
                    'ativo',
                    'data',
                    'id_praca'];
    protected $hidden = [];
    
}
