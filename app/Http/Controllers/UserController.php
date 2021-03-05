<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Service\ErrorsService;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Dao\ImageDao;

class UserController extends Controller {

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
                        	$filtro .= " and ( u.nome like '%".$str_filt."%' or u.email like '%".$str_filt."%' or ug.nome like '%".$str_filt."%' ) ";
               }
               
               
                $sql = "select u.id, u.nome, u.email, u.login, u.created_at, ug.nome as nivel_nome, '' as blnk, '' as user_link, '' as data "
                        . " from user u "
                        . " left join user_group ug on ug.id = u.group_id where 1 = 1 ". $filtro . " order by ".$order. " ".$order_type ;
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

                }

                
                $saida = array(
                             "qtde"=> count($itens),
                             "data" => $itens );
                         
                         
                return $saida;
	}

        
        function encrypt( $senha ){
               return md5( env("CRYPT_PASS") . $senha);
            //  return Hash::make( $senha);
        }
	public function testheader(Request $request){

              $o_auth_header  = $GLOBALS["auth_header"] ;
              return array("msg"=>"Teste", "header" => $o_auth_header );
	}

        
         public function login(Request $request){
             
             $erros = new ErrorsService();
            
                         $login = str_replace("'","''", $request->input( "email"));
                         $senha =  $request->input( "password") ;
                         
                         if ( $login == ""){
                             $erros->add("email","Informe o email");
                         }
                         
                         if ( $senha == ""){
                             $erros->add("password","Informe a senha");
                         }
                         
                         if ( $erros->hasErrors() ){
                             return view("login.login", ["errors" => $erros ]);
                         }
                         
                         
                         $DB_MIDICALIP  = env("DB_MIDICALIP");
                         
                         $sql = " select * from ". $DB_MIDICALIP. ".usuario where upper(login)=upper('". $login."') and upper(senha)=upper('".md5($senha)."') ";
                         
						//die( $sql ." -- ". $senha );
                         $lista = DB::select($sql);
                         
                         if ( count($lista) <= 0 ){
                             
                            $erros->add("email","Não existe usuário com este login");
                            return view("login.login", ["errors" => $erros ]);
                             
                         }
                         
                         $user_b = $lista[0];
                         
                         session(['user.info' => (object) $user_b ]);
                         session(['user.myauth' => $user_b->id."-".$this->getTokenId($user_b->id) ]);
                        // session(['user.group_id' => $user_b->group_id ]);
                         
                         $regUser = new User();
                         //$regUser->setTable($DB_MIDICALIP.".usuario");
                         $regUser->id = $user_b->id;
                         $regUser->nome = $user_b->nome;
                         $regUser->login = $user_b->login;
                         $regUser->senha = $user_b->senha;
                         $regUser->sequencial = $user_b->sequencial;
                         
                         
                         
                         //$logInstance = $regUser->where('id', $user_b->id);
                         
                         //print_r( $regUser);die(" ");
                              
                         Auth::login( $regUser );
                               
                             /*  if ( $user->group_id  == 1 ){
                                   
                               }else{
                                    //  return redirect('/'.$primeiro_item);
                                     
                               }
                              * 
                              * */
                           //die("estou aqui? ". Auth::check());
                           return redirect('/notificacoes3');     
                        // if ( $user->password == )
        }
        
        public function logout(Request $request){
            
            //Auth::logout();
            $request->session()->flush();
            return redirect()->route('login');
        }

	public function login_api(Request $request){


                         if ( trim($login) == ""){
                         	return $this->getErrorMsg("Login vazio!");
                         }
                           if ( trim($request->input( "pass")) == ""){
                         	return $this->getErrorMsg("Senha vazia!");
                         }


                         $sql = " select id, nome, email, login, remember_token as token from user where upper(login)=upper('". $login."') and upper(senha)=upper('". $senha."') ";

                         $itens = DB::select($sql);

                         $code = 2; $msg = "Login e senha não localizado!"; $remember_token = "";

                         if ( count($itens)> 0 ){


                                    $code = 1; $msg = "Login localizado!";
                                    $item = $itens[0];
		                            $remember_token = $this->encrypt( env("PHRASE_PASS")."|". $item->id );
                         }

                      

                       
                       $itens[0]->token = $remember_token;
                        //$sql
                       return array("msg"=>$msg, "code" =>  $code , "results"=> $itens );
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


                         $sql = " select count(*) as res from user where 1 = 1 ".$filtro ;
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

                         $sql = "select u.id, u.nome, u.email, u.login from user u where 1 = 1 ". $filtro . " order by ".$order. " ".$order_type . " limit " . $inicio. ", ". $pagesize ;
                         $itens = DB::select($sql);


                         $saida = array("page"=>$page, "pagesize" => $pagesize, "order"=>$order,
                          "total"=>$total_itens, "total_itens"=> $total_itens,
                          "order_type"=> $order_type, "itens" => $itens );

                         return $saida;
		
		
	}

	public function teste(Request $request){
		    
		   $msg_encriptado =   $this->encrypt("Teste");

		   $msg_final  = $this->decrypt(  $msg_encriptado );
		
		   return   $msg_encriptado . " --- a senha antes decriptada é: " . $msg_final;
         }

	public function testpost(Request $request){
		
		
                         $msg = $request->input( "msg");
						 
						 $txt = "Recebido um post. A msg é: ". $msg;
						 
						 return $txt;
	}
	private function loadRequests(Request $request, \App\User &$reg){

        $reg->nome = $request->input('nome');
		$reg->email = $request->input('email');
		$reg->login = $request->input('email');
		$reg->group_id = $request->input('group_id');

		if ( $request->input('senha') != ""){
   
		         $reg->senha = $this->encrypt( $request->input('senha') );
		}


	}

    public function setaTokenRemember($id){


		//$remember_token = $this->encrypt( env("PHRASE_PASS")."|". $item->id );
		$remember_token = $this->encrypt( "|".$id );

		$afected = DB::update('update user set remember_token = ? where id = ?', [$remember_token, $id]);

        return $afected ;
    }

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create(Request $request)
	{
		//print_r( $req ); 
		//die("to aqui? ");
		$reg = new \App\User;

		$this->loadRequests($request, $reg);
		
		$ret = $reg->save();

		$msg = "sucesso!"; $code = 1;
		if (! $ret  ){
              $code = 0;
              $msg = "erro";
		}

		$this->setaTokenRemember($reg->id);


        return array("msg"=>$msg, "code" =>  $code , "success" => $ret, "results"=> $reg, "item"=> $reg);
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
		
		   $reg = User::find($id);

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
		   $reg = User::find($id);

		   $this->loadRequests($request, $reg);

			$ret = $reg->save();

		     $msg = "sucesso!"; $code = 1;
			if (! $ret  ){
                  $code = 0;
	              $msg = "erro";
			}

		$this->setaTokenRemember($id);
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
		$reg = User::find($id);
		$ret = $reg->delete();
        return array("msg"=>"sucesso", "code" =>  1 , "success" => $ret, "results"=> $reg);
	}
        
        

}
