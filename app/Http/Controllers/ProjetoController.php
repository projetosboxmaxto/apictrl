<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Service\ErrorsService;

use Illuminate\Http\Request;
use App\Projeto;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Dao\ImageDao;
use App\Http\Dao\PostsDao;

set_time_limit ( 3000 );
class ProjetoController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(Request $request)
	{
	      $filtro = "";
              $order = " id "; $order_type = "desc";
              
              if ( $request->input( "filtro")  != ""){
                         	$str_filt = str_replace("'","''", $request->input( "filtro") );
                        	$filtro .= " and ( nome like '%".$str_filt."%' or email like '%".$str_filt."%' ) ";
               }
               
               
                $sql = "select p.*, '' as blnk, '' as user_link, '' as data "
                        . " from projeto p where 1 = 1 ". $filtro . " order by ".$order. " ".$order_type ;
                $itens = DB::select($sql);
                
                   // 

                for ($i=0; $i < count( $itens) ; $i++) { 
                  // $list[$i]->blnk = "<button class=\"btn btn-lg btn-info tab_row_button\"><i class=\"fa fa-edit\"></i><span style=\"display:none\">". $list[$i]->id. " </span></button>";
                    $titulo = $itens[$i]->nome;

                    $itens[$i]->user_link = "<a href=\"#\" class=\"botao_editar_grid\" idref=\"".$itens[$i]->id."\" >".
                                        $titulo."</a>";

                    $data =  $itens[$i]->created_at;


                    $stat_publicado  ="";
                    $itens[$i]->data = "<span style=\"display:none\">". $data . "</span>" . $this->DataBR(  $data, true );

					$itens[$i]->blnk = "<a href=\"#\" class=\"botao_editar_grid a_edit\" idref=\"".$itens[$i]->id."\" >"
                            . "<i class=\"fa fa-edit\"></i></a>";
					
                }

                
                $saida = array(
                             "qtde"=> count($itens),
                             "data" => $itens );
                         
                         
                return $saida;
	}
        
       
        
	
	/*
	            Route::get('/api/projeto', 'ProjetoController@index');
                Route::get('/api/projeto/{id}', 'ProjetoController@show');
                Route::put('/api/projeto/{id}', 'ProjetoController@update');
                Route::post('/api/projeto', 'ProjetoController@create');
                Route::delete('/api/projeto/{id}', 'ProjetoController@destroy');
				
				Route::resource('users', 'UserAPIController');
				
				*/

        
        function encrypt( $senha ){
               return md5( env("CRYPT_PASS") . $senha);
            //  return Hash::make( $senha);
        }
		public function testheader(Request $request){

				  $o_auth_header  = $GLOBALS["auth_header"] ;
				  return array("msg"=>"Teste", "header" => $o_auth_header );
		}

        
	
	public function grid(Request $request){
		
		
		         $filtro = ""; $str_filt = "";

                         $page = $request->input( "page");
                         $pagesize = $request->input( "pagesize");  

                         if ( $pagesize == "")
                         	$pagesize = 10;

                         if ( $page == "")
                         	$page = 1;

                         if ( $request->input( "filtro")  != ""){
                         	$str_filt = str_replace("'","''", $request->input( "filtro") );
                         	$filtro .= " and ( nome like '%".$str_filt."%' or email like '%".$str_filt."%' ) ";
                         }


                         $sql = " select count(*) as res from projeto where 1 = 1 ".$filtro ;
                         $total_itens = $this->executeScalar(  $sql );

                         $inicio = 0; $fim = 0;
                         $this->SetaRsetPaginacao($pagesize, $page,$total_itens, $inicio, $fim);

                         $order = $request->input("order");
                         $order_type = $request->input("order_type");
                         if ( $order == ""){
                         	$order = "id";
                         }
                          if ( $order_type == ""){
                         	$order_type = "asc";
                         }

                         $sql = "select p.* from projeto p where 1 = 1 ". $filtro . " order by ".$order. " ".$order_type .
						    $this->get_limit_sql(  $inicio,  $pagesize) ;
                         $itens = DB::select($sql);
                         //OFFSET 50 ROWS FETCH NEXT 100 ROWS ONLY 

                         $saida = array("page"=>$page, "pagesize" => $pagesize, "order"=>$order,
                          "total"=>$total_itens, "total_itens"=> $total_itens,
                          "order_type"=> $order_type, "itens" =>  $itens);

                         return $saida;
		
		
	}

	public function teste(Request $request){
		    
		   $msg_encriptado =   $this->encrypt("Teste");

		   $msg_final  = $this->decrypt(  $msg_encriptado );
		
		   return   $msg_encriptado . " --- a senha antes decriptada Ã©: " . $msg_final;
         }

	public function testpost(Request $request){
		
		
                         $msg = $request->input( "msg");
						 
						 $txt = "Recebido um post. A msg Ã©: ". $msg;
						 
						 return $txt;
	}
	private function loadRequests(Request $request, \App\Projeto &$reg){

            $reg->data = $request->input('data');  
            $reg->id_evento = $request->input('id_evento');  
            $reg->id_operador = $request->input('id_operador');  
            $reg->arquivos = $request->input('arquivos');  
            $reg->meta_dados = $request->input('meta_dados');  
            
           if ( $reg->data == ""){
               $reg->data = \App\Http\Service\UtilService::getCurrentBDdate();
           }
           
           $dia = \App\Http\Dao\ConfigDao::executeScalar("select dia as res from evento where id = ". $id_evento );
           
           $reg->dia = $dia;  
	
            PostsDao::blankToNull(  $reg );

	}
        
        public function removeRegistrosAntigos(){
            
             $sql = " select * from eventos where data <= '2019-10-01 00:00:00'  ";
             $lista = DB::select($sql);
             
             for ( $i  = 0; $i < count($lista); $i++ ){
                 
                 $item = $lista[$i];
                 
                 $subfolder = \App\Http\Service\EventoService::getPrePasta($item, true);
                 $subfolder = str_replace(DIRECTORY_SEPARATOR, "", $subfolder);
                 $subfolder = str_replace("/", "", $subfolder);
                 //die( $subfolder );
                 
                 $path_novo   =  \App\Http\Service\EventoService::getPathEvento2($item, true );
                 $path_antigo =  \App\Http\Service\EventoService::getPathEvento2($item, true, "0" );
                 
                 $this->removeArquivosEvento($item->id,  $path_antigo, $path_novo );			
                 
               DB::statement("delete from  eventos where id = ". $item->id );
                 
             }
             
             die("Feito para ". count($lista));
             
            
        }
        
        public function removeArquivosEvento($id, $path_antigo,  $path_novo){
            $sql = "select id, nome from eventos_arquivos where id_evento = ". $id;
               $lista = DB::select($sql);
               
               for ( $i  = 0; $i < count($lista); $i++ ){
                 
                 $item = $lista[$i];
                 
                    if (file_exists($path_antigo.DIRECTORY_SEPARATOR. $item->nome)){
                        unlink($path_antigo.DIRECTORY_SEPARATOR. $item->nome );
                    }
                    if (file_exists($path_novo.DIRECTORY_SEPARATOR. $item->nome)){
                        unlink($path_novo.DIRECTORY_SEPARATOR. $item->nome );
                    }
                 
               }
               
               DB::statement("delete from  eventos_arquivos where id_evento = ". $id );
             
        }
        
        public function ajustapasta(){
            
            
            set_time_limit ( 0 );
            
             $sql = "select id, tipo, dia, ajusta_pasta from eventos  where ajusta_pasta  = 0 ";
             $lista = DB::select($sql);
             
             for ( $i  = 0; $i < count($lista); $i++ ){
                 
                 $item = $lista[$i];
                 
                 $subfolder = \App\Http\Service\EventoService::getPrePasta($item, true);
                 $subfolder = str_replace(DIRECTORY_SEPARATOR, "", $subfolder);
                 $subfolder = str_replace("/", "", $subfolder);
                 //die( $subfolder );
                 
                 $path_novo   =  \App\Http\Service\EventoService::getPathEvento2($item, true );
                 $path_antigo =  \App\Http\Service\EventoService::getPathEvento2($item, true, "0" );
                 
                 if ( $path_antigo != $path_novo ){
                        \App\Http\Service\EventoService::criarDir($item->dia, $subfolder, $item->id);
                    //echo("antigo: ")
                        $this->moveArquivos($item->id, $subfolder, $path_antigo, $path_novo);
                 }
              
             }
             
             return array("msg"=>"Feito para ". count($lista));
        }
        
        public function ajustamateria(){
            $sql = " select ea.*, e.id_programa, e.id_emissora, e.dia, e.data from eventos_arquivos ea "
                    . " inner join eventos e on e.id = ea.id_evento "
                    . " where ea.id_materia_radiotv_jornal is not null ";
            
             $lista = DB::select($sql);
             
             for ( $i  = 0; $i < count($lista); $i++ ){
                 
                 $item = $lista[$i];
                 
                 $obj_dados = json_decode( $item->meta_dados );
                 $obj_dados->titulo = \App\Http\Dao\ConfigDao::executeScalar2("select titulo  as res from materia_radiotv_jornal where id = ". $item->id_materia_radiotv_jornal );
                 $obj_dados->id_materia_radiotv_jornal = $item->id_materia_radiotv_jornal;
                         
                 $reg = new \App\MateriaRascunho();
                 $reg->id_materia_radiotv_jornal = $item->id_materia_radiotv_jornal;
                 $reg->id_projeto = $item->id_evento;
                 $reg->data = $item->data;
                 $reg->data_cadastro = \App\Http\Service\UtilService::getCurrentBDdate();
                 $reg->dados_materia = json_encode($obj_dados);
                 $reg->dia = $item->dia;
                 $reg->id_programa = $item->id_programa;
                 $reg->status = 1;
                 
                 $reg->titulo = $obj_dados->titulo;
                 $reg->id_projeto = $obj_dados->id_evento;
                 
                 $reg->cliente_list = \App\Http\Service\UtilService::arrayToString($obj_dados->clientes, "nome");
                 
                 $reg->save();

                 
             }
            return array("msg"=>"Feito para ". count($lista) );
        }
        
        
         public function ajustapath(){
            
             $sql = "select id, path, id_evento, nome from eventos_arquivos  ";
             $lista = DB::select($sql);
             
             for ( $i  = 0; $i < count($lista); $i++ ){
                 
                 $item = $lista[$i];
                 $id_evento = $item->id_evento;
                 
                 $path_novo   =  \App\Http\Service\EventoService::getPathEvento($id_evento, false );
                 if ( $path_novo != ""){
                 DB::statement("update eventos_arquivos set path='".$path_novo."/".$item->nome."' where id = ". $item->id );
                 }
                 
             }
             
             return array("msg"=>"Feito para ". count($lista));
        }
        
        
           public function remove_todos_arquivos(Request $request){
               
               $acao = $request->input("acao");
               
               if ( $acao == "remove_tudo"){
            
                        $sql = "select * from eventos ";
                        $lista = DB::select($sql);

                        for ( $i  = 0; $i < count($lista); $i++ ){

                            $item = $lista[$i];
                            $id_evento = $item->id;

                            $path_novo   =  \App\Http\Service\EventoService::getPathEvento2($item, true );
                            if (is_dir($path_novo)){
                            \App\Http\Service\EventoService::rrmdir($path_novo);
                            }
                            //\App\Http\Service\EventoArquivoService::

                        }

                        return array("msg"=>"Feito para ". count($lista));
               }else{
                   
                        return array("msg"=>"Tem que passar o remove_tudo");
               }
        }
        
        public function moveArquivos($id_evento, $subfolder, $path_antigo, $path_novo){
           
            $arquivos = DB::select("select id, path, nome from eventos_arquivos where id_evento = " . $id_evento );
            for ($i = 0; $i < count($arquivos); $i++ ){
                $item = $arquivos[ $i ];
                
                $path_old = $item->path;
                
                if (file_exists($path_antigo.DIRECTORY_SEPARATOR. $item->nome)){
					
					$copiado = false;
					
					if ( ! file_exists($path_novo.DIRECTORY_SEPARATOR. $item->nome )){
						
                    $copiado = copy($path_antigo.DIRECTORY_SEPARATOR. $item->nome, 
                            $path_novo.DIRECTORY_SEPARATOR. $item->nome);
					}else{
						
                    $copiado = true;
					}
                    if ( $copiado ){
                        unlink($path_antigo.DIRECTORY_SEPARATOR. $item->nome); //deleta o arquivo antigo..
                    }else{
                        die("Não consegui copiar ". $path_antigo.DIRECTORY_SEPARATOR. $item->nome . " para: ".
                               $path_antigo.DIRECTORY_SEPARATOR. $item->nome );
                    }
                    
                    //@rename($path_antigo.DIRECTORY_SEPARATOR. $item->nome, );
                }else{
                    echo("Não achei o arquivo: " . $path_antigo.DIRECTORY_SEPARATOR. $item->nome );
                }
                
                if ( ! strpos(" ". $path_old, $subfolder) ){
                        $path_novo_update = str_replace(DIRECTORY_SEPARATOR,"", $subfolder)."/". $path_old;
                        DB::statement("update eventos_arquivos set path='".$path_novo_update."' where id = ". $item->id );
                }
            }
            
                DB::statement("update eventos set ajusta_pasta= 1  where id = ". $id_evento);
        }
        
        
        public function removeOldDirs(){
            
             $sql = "select id,  dia, tipo,  ajusta_pasta from eventos  where 1 = 1  and  ajusta_pasta  =  1  ";
             $lista = DB::select($sql);
             
             for ( $i  = 0; $i < count($lista); $i++ ){
                 
                 $item = $lista[$i];
                 
                 $path_antigo =  \App\Http\Service\EventoService::getPathEvento2($item, true, "0" );
                 
                 if (is_dir($path_antigo)){
                     \App\Http\Service\EventoService::rrmdir($path_antigo);
                 }
                 
             }
             
             return array("msg"=>"Feito para ". count($lista)); 
             
        }

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create(Request $request)
	{
		
               //Na realidade vou criar como um evento, porém um evento do tipo filho..
		$reg = new \App\Projeto;

		$this->loadRequests($request, $reg);
		
		$ret = $reg->save();
                
                

                $path_evento = \App\Http\Service\EventoService::getPathEvento($reg->id_evento);
                
                $path = $path_evento;

                $path .= DIRECTORY_SEPARATOR. $reg->id;
                
                if ( !is_dir($path )){
                    mkdir($path );
                }
                
                $reg->path = \App\Http\Service\EventoService::getPathEvento($reg->id_evento, false)."/". $reg->id; //Pasta do projeto.
                
                $reg->save();
                
                /*
                
                if ( $reg->arquivos != ""){
                      $arquivos = json_decode($reg->arquivos);
                      $files = array();
                      $extensao = "";
                      for($i = 0; $i < count($arquivos); $i++ ){
                          $arquivo = (object)$arquivos[$i];
                          $files[count($files)] = $path_evento. DIRECTORY_SEPARATOR . $arquivo->nome;
                          
                          if ( $i == 0 ){
                             $ext =  explode(".",  $arquivo->nome );
                             $extensao = $ext[count($ext) -1 ];
                          }
                      }
                      
                      $txt_file = \App\Http\Service\FFmpegService::getFileTxt($files, $path  );
                      
                      
                      $nome_final_arquivo = "file_join.".$extensao;
                      
                      
                      $comando_final = "-f concat -safe 0 -i \"" . $txt_file . "\" -c copy " . "\"" . $path . DIRECTORY_SEPARATOR . $nome_final_arquivo . "\"";
                      
                      \App\Http\Service\FFmpegService::executeCommand($comando_final);
                      
                }
                */
                

		$msg = "sucesso!"; $code = 1;
		if (! $ret  ){
                        $code = 0;
                        $msg = "erro";
		}


		return array("msg"=>$msg, "code" =>  $code , "success" => $ret, "results"=> $reg,
                       "item"=> $reg);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//      
	}
        public function agrupanotificacao($id, Request $request){
            $reg_evento_arquivo = \App\EventosArquivos::find($id);
            
            if ( is_null( $reg_evento_arquivo) ){
                die("arquivo não localizado com esta ID");
            }
            
            \App\Http\Dao\EventosArquivosPalavrasDao::agrupaNotificacoes($reg_evento_arquivo);
            
            return array("msg"=>"Feito para ". $id );
        }
        public function testetempo(Request $request){
            
		mb_internal_encoding('UTF-8');
                
               echo(  \App\Http\Service\UtilService::converteSegundos_ParaHoraMinuto(24419.63) );
                die("<br>pare aqui");
             $id = 754; // $request->request("id");  3167
           $reg_arquivo = \App\EventosArquivos::find($id);
           $reg_evento = \App\Eventos::find($reg_arquivo->id_evento);
           
           $palavra = "Secretaria";
           $trecho = "";
           
           $tempo_seg = \App\Http\Service\EventoArquivoService::buscarTextoFrom($reg_arquivo->json, 
                                      $palavra, $trecho);
                        
                              $obj_a = array( 
                                  "cita_diretamente"=> 0,
                                    "id_evento"=> $reg_arquivo->id_evento,
                                   "id_evento_arquivo" => $reg_arquivo->id,
                                   "data"=>$reg_evento->data,
                                   "tempo_seg" => $tempo_seg,
                                   "tempo" => \App\Http\Service\UtilService::converteSegundos_ParaHoraMinuto($tempo_seg),
                                   "palavra"=> $palavra ,
                                  "trecho"=>$trecho
                                  );
             $acao = $request->input("acao");
             
             if ( $acao == "corrige_tempo_arquivo_palavra"){
                 
                 
                 $sql = "select id, tempo_seg, tempo, palavra from eventos_arquivos_palavras where 1 = 1 and tempo_seg < 0 "; //tempo_seg > 1000
                 $lista =DB::select($sql);
                 for ( $i = 0; $i < count($lista); $i++ ){
                     $item = $lista[$i];
                     
                    $reg_arquivo_palavra = \App\EventosArquivosPalavras::find($item->id);
                     
                    $trecho = "";
                    $tempo_seg = \App\Http\Service\EventoArquivoService::buscarTextoFrom($reg_arquivo->json, 
                                       $item->palavra , $trecho);
                    
                    $reg_arquivo_palavra->trecho = $trecho;
                    $reg_arquivo_palavra->tempo_seg = $tempo_seg;
                    $reg_arquivo_palavra->tempo =  \App\Http\Service\UtilService::converteSegundos_ParaHoraMinuto( $tempo_seg );
                    $reg_arquivo_palavra->save();
                     
                     //$tempo_seg = \App\Http\Service\UtilService::time_to_seconds2($item->tempo);
                     //$sql_up  = "update eventos_arquivos_palavras set tempo_seg = ". $tempo_seg . " where id = ". $item->id;
                     
                     //DB::statement($sql_up);
                     
                 }
                 
                     $obj_a["qtde_corrigido"] = count($lista);
             }
             
             if ( $acao == "printa"){
                   $objects = json_decode($reg_arquivo->json);
                   
                  for ( $z = 0; $z < count($objects); $z++ ){
                      
                       $object = $objects[$z];
                       
                        if ( !property_exists($object, "alternatives") || count($object->alternatives) <= 0 )
                           continue;
                        
                        $completo = $object->alternatives[0]->text;
                        
                        $completo_semacento = str_replace("é","e",$completo);
                        //$completo_semacento = @mb_ereg_replace("/[éèê]/","e",$completo);
                        $completo_semacento = \App\Http\Service\UtilService::removeAcentos( $completo );
                        
                       
                        echo("<br><br>". $completo . " ---  ". $completo_semacento ." ---" .   strpos (" ".  $completo_semacento, strtolower( $palavra) )  );
                        
                  }
             }
                              
            return $obj_a;
        }

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		
		   $reg = Projeto::find($id);

           return array( "code" =>  1,  "results"=> $reg, "item"=> $reg);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id, Request $request)
	{
		 return "metodo EDIT";
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id, Request $request)
	{
		   $reg = Projeto::find($id);

		   $this->loadRequests($request, $reg);

			$ret = $reg->save();

		     $msg = "sucesso!"; $code = 1;
			if (! $ret  ){
                  $code = 0;
	              $msg = "erro";
			}
			
        return array("msg"=>$msg, "code" =>  $code , "success" => $ret, "results"=> $reg, "item" => $reg);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy(Request $request)
	{
                $id  = $request->input("id");
		$reg = \App\Eventos::find($id);
                
                $path_evento = \App\Http\Service\EventoService::getPathEvento2($reg, true);
                
                $sql_delete = "delete from eventos_arquivos where id_evento = ". $reg->id;
                DB::statement($sql_delete);
                
                \App\Http\Service\EventoService::rrmdir($path_evento); //Deleta a pasta inteira.
                
		$ret = $reg->delete();
                return array("msg"=>"sucesso", "code" =>  1 , "success" => $ret, "results"=> $reg);
	}
        
        

}
