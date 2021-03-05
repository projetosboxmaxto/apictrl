<?php
namespace App\Http\Dao;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use \App\Terms;
use \App\Taxonomies;
use \App\PostTaxonomy;
use \App\Posts;

class PostsDao {


	          public static function getFormObject($post_id){

	          	     $post = new \App\Posts();

	          	     $reg =  $post->find($post_id);

	          	     $categories_objs = TermsDao::getList("category", 
	          	     	    " and ta.id in ( select taxonomy_id from post_taxonomy where post_id = ". $post_id . " ) " );

	          	     $tags_objs = TermsDao::getList("post_tag", 
	          	     	    " and ta.id in ( select taxonomy_id from post_taxonomy where post_id = ". $post_id . " ) " );

	          	     $categories = TermsDao::arrayToString( $categories_objs, "id" );
	          	     $tags = TermsDao::arrayToString( $tags_objs, "name" );

	          	     return array("item"=> $reg, "categories"=> $categories, "tags" => $tags,
	          	          "categories_obj"=>$categories_objs, "tags_obj" => $tags_objs);


	          }

              public static function getListGrid( $post_type = "", $compl = ""){

                       /* case when p.status = 'publish' then DATE_FORMAT(p.publication, '%d/%m/%Y %H:%i')
                      when p.created_at is not null then DATE_FORMAT(p.updated_at, '%d/%m/%Y %H:%i') 
                         else DATE_FORMAT(p.updated_at, '%d/%m/%Y %H:%i') end as data */

                   $sql = " select p.id, p.title, p.title as title_clean, u.name as author_name,
                      case when p.status = 'publish' then p.publication
                      when p.updated_at is not null then p.updated_at 
                         else p.created_at end as data_p, 
                         ' ' as blnk, p.status, p.slug, p.seo_keywords, p.data, 
                         p.content, p.description

                       from posts p 
                       left join posts pai on pai.id = p.parent_id 
                        left join users u on u.id = p.author_id 
                    where 1 = 1 ";

                   if ( $post_type != ""){

                         $sql .= " and p.post_type  ='". $post_type . "' ";
                   }

                   $sql .= $compl;

                   $result = DB::select($sql);
                   //$result = $result->toArray();
                  return $result;

              }


              public static function savePost($post, $categories_ids, $tags_text){

                     $ret = $post->save();

                     $post_id = $post->id;

                     //Salvando categoria.
                     //die( $categories_ids );
                      self::saveTermsByIds($categories_ids, "category", $post_id);

                      $category_taxonomies_id = self::getIdsTaxonomies( $categories_ids );

                     //Salvando tags
                      $ids_tags = self::saveTermsByDescription($tags_text, "post_tag", $post_id);

                      self::removeExcludedTerms( $category_taxonomies_id, $post_id , "category");
                      self::removeExcludedTerms( $ids_tags, $post_id , "post_tag");

                      return $post->id;

              }

              public static function getIdsTaxonomies($ids_terms){

                     if ( $ids_terms == "")
                     	return " 0 ";

                     $ar = DB::select("select id from taxonomies where term_id in ( ". $ids_terms." ) ");

                     $ids = TermsDao::arrayToString( $ar , "id");

                     return  $ids ;

              }




              public static function saveTermsByDescription($text, $type,  $post_id){
                       
                       $ar = explode(",",$text);

                       $taxonomy_lists = [];

                       for ( $i = 0; $i < count($ar); $i++){
                                
                                if ( trim($ar[$i]) == "")
                                	continue;


                              $taxonomy_id = TermsDao::saveTaxonomyTerm( trim($ar[$i]), trim($type) );

                              self::savePostTaxonomy($taxonomy_id, $post_id );
                              
                              $taxonomy_lists[count($taxonomy_lists)] = $taxonomy_id;
                       }

                       return join(",", $taxonomy_lists);


              }




              public static function saveTermsByIds($ids, $type,  $post_id){

              	   $ar = explode(",",$ids);

                    $taxonomy_lists = [];


                       for ( $i = 0; $i < count($ar); $i++){
                                

                               // die( $sql );

                                if ( trim($ar[$i]) == "")
                                	continue;


                              $sql =  "select id as res from taxonomies where term_id = ". $ar[$i];
                              $taxonomy_id = self::executeScalar($sql);
                              self::savePostTaxonomy( $taxonomy_id, $post_id );
                              
                              //print_r(  $ar ); die(" -- " . $ar[$i]. " -- " . $post_id);
                              $taxonomy_lists[count($taxonomy_lists)] = $taxonomy_id;
                       }

                       return join(",", $taxonomy_lists);

              }

              public static function savePostTaxonomy($taxonomy_id, $post_id ){

                  
                if ( $taxonomy_id == "")
                     return  false;

              	$sql = " select * from post_taxonomy where post_id = ". $post_id . " 
              	      and taxonomy_id = ". $taxonomy_id;

               // die(" -- " . $sql );
                $itens = DB::select($sql);

                if ( count($itens) <= 0  ){

                	$reg = new PostTaxonomy();
                	$reg->post_id = $post_id;
                	$reg->taxonomy_id = $taxonomy_id;

                	$reg->save();
                }

              }

               public static function executeScalar( $sql ){
      					$ar = DB::select($sql);

                if ( count($ar) > 0 ){

                return $ar[0]->res;
                }

                return "";
					
					//print_r( $ar ); die(" ");
					
				}

              public static function removeExcludedTerms($ids_taxonomy, $post_id, $type){

              	  $str = $ids_taxonomy == "" ? "0 " : $ids_taxonomy;


              	  $sql = " delete from post_taxonomy where post_id = ". $post_id . 
              	         " and taxonomy_id not in ( ". $str ." ) and taxonomy_id in ( " .
              	           " select id from taxonomies where name = '". $type."' ) ";

              	  return DB::statement($sql);
              }
              
                public static function blankToNull(&$eloquentObj){
                       $columns =  $eloquentObj->getFillable();
                    
                    
                        foreach ($columns as $column)
                        {
                              if ( $eloquentObj->$column == "" ){
                                  $eloquentObj->$column = null;
                              }
                        }
              }
    
    
     
    public static function dataBanco($valor)
	{
		if ($valor == "")
			return "";
		
                $hora = "";
                if ( strpos(" ". $valor, ":")){
                    $ar = explode(" ", $valor);
                    
                    if ( @$ar[1] != ""){
                          $hora = " ". $ar[1];
                    }
                    $valor = $ar[0];
                }
                
                
		$arr = explode("/",$valor);
		
		return trim($arr[2])."-".trim($arr[1])."-".trim($arr[0]). $hora;
		
	}
        
      public static function DataBR($valor, $semhora =false)
            {

                if ($valor == "")
                    return "";

                if ( strpos(" ". $valor,".")){
                    $exp = explode(".", $valor);
                    $valor = $exp[0];
                }


                $valor = str_replace("-","/",$valor);

                $ar = explode(" ",$valor);
                $arr = explode("/",$ar[0]);

                if ( count($arr) < 3)
                    return "";

                $hora = "";
                if (! $semhora)
                    $hora = " " . @$ar[1];

                return $arr[2]."/".$arr[1]."/".$arr[0].$hora;

            }
            
         public static  function numeroBanco($valor)
	{
		$val = str_replace(".","",$valor);
		$val = str_replace(",",".",$val);
		
		$val  = round($val, 2 );
		
		$val = str_replace(".00","",$val);
		for ( $i =1 ; $i <= 9; $i++)
		{
			$val = str_replace(".".$i."0",".".$i,$val);
			
		}
		
		return $val;
	}
	public static  function numeroTela($valor, $removeZeros=1)
	{
		if ($valor == null || $valor =="")
			return "";
		
		$val = number_format($valor,2,",",".");
		
		// $val = str_replace(".",",",$valor);
		if ( $removeZeros )
		{
			$val = str_replace(",00","",$val);
			for ( $i =1 ; $i <= 9; $i++)
			{
				$val = str_replace(",".$i."0",",".$i,$val);
				
			}
			
		}
		if ( $removeZeros == 1 )
			$val = str_replace(".","",$val);
			
			
		return $val;
	}




}