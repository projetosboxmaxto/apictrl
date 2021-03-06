<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Service\ErrorsService;

use Illuminate\Http\Request;
use App\PalavrasChave;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Dao\ImageDao;
use App\Http\Dao\PostsDao;

class PalavrasChaveController extends Controller {

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
                        . " from palavras_chave p where 1 = 1 ". $filtro . " order by ".$order. " ".$order_type ;
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
	            Route::get('/api/palavras_chave', 'PalavrasChaveController@index');
                Route::get('/api/palavras_chave/{id}', 'PalavrasChaveController@show');
                Route::put('/api/palavras_chave/{id}', 'PalavrasChaveController@update');
                Route::post('/api/palavras_chave', 'PalavrasChaveController@create');
                Route::delete('/api/palavras_chave/{id}', 'PalavrasChaveController@destroy');
				
				Route::resource('users', 'UserAPIController');
				
				*/

        
        function encrypt( $senha ){
               return md5( config("app.CRYPT_PASS") . $senha);
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


                         $sql = " select count(*) as res from palavras_chave where 1 = 1 ".$filtro ;
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

                         $sql = "select p.* from palavras_chave p where 1 = 1 ". $filtro . " order by ".$order. " ".$order_type .
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
		
		   return   $msg_encriptado . " --- a senha antes decriptada ????: " . $msg_final;
         }

	public function testpost(Request $request){
		
		
                         $msg = $request->input( "msg");
						 
						 $txt = "Recebido um post. A msg ????: ". $msg;
						 
						 return $txt;
	}
	private function loadRequests(Request $request, \App\PalavrasChave &$reg){

          $reg->id_cliente = $request->input('id_cliente');  
  $reg->palavra = $request->input('palavra');  
  $reg->data = $request->input('data');  
  $reg->id_praca = $request->input('id_praca');  
		
		
         PostsDao::blankToNull(  $reg );

	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create(Request $request)
	{
		
		$reg = new \App\PalavrasChave;

		$this->loadRequests($request, $reg);
		
		$ret = $reg->save();

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

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		
		   $reg = PalavrasChave::find($id);

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
		   $reg = PalavrasChave::find($id);

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
	public function destroy($id)
	{
		$reg = PalavrasChave::find($id);
		$ret = $reg->delete();
        return array("msg"=>"sucesso", "code" =>  1 , "success" => $ret, "results"=> $reg);
	}
        
        

}
