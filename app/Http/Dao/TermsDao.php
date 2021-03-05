<?php
namespace App\Http\Dao;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use \App\Terms;
use \App\Taxonomies;
use \App\helpers;

class TermsDao {


    public static function getList($type, $compl = ""){

    		 $sql = " select t.*, ta.name as taxonomie_type, ta.id as taxonomy_id from taxonomies ta inner join terms t on t.id = ta.term_id where 1 = 1  ";

               if ( $type != ""){

               	      $sql .= " and ta.name='". $type."' ";
               }

               $sql  .= $compl ;

               $sql .= " order by t.name asc ";

               $itens = DB::select($sql);

               return $itens;
    }

    public static function getSlug($name){
      $ret = str_replace(" ","-", str_replace("  "," ", strtolower( helpers::removeAcentos( $name ) )));

      $ret = preg_replace_callback(
                  '/[^A-Za-z0-9\-]/',
                  function ($matches) {
                      return '';
                  },
                  $ret
              ); 

      return $ret;
    }


    public static function saveTaxonomyTerm($name, $type){


        $itens = self::getList( $type, " and lower(t.name) = lower('".trim($name)."')  " );

        if ( count($itens) ){
                   return $itens[0]->taxonomy_id;
        }

        $reg = new Terms;
        $reg->name = $name;
        $taxonomy = $type;
        $slug = self::getSlug(  $reg->name );

        $reg->slug = $slug;
        $reg->term_group = 0;        
        $ret = $reg->save();


        $msg = "sucesso!"; $code = 1;
        if (! $ret  ){
                  $code = 0;
                  $msg = "erro";
        }else {


              $tax = new Taxonomies();
              $tax->name = $taxonomy ;
              $tax->term_id =  $reg->id;
              $tax->parent = 0;
              $tax->count = 0;
              $tax->description = ' ';
              $tax->save(); 
        }

        $itens = self::getList( $type, " and lower(t.name) = lower('".trim($name)."')  " );

        if ( count($itens) ){
                   return $itens[0]->taxonomy_id;
        }

    }

    public static function arrayToString($list, $prop, $sep = ","){

           $saida = array();

           for ( $i =0; $i < count($list); $i++){
                  $item = get_object_vars($list[$i]);
                  $saida[ count( $saida ) ] = $item[  $prop ];
           }

           return join($sep, $saida);
    }



}






?>