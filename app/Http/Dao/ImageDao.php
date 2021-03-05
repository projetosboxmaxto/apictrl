<?php

namespace App\Http\Dao;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use \App\Config;


class ImageDao {
    
        public static function executeScalar( $sql ){
					$ar = DB::select($sql);
                                        
                                        if ( count($ar) <= 0 )
                                            return "";
					
					//print_r( $ar ); die(" ");
					
					return $ar[0]->res;
       }
       
       public static function getImageByParent($parent_id, $tipo ){
           
             $sql = "select * from images where parent_id='".$parent_id."' and type_image='".
                       $tipo."' ";
             
             $ar = DB::select($sql);
             
             //
             
             if ( count($ar) > 0 ){
                 $item = (array)$ar[0];
                 //print_r( $item ); die(" ");
                 $ar = array();
                 $ar["id"] = $item["id"];
                 $ar["filename"] = $item["filename"];
                 $ar["url"] = $item["url"];
                 
                 $sizes = unserialize( $item["sizes"] );
                 $file = $sizes["file"];
                 
                 
                 $thumb =  $sizes["sizes"]["thumbnail"] ;
                 
                // echo("<pre>"); print_r(  $thumb );
                // echo("</pre>"); die(" ");
                 $final_miniatura = str_replace( $sizes["file"], $thumb["file"] , $item["filename"] );
                 $ar["filename_thumb"] = $final_miniatura;
                 
                 return $ar;
                 
             }else{
                 return null;
             }
       }
    
}