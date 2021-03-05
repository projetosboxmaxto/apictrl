<?php
namespace App\Http\Service\Imagem;

class CurlService{
    
       static function download($url, $path_save){
           
           $client = new \GuzzleHttp\Client();
           $response = $client->request('GET', $url, ['sink' => $path_save]);
          
            return ['response_code'=>$response->getStatusCode(), 'name' => $path_save];
        }
       function sendImagem(Illuminate\Http\UploadedFile $file, $name){
           
             
             $UrlUploadEvento = \App\Http\Dao\ConfigDao::getValor("UrlUploadEvento");
      
           
             
             $pasta = \App\Http\Service\UtilService::left( $name,  stripos("/", $name));
             
            
             $client = new GuzzleHttp\Client();
             
            $response = $client->request('POST', $UrlUploadEvento."/index.php", 
                       [
                            'multipart' => [
                                [
                                    'name'     => 'pasta',
                                    'contents' => $pasta
                                ],
                                [
                                    'name'     => 'acao',
                                    'contents' => "upload"
                                ],
                                [
                                    'name'     => 'file',
                                    'contents' => fopen($file->path(), 'r'),
                                    'filename' => $file->getClientOriginalName(),
                                ],
                            ]
                        ]);
            
             return $response->getBody();
             
              $post_fields = array("acao" =>"upload", "pasta" => $pasta);
             
             
             $request = curl_init($UrlUploadEvento."/index.php");
             
             if (function_exists('curl_file_create')) { // php 5.5+
                    $post_fields["file"] = curl_file_create($file->path());
              } else { // 
                    $post_fields["file"] = '@' . $file->path();
              }
             

            // send a file
            curl_setopt($request, CURLOPT_POST, true);
            curl_setopt(
                $request,
                CURLOPT_POSTFIELDS,
                    $post_fields
               );

            // output the response
            curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
            $resultado =  curl_exec($request);

            // close the session
            curl_close($request);
            
            return $resultado;
           
           
       }
  
    
    
}

