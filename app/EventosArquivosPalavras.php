<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class EventosArquivosPalavras
 *
 * @package App
*/
class EventosArquivosPalavras extends Model
{
    protected $table = 'eventos_arquivos_palavras';
    public $timestamps = false;
    protected $fillable = ['data',
                            'id_evento',
                            'id_evento_arquivo',
                            'id_cliente',
                            'cita_diretamente',
                            'palavra',
                            'tempo',
                            'tempo_seg',
                            'id_dicionario_tag',
                            'status',
                            'operador',
                            'id_operador',
                            'id_materia_radiotv_jornal','sentimento'];
    protected $hidden = [];
    
}
