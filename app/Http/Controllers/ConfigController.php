<?php

namespace App\Http\Controllers;

use Illuminate\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Config;
use App\Http\Dao\ConfigDao;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Less_Cache;
use Illuminate\Support\Facades\DB;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;

class ConfigController extends Controller
{
     public function index()
     {
         return $this->getList();
         
     }
     //armazena todas as variáveis.
     public function store(Request $request){
         
         $keys = $request->input("keys");
         
         $ar = explode(",", $keys);
         
         $salvos = array();
         $configs = array();
         
         for ( $i = 0; $i < count($ar); $i++ ){
             
              $key = $ar[$i];
              if ( trim($key) == "")
                  continue;
              
              $value = $request->input(  $key );
              
              ConfigDao::setValor($key, $value);
              $salvos[] = $key;
                $configs[] = array($key=>$value);
         }
         
         
         $keys = $request->input("keys_cores");
         
         $ar = explode(",", $keys);
         
         for ( $i = 0; $i < count($ar); $i++ ){
             
              $key = $ar[$i];
              if ( trim($key) == "")
                  continue;
              
              $o_key = "@". str_replace("cor_","",  $key);
              $value = $request->input(  $key );
              
              ConfigDao::setValor($o_key, $value);
              $salvos[] = $o_key;
                $configs[] = array($o_key=>$value);
         }
         
         $cores_padrao = $this->getCoresPadrao();
         
         $deve_salvar_less = false;
         $passei_pelas_cores = false;
         
         for ( $i = 0; $i < count($cores_padrao); $i++ ){
               $item_padrao = $cores_padrao[$i];
               
         $passei_pelas_cores = true;
               
               $cor_salva = ConfigDao::getValor($item_padrao->name);
               
               if ( $cor_salva != $item_padrao->value ){
                   
                          $deve_salvar_less = true;
                          break;
               }
         }
         
         if ( $deve_salvar_less ){
             
             $this->salvarLess();
             ConfigDao::setValor("CSS_DEFAULT", "NO" );
             
             $version = ConfigDao::getValor("CSS_VERSION");
             
             if ( $version == "")
                 $version = "0";
             
             $version = (int)$version;
             $version++;
             ConfigDao::setValor("CSS_VERSION", $version);
         
         }else{
             
             ConfigDao::setValor("CSS_DEFAULT", "YES" );
         }
         
         return array("msg"=>"Salvo com sucesso!", "code"=>1, "keys"=> $salvos, "configs" => $configs,
              "por_cores"=> $passei_pelas_cores, "salvou_less"=> $deve_salvar_less);
     }
     
     
     public function salvarLess(){
         
          //$adapter2 = new Local( storage_path() . DIRECTORY_SEPARATOR . 'app'. DIRECTORY_SEPARATOR. 'less' );
          $file2 = new \Illuminate\Filesystem\Filesystem();
          $file2->cleanDirectory(storage_path() . DIRECTORY_SEPARATOR . 'app'. DIRECTORY_SEPARATOR. 'less');
         //die("teste");
         
         $ar = DB::select("select `key`, `value` from configs where `key` like '@%' ");
         
         $variables = array();
         for ( $i = 0; $i < count($ar); $i++ ){
             $item = $ar[$i];
             $variables[ $item->key ] = $item->value;
         }
         
         $path_less = "../site" ;
         
         $dir_less = realpath($path_less ). DIRECTORY_SEPARATOR . "assets". DIRECTORY_SEPARATOR . "less";
         $dir_css = realpath($path_less ). DIRECTORY_SEPARATOR . "assets". DIRECTORY_SEPARATOR . "css";
         
         //die( );
         
          $less_files = [$dir_less . DIRECTORY_SEPARATOR . 'style.less' => '../'];
             $options = ['cache_dir' => storage_path() . '/app/less', 'compress' => true];
             
        $css_file_name = Less_Cache::Get($less_files, $options, $variables);

          //die(" CSS: ". $css_file_name );
        // Get the compiled version and return it
        $complete_file_name = $dir_css. DIRECTORY_SEPARATOR. "style2.min.css";
                
                if (file_exists( $complete_file_name ) ){
                    @unlink( $complete_file_name );
                }
          $compiled = file_get_contents(storage_path('/app/less') . DIRECTORY_SEPARATOR . $css_file_name );
          //$oFile =  new File($dir_css.DIRECTORY_SEPARATOR."style2.min.css");
          
          $adapter = new Local( $dir_css );
          $filesystem = new Filesystem($adapter);
          
          $filesystem->write( "style2.min.css", $compiled , ['visibility' => 'public']);
          
          //$oFile->write( $compiled );
          // Storage::disk('local')->put($dir_css.DIRECTORY_SEPARATOR."style2.min.css", $compiled);
            
     }
     
     public function getList(){
         
         $data = ConfigDao::getAllParamaters();
         
         return array("data"=>$data);
     }
     public function getCoresPadrao(){
                 $file_path = storage_path('app/cores.txt');
                $txt = File::get($file_path);
                
                $ar = explode("\n", $txt);
                
                $cores = array();
                
                for ( $i = 0; $i < count($ar); $i++){
                    $linha = $ar[$i];
                    
                    if ( trim($linha) != ""){
                        $tmp = explode(":", $linha);
                        
                        //ConfigDao::setValor($tmp[0], $tmp[1]);
                        
                        $item = array("name"=>$tmp[0], "value"=> $tmp[1]);
                        
                        $cores[ count($cores) ] = (object)$item ;
                    }
                }
                
                return $cores;
     }
     public function storeColorDefault(){
         
                $file_path = storage_path('app/cores.txt');
                $txt = File::get($file_path);
                
                $ar = explode("\n", $txt);
                
                $cores = array();
                
                for ( $i = 0; $i < count($ar); $i++){
                    $linha = $ar[$i];
                    
                    if ( trim($linha) != ""){
                        $tmp = explode(":", $linha);
                        
                        ConfigDao::setValor($tmp[0], $tmp[1]);
                        
                        $item = array("name"=>$tmp[0], "value"=> $tmp[1]);
                        
                        $cores[ count($cores) ] = (object)$item ;
                    }
                }
                
                ConfigDao::setValor("CSS_DEFAULT", "YES" );
                
                return array("data"=>$cores, "msg"=>"Cores voltaram ao default");
         
     }
    
    
    
    
}