<?php
namespace App\Http\Service\Imagem;

use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;

class ImagePathService implements IServiceImage{
    
    
       var $client;
       var $adapter;
       var $filesystem;
       var $folder = "files";
    
        function __construct(){

            $adapter = new Local(config("app.PATH_ARQUIVOS"));
            $filesystem = new Filesystem($adapter);
            
            $this->adapter = $adapter ;
            $this->filesystem = $filesystem;

       }
       
       function getPathBase(){
           return config("app.PATH_ARQUIVOS");
       }

       function destroyImagem($name){

            /*
                  @unlink( public_path() . DIRECTORY_SEPARATOR . 
                             	   "files". DIRECTORY_SEPARATOR . $this->folder .
                             	    DIRECTORY_SEPARATOR . $name );
                  
            */
           
           
                  
                   if ( $this->filesystem->has( $name ) ){
                         $this->filesystem->delete( $name );
                   }
                  
                  
       }
       
        public function saveOrder(Request $request){

	        	$list = $request->input("list");

	        	$ar = explode(",", $list);

                for ( $i = 0; $i < count($ar); $i++ ){
                         $id = $ar[$i];

                         $sql = " update images set order_banner = ". ( $i + 1). " where id = ". $id;
                         DB::statement($sql);
                } 

                 return response()->json([
						            array("msg"=>"Feito com sucesso!"),
						        ], 200);
	}


       function sendImagem($file, $name){
           
                 $ar = explode("/", $name);
                   $path = "";
                   
                   if ( count( $ar ) > 1){
                       
                       for ( $i = 0; $i < count($ar)-1; $i++){
                             //Cria as pastas..
                           if ( $path  != "")
                               $path .="/";
                           
                           $path .= $ar[$i];
                           
                              $this->filesystem->createDir($path);
                       }
                       
                   }

                   if ( $this->filesystem->has( $name ) ){
                         $this->filesystem->delete( $name );
                   }
                   
                   
                  // print_r( $file ); die(" -- oq temos aqui?? ");
           

                    $this->filesystem->write( $name, file_get_contents( $file ) ,
                        ['visibility' => 'public']);


       }

       function getUrl($name){
       	  $str =  config("app.base_path"). str_replace("//","/", $this->folder."/". $name);

       	  return $str;
       }
}

