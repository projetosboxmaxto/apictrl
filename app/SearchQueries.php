<?php

/* 
 * 
 * CREATE TABLE `search_queries` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`id_cliente` INT(11) NULL DEFAULT NULL,
	`titulo` VARCHAR(300) NULL DEFAULT NULL,
	`querie` LONGTEXT NULL,
	`ativo` SMALLINT(6) NULL DEFAULT '0',
	`data` DATETIME NULL DEFAULT NULL,
	`id_praca` INT(11) NULL DEFAULT NULL,
	PRIMARY KEY (`id`),
	INDEX `ix_id_cliente` (`id_cliente`)
)
COLLATE='utf8mb4_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=7
;

 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Arquivos
 *
 * @package App
*/
class SearchQueries extends Model
{
    public $timestamps = false;
    
    protected $table = 'search_queries';
    protected $fillable = ['id_cliente','id_praca', 'exclusao',
                            'titulo',
                            'querie', 'ativo','data'];
    protected $hidden = [];
    
}
