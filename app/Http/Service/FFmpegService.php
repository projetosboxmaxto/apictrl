<?php
namespace App\Http\Service;

 class  FFmpegService
 {
     
     
    public static function getFileTxt($saidaArray , $arquivo_path )
    {
        
        $arquivo_txt = "";
        
        for ( $i = 0; $i < count($saidaArray); $i++ ){
              if ( $i > 0 ){
                  $arquivo_txt .= PHP_EOL;
              }
              
              $arquivo_txt .= "file '" . $saidaArray[$i] . "' ";
        }
        

         UtilService::escreveArquivo($arquivo_path . DIRECTORY_SEPARATOR . "list.txt", $arquivo_txt);
         
         return $arquivo_path. DIRECTORY_SEPARATOR . "list.txt";
   
     
    }
    
    public static function gerarCorte($arquivo_origem, $arquivo_destino, $inicio, $fim ){
        
             $time = $fim - $inicio;
        
            $comando = "-ss " .$inicio. " -i \"" .$arquivo_origem. "\" -vcodec copy -acodec copy -to " .$time. " \"" .$arquivo_destino. "\"";
            
            $retorno = self::executeCommand($comando);
            
            EventoArquivoService::$ffmpeg_last = array("comando" =>$comando, "retorno"=> $retorno );
            return $retorno;
    }

    public static function executeCommand($parameter){
            $path_ffmpeg = ENV("PATH_FFMPEG");
           // die( $path_ffmpeg ." ". $parameter );
            return exec("\"".$path_ffmpeg."\"" ." ". $parameter );
            return shell_exec($path_ffmpeg ." ". $parameter );
    }
     
     
 }