<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PalavrasChave
 *
 * @package App
*/
class PalavrasChave extends Model
{
    protected $table = 'palavras_chave';
    protected $fillable = ['id_cliente',
'palavra',
'data',
'id_praca',];
    protected $hidden = [];
    
}
