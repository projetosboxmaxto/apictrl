<?php
namespace App\Http\Service\Imagem;


class FaviconService{
    
    public static function saveFavicon($path_site, $file_zip ){
        
        $real_path_site = realpath($path_site);
        $path_favicon2 = $real_path_site . DIRECTORY_SEPARATOR . 'assets'. DIRECTORY_SEPARATOR. 'img'.
                DIRECTORY_SEPARATOR."favicon2";
        
        if ( ! is_dir($path_favicon2) ){
            @mkdir($path_favicon2);
        }
        
        $file2 = new \Illuminate\Filesystem\Filesystem();
        $file2->cleanDirectory( $path_favicon2 );
        
        $zipper = new \Chumper\Zipper\Zipper;
               
        $zipper->make($file_zip)->extractTo($path_favicon2);

        $zipper->close();
        
        //die("passei aqui ? ". $path_favicon2 . " -- ". $file_zip);
    }
    
    
    
    
}

